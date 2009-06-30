package de.brightbyte.wikiword.model;


public class LocalConceptReference extends WikiWordConceptReference<LocalConcept> {
	
	public LocalConceptReference(int id, String name, int cardinality, double relevance) {
		super(id, name, cardinality, relevance);
	}

	public static final Factory<LocalConceptReference> factory = new Factory<LocalConceptReference>(){
		public LocalConceptReference newInstance(int id, String name, int cardinality, double relevance) {
			return new LocalConceptReference(id, name, cardinality, relevance);
		}
	
		public LocalConceptReference[] newArray(int size) {
			return new LocalConceptReference[size];
		}
	}; 

	public static LocalConceptReference[] parseList(String s, ListFormatSpec spec) {
		return WikiWordReference.parseList(s, factory, spec);
	}

}
