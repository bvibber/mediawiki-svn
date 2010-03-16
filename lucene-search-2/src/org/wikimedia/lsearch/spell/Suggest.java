package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;
import java.util.WeakHashMap;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.Hits;
import org.apache.lucene.search.IndexSearcher;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.ScoreDoc;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.TopDocs;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.ObjectCache;
import org.wikimedia.lsearch.ranks.StringList;
import org.wikimedia.lsearch.ranks.StringList.LookupSet;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.FilterWrapper;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.spell.api.NgramIndexer;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;

public class Suggest {
	static Logger log = Logger.getLogger(Suggest.class);
	protected static GlobalConfiguration global=null;
	protected IndexId iid;
	protected IndexSearcher searcher;	
	protected IndexReader reader;
	protected static WeakHashMap<IndexSearcher,Set<String>> stopWordsIndexes = new WeakHashMap<IndexSearcher,Set<String>>();
	protected Set<String> stopWords;
	protected NamespaceFilter defaultNs;
	protected HashMap<String,Boolean> wordExistCache = new HashMap<String,Boolean>();
	protected enum Filtering { STRONG, WEAK };
	protected boolean useLogging = true;
	protected int minWordFreq = 0;
	
	/** Distance an metaphone metrics */
	static public class Metric {
		protected DoubleMetaphone dmeta =  new DoubleMetaphone();
		protected String meta1="", meta2="";
		protected EditDistance sd;
		protected EditDistance sdmeta1=null, sdmeta2=null;
		protected String word;
		protected String decomposed=null;
		
		public Metric(String word){
			this(word,true);
		}		
		public Metric(String word, boolean useMetaphones){
			this.word = word;
			this.decomposed = FastWikiTokenizerEngine.decompose(word);
			//sd = new EditDistance(word);
			sd = new EditDistance(decomposed);
			if(useMetaphones){
				meta1 = dmeta.doubleMetaphone(decomposed);
				meta2 = dmeta.doubleMetaphone(decomposed,true);
				sdmeta1 = new EditDistance(meta1,false);
				sdmeta2 = new EditDistance(meta2,false);
			}
		}
		public boolean hasDecomposed(){
			return decomposed != word; // equals() not necessary since decompose() returns same object 
		}
		/** Edit distance */
		public int distanceWithDecomposition(String w){
			return sd.getDistance(FastWikiTokenizerEngine.decompose(w));
		}
		/** Get distance when input words is already decomposed */
		public int distance(String w){
			return sd.getDistance(w);
		}
		/* Edit distance to decomposed word (input word is also decomposed) */
		/*public int decomposedDistance(String w){
			return sdd.getDistance(FastWikiTokenizerEngine.decompose(w));
		}*/
		/** Edit distance of primary metaphone of w */
		public int meta1Distance(String w){
			if(sdmeta1 != null)
				return sdmeta1.getDistance(dmeta.doubleMetaphone(w));
			else
				return 0;
		}
		/** Edit distance of alternative metaphone of w */
		public int meta2Distance(String w){
			if(sdmeta2 != null)
				return sdmeta2.getDistance(dmeta.doubleMetaphone(w,true));
			else
				return 0;
		}
		/** If string differs only in duplication of some letters */
		public boolean hasSameLetters(String w){
			return sd.hasSameLetters(w);
		}
		@Override
		public String toString() {
			return "("+word + ",meta1:"+meta1+",meta2:"+meta2+")" ;
		}

	}
	
	protected static class Namespaces {
		HashSet<Integer> namespaces = new HashSet<Integer>();
		/** If true, these namespaces are additional to the default namespaces,
		 *  if false, there is no intersection between these namespaces and default namespaces */
		boolean additional = false;
		String prefix = "ns_";
		public Namespaces(HashSet<Integer> namespaces, boolean additional) {
			this.namespaces = namespaces;
			this.additional = additional;
		}
	}
	
	protected Namespaces makeNamespaces(NamespaceFilter nsf){
		Namespaces ns = null;
		// all suggestions on canonical non-main namespaces with main, go to main only
		if(nsf != null && !(nsf.contains(0) && allCanonicalNamespaces(nsf))){			
			HashSet<Integer> defNs = defaultNs.getNamespaces();		
			HashSet<Integer> targetNs = nsf.getNamespaces();
			if(targetNs.isEmpty())
				ns = new Namespaces(targetNs,true);
			else if(targetNs.size()==1 && !targetNs.equals(defNs))
				ns = new Namespaces(targetNs,false);
			else if(defNs.containsAll(targetNs)) // subset of default namespaces
				ns = null;
			else if(!defaultNs.getIncluded().intersects(nsf.getIncluded())) // disjunct from default
				ns = new Namespaces(targetNs,false);
			else // some intersection
				ns = new Namespaces(targetNs,true);
		}
		return ns;
	}
	
	/** If all namespaces are canonical, i.e. predefined in define.php */
	protected boolean allCanonicalNamespaces(NamespaceFilter nsf){
		for(Integer ns : nsf.getNamespacesOrdered()){
			if(ns > 20) // found a custom namespace
				return false;
		}
		return true;
	}
	
	/** Number of results to fetch */
	public static final int POOL = 400;
	/** Number of results to fetch for titles */
	public static final int POOL_TITLE = 100;
	/** Number of results to fetch for fuzzy word matches */
	public static final int POOL_FUZZY = 500;
	/** Number of words to return for fuzzy queries */
	public static final int MAX_FUZZY = 50;
	
	/** Lower limit to hit rate for joining */
	public static final int JOIN_FREQ = 1;
	
	/** use for testing only */
	protected Suggest() {		
	}
	
	public Suggest(IndexId iid) throws IOException {
		this(iid,null,true);
	}
	
	public Suggest(IndexId iid, IndexSearcher searcher, boolean useLogging) throws IOException{
		SearcherCache cache = SearcherCache.getInstance();
		this.iid = iid;
		if(searcher == null)
			searcher = cache.getLocalSearcher(iid.getSpell());
		if(global == null)
			global = GlobalConfiguration.getInstance();
		this.defaultNs = iid.getDefaultNamespace();
		this.useLogging = useLogging;
		this.minWordFreq = global.getIntDBParam(iid.getDBname(),"spell","wordsMinFreq",3);
		
		if(searcher != null){
			this.searcher = searcher;
			this.reader = searcher.getIndexReader();


			synchronized(stopWordsIndexes){
				if(!stopWordsIndexes.containsKey(searcher)){
					HashSet<String> s = new HashSet<String>();
					stopWordsIndexes.put(searcher,s);
					TermDocs d = searcher.getIndexReader().termDocs(new Term("metadata_key","stopWords"));
					if(d.next()){
						String val = searcher.doc(d.doc()).get("metadata_value");
						for(String sw : val.split(" ")){
							s.add(sw);
						}
					}				
				}
				this.stopWords = new HashSet<String>();
				this.stopWords.addAll(stopWordsIndexes.get(searcher)); 			
				log.debug("Using stop words "+stopWords);
			}
		}
	}
	
	static class Change {		
		/** position -> new string */
		HashMap<Integer,String> substitutes = new HashMap<Integer,String>();
		/** position -> new string */
		HashMap<Integer,String> preserves = new HashMap<Integer,String>();
		/** edit distance */
		int dist;
		/** expected number of hits for changes query */
		int freq;
		enum Type { PHRASE, SPLIT, JOIN, WORD, TITLE_WORD };
		Type type;
		public Change(int dist, int freq, Type type) {
			this.dist = dist;
			this.freq = freq;
			this.type = type;
		}		
		public String toString(){
			return "["+type+" dist:"+dist+" freq:"+freq+" sub:"+substitutes+" pres:"+preserves+"]";
		}
	}
	
	/** 
	 * Extra information that will help disambiguate some suggest cases, 
	 * e.g. words from titles found in search, phrases found in text, ...  
	 * @author rainman
	 *
	 */
	public static class ExtraInfo implements Serializable {
		protected HashSet<String> phrases;
		protected HashSet<String> foundInContext;
		protected HashSet<String> foundInTitles;
		protected int firstRank;
		protected boolean foundAllInAlttitle;
		
		public ExtraInfo(HashSet<String> phrases, HashSet<String> foundInContext, HashSet<String> foundInTitles, int firstRank, boolean foundAllInAlttitle) {
			this.phrases = phrases;
			this.foundInContext = foundInContext;
			this.foundInTitles = foundInTitles;
			this.firstRank = firstRank;
			this.foundAllInAlttitle = foundAllInAlttitle;
		}
		
		public ExtraInfo(){
			this(new HashSet<String>(),new HashSet<String>(),new HashSet<String>(),0,false);
		}
		
		
	}
	
	/** 
	 * Make a suggestion for a query
	 *    
	 * @throws IOException 
	 */
	@SuppressWarnings("unchecked")
	public SuggestQuery suggest(String searchterm, ArrayList<Token> tokens, ExtraInfo info, NamespaceFilter nsf) throws IOException{		
		FilterFactory filters = new FilterFactory(iid);
		wordExistCache.clear();
		long start = System.currentTimeMillis();
		
		log.debug("tokens: "+tokens+" inContext:"+info.foundInContext+" phrases:"+info.phrases+", inTitles="+info.foundInTitles);

		if(tokens.size() > 15){
			logRequest(searchterm,"too many words to spellcheck ("+tokens.size()+")",start,searchterm);
			return new SuggestQuery(searchterm,new ArrayList<Integer>());
		}
		
		// figure out on what namespaces should we search
		Namespaces ns = makeNamespaces(nsf);
		
		// check if we are spellchecking wildcard and fuzzy queries, if so, only replace bad words
		boolean wordsOnly = false;
		for(Token t : tokens){
			if(t.type().equals("wildcard") || t.type().equals("fuzzy")){
				wordsOnly = true;
				break;
			}
		}
		if(wordsOnly){
			HashMap<Integer,String> changes = new HashMap<Integer,String>();
			for(int i=0;i<tokens.size();i++){
				Token t = tokens.get(i);
				if(!t.type().equals("wildcard") && !t.type().equals("fuzzy") 
						&& t.getPositionIncrement()>0 && !wordExists(t.termText(),ns)){
					String w = t.termText();
					ArrayList<SuggestResult> sug = suggestWords(w,POOL,ns);
					if(sug != null && sug.size()>0){
						changes.put(i,sug.get(0).word);
					}
				}
			}
			if(changes.size() > 0){
				SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,new HashSet<Integer>(),ns);
				logRequest(sq.getSearchterm(),"words only (wildcard or fuzzy query)",start,searchterm);
				return sq;
			} else{
				logRequest(searchterm,"CORRECT (by words, wildcard or fuzzy query)",start,searchterm);
				return new SuggestQuery(searchterm,new ArrayList<Integer>());				
			}
		}
		
		// init suggestions
		ArrayList<Change> suggestions = new ArrayList<Change>(); 
		ArrayList<Change> suggestionsTitle = new ArrayList<Change>();
		HashMap<String,HashSet<String>> contextCache = new HashMap<String,HashSet<String>>();
		
		// check title common misspells via redirects 
		String joinTokens = joinTokens(" ",tokens);		
		String redirectTarget = followRedirect(joinTokens,ns);
		if(redirectTarget != null){
			EditDistance ed = new EditDistance(joinTokens);
			if(ed.getDistance(redirectTarget) <= 2 && betterRank(titleRank(redirectTarget,ns),info.firstRank)){
				HashMap<Integer,String> changes = extractTitleChanges(joinTokens,redirectTarget,tokens);
				if(changes != null){
					SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,new HashSet<Integer>(),ns);
					logRequest(sq.getSearchterm(),"titles (via redirect)",start,searchterm);
					return sq;
				}
			}
		}
		
		// TODO: maybe attempt to spellcheck based on (all) words found in titles!
		// i.e. do a "fake suggest" on words based on these words
		
		/*if(info.foundAllInAlttitle && tokens.size()>1){
			logRequest(searchterm,"CORRECT (found all in alttitle)",start);
			return new SuggestQuery(searchterm,new ArrayList<Integer>());
		} */

		// check if all words are found within phrases during highlighting
		boolean correctByPhrases = false;
		if(tokens.size() > 1 && tokens.size() == info.phrases.size() + 1){
			correctByPhrases = true;
		}
		
		// title misspells via title suggestions
		ArrayList<SuggestResult> titleRes = new ArrayList<SuggestResult>();
		if(joinTokens.length() > 7)
			titleRes = suggestTitles(joinTokens,1,POOL_TITLE,4,ns);
		log.debug("title matches: "+titleRes);
		if(titleRes.size()>0 && (titleRes.get(0).dist<2 || (correctByPhrases && titleRes.get(0).dist<=2))){
			SuggestResult r = titleRes.get(0);
			if(r.isExactMatch()){
				logRequest(searchterm,"CORRECT (exact title match)",start,searchterm);
				return new SuggestQuery(searchterm,new ArrayList<Integer>());
			}
			if(betterRank(r.frequency,info.firstRank)){
				HashMap<Integer,String> changes = extractTitleChanges(joinTokens,r.word,tokens);
				if(changes != null){
					SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,changes.keySet(),ns);
					logRequest(sq.getSearchterm(),"titles (misspell)",start,searchterm);
					return sq;
				}
			}
		}

		ArrayList<SuggestResult> singleWordSug = null;
		
		// single word misspells
		if(tokens.size() == 1){
			String w = tokens.get(0).termText();
			singleWordSug = (!stopWords.contains(w))? suggestWords(w,POOL,ns) : new ArrayList<SuggestResult>();
			if(singleWordSug.size() > 0){
				SuggestResult r = singleWordSug.get(0);
				if(r.isExactMatch()){
					logRequest(searchterm,"CORRECT (by single word index)",start,searchterm);
					return new SuggestQuery(searchterm,new ArrayList<Integer>());
				} else{  //if(r.dist <= 1 && betterRank(r.frequency,info.firstRank)){
					SuggestResult best = null;
					int bestRank = 0;
					if(r.dist <=1 && betterRank(r.frequency,info.firstRank)){
						best = r;
						bestRank = titleRank(r.word,ns);
					}
					// find the best suggestion that is also a full title
					for(int i=0;i<singleWordSug.size();i++){
						SuggestResult sw = singleWordSug.get(i);
						if((best != null && sw.dist>best.dist) || sw.dist>2 || !betterRank(sw.frequency,info.firstRank))
							break;
						if(titleExists(sw.word,ns) && titleRank(sw.word,ns)>bestRank){
							best = sw;
							break;
						}
					}
					if(best != null){
						HashMap<Integer,String> proposedChanges = new HashMap<Integer,String>();
						proposedChanges.put(0,best.word);
						SuggestQuery sq = makeSuggestedQuery(tokens,proposedChanges,searchterm,filters,new HashSet<Integer>(),ns);
						logRequest(sq.getSearchterm(),"single word misspell",start,searchterm);
						return sq;
					}
				}
			}
			//logRequest(searchterm,"CORRECT (no single word suggest)",start);
			//return new SuggestQuery(searchterm,new ArrayList<Integer>());			
		}
		
		// don't go further - there is no similar title, and we found the phrase in text
		/*if(correctByPhrases){
			logRequest(searchterm,"CORRECT (by highlight phrases)",start);
			return new SuggestQuery(searchterm,new ArrayList<Integer>());
		}	 */
		
		// indexes of words in found during highlighting in phrases
		//HashSet<Integer> inPhrases = new HashSet<Integer>();
		// words that might spellcheck to stop words
		ArrayList<SuggestResult> possibleStopWords = new ArrayList<SuggestResult>();
		// word suggestions
		ArrayList<ArrayList<SuggestResult>> wordSug = new ArrayList<ArrayList<SuggestResult>>();
		
		// suggest words, splits, joins
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			String w = t.termText();
			// one-letter words and stop words are always correct
			if(w.length() < 2 || stopWords.contains(w)){ 
				addCorrectWord(w,wordSug,possibleStopWords);				
				continue;
			}
			// words found within context should be spell-checked only if they are not valid words
			if(info.foundInContext.contains(w) && wordExists(w,ns) && wordFrequency(w,ns)>minWordFreq*10){
				addCorrectWord(w,wordSug,possibleStopWords);
				continue;
			} 
				
			// suggest word
			ArrayList<SuggestResult> sug = new ArrayList<SuggestResult>();
			sug = (tokens.size() == 1)? singleWordSug : suggestWords(w,POOL,ns);
			if(sug.size() > 0){
				wordSug.add(sug);
				SuggestResult maybeStopWord = null;
				// find the result where the word is changed to stopword
				for(SuggestResult r : sug){
					if(stopWords.contains(r.getWord())){
						maybeStopWord = r;
						break;
					}						
				}
				possibleStopWords.add(maybeStopWord);
			} else{
				wordSug.add(null);
				possibleStopWords.add(null);
			}
			// suggest split
			SuggestResult split = suggestSplit(w,ns);
			if(split != null){
				Change sc = new Change(split.dist,split.frequency,Change.Type.SPLIT);
				sc.substitutes.put(i,split.word.replace("_"," "));
				suggestions.add(sc);
			}
			
			// suggest join
			if(i-1 >= 0 
					&& (wordSug.get(i-1)==null || !wordSug.get(i-1).get(0).isExactMatch())
					&& (wordSug.get(i)==null || !wordSug.get(i).get(0).isExactMatch())){
				SuggestResult join = suggestJoin(tokens.get(i-1).termText(),w,ns);
				if(join != null){
					Change sc = new Change(join.dist,join.frequency,Change.Type.JOIN);
					sc.substitutes.put(i-1,""); 
					sc.substitutes.put(i,join.word);
					suggestions.add(sc);
				}
			}
		}
		
		HashSet<String> allWordsCol = new HashSet<String>(); // all suggested words
		for(ArrayList<SuggestResult> sug : wordSug){
			if(sug != null){
				for(SuggestResult sr : sug)
					allWordsCol.add(sr.word);
			}
		}
		LookupSet allWords = new LookupSet(allWordsCol);
		
		// find all phrases up to some edit distance
		for(int i=0;i<tokens.size();i++){
			ArrayList<SuggestResult> sug1 = wordSug.get(i);
			String w1 = tokens.get(i).termText();
			if(sug1 == null || stopWords.contains(w1))
				continue;
			String w2 = null;
			String gap = "_";
			// if w1 is spellchecked right
			boolean good1 = sug1.get(0).isExactMatch();
			int i2 = i;
			boolean maybeStopWord = false; // if i2 might be spellcheked to stopword
			int distOffset = 0; // if we spellcheked to stop word, all phrases should have this initial dist
			do{
				if(maybeStopWord){
					maybeStopWord = false;
					SuggestResult r = possibleStopWords.get(i2);
					gap += r.word+"_";
					distOffset = r.dist;
				}
				ArrayList<SuggestResult> sug2 = null;
				for(i2++;i2<tokens.size();i2++){
					if(wordSug.get(i2) != null){
						if(stopWords.contains(tokens.get(i2).termText())){
							gap += tokens.get(i2).termText()+"_";
						} else{
							sug2 = wordSug.get(i2);
							w2 = tokens.get(i2).termText();
							maybeStopWord = possibleStopWords.get(i2) != null;
							break;
						}
					}
				}
				if(sug2 == null)
					continue;
				// if second word is spelled right
				boolean good2 = sug2.get(0).isExactMatch();
				int maxdist = Math.min((w1.length() + w2.length()) / 3, 5);
				int mindist = -1;
				boolean forTitlesOnly = false; 
				// construct phrases
				for(int in1=0;in1<sug1.size();in1++){
					SuggestResult s1 = sug1.get(in1);
					for(int in2=0;in2<sug2.size();in2++){
						SuggestResult s2 = sug2.get(in2);
						if(s1.dist+s2.dist > maxdist || (mindist != -1 && s1.dist+s2.dist > mindist))
							continue;
						String phrase = s1.word+gap+s2.word;
						Object[] ret = getPhrase(phrase,ns);
						int freq = (Integer)ret[0];
						boolean inTitle = (Boolean)ret[1];
						String misspell = (String)ret[2];
						// index-time detected misspell
						if(good1 && good2 && misspell != null){
							log.debug("Found misspell "+phrase+" -> "+misspell);
							Change c = new Change(1,freq,Change.Type.PHRASE);
							String[] parts = misspell.split("_");
							if(parts.length==2){ // sanity check, shouldn't have stop words
								if(parts[0].equals(w1))
									c.preserves.put(i,w1);
								else
									c.substitutes.put(i,parts[0]);
								if(parts[1].equals(w2))
									c.preserves.put(i2,w2);
								else
									c.substitutes.put(i2,parts[1]);
								
								suggestions.add(c);
								suggestionsTitle.add(c);
								continue;
							}
						}
											
						// check phrases
						//log.debug("Checking "+phrase);
						boolean inContext = inContext(s1.word,s2.word,contextCache,allWords,ns) || inContext(s2.word,s1.word,contextCache,allWords,ns);    
						if(freq > 0 || inContext){
							// number of characters added/substracted
							int diff1 = Math.abs(s1.word.length()-w1.length());
							int diff2 = Math.abs(s2.word.length()-w2.length());
							log.debug("Found "+phrase+" at dist="+(s1.dist+s2.dist)+", freq="+freq+" inTitle="+inTitle);
							int dist = s1.dist + s2.dist + distOffset;
							boolean accept = true;
							Change c = new Change(dist,freq,Change.Type.PHRASE);
							// we can estimate hit rate, do it !
							/* boolean acceptInContext = false;
							if(inContext && liberalInContext && 
									((inContext1 && betterRank(s1.frequency,info.firstRank))
											|| (inContext2 && betterRank(s2.frequency,info.firstRank)))) 
								acceptInContext = true; */
							// register changes
							if(s1.word.equals(w1))
								c.preserves.put(i,w1);
							else if((!good1 && !info.foundInTitles.contains(w1))
									|| ((inTitle||inContext) && diff1 <=2 && !info.foundInTitles.contains(w1))  )					
								c.substitutes.put(i,s1.word);
							else
								accept = false;
							
							if(s2.word.equals(w2))
								c.preserves.put(i2,w2);
							else if((!good2 && !info.foundInTitles.contains(w2)) 
									|| ((inTitle||inContext) && diff2 <= 2 && !info.foundInTitles.contains(w2)) )					
								c.substitutes.put(i2,s2.word);
							else
								accept = false;
							
							// for incontext: no all change or all preserve
							if(accept && !(c.freq==0 && (c.substitutes.size()==0 || (c.substitutes.size()==2 && good1 && good2)))){								
								if(mindist == -1)
									mindist = dist + 1;
								if(!forTitlesOnly)
									suggestions.add(c);
								suggestionsTitle.add(c);
							} 
						}
					}	
				}
			} while(maybeStopWord && i2+1<tokens.size());
		}
		log.debug("Suggestions: "+suggestions);
		// try to construct a valid title by spell-checking all words
		if(suggestionsTitle.size() > 0 && tokens.size() > 1){
			Object[] ret = calculateChanges(suggestionsTitle,searchterm.length()/2,tokens,contextCache,allWords,ns);
			HashMap<Integer,String> changes = (HashMap<Integer,String>) ret[0];
			// construct title
			StringBuilder proposedTitle = new StringBuilder();
			boolean first = true;
			for(int i=0;i<tokens.size();i++){
				if(!first)
					proposedTitle.append(" ");
				else
					first = false;
				String changed = changes.get(i);
				if(changed != null)
					proposedTitle.append(changed);
				else
					proposedTitle.append(tokens.get(i));
			}
			// check
			if( titleExists(proposedTitle.toString(),ns) ){
				SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,changes.keySet(),ns);
				logRequest(sq.getSearchterm(),"phrases (title match)",start,searchterm);
				return sq;
			}
		}
		log.debug("Spell-checking based on phrases...");
		// find best suggestion based on phrases
		HashMap<Integer,String> preserveTokens = new HashMap<Integer,String>();
		HashMap<Integer,String> proposedChanges = new HashMap<Integer,String>();
		HashMap<Integer,Change> changeCause = new HashMap<Integer,Change>(); // index -> Change
		HashSet<Integer> alwaysReplace = new HashSet<Integer>(); // tokens that always needs to be replaces (irregardless of stemming) 
		String using="";
		int distance = 0;
		if(suggestions.size() > 0){
			// found some suggestions
			Object[] ret = calculateChanges(suggestions,searchterm.length()/2,tokens,contextCache,allWords,ns);
			proposedChanges = (HashMap<Integer,String>) ret[0];
			preserveTokens = (HashMap<Integer,String>) ret[1];
			changeCause = (HashMap<Integer,Change>) ret[2];
			distance = (Integer)ret[3];
			using += "phrases";
		}
		
		// if some words are still unchecked
		if(titleRes.size() == 0){ // always prefer titles to words
			for(int i=0;i<tokens.size();i++){
				if(preserveTokens.containsKey(i) || proposedChanges.containsKey(i))
					continue;
				String w = tokens.get(i).termText();
				ArrayList<SuggestResult> sug = wordSug.get(i);
				if(sug == null)
					continue;
				SuggestResult s = sug.get(0);
				if(!s.isExactMatch() && !info.foundInTitles.contains(w) && acceptWordChange(w,s)){
					distance += s.dist;
					proposedChanges.put(i,s.word);
					if(using.equals("phrases"))
						using = "phrases+words";
					else
						using = "words";
				}
			}
		}
		
		// finally, see if we can find a better whole title (within current max distance) 
		if(joinTokens.length() > 7 && !info.foundAllInAlttitle){
			if(titleRes.size() > 0){
				SuggestResult tr = titleRes.get(0);
				HashMap<Integer,String> changes = extractTitleChanges(joinTokens,tr.word,tokens);
				if(changes != null){
					if(tr.dist <= distance && (betterRank(tr.frequency,info.firstRank) || proposedChanges.equals(changes))){
						// we found a much better suggestion ! 
						proposedChanges = changes;
						alwaysReplace.addAll(proposedChanges.keySet());
						using = "titles";						
					} else{
						// resolve conflicts
						for(Entry<Integer,String> e : changes.entrySet()){
							Integer inx = e.getKey();
							String w = e.getValue();
							Change c = changeCause.get(inx);
							String target = null;
							if(proposedChanges.containsKey(inx))
								target = proposedChanges.get(inx);
							else if(preserveTokens.containsKey(inx))
								target = preserveTokens.get(inx);
							// substitute words that are preserved via contextual info only (and isolated words)
							// prefer the title word if the proposed and title word stem to same
							if(c==null || (c.freq==0 && preserveTokens.containsKey(inx))
									|| (target!=null && filters.stemsToSame(w,target))){							
								proposedChanges.put(inx,w);
								alwaysReplace.add(inx);
								if(using.equals(""))
									using = "titles";
								else if(!using.endsWith("titles"))
									using += "+titles";								
							}
						}
					}
				}
			}
		}
		
		// see if both searchterm and proposed query all redirect to same article
		if(redirectTarget != null){
			String prop = followRedirect(joinTokens(" ",tokens,proposedChanges),ns);
			if(prop != null && prop.equals(redirectTarget)){
				logRequest(searchterm,"CORRECT (spellcheck to redirect to same article)",start,searchterm);
				return new SuggestQuery(searchterm,new ArrayList<Integer>());
			}			
		}
		
		SuggestQuery sq = makeSuggestedQuery(tokens,proposedChanges,searchterm,filters,alwaysReplace,ns);
		logRequest(sq.getSearchterm(),using,start,searchterm);
		return sq;
	}

	/** Accept a single word change (when not part of a phrase) */
	private boolean acceptWordChange(String w, SuggestResult s) {
		if(isNumber(w) || isNumber(s.word))
			return false;
		int minlen = Math.min(w.length(),s.word.length());
		if(minlen <= 4 && s.dist > 1) // at most 1 edit distance for short words 
			return false;
		
		return true;
	}

	/** compare if first rank is higher */
	final private boolean betterRank(int rank, int topRank){
		//System.out.println("betterRank("+rank+","+topRank+")");
		return rank > topRank + noise(topRank); // inc. noise - actual rank when searching can be higher than at index time 
	}
	
	final private int noise(int rank){
		if(rank < 5)
			return 0;
		if(rank < 10)
			return 1;
		if(rank < 20)
			return 2;
		if(rank < 100)
			return 5;
		if(rank < 1000)
			return 20;
		else
			return 50;
	}
	
	private boolean inContext(String context, String w, HashMap<String,HashSet<String>> contextCache, LookupSet allWords, Namespaces ns) throws IOException{
		HashSet<String> set = contextCache.get(context);
		if(set == null){
			set = getContext(context,allWords,ns);
			contextCache.put(context,set);
		}
		return set.contains(w);
	}
	
	private boolean inContext(String context, HashSet<String> words, HashMap<String,HashSet<String>> contextCache, LookupSet allWords, Namespaces ns) throws IOException{
		HashSet<String> set = contextCache.get(context);
		if(set == null){
			set = getContext(context,allWords,ns);
			contextCache.put(context,set);
		}
		if(set == null || set.size()==0)
			return false;		
		for(String w : words){
			if(!stopWords.contains(w) && !w.equals(context) && !set.contains(w))
				return false;				
		}
		return true;
	}
	
	@SuppressWarnings("unchecked")
	private HashSet<String> getContext(String w, LookupSet allWords, Namespaces ns) throws IOException{		
		if(ns == null || ns.additional){ // no context for nondefault namespaces
			TermDocs td = reader.termDocs(new Term("context_key",w));
			if(td.next())
				return new StringList(reader.document(td.doc()).get("context")).toHashSet(allWords);					
		}
		return new HashSet<String>();
	}
	
	/** Return true if word exists in the index */
	private boolean wordExists(String w, Namespaces ns) throws IOException{
		Boolean b = wordExistCache.get(w);
		if(b == null){
			if(ns == null) // default
				b = reader.docFreq(new Term("word",w)) != 0;				
			else{ // other namespaces
				b = reader.docFreq(new Term(ns.prefix+"word",w)) != 0;
				if(!b) // always look in main (was  && ns.additional)
					b = reader.docFreq(new Term("word",w)) != 0;
			}
			wordExistCache.put(w,b);
		}
		return b;
	}
	/** Get frequency of a word if exists (0 if not) */
	private int wordFrequency(String w, Namespaces ns) throws IOException {
		if(ns == null){ // default
			TermDocs td = reader.termDocs(new Term("word",w));
			if(td.next())
				return getFrequency(reader.document(td.doc()),null);			
			return 0;
		} else{ // other
			int freq = 0;
			TermDocs td = reader.termDocs(new Term(ns.prefix+"word",w));
			if(td.next())
				freq = getFrequency(reader.document(td.doc()),ns);
			//if(ns.additional){ // also look in main
			// always look in main
				TermDocs td2 = reader.termDocs(new Term("word",w));
				if(td2.next())
					freq += getFrequency(reader.document(td2.doc()),null);
			//}
			return freq;
		}
	}
	
	/** Return true if (striped) title exists in the index */
	private boolean titleExists(String w, Namespaces ns) throws IOException{
		if(ns == null)
			return reader.docFreq(new Term("title",w)) != 0;

		if(ns.additional){
			boolean b = reader.docFreq(new Term("title",w)) != 0;
			if(b) return true;
		}
		for(Integer i : ns.namespaces){
			boolean b = reader.docFreq(new Term(ns.prefix+"title",i+":"+w)) != 0;
			if(b) return true;
		}
		return false;
	}
	
	private final String getPrefix(Namespaces ns){
		return ns!=null? ns.prefix : "";
	}
	
	/** Return title rank  */
	private int titleRank(String w, Namespaces ns) throws IOException{
		String prefix = getPrefix(ns);
		TermDocs td = reader.termDocs(new Term(prefix+"title",w));
		if(td.next()){
			String s = reader.document(td.doc()).get(prefix+"rank");
			if(s != null)
				return Integer.parseInt(s);
		}
		return 0;
	}
	
	/** return redirect target if w is a redirect, null otherwise */
	private String followRedirect(String w, Namespaces ns) throws IOException{
		String prefix = getPrefix(ns);
		TermDocs td = reader.termDocs(new Term(prefix+"title",w));
		if(td.next()){
			return reader.document(td.doc()).get(prefix+"redirect");
		}
		return null;
	}
	
	/** Add a correct word to word suggestions */
	private void addCorrectWord(String w, ArrayList<ArrayList<SuggestResult>> wordSug, ArrayList<SuggestResult> possibleStopWords) {
		ArrayList<SuggestResult> sug = new ArrayList<SuggestResult>();
		sug.add(new SuggestResult(w,0,0));
		wordSug.add(sug);
		possibleStopWords.add(null);		
	}

	/** Make the resulting SuggestQuery object using proposed changes */
	protected SuggestQuery makeSuggestedQuery(ArrayList<Token> tokens, HashMap<Integer,String> changes, 
			String searchterm, FilterFactory filters, Set<Integer> alwaysReplace, Namespaces ns) throws IOException{
		ArrayList<Integer> ranges = new ArrayList<Integer>();
		StringBuilder sb = new StringBuilder();
		int start = 0;
		ArrayList<Entry<Integer,String>> changeList = new ArrayList<Entry<Integer,String>>();
		changeList.addAll(changes.entrySet());
		// sort changes in asc order
		Collections.sort(changeList,new Comparator<Entry<Integer,String>>() {
			public int compare(Entry<Integer,String> o1, Entry<Integer,String> o2){
				return o1.getKey() - o2.getKey();
			}
		});
		for(Entry<Integer,String> e : changeList){
			Token t = tokens.get(e.getKey());
			String nt = e.getValue();
			String w = t.termText();
			if(w.equals(nt))
				continue; // trying to subtitute same			
			// incorrect words, or doesn't stem to same
			boolean sameStem = (alwaysReplace.contains(e.getKey()))? false : filters.stemsToSame(FastWikiTokenizerEngine.decompose(w),FastWikiTokenizerEngine.decompose(nt)) || filters.stemsToSame(w,nt); 
			//if(!sameStem || (sameStem && !wordExists(w,ns))){
			if(!sameStem){
				int so = t.startOffset();
				int eo = t.endOffset();
				if(so != start)
					sb.append(searchterm.substring(start,so));
				if(!nt.equals("")){
					ranges.add(getLength(sb));
					sb.append(simulateCase(searchterm,t,nt));
					ranges.add(getLength(sb));
				}
				start = eo;				
				// delete any trailing chars as well
				if(nt.equals("")){
					while(start<searchterm.length() && FastWikiTokenizerEngine.isTrailing(searchterm.charAt(start)))
						start++;
				}
			}
		}
		if(start != searchterm.length())
			sb.append(searchterm.substring(start,searchterm.length()));
		return new SuggestQuery(sb.toString(),ranges);
	}
	
	
	/** Extract a map: token_index -> new string for changed titles */
	final public HashMap<Integer,String> extractTitleChanges(String joined, String corrected, ArrayList<Token> tokens){
		HashMap<Integer,String> map = new HashMap<Integer,String>();
		// based on edit distance, examine which spaces are eaten up, and which created, so that
		// we can correctly show differences between new and old strings
		EditDistance ed = new EditDistance(joined);
		int d[][] = ed.getMatrix(corrected);
		// map: space -> same space in edited string
		HashMap<Integer,Integer> spaceMap = new HashMap<Integer,Integer>();
		spaceMapCalls = 0;
		extractSpaceMap(d,joined.length(),corrected.length(),spaceMap,joined,corrected);
		// indexes where spaces are in the edited string
		ArrayList<Integer> spaces = new ArrayList<Integer>();
		int next=0;
		spaces.add(-1);
		while((next = joined.indexOf(' ',next+1)) != -1){
			spaces.add(spaceMap.get(next));
		}
		spaces.add(corrected.length());
		for(int i=1;i<spaces.size();i++){			
			if(spaces.get(i) == null){ // join
				int j=i;
				for(;spaces.get(j)==null;j++);
				map.put(i-1,corrected.substring(spaces.get(i-1)+1,spaces.get(j)));
				for(int z=i;z<j;z++)
					map.put(z,"");				
				if(!acceptChange(tokens.get(i-1).termText()+tokens.get(i).termText(),map.get(i-1)))
					return null; // no large changes
				i=j;
			} else if(spaces.get(i-1)==spaces.get(i)){ // deletion
				// map.put(i-1,"");
				return null; // no deletions! 
			} else{ // split/changed word
				map.put(i-1,corrected.substring(spaces.get(i-1)+1,spaces.get(i)));
				if(!acceptChange(tokens.get(i-1).termText(),map.get(i-1)))
					return null; // no large changes
			}
		}
		// cleanup unchanged
		HashMap<Integer,String> changes = new HashMap<Integer,String>();
		for(Entry<Integer,String> e : map.entrySet()){
			if(!tokens.get(e.getKey()).termText().equals(e.getValue()))
				changes.put(e.getKey(),e.getValue());
		}
		
		return changes;
	}
	
	final protected boolean isNumber(String str){
		for(int i=0;i<str.length();i++)
			if(!Character.isDigit(str.charAt(i)))
				return false;
		return true;
	}
	
	/** Accept change in the title iff we would accept it as a word change */
	final protected boolean acceptChange(String orig, String corr){
		if(orig.equals("") || corr.equals("") || isNumber(orig) || isNumber(corr))
			return false;
		Metric metric = new Metric(orig);
		DoubleMetaphone dmeta =  new DoubleMetaphone();
		String meta1 = dmeta.doubleMetaphone(corr);
		String meta2 = dmeta.doubleMetaphone(corr,true);
		SuggestResult r = new SuggestResult(corr, // new NamespaceFreq(d.get("freq")).getFrequency(nsf),
				0, metric, meta1, meta2);			
		return acceptWord(r,metric);
	}
	
	protected int spaceMapCalls = 0;
	
	/** Transverse the cost matrix and extract mapping of old vs new spaces */
	final protected void extractSpaceMap(int[][] d, int i, int j, Map<Integer,Integer> spaceMap, String str1, String str2) {
		spaceMapCalls++;
		if(spaceMapCalls > 100000){
			log.warn("Long SpaceMap call: str1="+str1+", str2="+str2);
			// FIXME !!
			return;
		}
		int cost = d[i][j];
		if(i == 0 || j == 0)
			return;				

		if(d[i-1][j] <= cost)
			extractSpaceMap(d,i-1,j,spaceMap,str1,str2);
		
		if(d[i][j-1] <= cost)
			extractSpaceMap(d,i,j-1,spaceMap,str1,str2);
		
		if(d[i-1][j-1] <= cost)
			extractSpaceMap(d,i-1,j-1,spaceMap,str1,str2);
		
		if(str1.charAt(i-1)==' ' && str2.charAt(j-1)==' ')
			spaceMap.put(i-1,j-1);
	}
	
	/** Glue tokens together */
	protected String joinTokens(String glue, ArrayList<Token> tokens) {
		return joinTokens(glue,tokens,null);
	}
	protected String joinTokens(String glue, ArrayList<Token> tokens, HashMap<Integer,String> changes) {
		StringBuilder sb = new StringBuilder();
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			if(i>0)
				sb.append(glue);
			if(changes!=null && changes.containsKey(i))
				sb.append(changes.get(i));
			else
				sb.append(t.termText());
		}
		return sb.toString();
	}


	/** Current length of the stringbuilder (in utf-8 bytes) */
	protected int getLength(StringBuilder sb){
		try{
			// would be nice if this would be more efficient 
			return sb.toString().getBytes("utf-8").length;
		} catch(Exception e){
			e.printStackTrace();
			return sb.length();
		}
	}
	
	/** try to figure out the case of original spell-checked word, and output the new word in that case */
	protected String simulateCase(String searchterm, Token t, String newWord) {
		String old = searchterm.substring(t.startOffset(),t.endOffset());
		if(old.equals(old.toLowerCase()))
			return newWord.toLowerCase();
		if(old.equals(old.toUpperCase()))
			return newWord.toUpperCase();
		if(old.length()>1 && old.equals(old.substring(0,1).toUpperCase()+old.substring(1)))
			return newWord.substring(0,1).toUpperCase()+newWord.substring(1).toLowerCase();
		return newWord;
	}
		
	/** 
	 * Extract the maximal number of non-confilicting suggestions, starting with the one
	 * that changes the query least.
	 * 
	 * @return accept, preserve, distance, summed_distance
	 * @throws IOException 
	 */ 
	protected Object[] calculateChanges(ArrayList<Change> changesImmutable, int maxDist, ArrayList<Token> tokens, 
			HashMap<String,HashSet<String>> contextCache, LookupSet allWords, Namespaces ns) throws IOException{
		ArrayList<Change> changes = new ArrayList<Change>();
		changes.addAll(changesImmutable);
		recalculate: for(;;){
			// sort suggested changes by relevance
			Collections.sort(changes,new Comparator<Change>() {
				public int compare(Change o1, Change o2){					
					if(o1.dist == o2.dist){
						if(o1.type != o2.type)
							return o2.freq - o1.freq;
						else if(o2.type == Change.Type.PHRASE && o2.type != o1.type) // favour phrase suggestions
							return 1;
						else if(o1.type == Change.Type.PHRASE && o2.type != o1.type)
							return -1;					
						else if(o2.type == Change.Type.WORD && o2.type != o1.type) // disfavour word suggestions
							return -1;					
						else if(o1.type == Change.Type.WORD && o2.type != o1.type) 
							return 1;
						else
							return o2.freq - o1.freq;
					} else if(o1.dist - o2.dist == -1 && o1.freq * 100 < o2.freq && o1.freq != 0){
						return 1;
					} else if(o1.dist - o2.dist == 1 && o2.freq * 100 < o1.freq && o2.freq != 0){
						return -1;
					} else 
						return o1.dist - o2.dist;					
				}
			});

			log.debug("Sorted changes: "+changes);

			HashMap<Integer,String> accept = new HashMap<Integer,String>();
			HashMap<Integer,String> preserve = new HashMap<Integer,String>();
			HashMap<Integer,Change> changeObjects = new HashMap<Integer,Change>();
			HashSet<Integer> processedChange = new HashSet<Integer>(); // indexes in changes processed in stages
			int dist = 0;
			// first pass: preserve good words
			for(int i=0;i<changes.size();i++){
				Change c = changes.get(i);
				if(c.substitutes.size() == 0){
					for(Entry<Integer,String> e : c.preserves.entrySet()){
						preserve.put(e.getKey(),e.getValue());				
						changeObjects.put(e.getKey(),c);
					}
					processedChange.add(i);
				}
			}
			// second pass: change words that are not valid words
			for(int i=0;i<changes.size();i++){
				if(processedChange.contains(i))
					continue;
				Change c = changes.get(i);
				if(c.substitutes.size() > 0){
					boolean changesBadWord = false;
					for(Integer inx : c.substitutes.keySet())
						if(!wordExists(tokens.get(inx).termText(),ns)){
							changesBadWord = true;
							break;
						}
					if(changesBadWord){
						log.debug("Considering "+c);
						boolean acceptChange = true;
						for(Entry<Integer,String> e : c.substitutes.entrySet()){
							String acceptedTerm = accept.get(e.getKey());
							String preservedTerm = preserve.get(e.getKey());
							if((acceptedTerm != null && !acceptedTerm.equals(e.getValue()))
									|| (preservedTerm != null && !preservedTerm.equals(e.getValue()))){
								acceptChange = false; // conflicting suggestions
								break;
							}
						}
						if(acceptChange && (dist + c.dist < maxDist)){
							log.debug("Applying "+c);
							processedChange.add(i);
							for(Entry<Integer,String> e : c.substitutes.entrySet()){
								accept.put(e.getKey(),e.getValue());
								changeObjects.put(e.getKey(),c);
							}
							for(Entry<Integer,String> e : c.preserves.entrySet()){
								preserve.put(e.getKey(),e.getValue());				
								changeObjects.put(e.getKey(),c);
							}
							dist += c.dist;
						}
					}
				}
			}
			// third pass: valid words changed by phrases
			for(int i=0;i<changes.size();i++){
				if(processedChange.contains(i))
					continue;
				Change c = changes.get(i);
				boolean acceptChange = true;
				for(Entry<Integer,String> e : c.substitutes.entrySet()){
					String acceptedTerm = accept.get(e.getKey());
					String preservedTerm = preserve.get(e.getKey());
					if((acceptedTerm != null && !acceptedTerm.equals(e.getValue()))
							|| (preservedTerm != null && !preservedTerm.equals(e.getValue()))){
						acceptChange = false; // conflicting suggestions
						break;
					}
				}
				if(acceptChange && (dist + c.dist < maxDist)){
					log.debug("Applying "+c);
					processedChange.add(i);
					for(Entry<Integer,String> e : c.substitutes.entrySet()){
						accept.put(e.getKey(),e.getValue());
						changeObjects.put(e.getKey(),c);
					}
					for(Entry<Integer,String> e : c.preserves.entrySet()){
						preserve.put(e.getKey(),e.getValue());				
						changeObjects.put(e.getKey(),c);
					}
					dist += c.dist;
				}
			}
			// check changes made on context - all words need to be in context
			if(contextCache!=null && allWords != null){
				HashSet<String> words = new HashSet<String>();
				words.addAll(accept.values()); 
				words.addAll(preserve.values());
				for(int i=0;i<tokens.size();i++)
					if(!accept.containsKey(i) && !preserve.containsKey(i))
						words.add(tokens.get(i).termText());
				for(Entry<Integer,String> e : accept.entrySet()){
					Change c = changeObjects.get(e.getKey());
					if(c.freq == 0){
						boolean contextMatch = false;
						for(String w : c.preserves.values())
							if(inContext(w,words,contextCache,allWords,ns))
								contextMatch = true;
						for(String w : c.substitutes.values())
							if(inContext(w,words,contextCache,allWords,ns))
								contextMatch = true;
						if(!contextMatch){ // the substituon doesn't match the whole context of query
							changes.remove(c);
							continue recalculate;
						}
					}
				}
			}

			return new Object[] {accept, preserve, changeObjects, dist};
		}
	}
	
	/** Merge two result sets */
	public ArrayList<SuggestResult> mergeResults(ArrayList<SuggestResult> main, ArrayList<SuggestResult> add, int num, Filtering filter){
		// merge
		HashMap<String,SuggestResult> map = new HashMap<String,SuggestResult>();
		ArrayList<SuggestResult> toAdd = new ArrayList<SuggestResult>();
		for(SuggestResult m : main)
			map.put(m.getWord(),m);	 // hash to speedup duplicate lookup		
		for(SuggestResult a : add){
			SuggestResult m = map.get(a.getWord());
			if(m != null){ // merge
				m.frequency += a.frequency;
			} else { // add
				toAdd.add(a);
			}
		}
		main.addAll(toAdd);
		// re-sort
		if(filter == Filtering.WEAK)
			Collections.sort(main,new SuggestResult.ComparatorNoCommonMisspell());
		else
			Collections.sort(main,new SuggestResult.Comparator());
		// trim
		ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
		for(int i=0;i<num && i<main.size();i++)
			ret.add(main.get(i));
		return ret;

	}
	
	/**
	 * Suggest words alone
	 *  
	 * @param additional - if matched in namespaces should be return in addition to default (true), or alone (false)
	 * @return
	 */
	public ArrayList<SuggestResult> suggestWords(String word, int num, Namespaces namespaces, Filtering filter){
		log.debug("Suggesting words for "+word);
		if(namespaces == null) // default
			return suggestWordsOnNamespaces(word,word,num,num,null,filter);
		
		// in other namespaces
		ArrayList<SuggestResult> res = suggestWordsOnNamespaces(word,word,num,num,namespaces,filter);
		if(namespaces.additional){
			ArrayList<SuggestResult> def = suggestWordsOnNamespaces(word,word,num,num,null,filter); // add from default
			return mergeResults(def,res,num,filter);
		}
		return res;
	}
	/** Suggest words using a strong filter, i.e. by using original acceptWord() function */
	public ArrayList<SuggestResult> suggestWords(String word, int num, Namespaces namespaces){
		return suggestWords(word,num,namespaces,Filtering.STRONG);
	}
	
	public ArrayList<SuggestResult> suggestWordsOnNamespaces(String word, String searchword, int num, int pool_size, Namespaces namespaces, Filtering filter){
		String prefix = "";
		if(namespaces != null) // namespaces=null -> default namespace, empty -> all
			prefix = namespaces.prefix;
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeWordQuery(FastWikiTokenizerEngine.decompose(searchword),prefix+"word"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = searcher.search(bq,null,pool_size);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = searcher.doc(sc.doc);
				String w = d.get(prefix+"word");
				String meta1 = d.get(prefix+"meta1");
				String meta2 = d.get(prefix+"meta2");
				String serializedContext = d.get(prefix+"context");
				int freq = getFrequency(d,namespaces);
				if(freq == 0)
					continue; 
				
				SuggestResult r = new SuggestResult(w,	freq, metric, meta1, meta2, serializedContext);
				if(filter == Filtering.STRONG && acceptWord(r,metric))
					res.add(r);
				else if(filter == Filtering.WEAK && acceptWordWeak(r,metric))
					res.add(r);
			}
			// sort
			if(filter == Filtering.WEAK)
				Collections.sort(res,new SuggestResult.ComparatorNoCommonMisspell());
			else
				Collections.sort(res,new SuggestResult.Comparator());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+word+" at "+iid+" : "+e.getMessage(),e);
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}
	
	private int getFrequency(Document d, Namespaces namespaces) {
		String prefix = getPrefix(namespaces);
		int freq = 0;
		if(namespaces == null)
			freq = Integer.parseInt(d.get(prefix+"freq"));
		else{ // all ns
			if(namespaces.namespaces.isEmpty()){
				freq = Integer.parseInt(d.get(prefix+"freq"));
			} else{
				for(Integer i : namespaces.namespaces){
					String f = d.get(prefix+"freq_"+i);
					if(f != null)
						freq += Integer.parseInt(f);
				}		
			}
		}
		return freq;
	}

	/** @return {frequency (int), inTitle (boolean), misspell (String)} */
	private Object[] getPhrase(String phrase, Namespaces namespaces) throws IOException {
		String prefix = getPrefix(namespaces);		
		int freq = 0;
		boolean inTitle = false;
		String misspell = null;
		// default namespaces
		if(namespaces == null || namespaces.additional){
			TermDocs td = reader.termDocs(new Term("phrase",phrase));
			if(td.next()){
				Document d = reader.document(td.doc());
				String f = d.get("freq");
				freq = Integer.parseInt(f);
				String it = d.get("intitle");
				if(it!=null && it.equals("1"))
					inTitle = true;
				misspell = d.get("misspell");
			}
		}
		// other
		if(namespaces!=null){
			TermDocs td = reader.termDocs(new Term(prefix+"phrase",phrase));
			if(td.next()){
				Document d = reader.document(td.doc());
				String it = d.get(prefix+"intitle");
				if(it!=null && it.equals("1"))
					inTitle = true;
				
				if(namespaces.namespaces.isEmpty()){ // all
					String f = d.get(prefix+"freq");
					if(f != null)
						freq += Integer.parseInt(f);
				} else{ // some subset
					for(Integer i : namespaces.namespaces){
						String f = d.get(prefix+"freq_"+i);
						if(f != null)
							freq += Integer.parseInt(f);
					}
				}
			}
		}
			
		return new Object[] { freq, inTitle, misspell };
	}

	public ArrayList<SuggestResult> suggestTitles(String title, int num, int pool_size, int distance, Namespaces namespaces){
		if(namespaces == null)
			return suggestTitlesOnNamespaces(title,num,pool_size,distance,null);
		
		ArrayList<SuggestResult> res = suggestTitlesOnNamespaces(title,num,pool_size,distance,namespaces);
		if(namespaces.additional){
			ArrayList<SuggestResult> main = suggestTitlesOnNamespaces(title,num,pool_size,distance,null);
			return mergeResults(main,res,num,Filtering.STRONG);
		}
		return res;
	}
	
	public ArrayList<SuggestResult> suggestTitlesOnNamespaces(String title, int num, int pool_size, int distance, Namespaces namespaces){
		String prefix = getPrefix(namespaces);
		Metric metric = new Metric(title);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeTitleQuery(FastWikiTokenizerEngine.decompose(title),prefix+"title"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = searcher.search(bq,null,pool_size);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = searcher.doc(sc.doc);
				String w = d.get(prefix+"title");
				if(namespaces != null)
					w = w.substring(w.indexOf(":")+1);
				
				int rank = Integer.parseInt(d.get(prefix+"rank")); 
				SuggestResult r = new SuggestResult(w, rank, metric, "", "");			
				if(acceptTitle(r,metric,distance))				
					res.add(r);
			}
			log.debug("All title results: "+res);
			// sort
			Collections.sort(res,new SuggestResult.ComparatorForTitles());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get title suggestions for "+title+" at "+iid+" : "+e.getMessage(),e);
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}

	
	/** Check if word can be accepted as suggestion, i.e. if it's not too different from typed-in word */
	final protected boolean acceptWord(SuggestResult r, Metric m){
		// for very short words, don't check anything, rely of frequency only
		if(m.word.length() == 2 && r.word.length() == 2)
			return true;
		// check metaphones: don't add if the pronunciation is something completely unrelated
		else if((r.distMetaphone < m.meta1.length() || r.distMetaphone2 < m.meta2.length() 
				   || (r.meta1!=null && m.meta1!=null && (r.meta1.contains(m.meta1) || m.meta1.contains(r.meta1)))) 
				&& (r.distMetaphone<=3 || r.distMetaphone2<=3)
				&& (r.dist <= m.word.length()/2 || r.dist <= r.word.length()/2) 
				&& (Math.abs(m.word.length()-r.word.length()) <= 3 || r.dist <= 3)
				&& r.dist<m.word.length() && r.dist<r.word.length())
			return true;
		else
			return false;
	}
	
	/** Same as acceptWord() but with weaker criteria */
	final protected boolean acceptWordWeak(SuggestResult r, Metric m){
		// for very short words, don't check anything, rely of frequency only
		if(m.word.length() == 2 && r.word.length() == 2)
			return true;
		else if((r.distMetaphone < m.meta1.length() || r.distMetaphone2 < m.meta2.length() 
				   || (r.meta1!=null && m.meta1!=null && (r.meta1.contains(m.meta1) || m.meta1.contains(r.meta1))))
				&& (r.distMetaphone<=3 || r.distMetaphone2<=3)
				&& (r.dist <= m.word.length()/2 || r.dist <= r.word.length()/2) 
				&& (Math.abs(m.word.length()-r.word.length()) <= 3 || r.dist <= 3)
				&& r.dist<m.word.length() && r.dist<r.word.length())
			return true;
		else
			return false;
	}
	
	/** Check if we should accept the title as valid suggestion */
	protected boolean acceptTitle(SuggestResult r, Metric m, int distance){
		// limit edit distance
		if(r.dist < r.word.length()/3 && r.dist<=distance && Math.abs(m.word.length()-r.word.length()) <= 2)
			return true;
		else
			return false;
	}

	/** Make an ngram query on fields with specific prefix, e.g. phrase_ngram2, etc ..  */
	public Query makeWordQuery(String word, String prefix){
		return makeQuery(word,prefix,NgramIndexer.Type.WORDS);
	}
	/** Make ngram query on titles */
	public Query makeTitleQuery(String word, String prefix){
		return makeQuery(word,prefix,NgramIndexer.Type.TITLES);
	}
	
	public final static Query makeQuery(String word, String prefix, NgramIndexer.Type type){
		BooleanQuery bq = new BooleanQuery(true);
		int min = NgramIndexer.getMinNgram(word,type);
		int max = NgramIndexer.getMaxNgram(word,type);
		String fieldBase = NgramIndexer.getNgramField(prefix);
		String startField= NgramIndexer.getStartField(prefix);
		for(int i=min; i <= max; i++ ){
			String[] ngrams = NgramIndexer.nGrams(word,i);
			String field = fieldBase+i;
			for(int j=0 ; j<ngrams.length ; j++){
				String ngram = ngrams[j];
				if(j == 0 && word.length() > 3)
					addQuery(bq,startField+i,ngram,1);
				addQuery(bq,field,ngram,1);
			}
		}
		return bq;
	}

	/** Add a SHOULD clause to boolean query */
	protected static final void addQuery(BooleanQuery q, String field, String value, float boost) {
		Query tq = new TermQuery(new Term(field, value));
		tq.setBoost(boost);
		q.add(new BooleanClause(tq, BooleanClause.Occur.SHOULD));
	}
	
	/** Try to split word into 2 words which make up a phrase */
	public SuggestResult suggestSplit(String word, Namespaces ns){
		ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
		try {
			// find frequency
			int wordFreq = wordFrequency(word,ns); 

			// try different splits 
			for(int i=1;i<word.length()-1;i++){
				String phrase = word.substring(0,i) + "_" + word.substring(i);
				Object[] ret = getPhrase(phrase,ns);
				int freq = (Integer)ret[0];
				if(freq > wordFreq)
					res.add(new SuggestResult(phrase,freq,2));
			}
			if(res.size() > 0){
				Collections.sort(res,new SuggestResult.Comparator());
				return res.get(0);
			}			
		} catch (IOException e) {
			log.warn("I/O error while suggesting split on "+iid+" : "+e.getMessage(),e);
			e.printStackTrace();			
		}
		return null;
	}
	
	/** Returns suggestion if joining words makes sense */
	public SuggestResult suggestJoin(String word1, String word2, Namespaces ns){
		try {
			Object[] ret = getPhrase(word1+"_"+word2,ns);
			int freqPhrase = (Integer)ret[0];
			int freqJoin = wordFrequency(word1+word2,ns);
			if(freqJoin > 0 && freqJoin > freqPhrase)
				return new SuggestResult(word1+word2,freqJoin,1);				
		} catch (IOException e) {
			log.warn("I/O error while suggesting join on "+iid+" : "+e.getMessage(),e);
			e.printStackTrace();
		}
		return null;
	}
	
	/** Fetch a set of string for fuzzy queries */
	public ArrayList<SuggestResult> getFuzzy(String word, NamespaceFilter nsf){
		Namespaces ns = makeNamespaces(nsf);
		int pool = POOL_FUZZY;
		if(word.length() <= 4)
			pool *= 2;
		ArrayList<SuggestResult> sug = suggestWords(word,pool,ns,Filtering.WEAK);
		ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
		for(int i=0;i<MAX_FUZZY && i<sug.size();i++){
			ret.add(sug.get(i));
		}
		return ret;
	}
	
	protected void logRequest(String searchterm, String using, long start, String original){
		if(useLogging)
			log.info(iid+" for original=["+ original +"] suggest: ["+searchterm+"] using=["+using+"] in "+(System.currentTimeMillis()-start)+" ms");
	}
	
}
