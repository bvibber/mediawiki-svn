package de.brightbyte.wikiword.integrator.data;


public class DefaultAssociation extends DefaultRecord implements  Association {

	private static final long serialVersionUID = 4662164671166377257L;
	
	private ConceptEntityRecord conceptEntity;
	private ForeignEntityRecord foreignEntity;
	
	private String foreignAuthorityField;
	private String foreignIdField;
	private String foreignNameField;
	private String conceptIdField;
	private String conceptNameField;
	
	public DefaultAssociation(Record data, 
					String foreignAuthorityField, String foreignIdField, String foreignNameField, 
					String conceptIdField, String conceptNameField) {
		
		this.foreignAuthorityField = foreignAuthorityField;
		this.foreignIdField = foreignIdField;
		this.foreignNameField = foreignNameField;
		this.conceptIdField = conceptIdField;
		this.conceptNameField = conceptNameField;
		
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

	public DefaultAssociation clone() {
			DefaultAssociation r = (DefaultAssociation)super.clone();
			r.foreignEntity = new DefaultForeignEntityRecord(r.data, foreignAuthorityField, foreignIdField, foreignNameField);
			r.conceptEntity = new DefaultConceptEntityRecord(r.data, conceptIdField, conceptNameField);
			return r;
	}
}
