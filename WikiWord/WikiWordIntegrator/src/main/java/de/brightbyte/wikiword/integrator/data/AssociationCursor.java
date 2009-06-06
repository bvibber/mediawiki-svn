package de.brightbyte.wikiword.integrator.data;

import java.util.Arrays;

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
		FeatureSet source = newFeatureSet(row, sourceFields);
		FeatureSet target = newFeatureSet(row, targetFields);
		FeatureSet props = newFeatureSet(row, propertyFields);
		
		return new Association(source, target, props);
	}
	
	protected FeatureSet newFeatureSet(FeatureSet row, Iterable<String> fields) {
		FeatureSet m = new DefaultFeatureSet();
		
		int i = 0;
		for (String f: fields) {
			m.putAll(f, row.get(i++)); 
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
