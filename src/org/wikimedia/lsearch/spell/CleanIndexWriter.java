package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.Collection;
import java.util.HashSet;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.Field.Index;
import org.apache.lucene.document.Field.Store;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.PrefixAnalyzer;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.Transaction;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.search.NamespaceFilter;

/**
 * IndexWriter for making temporary "clean" indexes which
 * are to be used to rebuild the word-suggest indexes
 * 
 * @author rainman
 *
 */
public class CleanIndexWriter {
	static Logger log = Logger.getLogger(CleanIndexWriter.class);
	protected IndexId iid;
	protected IndexWriter writer;
	protected FieldBuilder builder;
	protected String langCode;
	protected Analyzer analyzer;
	protected HashSet<String> stopWords;
	protected NamespaceFilter nsf;
	
	/** Make a new index, and init writer on it (on importPath())*/
	public static CleanIndexWriter newForWrite(IndexId iid) throws IOException{
		CleanIndexWriter w = new CleanIndexWriter(iid);
		w.openWriter(iid.getImportPath(),true);
		w.addMetadata("stopWords",w.stopWords);
		return w;
	}
	/** Opet index for modifiation (on indexPath())*/
	public static CleanIndexWriter openForModification(IndexId iid) throws IOException{
		CleanIndexWriter w = new CleanIndexWriter(iid);
		w.openWriter(iid.getIndexPath(),false);
		return w;
	}
	
	/** Opet index for batch modifiation (on indexPath())*/
	public static CleanIndexWriter openForBatchModification(IndexId iid) throws IOException{
		CleanIndexWriter w = new CleanIndexWriter(iid);
		return w;
	}
	
	private CleanIndexWriter(IndexId iid) throws IOException{
		this.iid = iid;		
		this.builder = new FieldBuilder(iid,FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER,FieldBuilder.Options.SPELL_CHECK);
		this.langCode = iid.getLangCode();
		analyzer = Analyzers.getIndexerAnalyzer(builder);
		IndexId db = iid.getDB();		
		this.nsf = db.getDefaultNamespace();
		
		this.stopWords = new HashSet<String>();
		for(String w : StopWords.getStopWords(db))
			stopWords.add(w);
		
		log.info("Using phrase stopwords: "+stopWords);
		builder.getBuilder().getFilters().setStopWords(stopWords);
		
	}
	
	protected void openWriter(String path, boolean overwrite) throws IOException {
		writer = WikiIndexModifier.openForWrite(path,overwrite);
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);		
		writer.setUseCompoundFile(true);
		writer.setMaxFieldLength(WikiIndexModifier.MAX_FIELD_LENGTH);
	}
	
	/** Old-fashioned batch update, all-delete + all-add */
	public void batchUpdate(Collection<IndexUpdateRecord> records) throws IOException {
		Transaction trans = new Transaction(iid, IndexId.Transaction.INDEX);
		trans.begin();
		try{
			IndexReader reader = IndexReader.open(iid.getIndexPath()); 
			// batch delete
			for(IndexUpdateRecord rec : records){
				if(rec.doDelete()){
					Article a = rec.getArticle();
					log.debug(iid+": Deleting "+a);
					reader.deleteDocuments(new Term("key",rec.getIndexKey()));
				}
			}
			reader.close();
			// batch add
			openWriter(iid.getIndexPath(),false);
			for(IndexUpdateRecord rec : records){
				if(rec.doAdd()){
					Article a = rec.getArticle();
					log.debug(iid+": Adding "+a.toStringFull());
					addArticleInfo(rec.getArticle());
				}
			}
			writer.close();
			trans.commit();
		} catch(IOException e){
			trans.rollback();
			throw e;
		}
	}
	
	public void deleteArticleInfo(String pageId) throws IOException {
		writer.deleteDocuments(new Term("key",pageId));
	}
	
	/** Call this to add information about the article into index */
	public void addArticleInfo(Article a){
		// only for articles in default namespace(s)
		if(nsf.contains(Integer.parseInt(a.getNamespace())))
			addArticle(a);
		else
			addTitleOnly(a);
	}
	
	/** Add single article */
	protected void addArticle(Article a){
		//if(!WikiIndexModifier.checkAddPreconditions(a,langCode))
		//return; // don't add if preconditions are not met
		
		try {
			Document doc = WikiIndexModifier.makeDocument(a,builder,iid,stopWords,analyzer,true);
			writer.addDocument(doc,analyzer);
			log.debug(iid+": Adding document "+a);
		} catch (IOException e) {
			e.printStackTrace();
			log.error("I/O Error writing articlet "+a+" to index "+writer);
		} catch(Exception e){
			e.printStackTrace();
			log.error("Error adding document "+a+" with message: "+e.getMessage());
		}
	}
	
	/** Add title/redirect with ranks information only */
	protected void addTitleOnly(Article article) {
		Document doc = new Document();
		doc.add(new Field("key",article.getIndexKey(),Store.NO,Index.UN_TOKENIZED));
		doc.add(new Field("ns_title",article.getTitle(),Store.YES,Index.TOKENIZED));
		doc.add(new Field("ns_namespace",article.getNamespace(),Store.YES,Index.UN_TOKENIZED));
		doc.add(new Field("ns_rank",Integer.toString(article.getReferences()),Store.YES,Index.NO));
		if(article.isRedirect())
			doc.add(new Field("ns_redirect",article.getRedirectTarget(),Store.YES,Index.NO));
		try {
			log.debug("Addint title "+article);
			writer.addDocument(doc,analyzer);
		} catch (IOException e) {
			e.printStackTrace();
			log.error("Error adding title info for article "+article+" with message: "+e.getMessage());
		}
	}
	
	/** Close and optimize index 
	 * @throws IOException */
	public void closeAndOptimize() throws IOException{
		try{
			writer.optimize();
			writer.close();
		} catch(IOException e){
			log.error("I/O error optimizing/closing index at "+iid.getImportPath()+" : "+e.getMessage());
			throw e;
		}
	}
	
	public void close() throws IOException {
		writer.close();
	}
	
	/** 
	 * Add into metadata_key and metadata_value. 
	 * Collection is assumed to contain words (without spaces) 
	 */
	public void addMetadata(String key, Collection<String> values){
		StringBuilder sb = new StringBuilder();
		// serialize by joining with spaces
		for(String val : values){
			if(sb.length() != 0)
				sb.append(" ");
			sb.append(val);
		}
		Document doc = new Document();
		doc.add(new Field("metadata_key",key, Field.Store.YES, Field.Index.UN_TOKENIZED));
		doc.add(new Field("metadata_value",sb.toString(), Field.Store.YES, Field.Index.NO));
		
		try {
			writer.addDocument(doc);
		} catch (IOException e) {
			log.warn("Cannot write metadata : "+e.getMessage());
		}
	}

}
