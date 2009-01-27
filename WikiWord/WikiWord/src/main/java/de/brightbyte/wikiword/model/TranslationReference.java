package de.brightbyte.wikiword.model;



public class TranslationReference extends WikiWordReference {

	private String language;

	public TranslationReference(int id, String language, String name, int cardinality, double relevance) {
		super(id, name, cardinality, relevance);
		this.language = language;
	}

	public String getLanguage() {
		return language;
	}
	
	@Override
	public String toString() {
		return language+":"+name;
	}

	public static final Factory<TranslationReference> factory = new Factory<TranslationReference>(){
		public TranslationReference newInstance(int id, String name, int cardinality, double relevance) {
			int idx = name.indexOf(':');
			String lang = "";
			
			if (idx>=0) {
				lang = name.substring(0, idx);
				name = name.substring(idx+1);
			}
			
			return new TranslationReference(id, lang, name, cardinality, relevance);
		}
	
		public TranslationReference[] newArray(int size) {
			return new TranslationReference[size];
		}
	}; 

	public static TranslationReference[] parseList(String s, ListFormatSpec spec) {
		return WikiWordReference.parseList(s, factory, spec);
	}

}
