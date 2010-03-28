package de.brightbyte.wikiword.model;

import java.util.Map;

public class ConceptRelations<R extends WikiWordConcept> {

	protected R[] inlinks;
	protected R[] outlinks;
	protected R[] broader;
	protected R[] narrower;
	protected R[] similar;
	protected R[] related;
	protected Map<String, LocalConcept> langlinks;
	
	//TODO: inlinks, outlinks, coocc, co-coocc

	public ConceptRelations(R[] broader, 
			R[] narrower, 
			R[] inlinks, 
			R[] outlinks, 
			R[] similar,
			R[] related,
			Map<String, LocalConcept> langlinks) {
		
		if (inlinks==null) throw new NullPointerException();
		if (outlinks==null) throw new NullPointerException();
		if (broader==null) throw new NullPointerException();
		if (narrower==null) throw new NullPointerException();
		//if (similar==null) throw new NullPointerException();
		//if (related==null) throw new NullPointerException();
		if (langlinks==null) throw new NullPointerException();
	
		this.inlinks = inlinks;
		this.outlinks = outlinks;
		this.broader = broader;
		this.narrower = narrower;
		this.similar = similar;
		this.related = related;
		this.langlinks = langlinks;
	}

	public R[] getBroader() {
		return broader;
	}

	public Map<String, LocalConcept> getLanglinks() {
		return langlinks;
	}

	public R[] getNarrower() {
		return narrower;
	}
	
	public R[] getSimilar() {
		return similar;
	}

	public R[] getRelated() {
		return related;
	}

	public R[] getInLinks() {
		return inlinks;
	}
	
	public R[] getOutLinks() {
		return outlinks;
	}
	
}
