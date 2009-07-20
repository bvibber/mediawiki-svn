package de.brightbyte.wikiword.integrator.data;


public class GenericAssociation implements  Association {

	private ConceptEntityRecord conceptEntity;
	private ForeignEntityRecord foreignEntity;
	private Record qualifiers;
	
	public GenericAssociation( ForeignEntityRecord foreignEntity, ConceptEntityRecord conceptEntity, Record qualifiers) {
		if (qualifiers==null) throw new NullPointerException(); 
		if (foreignEntity==null) throw new NullPointerException(); 
		if (conceptEntity==null) throw new NullPointerException(); 
		
		this.qualifiers = qualifiers;
		this.foreignEntity = foreignEntity;
		this.conceptEntity =	conceptEntity;
	}

	public ConceptEntityRecord getConceptEntity() {
		return conceptEntity;
	}

	public ForeignEntityRecord getForeignEntity() {
		return foreignEntity;
	}
	
	public Record getQualifiers() {
		return qualifiers;
	}
	
	public String toString() {
		return foreignEntity + " <-> " + conceptEntity;
	}

	public GenericAssociation clone() {
		try {
			return(GenericAssociation)super.clone();
		} catch (CloneNotSupportedException e) {
			throw new Error("CloneNotSupported in clonable object");
		}
	}
}
