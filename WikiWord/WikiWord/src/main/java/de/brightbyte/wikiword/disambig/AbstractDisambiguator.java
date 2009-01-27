package de.brightbyte.wikiword.disambig;

import de.brightbyte.io.Output;
import de.brightbyte.wikiword.store.LocalConceptStore;

public abstract class AbstractDisambiguator implements Disambiguator {

	protected LocalConceptStore conceptStore;
	protected Output trace;
	
	public AbstractDisambiguator(LocalConceptStore conceptStore) {
		if (conceptStore==null) throw new NullPointerException();
		this.conceptStore = conceptStore;
	}

	public Output getTrace() {
		return trace;
	}

	public void setTrace(Output trace) {
		this.trace = trace;
	}

	public LocalConceptStore getConceptStore() {
		return conceptStore;
	}

	protected void trace(String msg) {
		if (trace!=null) trace.println(msg);
	}

}