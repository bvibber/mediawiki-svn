package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class ForeignEntityCursor implements DataCursor<ForeignEntity> {

	private DataCursor<FeatureSet> source;

	protected String authorityId;
	protected String idField;
	protected String nameField;

	public ForeignEntityCursor(DataCursor<FeatureSet> source, String authorityId, String idField, String nameField) {
		if (idField==null) throw new NullPointerException();
		if (authorityId==null) throw new NullPointerException();
		if (source==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.source = source;
		this.authorityId = authorityId;
		this.idField = idField;
		this.nameField = nameField;
	}

	public ForeignEntity next() throws PersistenceException {
		FeatureSet row = source.next();
		if (row==null) return null;
		
		return newForeignEntity(row);
	}

	public ForeignEntity newForeignEntity(FeatureSet row) throws PersistenceException {
		return new DefaultForeignEntity(authorityId, idField, nameField, row);
	}
	
	public void close() {
		source.close();
	}
	
	protected void finalize() {
		close();
	}

}
