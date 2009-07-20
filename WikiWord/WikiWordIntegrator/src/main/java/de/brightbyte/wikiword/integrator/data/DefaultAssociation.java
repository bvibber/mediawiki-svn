package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;
import java.util.Map;

import de.brightbyte.data.Accumulator;



public class DefaultAssociation implements  Association {

	private static final long serialVersionUID = 4662164671166377257L;
	
	private ConceptEntityRecord conceptEntity;
	private ForeignEntityRecord foreignEntity;
	
	private Record data;
	
	private String foreignAuthorityField;
	private String foreignIdField;
	private String foreignNameField;
	private String conceptIdField;
	private String conceptNameField;
	
	public DefaultAssociation(Record data, 
					String foreignAuthorityField, String foreignIdField, String foreignNameField, 
					String conceptIdField, String conceptNameField) {
		
		if (data==null) throw new NullPointerException(); 
		if (foreignAuthorityField==null) throw new NullPointerException(); 
		if (foreignIdField==null) throw new NullPointerException(); 
		if (conceptIdField==null) throw new NullPointerException(); 
		if (foreignNameField==null) foreignNameField = foreignIdField; 
		if (conceptNameField==null) conceptNameField = conceptNameField; 
		
		this.data = data;
		this.foreignAuthorityField = foreignAuthorityField;
		this.foreignIdField = foreignIdField;
		this.foreignNameField = foreignNameField;
		this.conceptIdField = conceptIdField;
		this.conceptNameField = conceptNameField;
		
		data.addAll(data);
		
		foreignEntity = new DefaultForeignEntityRecord(this.data, foreignAuthorityField, foreignIdField, foreignNameField);
		conceptEntity = new DefaultConceptEntityRecord(this.data, conceptIdField, conceptNameField);
	}

	public ConceptEntityRecord getConceptEntity() {
		return conceptEntity;
	}

	public ForeignEntityRecord getForeignEntity() {
		return foreignEntity;
	}
	
	public Record getQualifiers() {
		return data;
	}
	
	public String toString() {
		return data.toString();
	}

	public DefaultAssociation clone() {
			try {
				DefaultAssociation r = (DefaultAssociation)super.clone();
				r.data = data.clone();
				r.foreignEntity = new DefaultForeignEntityRecord(r.data, foreignAuthorityField, foreignIdField, foreignNameField);
				r.conceptEntity = new DefaultConceptEntityRecord(r.data, conceptIdField, conceptNameField);
				return r;
			} catch (CloneNotSupportedException e) {
				throw new Error("CloneNotSupported in clonable object");
			}
	}

	public void aggregate(DefaultAssociation add, Map<String, Accumulator<?, ?>> accumulators) {
			for (Map.Entry<String, Accumulator<?, ?>> e: accumulators.entrySet()) {
				String field = e.getKey();
				Accumulator<Object, Object> accumulator = (Accumulator<Object, Object>)e.getValue(); //XXX: unsafe case
				
				Object a = data.get(field); 
				Object b = add.data.get(field); 
				Object v = null;
				Collection c = null;
				
				if (a==null) v  = b;
				else if (b==null) v  = a;
				else { 
					//deal with multi-value fields
					if (a instanceof Collection) {
						if (b instanceof Collection) {
							((Collection)a).addAll((Collection)b);
						} else {
							((Collection)a).add(b);
						}
						c = (Collection)a;
					} else if (b instanceof Collection) {
						c = new ArrayList();
						c.addAll((Collection)b);
						c.add(a);
					} else {
						v = accumulator.apply(a, b); //XXX: unsafe type assumption in apply. not sure accumulator matches object type
					}
				}
				
				if (c!=null) { //if we have a collectiopn, aggregate
					if (c.isEmpty()) return;
					v = null;
					
					for (Object x: c) {
						if (v==null) v = x;
						else if (x!=null) {
							v = accumulator.apply(v, x);
						}
					}
				} 
				
				data.set(field, v);
			}
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((conceptEntity == null) ? 0 : conceptEntity.hashCode());
		result = PRIME * result + ((conceptIdField == null) ? 0 : conceptIdField.hashCode());
		result = PRIME * result + ((conceptNameField == null) ? 0 : conceptNameField.hashCode());
		result = PRIME * result + ((data == null) ? 0 : data.hashCode());
		result = PRIME * result + ((foreignAuthorityField == null) ? 0 : foreignAuthorityField.hashCode());
		result = PRIME * result + ((foreignEntity == null) ? 0 : foreignEntity.hashCode());
		result = PRIME * result + ((foreignIdField == null) ? 0 : foreignIdField.hashCode());
		result = PRIME * result + ((foreignNameField == null) ? 0 : foreignNameField.hashCode());
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
		final DefaultAssociation other = (DefaultAssociation) obj;
		if (conceptEntity == null) {
			if (other.conceptEntity != null)
				return false;
		} else if (!conceptEntity.equals(other.conceptEntity))
			return false;
		if (conceptIdField == null) {
			if (other.conceptIdField != null)
				return false;
		} else if (!conceptIdField.equals(other.conceptIdField))
			return false;
		if (conceptNameField == null) {
			if (other.conceptNameField != null)
				return false;
		} else if (!conceptNameField.equals(other.conceptNameField))
			return false;
		if (data == null) {
			if (other.data != null)
				return false;
		} else if (!data.equals(other.data))
			return false;
		if (foreignAuthorityField == null) {
			if (other.foreignAuthorityField != null)
				return false;
		} else if (!foreignAuthorityField.equals(other.foreignAuthorityField))
			return false;
		if (foreignEntity == null) {
			if (other.foreignEntity != null)
				return false;
		} else if (!foreignEntity.equals(other.foreignEntity))
			return false;
		if (foreignIdField == null) {
			if (other.foreignIdField != null)
				return false;
		} else if (!foreignIdField.equals(other.foreignIdField))
			return false;
		if (foreignNameField == null) {
			if (other.foreignNameField != null)
				return false;
		} else if (!foreignNameField.equals(other.foreignNameField))
			return false;
		return true;
	}
	
	
}
