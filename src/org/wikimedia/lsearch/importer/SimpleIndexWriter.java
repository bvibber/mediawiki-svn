package org.wikimedia.lsearch.importer;

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
import org.wikimedia.lsearch.ranks.Links;

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
	protected FieldBuilder builder;
	protected Boolean optimize;
	protected Integer mergeFactor, maxBufDocs;
	protected boolean newIndex;
	protected String langCode;
	protected Links links;
	
	public SimpleIndexWriter(Links links, IndexId iid, Boolean optimize, Integer mergeFactor, Integer maxBufDocs, boolean newIndex){
		this.links = links;
		this.iid = iid;
		this.optimize = optimize;
		this.mergeFactor = mergeFactor;
		this.maxBufDocs = maxBufDocs;
		this.newIndex = newIndex;
		GlobalConfiguration global = GlobalConfiguration.getInstance(); 
		langCode = global.getLanguage(iid.getDBname());
		FieldBuilder.Case dCase = (global.exactCaseIndex(iid.getDBname()))? FieldBuilder.Case.EXACT_CASE : FieldBuilder.Case.IGNORE_CASE; 		
		builder = new FieldBuilder(iid,dCase);
		indexes = new HashMap<String,IndexWriter>();
		// open all relevant indexes
		if(iid.isSingle())
			indexes.put(iid.toString(),openIndex(iid));
		else if(iid.isMainsplit()){
			indexes.put(iid.getMainPart().toString(),openIndex(iid.getMainPart()));
			indexes.put(iid.getRestPart().toString(),openIndex(iid.getRestPart()));
		} else if(iid.isSplit() || iid.isNssplit()){
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
				WikiIndexModifier.makeDBPath(path); // ensure all directories are made
				log.info("Making new index at path "+path);
				writer = new IndexWriter(path,null,true);
			} catch (IOException e1) {
				log.error("I/O error openning index for addition of documents at "+path+" : "+e.getMessage());
				return null;
			}				
		}
		writer.setSimilarity(new WikiSimilarity());
		int glMergeFactor = iid.getIntParam("mergeFactor",10);
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
		IndexId target;
		if(iid.isSingle())
			target = iid;
		else if(iid.isMainsplit() || iid.isNssplit()) // assign according to namespace
			target = iid.getPartByNamespace(a.getNamespace());
		else // split index, randomly assign to some index part
			target = iid.getPart(1+(int)(Math.random()*iid.getSplitFactor()));
		
		IndexWriter writer = indexes.get(target.toString());
		if(writer == null)
			return;
		Object[] ret = WikiIndexModifier.makeDocumentAndAnalyzer(a,builder,iid,links);
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
	
	/** Close and (if specified in global config) optimize indexes 
	 * @throws IOException */
	public void close() throws IOException{
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
				throw e;
			}
		}
	}
}
