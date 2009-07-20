package de.brightbyte.wikiword.integrator.data;

import java.util.Map;

import de.brightbyte.data.Functor;


public class DefaultForeignEntityRecord extends DefaultRecord implements  ForeignEntityRecord{

	public static class FromRecord implements Functor<DefaultForeignEntityRecord, Record> {
		protected String authorityId;
		protected String idField;
		protected String nameField;

		public FromRecord(String authorityId, String idField, String nameField) {
			if (authorityId==null) throw new NullPointerException();
			if (idField==null) throw new NullPointerException();
			if (nameField==null) nameField = idField;
		}

		public DefaultForeignEntityRecord apply(Record rec) {
			return new DefaultForeignEntityRecord(rec, authorityId, idField, nameField);
		}
		
	}
	
	protected String authorityId;
	protected String idField;
	protected String nameField;
	
	public DefaultForeignEntityRecord(Map<String, Object> data, String authorityId, String idField, String nameField) {
		super(data);
		
		if (authorityId==null) throw new NullPointerException();
		if (idField==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.authorityId = authorityId;
		this.idField = idField;
		this.nameField = nameField;
	}
	
	public DefaultForeignEntityRecord(Record rec, String authorityId, String idField, String nameField) {
		this(rec instanceof DefaultRecord ? ((DefaultRecord)rec).data : null, authorityId, idField, nameField);
		if (!(rec instanceof DefaultRecord)) addAll(data);
	}

	protected DefaultForeignEntityRecord( String authorityId, String idField, String nameField) {
		this((Map<String, Object>)null, authorityId, idField, nameField);
		addAll(data);
	}

	public String getAuthority() {
		return authorityId;
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
	
	public boolean add(String key, Object value) {
		throw new UnsupportedOperationException("foreign entity is unmodifiable");
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = super.hashCode();
		result = PRIME * result + ((authorityId == null) ? 0 : authorityId.hashCode());
		result = PRIME * result + ((idField == null) ? 0 : idField.hashCode());
		result = PRIME * result + ((nameField == null) ? 0 : nameField.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final DefaultForeignEntityRecord other = (DefaultForeignEntityRecord) obj;
		if (authorityId == null) {
			if (other.authorityId != null)
				return false;
		} else if (!authorityId.equals(other.authorityId))
			return false;
		if (idField == null) {
			if (other.idField != null)
				return false;
		} else if (!idField.equals(other.idField))
			return false;
		if (nameField == null) {
			if (other.nameField != null)
				return false;
		} else if (!nameField.equals(other.nameField))
			return false;
		return super.equals(obj);
	}

}
