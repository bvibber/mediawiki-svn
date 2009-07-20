package de.brightbyte.wikiword.integrator.data;

public interface Association extends Record, Cloneable {

	public ForeignEntityRecord getForeignEntity();
	public ConceptEntityRecord getConceptEntity();
	public DefaultAssociation clone();
	
}
