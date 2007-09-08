package org.wikimedia.lsearch.search;

import java.io.IOException;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.store.Directory;

/** IndexSearcher that can return multiple documents in one method call */
public class IndexSearcherMul extends IndexSearcher implements SearchableMul {

	public IndexSearcherMul(Directory directory) throws IOException {
		super(directory);
	}

	public IndexSearcherMul(IndexReader r) {
		super(r);
	}

	public IndexSearcherMul(String path) throws IOException {
		super(path);
	}

	public Document[] docs(int[] docIds) throws IOException {
		  Document[] ds = new Document[docIds.length];
		  for(int j=0;j<docIds.length;j++)
		    ds[j] = doc(docIds[j]);
		  return ds;
	}
	
	public Document[] docs(int[] docIds, FieldSelector sel) throws IOException {
		  Document[] ds = new Document[docIds.length];
		  for(int j=0;j<docIds.length;j++)
		    ds[j] = doc(docIds[j],sel);
		  return ds;
	}

	@Override
	public String toString() {
		return "IndexSearcherMul:"+getIndexReader().getVersion();
	}
	
	

}
