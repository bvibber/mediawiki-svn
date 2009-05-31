package de.brightbyte.wikiword.integrator.data;

import java.util.List;

public class DefaultForeignEntity implements ForeignEntity {

	protected String authorityId;
	protected String idField;
	protected String nameField;
	protected FeatureSet properties;
	
	public DefaultForeignEntity(String authorityId, String idField, String nameField, FeatureSet properties) {
		if (authorityId==null) throw new NullPointerException();
		if (idField==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		if (properties==null) properties = new DefaultFeatureSet();
		
		this.authorityId = authorityId;
		this.idField = idField;
		this.nameField = nameField;
		this.properties = properties;
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
		List<Object> v = properties.get(field);
		if (v==null || v.isEmpty()) return null;
		return String.valueOf(v.get(0));
	}

	public FeatureSet getProperties() {
		return properties;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((authorityId == null) ? 0 : authorityId.hashCode());
		result = PRIME * result + ((idField == null) ? 0 : idField.hashCode());
		result = PRIME * result + ((nameField == null) ? 0 : nameField.hashCode());
		result = PRIME * result + ((properties == null) ? 0 : properties.hashCode());
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
		final DefaultForeignEntity other = (DefaultForeignEntity) obj;
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
		if (properties == null) {
			if (other.properties != null)
				return false;
		} else if (!properties.equals(other.properties))
			return false;
		return true;
	}

	public String toString() {
		return authorityId + "::" + getID();
	}
}
