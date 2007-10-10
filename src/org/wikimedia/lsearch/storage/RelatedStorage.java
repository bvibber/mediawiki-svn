package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;

import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.related.CompactRelated;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;

/**
 * Store related articles
 * 
 * @author rainman
 *
 */
public class RelatedStorage extends LuceneStorage {
	
	public RelatedStorage(IndexId iid, String path){
		super(iid.getRelated(),path);
	}
	
	public RelatedStorage(IndexId iid){
		super(iid.getRelated());
	}

	public void addCompactRelated(String key, Collection<CompactRelated> rel) throws IOException{
		ensureWrite();
		StringList sl = new StringList(CompactRelated.convertToStringList(rel));
		Document doc = new Document();
		doc.add(new Field("key",key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("related",sl.toString(),Field.Store.COMPRESS,Field.Index.NO));
		writer.addDocument(doc);
	}
	
	public void addRelated(String key, Collection<Related> rel) throws IOException{
		ensureWrite();
		StringList sl = new StringList(Related.convertToStringList(rel));
		Document doc = new Document();
		doc.add(new Field("key",key,Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("related",sl.toString(),Field.Store.COMPRESS,Field.Index.NO));
		writer.addDocument(doc);
	}
	
	public ArrayList<RelatedTitle> getRelated(String key) throws IOException{
		ensureRead();
		
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Document d = reader.document(td.doc());
			return Related.convertToRelatedTitleList(new StringList(d.get("related")).toCollection());
		} 
		
		return new ArrayList<RelatedTitle>();		
	}
}
