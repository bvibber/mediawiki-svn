package de.brightbyte.wikiword.model;



public class GlobalConceptReference extends WikiWordConceptReference<GlobalConcept> {

	public GlobalConceptReference(int id, String name, int cardinality, double relevance) {
		super(id, name, cardinality, relevance);
	}

	public static final Factory<GlobalConceptReference> factory = new Factory<GlobalConceptReference>(){
		public GlobalConceptReference newInstance(int id, String name, int cardinality, double relevance) {
			return new GlobalConceptReference(id, name, cardinality, relevance);
		}
	
		public GlobalConceptReference[] newArray(int size) {
			return new GlobalConceptReference[size];
		}
	}; 

	public static GlobalConceptReference[] parseList(String s, ListFormatSpec spec) {
		return WikiWordReference.parseList(s, factory, spec);
	}

}
