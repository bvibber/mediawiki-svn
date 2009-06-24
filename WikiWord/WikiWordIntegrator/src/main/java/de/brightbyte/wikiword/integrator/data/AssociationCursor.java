package de.brightbyte.wikiword.integrator.data;

import java.util.Arrays;
import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.CollectionUtils;
import de.brightbyte.util.PersistenceException;

public class AssociationCursor implements DataCursor<Association> {

	private DataCursor<FeatureSet> source;

	protected Iterable<String> foreignFields;
	protected Iterable<String> conceptFields;
	protected Iterable<String> propertyFields;
	
	public AssociationCursor(DataCursor<FeatureSet> source, String[] sourceFields, String[] targetFields, String[] propertyFields) {
		this(source, 
				Arrays.asList(sourceFields), 
				Arrays.asList(targetFields), 
				Arrays.asList(propertyFields));
	}
	
	public AssociationCursor(DataCursor<FeatureSet> source, Iterable<String> sourceFields, Iterable<String> targetFields, Iterable<String> propertyFields) {
		if (source==null) throw new NullPointerException();
		this.source = source;
		this.foreignFields = CollectionUtils.toCleanIterable(sourceFields, false, false);
		this.conceptFields = CollectionUtils.toCleanIterable(targetFields, false, false);
		this.propertyFields = CollectionUtils.toCleanIterable(propertyFields, false, false);
	}

	public Association next() throws PersistenceException {
		FeatureSet row = source.next();
		if (row==null) return null;
		
		return newAssociation(row);
	}

	public Association newAssociation(FeatureSet row) throws PersistenceException {
		FeatureSet source = foreignFields==null ? row : newFeatureSet(row, foreignFields);
		FeatureSet target = conceptFields==null ? row : newFeatureSet(row, conceptFields);
		FeatureSet props = propertyFields==null ? row : newFeatureSet(row, propertyFields);
		
		return new Association(source, target, props);
	}
	
	protected FeatureSet newFeatureSet(FeatureSet row, Iterable<String> fields) {
		FeatureSet m = new DefaultFeatureSet();
		
		for (String f: fields) {
			if (f==null) continue;
			List<Object> values = row.get(f);
			m.putAll(f, values); 
		}
		
		return m;
	}

	public void close() {
		source.close();
	}
	
	protected void finalize() {
		close();
	}

}
