package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexRegistry;
import org.wikimedia.lsearch.search.IndexSearcherMul;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.util.HighFreqTerms;

/**
 * Offer various ways of retrieving a list of stop words.
 * For some languages, it's a fixed list, for other, it will
 * be extracted from an index snapshot
 * 
 * @author rainman
 *
 */
public class StopWords {
	static Logger log = Logger.getLogger(StopWords.class);
	public static final String[] ENGLISH_STOP_WORDS = {
		"a", "an", "and", "are", "as", "at", "be", "but", "by",
		"for", "if", "in", "into", "is", "it",
		"no", "not", "of", "on", "or", "such",
		"that", "the", "their", "then", "there", "these",
		"they", "this", "to", "was", "will", "with",
		// questions:
		"when", "who", "what", "whom", "whose", "why", "how",
		"whoever", "do", "does", "also"
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
		"als", "fur", "von", "mit",
		"dich", "dir", "mich", "mir",
		"mein", "sein", "kein",
		"durch", "wegen", "wird"
	};
	
	public static String[] getStopWords(IndexId iid, String langCode){
		if(langCode.equals("en"))
			return ENGLISH_STOP_WORDS;
		else if(langCode.equals("de"))
			return GERMAN_STOP_WORDS;
		else if(langCode.equals("fr"))
			return FRENCH_STOP_WORDS;
		try{
			return HighFreqTerms.getHighFreqTerms(iid.getDB(),"contents",20).toArray(new String[] {});
		} catch(IOException e){
			log.warn("Cannot fetch stop words for "+iid);
			return new String[] {};
		}		
	}
	
	protected static Hashtable<String,ArrayList<String>> cache = new Hashtable<String,ArrayList<String>>();
	protected static IndexRegistry registry = null;
	
	/** Get a cached entry from spell-check metadata 
	 * @throws IOException */
	@SuppressWarnings("unchecked")
	public static ArrayList<String> getCached(IndexId iid) throws IOException{
		ArrayList<String> stopWords = cache.get(iid.toString());
		if(stopWords != null)
			return (ArrayList<String>) stopWords.clone();
		else{
			if(registry == null)
				registry = IndexRegistry.getInstance();
			if(iid.hasSpell() && registry.getCurrentSearch(iid.getSpell()) != null){
				synchronized(cache){
					stopWords = new ArrayList<String>();
					IndexSearcherMul searcher = SearcherCache.getInstance().getLocalSearcher(iid.getSpell());
					TermDocs d = searcher.getIndexReader().termDocs(new Term("metadata_key","stopWords"));
					if(d.next()){
						String val = searcher.doc(d.doc()).get("metadata_value");
						for(String sw : val.split(" ")){
							stopWords.add(sw);
						}
					}
					cache.put(iid.toString(),stopWords);
				}
				return (ArrayList<String>) stopWords.clone();
			}
		}
		return new ArrayList<String>();
	}
	
	/** Get brand new set of stop words (to be used within one thread) */
	public static HashSet<String> getCachedSet(IndexId iid) {
		HashSet<String> ret = new HashSet<String>();		
		try {
			ret.addAll(getCached(iid));
		} catch (IOException e) {
			log.warn("Cannot get cached stop words for "+iid);
		}
		return ret;
	}
	
	/** Get a brand new hash set of predifined stop words (i.e. not those from spell-check files, etc..) */ 
	public static HashSet<String> getPredefinedSet(IndexId iid){
		HashSet<String> ret = new HashSet<String>();
		String langCode = iid.getLangCode();
		String[] def = null;
		if(langCode.equals("en"))
			def = ENGLISH_STOP_WORDS;
		else if(langCode.equals("de"))
			def = GERMAN_STOP_WORDS;
		else if(langCode.equals("fr"))
			def = FRENCH_STOP_WORDS;
		if(def != null){
			for(String s : def)
				ret.add(s);
		}
		return ret;
	}
}
