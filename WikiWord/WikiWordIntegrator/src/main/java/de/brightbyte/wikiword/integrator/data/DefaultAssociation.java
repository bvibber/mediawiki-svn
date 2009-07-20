package de.brightbyte.wikiword.integrator.data;

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
		return foreignEntity + " <-> " + conceptEntity;
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
				
				Object a = data.getPrimitive(field); //TODO: aggregate instead?!
				Object b = add.data.getPrimitive(field); //TODO: aggregate instead?!
				Object v;
				
				if (a==null) v  = b;
				else if (b==null) v  = a;
				else v = accumulator.apply(a, b); //XXX: unsafe type assumption in apply. not sure accumulator matches object type
				
				data.set(field, v);
			}
	}
}
