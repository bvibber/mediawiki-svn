package de.brightbyte.wikiword.integrator.data;

import java.util.Map;


public class DefaultConceptEntityRecord extends DefaultRecord implements  ConceptEntityRecord{

	protected String idField;
	protected String nameField;
	
	public DefaultConceptEntityRecord(String idField, String nameField) {
		this(null, idField, nameField);
	}
	
	protected DefaultConceptEntityRecord(Map<String, Object> data, String idField, String nameField) {
		super(data);
		
		if (idField==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.idField = idField;
		this.nameField = nameField;
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
		final DefaultConceptEntityRecord other = (DefaultConceptEntityRecord) obj;
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
