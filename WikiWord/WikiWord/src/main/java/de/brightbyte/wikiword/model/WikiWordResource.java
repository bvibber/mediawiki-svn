package de.brightbyte.wikiword.model;

import java.net.URL;

import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;

public class WikiWordResource {
	protected Corpus corpus;

	protected int id;
	protected String name;
	
	protected ResourceType type;

	public WikiWordResource(Corpus corpus, int id, String name, ResourceType type) {
		if (id<=0) throw new IllegalArgumentException("invalid id: "+id);
		if (corpus==null) throw new NullPointerException();
		if (name==null) throw new NullPointerException();
		if (type==null) throw new NullPointerException();
		
		//TODO: timestamp?!
		this.corpus = corpus;
		this.id = id;
		this.name = name;
		this.type = type;
	}

	public URL getURL() {
		return corpus.getResourceURL(name); //TODO: with/without timestamp? rev-id? page-id?
	}

	public Corpus getCorpus() {
		return corpus;
	}


	public int getId() {
		return id;
	}


	public String getName() {
		return name;
	}


	public ResourceType getType() {
		return type;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((corpus == null) ? 0 : corpus.hashCode());
		result = PRIME * result + id;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final WikiWordResource other = (WikiWordResource) obj;
		if (corpus == null) {
			if (other.corpus != null)
				return false;
		} else if (!corpus.equals(other.corpus))
			return false;
		if (id != other.id)
			return false;
		return true;
	}

	@Override
	public String toString() {
		return getURL().toExternalForm();
	}
}
