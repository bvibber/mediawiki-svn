package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.function.DocValues;
import org.apache.lucene.search.function.ValueSource;

public class RankValueSource extends ValueSource {

	@Override
	public String description() {
		return "";
	}

	@Override
	public boolean equals(Object o) {
		if(o == this)
			return true;
		else
			return false;
	}

	@Override
	public DocValues getValues(IndexReader reader) throws IOException {
		return new RankDocValues(reader);
	}

	@Override
	public int hashCode() {
		return 0;
	}

}
