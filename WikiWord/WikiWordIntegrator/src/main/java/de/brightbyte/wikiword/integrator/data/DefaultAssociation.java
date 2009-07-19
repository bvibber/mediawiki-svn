package de.brightbyte.wikiword.integrator.data;


public class DefaultAssociation extends DefaultRecord implements  Association {

	protected ConceptEntityRecord conceptEntity;
	protected ForeignEntityRecord foreignEntity;
	
	public DefaultAssociation(Record data, 
					String foreignAuthorityField, String foreignIdField, String foreignNameField, 
					String conceptIdField, String conceptNameField) {
		
		addAll(data);
		
		foreignEntity = new DefaultForeignEntityRecord(this.data, foreignAuthorityField, foreignIdField, foreignNameField);
		conceptEntity = new DefaultConceptEntityRecord(this.data, conceptIdField, conceptNameField);
	}

	public ConceptEntityRecord getConceptEntity() {
		return conceptEntity;
	}

	public ForeignEntityRecord getForeignEntity() {
		return foreignEntity;
	}
	
	public String toString() {
		return foreignEntity + " <-> " + conceptEntity;
	}

}
