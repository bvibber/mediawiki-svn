package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.search.function.DocValues;

public class RankDocValues extends DocValues {
	IndexReader reader;
	
	public RankDocValues(IndexReader reader){
		super(reader.maxDoc());
		this.reader = reader;
	}
	
	protected int getValue(int doc){
		try{
			return Integer.parseInt(reader.document(doc).get("rank"));
		} catch(IOException e){
			return 0;
		}
	}
	
	@Override
	public float floatVal(int doc) {
		return getValue(doc);
	}

	@Override
	public String toString(int doc) {
		return "rank: "+getValue(doc);
	}

}
