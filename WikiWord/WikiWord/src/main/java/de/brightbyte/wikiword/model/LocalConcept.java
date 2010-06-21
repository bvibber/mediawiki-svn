package de.brightbyte.wikiword.model;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;

public class LocalConcept extends WikiWordConcept {
	
	protected String definition;
	protected String language;
	
	public LocalConcept(Corpus corpus, int id,  ConceptType type, String name) {
		super(corpus, id, type);
		setName(name);
	}

	public Corpus getCorpus() {
		return (Corpus)getDatasetIdentifier();
	}

	public String getDefinition() {
		return definition;
	}

	public void setDefinition(String definition) {
		if (this.definition!=null) throw new IllegalStateException("property already initialized");
		this.definition = definition;
	}

	public String getLanguage() {
		if (language==null) return getCorpus().getLanguage();
		else return language;
	}

	public void setLanguage(String language) {
		if (this.language!=null) throw new IllegalStateException("property already initialized");
		this.language = language;
	}

}
