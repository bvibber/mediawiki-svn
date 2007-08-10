package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.index.WikiSimilarity;

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
	protected IndexWriter writerMain;
	protected IndexWriter writerAll;
	protected FieldBuilder builder;
	protected String langCode;
	
	public CleanIndexWriter(IndexId iid) throws IOException{
		this.iid = iid;		
		this.builder = new FieldBuilder("",FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER);
		this.langCode = GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		String pathMain = iid.getSpellWords().getTempPath();
		String pathAll = iid.getSpellTitles().getTempPath();
		writerMain = open(pathMain);
		writerAll = open(pathAll);			
	}
	
	protected IndexWriter open(String path) throws IOException {
		IndexWriter writer;
		try {
			writer = new IndexWriter(path,null,true); // always make new index
		} catch (IOException e) {				
			try {
				// try to make brand new index
				WikiIndexModifier.makeDBPath(path); // ensure all directories are made
				log.info("Making new index at path "+path);
				writer = new IndexWriter(path,null,true);
			} catch (IOException e1) {
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
				throw e1;
			}				
		}
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);		
		writer.setUseCompoundFile(true);
		writer.setMaxFieldLength(WikiIndexModifier.MAX_FIELD_LENGTH);
		
		return writer;
	}

	/** Add to index used for spell_words */
	public void addMainArticle(Article a){
		if(a.getNamespace().equals("0"))
			addArticle(a,writerMain);
	}
	/** Add to inde used for spell_titles */
	public void addAllArticle(Article a){
		addArticle(a,writerAll);
	}
	
	/** Add single article */
	protected void addArticle(Article a, IndexWriter writer){
		if(!WikiIndexModifier.checkAddPreconditions(a,langCode))
			return; // don't add if preconditions are not met

		Object[] ret = WikiIndexModifier.makeDocumentAndAnalyzer(a,builder,iid);
		Document doc = (Document) ret[0];
		Analyzer analyzer = (Analyzer) ret[1];
		try {
			writer.addDocument(doc,analyzer);
			log.debug(iid+": Adding document "+a);
		} catch (IOException e) {
			log.error("I/O Error writing articlet "+a+" to index "+writer);
		} catch(Exception e){
			e.printStackTrace();
			log.error("Error adding document "+a+" with message: "+e.getMessage());
		}
	}
	
	/** Close and optimize index 
	 * @throws IOException */
	public void close() throws IOException{
		try{
			writerMain.optimize();
			writerMain.close();
			writerAll.optimize();
			writerAll.close();
		} catch(IOException e){
			log.warn("I/O error optimizing/closing index at "+iid.getTempPath());
			throw e;
		}
	}
}
