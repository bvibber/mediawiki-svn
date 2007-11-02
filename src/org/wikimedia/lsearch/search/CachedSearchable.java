package org.wikimedia.lsearch.search;

import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectOutputStream;
import java.io.Serializable;
import java.util.Arrays;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.Explanation;
import org.apache.lucene.search.Filter;
import org.apache.lucene.search.HitCollector;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.Searchable;
import org.apache.lucene.search.SearchableMul;
import org.apache.lucene.search.Sort;
import org.apache.lucene.search.TopDocs;
import org.apache.lucene.search.TopFieldDocs;
import org.apache.lucene.search.Weight;

/**
 * Wrapper for remote searchable objects. 
 * 
 * Locally cache maxDoc() call of remote Searchable, to avoid
 * calling the method each time remotely when MultiSearcher is 
 * constructed (which is at every search). 
 * 
 * @author rainman
 *
 */
public class CachedSearchable implements SearchableMul {
	static org.apache.log4j.Logger log = Logger.getLogger(CachedSearchable.class);
	protected SearchableMul searchable;
	protected int maxDocCached;		
	
	CachedSearchable(SearchableMul searchable){
		maxDocCached = -1;
		this.searchable = searchable;
		log.debug("New cached searchable for "+searchable);
	}
	
	public void close() throws IOException {
		log.debug("called close()");
		try{
			searchable.close();
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);		
			throw new IOException(e.getMessage());
		}
	}
	
	public Document doc(int i) throws IOException {
		log.debug("called doc("+i+")");
		try{
			return searchable.doc(i);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}
	
	public Document doc(int i, FieldSelector sel) throws IOException {
		log.debug("called doc("+i+","+sel+")");
		try{
			return searchable.doc(i,sel);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}
	
	public Document[] docs(int[] i) throws IOException {
		log.debug("called docs("+Arrays.toString(i)+")");
		try{
			return searchable.docs(i);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}
	
	public Document[] docs(int[] i, FieldSelector sel) throws IOException {
		log.debug("called docs("+Arrays.toString(i)+","+sel+")");
		try{
			return searchable.docs(i,sel);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public int docFreq(Term term) throws IOException {
		log.debug("called docFreq("+term+")");
		Thread.dumpStack();
		try{
			return searchable.docFreq(term);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public int[] docFreqs(Term[] terms) throws IOException {
		log.debug("called docFreqs("+Arrays.toString(terms)+")");
		try{
			return searchable.docFreqs(terms);
		} catch(Exception e){
			e.printStackTrace();
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public Explanation explain(Weight weight, int doc) throws IOException {
		log.debug("called explaint("+weight+","+doc+")");
		try{
			return searchable.explain(weight,doc);
		} catch(Exception e){
			e.printStackTrace();
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public int maxDoc() throws IOException {
		try{
			if(maxDocCached == -1){
				log.debug("called maxDoc(), fetching initial value");
				maxDocCached = searchable.maxDoc();
			} else
				log.debug("called maxDoc(), returning cached value: "+maxDocCached);
			return maxDocCached;
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public Query rewrite(Query query) throws IOException {
		log.debug("called rewrite("+query+")");
		try{
			return searchable.rewrite(query);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public void search(Weight weight, Filter filter, HitCollector results) throws IOException {
		log.debug("called search("+weight+","+filter+","+results+")");
		try{
			searchable.search(weight,filter,results);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public TopFieldDocs search(Weight weight, Filter filter, int n, Sort sort) throws IOException {
		log.debug("called search("+weight+","+filter+","+n+","+sort+")");
		try{
			return searchable.search(weight,filter,n,sort);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	public TopDocs search(Weight weight, Filter filter, int n) throws IOException {
		log.debug("called search("+weight+","+filter+","+n+")");
		try{
			return searchable.search(weight,filter,n);
		} catch(Exception e){
			SearcherCache.getInstance().checkSearchable(searchable,this);
			throw new IOException(e.getMessage());
		}
	}

	@Override
	public String toString() {
		return searchable.toString();
	}

	
}
