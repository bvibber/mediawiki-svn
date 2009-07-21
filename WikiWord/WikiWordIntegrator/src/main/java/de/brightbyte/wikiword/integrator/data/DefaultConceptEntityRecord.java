package de.brightbyte.wikiword.integrator.data;

import java.util.Map;

import de.brightbyte.data.Functor;
import de.brightbyte.db.DatabaseUtil;


public class DefaultConceptEntityRecord extends DefaultRecord implements  ConceptEntityRecord{

	public static class FromRecord implements Functor<DefaultConceptEntityRecord, Record> {
		protected String idField;
		protected String nameField;

		public FromRecord(String authorityId, String idField, String nameField) {
			if (idField==null) throw new NullPointerException();
			if (nameField==null) nameField = idField;
		}

		public DefaultConceptEntityRecord apply(Record rec) {
			return new DefaultConceptEntityRecord(rec, idField, nameField);
		}
	}
	
	protected String idField;
	protected String nameField;
	
	public DefaultConceptEntityRecord(String idField, String nameField) {
		this((Map<String, Object>)null, idField, nameField);
	}
	
	public DefaultConceptEntityRecord(Record rec, String idField, String nameField) {
		this(rec instanceof DefaultRecord ? ((DefaultRecord)rec).data : null, idField, nameField);
		if (!(rec instanceof DefaultRecord)) addAll(rec);
	}

	protected DefaultConceptEntityRecord(Map<String, Object> data, String idField, String nameField) {
		super(data);

		if (idField==null) throw new NullPointerException();
		if (nameField==null) nameField = idField;
		
		this.idField = idField;
		this.nameField = nameField;
	}

	public int getID() {
		return idField==null ? null : getIntProperty(idField);
	}

	public String getName() {
		return nameField==null ? null : getStringProperty(nameField);
	}

	private int getIntProperty(String field) {
		Object v = get(field);
		return DatabaseUtil.asInt(v);
	}

	private String getStringProperty(String field) {
		Object v = get(field);
		return DatabaseUtil.asString(v);
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = super.hashCode();
		result = PRIME * result + getID();
		result = PRIME * result + ((getName() == null) ? 0 : getName().hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (!(obj instanceof ConceptEntity))
			return false;
		
		final ConceptEntity other = (ConceptEntity) obj;
		
		if (getID()!=other.getID()) return false;
		
		if (getName()!=null && other.getName()!=null) {
			if (!getName().equals(other.getName())) return false;
		}

		return true;
	}

	public String getIDField() {
		return idField;
	}

	public String getNameField() {
		return nameField;
	}

}
