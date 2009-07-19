package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class AssociationCursor implements DataCursor<Association> {

	private DataCursor<Record> source;

	protected String foreignAuthorityField;
	protected String foreignIdField;
	protected String foreignNameField; 
	protected String conceptIdField;
	protected String conceptNameField;
	
	public AssociationCursor(DataCursor<Record> source, String foreignAuthorityField, String foreignIdField, String foreignNameField, String conceptIdField, String conceptNameField) {
		super();
		this.source = source;
		this.foreignAuthorityField = foreignAuthorityField;
		this.foreignIdField = foreignIdField;
		this.foreignNameField = foreignNameField;
		this.conceptIdField = conceptIdField;
		this.conceptNameField = conceptNameField;
	}

	public Association next() throws PersistenceException {
		Record row = source.next();
		if (row==null) return null;
		
		return newAssociation(row);
	}

	public Association newAssociation(Record row) throws PersistenceException {
		
		return new DefaultAssociation(row, foreignAuthorityField, foreignIdField, foreignNameField, conceptIdField, conceptNameField);
	}
	
	public void close() {
		source.close();
	}
	
	protected void finalize() {
		close();
	}

}
