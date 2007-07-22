package org.wikimedia.lsearch.suggest.api;

import org.wikimedia.lsearch.suggest.api.WordsIndexer.Word;


public interface Dictionary {
	/** Get next term or null if there is no more terms */
	public Word next();
}
