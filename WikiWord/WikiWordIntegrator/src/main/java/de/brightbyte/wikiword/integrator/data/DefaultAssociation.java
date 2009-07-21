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
		if (conceptNameField==null) conceptNameField = conceptIdField; 
		
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

	public DefaultAssociation(Association assoc) {
		this(new DefaultRecord(assoc.getForeignEntity(), assoc.getConceptEntity(), assoc.getQualifiers()),
				assoc.getForeignEntity().getAuthorityField(), 
				assoc.getForeignEntity().getIDField(),
				assoc.getForeignEntity().getNameField(),
				assoc.getConceptEntity().getIDField(),
				assoc.getConceptEntity().getNameField());
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

	public void aggregate(Association other, Map<String, Accumulator<?, ?>> accumulators) {
		if (other instanceof DefaultAssociation) {
			for (Map.Entry<String, Accumulator<?, ?>> e: accumulators.entrySet()) {
				String field = e.getKey();
				Accumulator<Object, Object> accumulator = (Accumulator<Object, Object>)e.getValue(); //XXX: unsafe cast
				Object b = ((DefaultAssociation)other).data.get(field);
				
				margeValue(field, b, accumulator);
			}
		} else {
			margeValues(other.getForeignEntity(), accumulators);
			margeValues(other.getConceptEntity(), accumulators);
			margeValues(other.getQualifiers(), accumulators);
		}
	}

	protected void margeValues(Record rec, Map<String, Accumulator<?, ?>> accumulators) {
		for (String field: rec.keys()) {
			if (!accumulators.containsKey(field)) continue;
			Accumulator<Object, Object> accumulator = (Accumulator<Object, Object>)accumulators.get(field);  //XXX: unsafe cast
			
			Object b = rec.get(field);
			margeValue(field, b, accumulator);
		}
	}
	
	protected void margeValue(String field, Object b, Accumulator<Object, Object> accumulator) {
			Object a = data.get(field); 
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
				} else if (accumulator == null) {
					c = new ArrayList();
					c.add(a);
					c.add(b);
				} else {
					v = accumulator.apply(a, b); //XXX: unsafe type assumption in apply. not sure accumulator matches object type
				}
			}
			
			if (c!=null) { //if we have a collectiopn, aggregate
				if (c.isEmpty()) return;
				if (accumulator == null) v = c;
				else {
					v = null;
					
					for (Object x: c) {
						if (v==null) v = x;
						else if (x!=null) {
							v = accumulator.apply(v, x);
						}
					}
				}
			} 
			
			data.set(field, v);
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + getForeignEntity().hashCode();
		result = PRIME * result + getConceptEntity().hashCode();
		result = PRIME * result + getQualifiers().hashCode();
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (! (obj instanceof Association))
			return false;
		final Association other = (Association) obj;

		if (!getForeignEntity().equals(other.getForeignEntity())) return false;
		if (!getConceptEntity().equals(other.getConceptEntity())) return false;
		if (!getQualifiers().equals(other.getQualifiers())) return false;
		
		return true;
	}
	
	
}
