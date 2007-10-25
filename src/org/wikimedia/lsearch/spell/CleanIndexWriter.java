package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexWriter;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.IndexReportCard;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.IndexUpdateRecord;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.index.WikiSimilarity;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.util.HighFreqTerms;

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
	protected NamespaceFilter nsf;
	protected Analyzer analyzer;
	protected HashSet<String> stopWords;
	
	public CleanIndexWriter(IndexId iid) throws IOException{
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.iid = iid;		
		this.builder = new FieldBuilder(iid,FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER,FieldBuilder.Options.SPELL_CHECK);
		this.langCode = global.getLanguage(iid.getDBname());
		analyzer = Analyzers.getIndexerAnalyzer(builder);
		this.stopWords = StopWords.getPredefinedSet(iid);
		
		HashSet<String> stopWords = new HashSet<String>();
		for(String w : StopWords.getStopWords(iid,langCode))
			stopWords.add(w);			
		log.info("Using phrase stopwords: "+stopWords);
		builder.getBuilder().getFilters().setStopWords(stopWords);
		String path = iid.getSpell().getTempPath();
		writer = open(path);
		addMetadata(writer,"stopWords",stopWords);
		nsf = global.getDefaultNamespace(iid);
		log.info("Rebuild for namespaces: "+nsf);
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

	/** Add to index used for spell-check */
	public void addArticle(Article a){
		if(nsf.contains(Integer.parseInt(a.getNamespace())))
			addArticle(a,writer);
	}

	/** Add single article */
	protected void addArticle(Article a, IndexWriter writer){
		if(!WikiIndexModifier.checkAddPreconditions(a,langCode))
			return; // don't add if preconditions are not met

		Document doc = WikiIndexModifier.makeDocument(a,builder,iid,stopWords);
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
			writer.optimize();
			writer.close();
		} catch(IOException e){
			log.error("I/O error optimizing/closing index at "+iid.getTempPath()+" : "+e.getMessage());
			throw e;
		}
	}
	
	/** 
	 * Add into metadata_key and metadata_value. 
	 * Collection is assumed to contain words (without spaces) 
	 */
	public void addMetadata(IndexWriter writer, String key, Collection<String> values){
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
