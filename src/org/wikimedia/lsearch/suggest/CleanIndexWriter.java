package org.wikimedia.lsearch.suggest;

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
	protected IndexWriter writer;
	protected FieldBuilder builder;
	protected String langCode;
	
	public CleanIndexWriter(IndexId iid) throws IOException{
		this.iid = iid;		
		this.builder = new FieldBuilder("",FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER);
		this.langCode = GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		String path = iid.getSuggestCleanPath();
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
			
	}

	/** Add single article to logical index. It will add the article to the right index part */
	public void addArticle(Article a){
		if(!WikiIndexModifier.checkAddPreconditions(a,langCode))
			return; // don't add if preconditions are not met
		IndexId target;
		if(iid.isSingle())
			target = iid;
		else if(iid.isMainsplit() || iid.isNssplit()) // assign according to namespace
			target = iid.getPartByNamespace(a.getNamespace());
		else // split index, randomly assign to some index part
			target = iid.getPart(1+(int)(Math.random()*iid.getSplitFactor()));
		
		Object[] ret = WikiIndexModifier.makeDocumentAndAnalyzer(a,builder,iid);
		Document doc = (Document) ret[0];
		Analyzer analyzer = (Analyzer) ret[1];
		try {
			writer.addDocument(doc,analyzer);
			log.debug(iid+": Adding document "+a);
		} catch (IOException e) {
			log.error("I/O Error writing articlet "+a+" to index "+target.getImportPath());
		} catch(Exception e){
			e.printStackTrace();
			log.error("Error adding document "+a+" with message: "+e.getMessage());
		}
	}
	
	/** Close and optimize index 
	 * @throws IOException */
	public void close() throws IOException{
		try{
			writer.optimize();
			writer.close();
		} catch(IOException e){
			log.warn("I/O error optimizing/closing index at "+iid.getSuggestCleanPath());
			throw e;
		}
	}
}
