package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.HashMap;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.index.WikiSimilarity;

/**
 * IndexWriter for building indexes from scratch.
 * 
 * @author rainman
 *
 */
public class SimpleIndexWriter {
	static Logger log = Logger.getLogger(SimpleIndexWriter.class);
	protected IndexId iid;
	protected HashMap<String,IndexWriter> indexes;
	protected FilterFactory filters;
	protected Boolean optimize;
	protected Integer mergeFactor, maxBufDocs;
	protected boolean newIndex;
	protected String langCode;
	
	public SimpleIndexWriter(IndexId iid, Boolean optimize, Integer mergeFactor, Integer maxBufDocs, boolean newIndex){
		this.iid = iid;
		this.optimize = optimize;
		this.mergeFactor = mergeFactor;
		this.maxBufDocs = maxBufDocs;
		this.newIndex = newIndex;
		langCode = GlobalConfiguration.getInstance().getLanguage(iid.getDBname());
		filters = new FilterFactory(langCode);
		indexes = new HashMap<String,IndexWriter>();
		// open all relevant indexes
		if(iid.isSingle())
			indexes.put(iid.toString(),openIndex(iid));
		else if(iid.isMainsplit()){
			indexes.put(iid.getMainPart().toString(),openIndex(iid.getMainPart()));
			indexes.put(iid.getRestPart().toString(),openIndex(iid.getRestPart()));
		} else if(iid.isSplit()){
			for(String dbpart : iid.getSplitParts()){
				indexes.put(IndexId.get(dbpart).toString(),openIndex(IndexId.get(dbpart)));
			}
		} else
			log.fatal("Unrecognized index architecture for "+iid);
			
	}
	
	/** Open and initialize index denoted by iid */
	protected IndexWriter openIndex(IndexId iid) {
		String path = iid.getImportPath();
		IndexWriter writer;
		try {
			writer = new IndexWriter(path,null,newIndex); // if newIndex==true, overwrites old index
		} catch (IOException e) {				
			try {
				// try to make brand new index
				WikiIndexModifier.makeDBPath(iid.getIndexPath()); // ensure all directories are made
				log.info("Making new index at path "+path);
				writer = new IndexWriter(path,null,true);
			} catch (IOException e1) {
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
				return null;
			}				
		}
		writer.setSimilarity(new WikiSimilarity());
		int glMergeFactor = iid.getIntParam("mergeFactor",2);
		int glMaxBufDocs = iid.getIntParam("maxBufDocs",10);
		if(mergeFactor!=null)
			writer.setMergeFactor(mergeFactor);
		else
			writer.setMergeFactor(glMergeFactor);
		if(maxBufDocs!=null)
			writer.setMaxBufferedDocs(maxBufDocs);
		else
			writer.setMaxBufferedDocs(glMaxBufDocs);		
		writer.setUseCompoundFile(true);
		writer.setMaxFieldLength(WikiIndexModifier.MAX_FIELD_LENGTH);
		
		return writer;
	}

	/** Add single article to logical index. It will add the article to the right index part */
	public void addArticle(Article a){
		if(!WikiIndexModifier.checkAddPreconditions(a,langCode))
			return; // don't add if preconditions are not met
		WikiIndexModifier.transformArticleForIndexing(a);
		IndexId target;
		if(iid.isSingle())
			target = iid;
		else if(iid.isMainsplit()) // assign according to namespace
			target = (a.getNamespace().equals("0"))? iid.getMainPart() : iid.getRestPart();
		else // split index, randomly assign to some index part
			target = iid.getPart(1+(int)(Math.random()*iid.getSplitFactor()));
		
		IndexWriter writer = indexes.get(target.toString());
		if(writer == null)
			return;
		Object[] ret = WikiIndexModifier.makeDocumentAndAnalyzer(a,filters);
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
	
	/** Close and (if specified in global config) optimize indexes */
	public void close(){
		for(Entry<String,IndexWriter> en : indexes.entrySet()){
			IndexId iid = IndexId.get(en.getKey());
			IndexWriter writer = en.getValue();
			try{
				if(optimize!=null){
					if(optimize)
						writer.optimize();
				}
				else if(iid.getBooleanParam("optimize",true))
					writer.optimize();
				writer.close();
			} catch(IOException e){
				log.warn("I/O error optimizing/closing index at "+iid.getImportPath());
			}
		}
	}
}
