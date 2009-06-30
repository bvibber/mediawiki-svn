package de.brightbyte.wikiword.model;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;

public class ConceptDescription {
	protected WikiWordResource resource;
	protected Corpus corpus;

	//protected int id;
	//protected String name;
	
	protected ConceptType type;
		
	protected String definition;
	protected TermReference[] terms;
	
	public ConceptDescription(Corpus corpus, WikiWordResource resource, /*int id, String name,*/ ConceptType type, String definition, TermReference[] terms) {
		//if (id<=0) throw new IllegalArgumentException("invalid id: "+id);
		//if (name==null) throw new NullPointerException();
		//if (resource==null) throw new NullPointerException();
		if (type==null) throw new NullPointerException();
		if (terms==null) throw new NullPointerException();
		
		this.resource = resource;
		this.corpus = corpus;
		
		//this.id = id;
		//this.name = name;
		this.definition = definition;
		this.terms = terms;
		this.type = type;
	}
	
	public Corpus getCorpus() {
		return corpus;
	}
	
	public String getDefinition() {
		return definition;
	}
	
	/*
	public int getId() {
		return id;
	}
	
	public String getName() {
		return name;
	}
	*/
	
	public WikiWordResource getResource() {
		return resource;
	}
	
	public TermReference[] getTerms() {
		return terms;
	}
	
}
