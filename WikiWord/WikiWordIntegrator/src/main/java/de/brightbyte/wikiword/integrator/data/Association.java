package de.brightbyte.wikiword.integrator.data;

public interface Association extends Cloneable {

	public ForeignEntityRecord getForeignEntity();
	public ConceptEntityRecord getConceptEntity();
	public Record getQualifiers();
	
	public Association clone();
	
}
