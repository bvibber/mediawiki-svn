package de.brightbyte.wikiword.model;

import java.text.Collator;
import java.util.Comparator;
import java.util.Locale;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;

public class LocalConcept extends WikiWordConcept {
	
	public static class ByName implements Comparator<LocalConcept> {
		protected Collator collator;
		
		public ByName() {
			this((Collator)null);
		}
		
		public ByName(Locale locale) {
			this.collator = Collator.getInstance(locale);
		}
		
		public ByName(Collator collator) {
			this.collator = collator;
		}
		
		public int compare(LocalConcept a, LocalConcept b) {
			if (collator==null) return a.getName().compareTo(b.getName());
			else return collator.compare(a.getName(), b.getName());
		}
	};
	
	protected String name;
	protected String definition;
	protected String language;
	protected WikiWordResource resource;
	
	public LocalConcept(Corpus corpus, int id,  ConceptType type, String name) {
		super(corpus, id, type);

		this.name = name;
	}

	@Override
	public String toString() {
		if (name!=null) return "#"+id+":[["+name+"]]";
		else return "#"+id;
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

	public WikiWordResource getResource() {
		return resource;
	}

	public void setResource(WikiWordResource resource) {
		if (this.resource!=null) throw new IllegalStateException("property already initialized");
		this.resource = resource;
	}

	public String getLanguage() {
		if (language==null) return getCorpus().getLanguage();
		else return language;
	}

	public void setLanguage(String language) {
		if (this.language!=null) throw new IllegalStateException("property already initialized");
		this.language = language;
	}

	public String getName() {
		return name;
	}

	public void setName(String name) {
		if (this.name!=null) throw new IllegalStateException("property already initialized");
		this.name = name;
	}

}
