package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.Collection;
import java.util.HashSet;
import java.util.Iterator;

import javax.naming.OperationNotSupportedException;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.document.SetBasedFieldSelector;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.LocalIndex;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.index.IndexThread;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.spell.api.LuceneDictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;

/**
 * Store/retrieve link analysis results
 * 
 * @author rainman
 *
 */
@Deprecated
public class LinkAnalysisStorage extends LuceneStorage {
	static Logger log = Logger.getLogger(LinkAnalysisStorage.class);
	protected SetBasedFieldSelector selRef;
	
	public LinkAnalysisStorage(IndexId iid){
		//super(iid.getLinkAnalysis());
		super(iid);
		init();
	}
	
	public LinkAnalysisStorage(IndexId iid, String path){
		//super(iid.getLinkAnalysis(),path);
		super(iid,path);
		init();
	}
	
	protected void init(){
		HashSet<String> now = new HashSet<String>();
		HashSet<String> later = new HashSet<String>();
		now.add("references");
		now.add("anchor");
		selRef = new SetBasedFieldSelector(now,later);
	}

	/**
	 * Add rank analysis stuff for a single article  
	 * @throws IOException
	 */
	public void addAnalitics(ArticleAnalytics aa) throws IOException{
		ensureWrite();
		//log.info("Writing analitics "+aa);
		Document doc = new Document();
		doc.add(new Field("key",aa.key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("references",Integer.toString(aa.references),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("anchor",new StringList(aa.anchorText).toString(),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("related",new StringList(Related.convertToStringList(aa.related)).toString(),Field.Store.YES,Field.Index.NO));
		doc.add(new Field("redirect",new StringList(aa.redirectKeys).toString(),Field.Store.YES,Field.Index.NO));
		if(aa.redirectTarget != null)
			doc.add(new Field("redirect_to",aa.redirectTarget,Field.Store.YES,Field.Index.NO));
		writer.addDocument(doc);
	}
	
	/**
	 * Read analitics from latest link analysis index snapshot
	 * @param key ns:title 
	 * @return
	 * @throws IOException
	 */
	public ArticleAnalytics getAnaliticsForArticle(String key) throws IOException{
		ensureRead();
		
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Document d = reader.document(td.doc());
			return getAnalitics(key,d);
		}
		
		return null;
	}
	
	/**
	 * Read analitics from latest link analysis index snapshot
	 * @param key ns:title 
	 * @return
	 * @throws IOException
	 */
	public ArticleAnalytics getAnaliticsForReferences(String key) throws IOException{
		ensureRead();
		
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Document d = reader.document(td.doc(),selRef);
			return getAnalitics(key,d);
		}
		
		return null;
	}
	
	protected ArticleAnalytics getAnalitics(String key, Document d) throws IOException{		
		int ref = d.get("references") == null? 0 : Integer.parseInt(d.get("references"));
		StringList anchor = new StringList(d.get("anchor"));
		StringList related = new StringList(d.get("related"));
		StringList redirect = new StringList(d.get("redirect"));
		String redirectTarget = d.get("redirect_to");
		return new ArticleAnalytics(key,ref,redirectTarget,
				anchor.toCollection(),
				Related.convertToRelatedList(related.toCollection()),
				redirect.toCollection());
	}
	
	public Iterator<ArticleAnalytics> iterator() throws IOException{
		return new LinkAnalysisIterator();
	}
	
	public class LinkAnalysisIterator implements Iterator<ArticleAnalytics>{
		int inx = -1, next = -1;
		int maxdoc;
		
		public LinkAnalysisIterator() throws IOException{
			ensureRead();
			maxdoc = reader.maxDoc();
		}
		
		public boolean hasNext() {
			if(inx >= maxdoc)
				return false;
			if(next == -1){
				for(next=inx+1;next<maxdoc;next++)
					if(!reader.isDeleted(next))
						return true;
				return false;
			} else 
				return next < maxdoc;
		}

		public ArticleAnalytics next() {
			if(next != -1){
				if(next >= maxdoc)
					return null; // EOS in hasNext()
				inx = next;
				next = -1;	
			} else{
				if(inx == -1)
					inx = 0;
				for(;inx<maxdoc;inx++){
					if(!reader.isDeleted(inx))
						break;
				}
				if(inx >= maxdoc)
					return null; // EOS
			}
			try{
				Document d = reader.document(inx);
				return getAnalitics(d.get("key"),d);
			} catch(IOException e){
				//TODO: Java is not letting us throw exception here
				log.error("I/O exception in LinkAnalysisIterator:next() : "+e.getMessage());
				return null;				
			}
		}

		public void remove() {
			throw new UnsupportedOperationException();
			
		}
		
	}
}
