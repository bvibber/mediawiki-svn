package de.brightbyte.wikiword.integrator.data;

import java.util.Arrays;
import java.util.List;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class AssociationCursor implements DataCursor<Association> {

	private DataCursor<FeatureSet> source;

	protected Iterable<String> sourceFields;
	protected Iterable<String> targetFields;
	protected Iterable<String> propertyFields;
	
	public AssociationCursor(DataCursor<FeatureSet> source, String[] sourceFields, String[] targetFields, String[] propertyFields) {
		this(source, Arrays.asList(sourceFields), Arrays.asList(targetFields), Arrays.asList(propertyFields));
	}
	
	public AssociationCursor(DataCursor<FeatureSet> source, Iterable<String> sourceFields, Iterable<String> targetFields, Iterable<String> propertyFields) {
		if (source==null) throw new NullPointerException();
		this.source = source;
		this.sourceFields = sourceFields;
		this.targetFields = targetFields;
		this.propertyFields = propertyFields;
	}

	public Association next() throws PersistenceException {
		FeatureSet row = source.next();
		if (row==null) return null;
		
		return newAssociation(row);
	}

	public Association newAssociation(FeatureSet row) throws PersistenceException {
		FeatureSet source = sourceFields==null ? row : newFeatureSet(row, sourceFields);
		FeatureSet target = targetFields==null ? row : newFeatureSet(row, targetFields);
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
