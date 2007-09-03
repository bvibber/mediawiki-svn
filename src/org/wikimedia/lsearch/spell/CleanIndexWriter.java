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
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FilterFactory;
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
	
	public static final String[] ENGLISH_STOP_WORDS = {
		"a", "an", "and", "are", "as", "at", "be", "but", "by",
		"for", "if", "in", "into", "is", "it",
		"no", "not", "of", "on", "or", "such",
		"that", "the", "their", "then", "there", "these",
		"they", "this", "to", "was", "will", "with"
	};

	public final static String[] FRENCH_STOP_WORDS = {
		"a", "afin", "ai", "ainsi", "apres", "attendu", "au", "aujourd", "auquel", "aussi",
		"autre", "autres", "aux", "auxquelles", "auxquels", "avait", "avant", "avec", "avoir",
		"c", "car", "ce", "ceci", "cela", "celle", "celles", "celui", "cependant", "certain",
		"certaine", "certaines", "certains", "ces", "cet", "cette", "ceux", "chez", "ci",
		"combien", "comme", "comment", "concernant", "contre", "d", "dans", "de", "debout",
		"dedans", "dehors", "dela", "depuis", "derriere", "des", "desormais", "desquelles",
		"desquels", "dessous", "dessus", "devant", "devers", "devra", "divers", "diverse",
		"diverses", "doit", "donc", "dont", "du", "duquel", "durant", "des", "elle", "elles",
		"en", "entre", "environ", "est", "et", "etc", "etre", "eu", "eux", "excepte", "hormis",
		"hors", "helas", "hui", "il", "ils", "j", "je", "jusqu", "jusque", "l", "la", "laquelle",
		"le", "lequel", "les", "lesquelles", "lesquels", "leur", "leurs", "lorsque", "lui", "la",
		"ma", "mais", "malgre", "me", "merci", "mes", "mien", "mienne", "miennes", "miens", "moi",
		"moins", "mon", "moyennant", "meme", "memes", "n", "ne", "ni", "non", "nos", "notre",
		"nous", "neanmoins", "notre", "notres", "on", "ont", "ou", "outre", "ou", "par", "parmi",
		"partant", "pas", "passe", "pendant", "plein", "plus", "plusieurs", "pour", "pourquoi",
		"proche", "pres", "puisque", "qu", "quand", "que", "quel", "quelle", "quelles", "quels",
		"qui", "quoi", "quoique", "revoici", "revoila", "s", "sa", "sans", "sauf", "se", "selon",
		"seront", "ses", "si", "sien", "sienne", "siennes", "siens", "sinon", "soi", "soit",
		"son", "sont", "sous", "suivant", "sur", "ta", "te", "tes", "tien", "tienne", "tiennes",
		"tiens", "toi", "ton", "tous", "tout", "toute", "toutes", "tu", "un", "une", "va", "vers",
		"voici", "voila", "vos", "votre", "vous", "vu", "votre", "votres", "y", "a", "ca", "es",
		"ete", "etre", "o"
	};
	
	public final static String[] GERMAN_STOP_WORDS = {
		"einer", "eine", "eines", "einem", "einen",
		"der", "die", "das", "dass", "daß",
		"du", "er", "sie", "es",
		"was", "wer", "wie", "wir",
		"und", "oder", "ohne", "mit",
		"am", "im", "in", "aus", "auf",
		"ist", "sein", "war", "wird",
		"ihr", "ihre", "ihres",
		"als", "für", "von", "mit",
		"dich", "dir", "mich", "mir",
		"mein", "sein", "kein",
		"durch", "wegen", "wird"
	};

	public CleanIndexWriter(IndexId iid) throws IOException{
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.iid = iid;		
		this.builder = new FieldBuilder("",FieldBuilder.Case.IGNORE_CASE,FieldBuilder.Stemmer.NO_STEMMER,FieldBuilder.Options.SPELL_CHECK);
		this.langCode = global.getLanguage(iid.getDBname());
		HashSet<String> stopWords = new HashSet<String>();
		String[] words = null;
		if(langCode.equals("en"))
			words = ENGLISH_STOP_WORDS;
		else if(langCode.equals("de"))
			words = GERMAN_STOP_WORDS;
		else if(langCode.equals("fr"))
			words = FRENCH_STOP_WORDS;
		
		if(words != null){
			for(String w : words)
				stopWords.add(w);			
		} else{
			stopWords.addAll(HighFreqTerms.getHighFreqTerms(iid.getDB(),"contents",20));
		}
		log.info("Using phrase stopwords: "+stopWords);
		builder.getBuilder().getFilters().setStopWords(stopWords);
		String path = iid.getSpell().getTempPath();
		writer = open(path);
		addMetadata(writer,"stopWords",stopWords);
		nsf = global.getDefaultNamespace(iid);
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
