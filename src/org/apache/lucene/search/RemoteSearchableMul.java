package org.apache.lucene.search;

import java.io.IOException;
import java.rmi.RemoteException;
import java.rmi.server.UnicastRemoteObject;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.Term;

public class RemoteSearchableMul extends UnicastRemoteObject
implements SearchableMul {
	  
	  private Searchable local;
	  
	  /** Constructs and exports a remote searcher. */
	  public RemoteSearchableMul(Searchable local) throws RemoteException {
	    super();
	    this.local = local;
	  }


	  public void search(Weight weight, Filter filter, HitCollector results)
	    throws IOException {
	    local.search(weight, filter, results);
	  }

	  public void close() throws IOException {
	    local.close();
	  }

	  public int docFreq(Term term) throws IOException {
	    return local.docFreq(term);
	  }


	  public int[] docFreqs(Term[] terms) throws IOException {
	    return local.docFreqs(terms);
	  }

	  public int maxDoc() throws IOException {
	    return local.maxDoc();
	  }

	  public TopDocs search(Weight weight, Filter filter, int n) throws IOException {
	    return local.search(weight, filter, n);
	  }


	  public TopFieldDocs search (Weight weight, Filter filter, int n, Sort sort)
	  throws IOException {
	    return local.search (weight, filter, n, sort);
	  }

	  public Document doc(int i) throws IOException {
	    return local.doc(i);
	  }
	  
	  public Document[] docs(int[] docIds) throws IOException {
		  Document[] ds = new Document[docIds.length];
		  for(int j=0;j<docIds.length;j++)
		    ds[j] = local.doc(docIds[j]);
		  return ds;
	  }
	  
		public Document[] docs(int[] docIds, FieldSelector sel) throws IOException {
			Document[] ds = new Document[docIds.length];
			for(int j=0;j<docIds.length;j++)
				ds[j] = local.doc(docIds[j],sel);
			return ds;
		}

	  public Query rewrite(Query original) throws IOException {
	    return local.rewrite(original);
	  }

	  public Explanation explain(Weight weight, int doc) throws IOException {
	    return local.explain(weight, doc);
	  }


	  public Document doc(int n, FieldSelector fieldSelector) throws CorruptIndexException, IOException {
		  return local.doc(n,fieldSelector);
	  }

}
