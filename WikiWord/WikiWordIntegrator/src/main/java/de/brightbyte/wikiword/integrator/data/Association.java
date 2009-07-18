package de.brightbyte.wikiword.integrator.data;

public interface Association extends Record {

	public ForeignEntityRecord getForeignEntity();
	public ConceptEntityRecord getConceptEntity();
	
}
