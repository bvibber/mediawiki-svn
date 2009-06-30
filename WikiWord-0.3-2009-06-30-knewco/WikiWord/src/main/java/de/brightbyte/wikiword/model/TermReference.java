package de.brightbyte.wikiword.model;



public class TermReference extends WikiWordReference {

	public TermReference(int id, String term, int cardinality, double relevance) {
		super(id, term, cardinality, relevance);
	}

	public static final Factory<TermReference> factory = new Factory<TermReference>(){
		public TermReference newInstance(int id, String name, int cardinality, double relevance) {
			return new TermReference(id, name, cardinality, relevance);
		}
	
		public TermReference[] newArray(int size) {
			return new TermReference[size];
		}
	}; 

	public static TermReference[] parseList(String s, ListFormatSpec spec) {
		return WikiWordReference.parseList(s, factory, spec);
	}

}
