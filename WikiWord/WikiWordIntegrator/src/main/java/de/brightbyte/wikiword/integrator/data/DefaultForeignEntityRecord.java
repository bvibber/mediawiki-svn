package de.brightbyte.wikiword.integrator.data;

import java.util.Map;

import de.brightbyte.data.Functor;


public class DefaultForeignEntityRecord extends DefaultRecord implements  ForeignEntityRecord{

	public static class FromRecord implements Functor<DefaultForeignEntityRecord, Record> {
		protected String authorityField;
		protected String idField;
		protected String nameField;

		public FromRecord(String authorityField, String idField, String nameField) {
			if (authorityField==null) throw new NullPointerException();
			if (idField==null) throw new NullPointerException();
			if (nameField==null) nameField = idField;
			
			this.authorityField= authorityField;
			this.idField= idField;
			this.nameField= nameField;
		}

		public DefaultForeignEntityRecord apply(Record rec) {
			return new DefaultForeignEntityRecord(rec, authorityField, idField, nameField);
		}
		
	}
	
	protected String authorityField;
	protected String idField;
	protected String nameField;
	
	public DefaultForeignEntityRecord(Map<String, Object> data, String authorityField, String idField, String nameField) {
		super(data);
		
		if (authorityField==null) throw new NullPointerException();
		if (idField==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.authorityField = authorityField;
		this.idField = idField;
		this.nameField = nameField;
	}
	
	public DefaultForeignEntityRecord(Record rec, String authorityField, String idField, String nameField) {
		this(rec instanceof DefaultRecord ? ((DefaultRecord)rec).data : null, authorityField, idField, nameField);
		if (!(rec instanceof DefaultRecord)) addAll(data);
	}

	protected DefaultForeignEntityRecord( String authorityField, String idField, String nameField) {
		this((Map<String, Object>)null, authorityField, idField, nameField);
		addAll(data);
	}

	public String getAuthority() {
		return authorityField;
	}

	public String getID() {
		return idField==null ? null : getStringProperty(idField);
	}

	public String getName() {
		return nameField==null ? null : getStringProperty(nameField);
	}

	private String getStringProperty(String field) {
		Object v = get(field);
		return String.valueOf(v);
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = super.hashCode();
		result = PRIME * result + getID().hashCode();
		result = PRIME * result + getAuthority().hashCode();
		result = PRIME * result + ((getName() == null) ? 0 : getName().hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (!(obj instanceof ForeignEntity))
			return false;
		
		final ForeignEntity other = (ForeignEntity) obj;
		
		if (!getAuthority().equals(other.getAuthority())) return false;
		if (!getID().equals(other.getID())) return false;
		
		if (getName()!=null && other.getName()!=null) {
			if (!getName().equals(other.getName())) return false;
		}

		return true;
	}

	public String getAuthorityField() {
		return authorityField;
	}

	public String getIDField() {
		return idField;
	}

	public String getNameField() {
		return nameField;
	}
	

}
