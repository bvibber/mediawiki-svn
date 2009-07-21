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
