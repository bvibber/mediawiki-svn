package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class ForeignEntityFeatureSetCursor implements DataCursor<DefaultForeignEntityFeatureSet> {

	private DataCursor<FeatureSet> source;

	protected String authorityId;
	protected String idField;
	protected String nameField;

	public ForeignEntityFeatureSetCursor(DataCursor<FeatureSet> source, String authorityId, String idField, String nameField) {
		if (idField==null) throw new NullPointerException();
		if (authorityId==null) throw new NullPointerException();
		if (source==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.source = source;
		this.authorityId = authorityId;
		this.idField = idField;
		this.nameField = nameField;
	}

	public DefaultForeignEntityFeatureSet next() throws PersistenceException {
		FeatureSet row = source.next();
		if (row==null) return null;
		
		return newForeignEntity(row);
	}

	public DefaultForeignEntityFeatureSet newForeignEntity(FeatureSet row) throws PersistenceException {
		DefaultForeignEntityFeatureSet fs = new DefaultForeignEntityFeatureSet(authorityId, idField, nameField);
		fs.addAll(row);
		return fs;
	}
	
	public void close() {
		source.close();
	}
	
	protected void finalize() {
		close();
	}

}
