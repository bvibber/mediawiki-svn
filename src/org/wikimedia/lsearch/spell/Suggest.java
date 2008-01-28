package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
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
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.NamespaceFilterWrapper;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.spell.api.NgramIndexer;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;

public class Suggest {
	static Logger log = Logger.getLogger(Suggest.class);
	protected IndexId iid;
	protected IndexSearcher searcher;	
	protected IndexReader reader;
	protected static WeakHashMap<IndexSearcher,Set<String>> stopWordsIndexes = new WeakHashMap<IndexSearcher,Set<String>>();
	protected Set<String> stopWords;
	
	/** Distance an metaphone metrics */
	static public class Metric {
		protected DoubleMetaphone dmeta =  new DoubleMetaphone();
		protected String meta1="", meta2="";
		protected EditDistance sd;
		protected EditDistance sdmeta1=null, sdmeta2=null;
		protected String word;
		
		public Metric(String word){
			this(word,true);
		}		
		public Metric(String word, boolean useMetaphones){
			this.word = word;
			sd = new EditDistance(word);
			if(useMetaphones){
				meta1 = dmeta.doubleMetaphone(word);
				meta2 = dmeta.doubleMetaphone(word,true);
				sdmeta1 = new EditDistance(meta1,false);
				sdmeta2 = new EditDistance(meta2,false);
			}
		}
		/** Edit distance */
		public int distance(String w){
			return sd.getDistance(w);
		}		
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
	}
	
	/** Number of results to fetch */
	public static final int POOL = 800;
	/** Number of results to fetch for titles */
	public static final int POOL_TITLE = 100;
	
	/** Lower limit to hit rate for joining */
	public static final int JOIN_FREQ = 1;
	
	public Suggest(IndexId iid) throws IOException{
		SearcherCache cache = SearcherCache.getInstance();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.iid = iid;
		this.searcher = cache.getLocalSearcher(iid.getSpell());
		this.reader = searcher.getIndexReader();
		
		synchronized(stopWordsIndexes){
			if(!stopWordsIndexes.containsKey(searcher)){
				Set<String> s = Collections.synchronizedSet(new HashSet<String>());
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
	 * Make a suggestion:
	 *    
	 * @return suggested query, or null if no suggestions 
	 * @throws IOException 
	 */
	@SuppressWarnings("unchecked")
	public SuggestQuery suggest(String searchterm, ArrayList<Token> tokens, HashSet<String> phrases, HashSet<String> foundInContext) throws IOException{		
		FilterFactory filters = new FilterFactory(iid);
		long start = System.currentTimeMillis();

		// init suggestions
		int minFreq = 0;
		ArrayList<Change> suggestions = new ArrayList<Change>(); 
		ArrayList<Change> suggestionsTitle = new ArrayList<Change>();
		
		System.out.println("Phrases: "+phrases);
		System.out.println("InContext: "+foundInContext);
				
		// check exact title matches		
		String joinTokens = joinTokens(" ",tokens);
		if(joinTokens.length() > 7){
			ArrayList<SuggestResult> titleRes = suggestTitles(joinTokens,1,POOL_TITLE);
			if(titleRes.size() > 0){
				SuggestResult tr = titleRes.get(0);
				if(tr.dist == 0){
					logRequest(searchterm,"CORRECT (exact title match)",start);
					return new SuggestQuery(searchterm,new ArrayList<Integer>());
				} else{
					HashMap<Integer,String> changes = extractTitleChanges(joinTokens,tr.word,tokens);
					if(changes != null){
						SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,true);
						logRequest(sq.getSearchterm(),"titles",start);
						return sq;
					}
				}
			}
		}
		
		// check if all words are found within phrases during highlighting
		if(tokens.size() > 1 && tokens.size() == phrases.size() + 1){
			logRequest(searchterm,"CORRECT (by highlight phrases)",start);
			return new SuggestQuery(searchterm,new ArrayList<Integer>());
		}

		// word suggestions
		ArrayList<ArrayList<SuggestResult>> wordSug = new ArrayList<ArrayList<SuggestResult>>();
		// indexes of words in found during highlighting in phrases
		HashSet<Integer> inPhrases = new HashSet<Integer>();
		// words that might spellcheck to stop words
		ArrayList<SuggestResult> possibleStopWords = new ArrayList<SuggestResult>();
		
		// suggest words, splits, joins
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			String w = t.termText();
			// one-letter words are always correct
			if(w.length() < 2){ 
				addCorrectWord(w,wordSug,possibleStopWords);				
				continue;
			}
			// words in phrases are also always correct
			if(i+1<tokens.size() && phrases.contains(w+"_"+tokens.get(i+1).termText())){
				inPhrases.add(i);
				addCorrectWord(w,wordSug,possibleStopWords);
				inPhrases.add(i+1);
				addCorrectWord(tokens.get(i+1).termText(),wordSug,possibleStopWords);
				i++;
				continue;
			}
			// words found within context should be spell-checked only if they are not valid words
			if(foundInContext.contains(w) && wordExists(w)){
				addCorrectWord(w,wordSug,possibleStopWords);
				continue;
			}
				
			// suggest word
			ArrayList<SuggestResult> sug = suggestWords(w,POOL);
			if(sug.size() > 0){
				wordSug.add(sug);
				SuggestResult maybeStopWord = null;
				// find the result where the wors is changed to stopword
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
			SuggestResult split = suggestSplit(w,minFreq);
			if(split != null){
				Change sc = new Change(split.dist,split.frequency,Change.Type.SPLIT);
				sc.substitutes.put(i,split.word.replace("_"," "));
				suggestions.add(sc);
			}
			
			// suggest join
			if(i-1 >= 0 
					&& (wordSug.get(i-1)==null || wordSug.get(i-1).get(0).dist!=0)
					&& (wordSug.get(i)==null || wordSug.get(i).get(0).dist!=0)){
				SuggestResult join = suggestJoin(tokens.get(i-1).termText(),w,minFreq);
				if(join != null){
					Change sc = new Change(join.dist,join.frequency,Change.Type.JOIN);
					sc.substitutes.put(i-1,""); 
					sc.substitutes.put(i,join.word);
					suggestions.add(sc);
				}
			}
		}
		
		// find all phrases up to some edit distance
		for(int i=0;i<tokens.size();i++){
			ArrayList<SuggestResult> sug1 = wordSug.get(i);
			String w1 = tokens.get(i).termText();
			if(sug1 == null || stopWords.contains(w1))
				continue;
			ArrayList<SuggestResult> sug2 = null;
			String w2 = null;
			String gap = "_";
			// if w1 is spellchecked right
			boolean good1 = sug1.get(0).getDist() == 0;
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
				boolean good2 = sug2.get(0).getDist() == 0;
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
						int freq = 0;
						boolean inTitle = false;
						TermDocs td = reader.termDocs(new Term("phrase",phrase));
						if(td.next()){
							int docid = td.doc();
							freq = Integer.parseInt(reader.document(docid).get("freq"));
							String it = reader.document(docid).get("intitle");
							if(it!=null && it.equals("1"))
								inTitle = true;

						}
						//log.info("Checking "+phrase);
						if(freq > 0){
							// number of characters added/substracted
							int diff1 = Math.abs(s1.word.length()-w1.length());
							int diff2 = Math.abs(s2.word.length()-w2.length());
							log.info("Found "+phrase+" at dist="+(s1.dist+s2.dist)+", freq="+freq+" inTitle="+inTitle);
							int dist = s1.dist + s2.dist + distOffset;
							boolean accept = true;
							Change c = new Change(dist,freq,Change.Type.PHRASE);
							// accept changes: 1) if the word is misspelled, 2) if it's not then for title matches
							if(s1.word.equals(w1))
								c.preserves.put(i,w1);
							else if(!good1 || (inTitle && diff1 <=2 && !foundInContext.contains(w1)) )					
								c.substitutes.put(i,s1.word);
							/*else if(!good1 || (inTitle && diff1 <=2)){
								forTitlesOnly = true;
								c.substitutes.put(i,s1.word);
							}*/ else
								accept = false;
							
							if(s2.word.equals(w2))
								c.preserves.put(i2,w2);
							else if(!good2 || (inTitle && diff2 <= 2 && !foundInContext.contains(w2)))					
								c.substitutes.put(i2,s2.word);
							/*else if(!good2 || (inTitle && diff2 <= 2)){
								forTitlesOnly = true;
								c.substitutes.put(i2,s2.word);
							}*/ else
								accept = false;
							
							if(accept){
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
		// try to construct a valid title by spell-checking all words
		if(suggestionsTitle.size() > 0 && tokens.size() > 1){
			Object[] ret = calculateChanges(suggestionsTitle,searchterm.length()/2);
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
			if( titleExists(proposedTitle.toString()) ){
				SuggestQuery sq = makeSuggestedQuery(tokens,changes,searchterm,filters,true);
				logRequest(sq.getSearchterm(),"phrases (title match)",start);
				return sq;
			}
		}

		// find best suggestion based on phrases
		HashMap<Integer,String> preserveTokens = new HashMap<Integer,String>();
		HashMap<Integer,String> proposedChanges = new HashMap<Integer,String>();
		String using="";
		if(suggestions.size() > 0){
			// found some suggestions
			Object[] ret = calculateChanges(suggestions,searchterm.length()/2);
			proposedChanges = (HashMap<Integer,String>) ret[0];
			preserveTokens = (HashMap<Integer,String>) ret[1];
			using += "phrases";
		}
		
		// if some words are still unchecked
		for(int i=0;i<tokens.size();i++){
			if(preserveTokens.containsKey(i) || proposedChanges.containsKey(i))
				continue;
			ArrayList<SuggestResult> sug = wordSug.get(i);
			if(sug == null)
				continue;
			SuggestResult s = sug.get(0);
			if(s.dist!=0){
				proposedChanges.put(i,s.word);
				if(using.equals("phrases"))
					using = "phrases+words";
				else
					using = "words";
			}
		}
		
		SuggestQuery sq = makeSuggestedQuery(tokens,proposedChanges,searchterm,filters,false);
		logRequest(sq.getSearchterm(),using,start);
		return sq;
	}
	
	/** Return true if word exists in the index */
	private boolean wordExists(String w) throws IOException{
		return reader.docFreq(new Term("word",w)) != 0;
	}
	
	/** Return true if (striped) title exists in the index */
	private boolean titleExists(String w) throws IOException{
		return reader.docFreq(new Term("title",w)) != 0;
	}
	
	/** Add a correct word to word suggestions */
	private void addCorrectWord(String w, ArrayList<ArrayList<SuggestResult>> wordSug, ArrayList<SuggestResult> possibleStopWords) {
		ArrayList<SuggestResult> sug = new ArrayList<SuggestResult>();
		sug.add(new SuggestResult(w,0,0));
		wordSug.add(sug);
		possibleStopWords.add(null);		
	}

	/** Make the resulting SuggestQuery object using proposed changes */
	protected SuggestQuery makeSuggestedQuery(ArrayList<Token> tokens, HashMap<Integer,String> changes, String searchterm, FilterFactory filters, boolean allowSameStems) throws IOException{
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
			if(t.termText().equals(nt))
				continue; // trying to subtitute same
			// incorrect words, or doesn't stem to same
			boolean sameStem = (allowSameStems)? false : stemsToSame(t.termText(),nt,filters); 
			if(!sameStem || (sameStem && reader.docFreq(new Term("word",t.termText())) == 0)){
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
			}
		}
		if(start != searchterm.length())
			sb.append(searchterm.substring(start,searchterm.length()));
		return new SuggestQuery(sb.toString(),ranges);
	}
	
	
	/** Extract a map: token_index -> new string for changed titles */
	public HashMap<Integer,String> extractTitleChanges(String joined, String corrected, ArrayList<Token> tokens){
		HashMap<Integer,String> map = new HashMap<Integer,String>();
		// based on edit distance, examine which spaces are eaten up, and which created, so that
		// we can correctly show differences between new and old strings
		EditDistance ed = new EditDistance(joined);
		int d[][] = ed.getMatrix(corrected);
		// map: space -> same space in edited string
		HashMap<Integer,Integer> spaceMap = new HashMap<Integer,Integer>(); 
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
				if(!acceptChange(tokens.get(i-1).termText(),map.get(i-1)))
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
		
		return map;
	}
	
	/** Accept change in the title iff we would accept it as a word change */
	protected boolean acceptChange(String orig, String corr){		
		/*EditDistance ed = new EditDistance(orig);
		int dist = ed.getDistance(corr);
		if(dist >= orig.length()-1)
			return true; */
		if(orig.equals("") || corr.equals(""))
			return false;
		Metric metric = new Metric(orig);
		DoubleMetaphone dmeta =  new DoubleMetaphone();
		String meta1 = dmeta.doubleMetaphone(corr);
		String meta2 = dmeta.doubleMetaphone(corr,true);
		SuggestResult r = new SuggestResult(corr, // new NamespaceFreq(d.get("freq")).getFrequency(nsf),
				0, metric, meta1, meta2);			
		return acceptWord(r,metric);			
	}
	
	/** Transverse the cost matrix and extract mapping of old vs new spaces */
	protected static void extractSpaceMap(int[][] d, int i, int j, HashMap<Integer,Integer> spaceMap, String str1, String str2) {
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
		boolean first = true;
		StringBuilder sb = new StringBuilder();
		for(Token t : tokens){
			if(!first)
				sb.append(glue);
			else
				first = false;
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
	 * @return set of token_number -> new string.
	 */ 
	protected Object[] calculateChanges(ArrayList<Change> changes, int maxDist){
		// sort suggested changes by relevance
		Collections.sort(changes,new Comparator<Change>() {
			public int compare(Change o1, Change o2){					
				if(o1.dist == o2.dist){
					if(o2.type == Change.Type.PHRASE && o2.type != o1.type) // favour phrase suggestions
						return 1;
					else if(o2.type == Change.Type.WORD && o2.type != o1.type) // disfavour word suggestions
						return -1;					
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
		
		log.info("Sorted changes: "+changes);
		
		HashMap<Integer,String> accept = new HashMap<Integer,String>();
		HashMap<Integer,String> preserve = new HashMap<Integer,String>();
		int dist = 0;
		for(Change c : changes){
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
				for(Entry<Integer,String> e : c.substitutes.entrySet())
					accept.put(e.getKey(),e.getValue());
				for(Entry<Integer,String> e : c.preserves.entrySet())
					preserve.put(e.getKey(),e.getValue());
				dist += c.dist;
			}
		}
		return new Object[] {accept, preserve};
	}
	
	public ArrayList<SuggestResult> suggestWords(String word, int num){
		ArrayList<SuggestResult> r1 = suggestWords(word,word,POOL,POOL);
		if(r1 != null && r1.size() > 0){
			if(r1.get(0).dist == 0)
				return r1;
			ArrayList<SuggestResult> r2 = suggestWords(word,r1.get(0).word,POOL/2,POOL/2);
			if(r2 != null && r2.size() > 0){
				HashSet<SuggestResult> hr = new HashSet<SuggestResult>();
				hr.addAll(r1); hr.addAll(r2);
				ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
				res.addAll(hr);
				Collections.sort(res,new SuggestResult.ComparatorNoCommonMisspell());
				return res;
			}
			return r1;
		} else
			return r1;
	}
	
	public ArrayList<SuggestResult> suggestWords(String word, String searchword, int num, int pool_size){
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeWordQuery(searchword,"word"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = searcher.search(bq,null,pool_size);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = searcher.doc(sc.doc);
				String w = d.get("word");
				String meta1 = d.get("meta1");
				String meta2 = d.get("meta2");
				SuggestResult r = new SuggestResult(w, // new NamespaceFreq(d.get("freq")).getFrequency(nsf),
						Integer.parseInt(d.get("freq")),
						metric, meta1, meta2);			
				if(acceptWord(r,metric))				
					res.add(r);
			}
			// sort
			Collections.sort(res,new SuggestResult.Comparator());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+word+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}
	
	public ArrayList<SuggestResult> suggestTitles(String title, int num, int pool_size){
		Metric metric = new Metric(title);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeTitleQuery(title,"title"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = searcher.search(bq,null,pool_size);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = searcher.doc(sc.doc);
				String w = d.get("title");
				SuggestResult r = new SuggestResult(w, // new NamespaceFreq(d.get("freq")).getFrequency(nsf),
						Integer.parseInt(d.get("rank")),
						metric, "","");			
				if(acceptTitle(r,metric))				
					res.add(r);
			}
			// sort
			Collections.sort(res,new SuggestResult.ComparatorForTitles());
			ArrayList<SuggestResult> ret = new ArrayList<SuggestResult>();
			for(int i=0;i<num && i<res.size();i++)
				ret.add(res.get(i));
			return ret;
		} catch (IOException e) {
			log.error("Cannot get title suggestions for "+title+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
	}

	
	/** Check if word can be accepted as suggestion, i.e. if it's not too different from typed-in word */
	protected boolean acceptWord(SuggestResult r, Metric m){
		// for very short words, don't check anything, rely of frequency only
		if(m.word.length() == 2 && r.word.length() == 2)
			return true;
		// check metaphones: don't add if the pronunciation is something completely unrelated
		else if((r.distMetaphone < m.meta1.length() || r.distMetaphone2 < m.meta2.length()) && (r.distMetaphone<=3 || r.distMetaphone2<=3)
				&& (r.dist <= m.word.length()/2 || r.dist <= r.word.length()/2) && Math.abs(m.word.length()-r.word.length()) <= 3)
			return true;
		else
			return false;
	}
	
	/** Check if we should accept the title as valid suggestion */
	protected boolean acceptTitle(SuggestResult r, Metric m){
		// limit edit distance
		if(r.dist < r.word.length()/3 && r.dist<=4 && Math.abs(m.word.length()-r.word.length()) <= 2)
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
	
	public Query makeQuery(String word, String prefix, NgramIndexer.Type type){
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
	protected void addQuery(BooleanQuery q, String field, String value, float boost) {
		Query tq = new TermQuery(new Term(field, value));
		tq.setBoost(boost);
		q.add(new BooleanClause(tq, BooleanClause.Occur.SHOULD));
	}
	
	/** Try to split word into 2 words which make up a phrase */
	public SuggestResult suggestSplit(String word, int minFreq){
		int freq = 0;
		Hits hits;
		ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
		try {
			// find frequency
			hits = searcher.search(new TermQuery(new Term("word",word)));
			if(hits.length() == 1)
				freq = Integer.parseInt(hits.doc(0).get("freq"));

			// try different splits 
			for(int i=1;i<word.length()-1;i++){
				String phrase = word.substring(0,i) + "_" + word.substring(i);
				hits = searcher.search(new TermQuery(new Term("phrase",phrase)));
				if(hits.length() > 0){
					int pfreq = Integer.parseInt(hits.doc(0).get("freq"));
					if(pfreq >= freq && pfreq > minFreq)
						res.add(new SuggestResult(phrase,pfreq,2));
				}
			}
			if(res.size() > 0){
				Collections.sort(res,new SuggestResult.Comparator());
				return res.get(0);
			}			
		} catch (IOException e) {
			log.warn("I/O error while suggesting split on "+iid+" : "+e.getMessage());
			e.printStackTrace();			
		}
		return null;
	}
	
	/** Returns suggestion if joining words makes sense */
	public SuggestResult suggestJoin(String word1, String word2, int minFreq){
		try {
			Hits hits = searcher.search(new TermQuery(new Term("word",word1+word2)));
			if(hits.length() > 0){
				int freq = Integer.parseInt(hits.doc(0).get("freq"));
				if(freq >= minFreq)
					return new SuggestResult(word1+word2,freq,1);
			}
		} catch (IOException e) {
			log.warn("I/O error while suggesting join on "+iid+" : "+e.getMessage());
			e.printStackTrace();
		}
		return null;
	}
	
	/** check if two words have same stemmed variants */
	public boolean stemsToSame(String word1, String word2, FilterFactory filters){
		if(!filters.hasStemmer())
			return false;
		ArrayList<String> in = new ArrayList<String>();
		in.add(word1); in.add(word2);
		TokenStream ts = filters.makeStemmer(new StringsTokenStream(in));
		try {
			Token t1 = ts.next();
			Token t2 = ts.next();
			if(t1 != null && t2 != null && t1.termText().equals(t2.termText()))
				return true;
		} catch (IOException e) {
			log.error("Cannot stem words "+word1+", "+word2+" : "+e.getMessage());			
		}
		return false;
	}
	
	/** check if stemmed newWord is 1) not same to stememed oldWord, OR  2) not in stemmed set*/
	public boolean stemNotSameOrInSet(String oldWord, String newWord, FilterFactory filters, Set<String> stemmedSet){
		if(!filters.hasStemmer())
			return false;
		ArrayList<String> in = new ArrayList<String>();
		in.add(oldWord); in.add(newWord);
		TokenStream ts = filters.makeStemmer(new StringsTokenStream(in));
		try {
			Token t1 = ts.next();
			Token t2 = ts.next();
			if(t1 != null && t2 != null && (t1.termText().equals(t2.termText()) && stemmedSet.contains(t2.termText())))
				return false;
		} catch (IOException e) {
			log.error("Cannot stem words "+oldWord+", "+oldWord+" : "+e.getMessage());			
		}
		return true;
	}
	
	/** stem all words in the set */
	public static HashSet<String> stem(HashSet<String> set, FilterFactory filters){
		if(!filters.hasStemmer())
			return new HashSet<String>();
		HashSet<String> ret = new HashSet<String>();
		TokenStream ts = filters.makeStemmer(new StringsTokenStream(set));
		try {
			Token t;
			while((t = ts.next()) != null)
				ret.add(t.termText());
			return ret;
		} catch (IOException e) {
			log.error("Cannot stem set "+set+" : "+e.getMessage());
			return new HashSet<String>();
		}
	}
	/** Stem one word */
	public static String stem(String word, FilterFactory filters){
		if(!filters.hasStemmer())
			return null;
		HashSet<String> set = new HashSet<String>();
		set.add(word);
		HashSet<String> ret = stem(set,filters);
		if(ret.size() == 0)
			return null;
		else
			return ret.iterator().next();
	}
	
	static class StringsTokenStream extends TokenStream {
		Iterator<String> input;
		int count = 0;
		StringsTokenStream(Collection<String> input){
			this.input = input.iterator();
		}
		@Override
		public Token next() throws IOException {
			if(input.hasNext())
				return new Token(input.next(),count,count++);
			else
				return null;
		}
		
	}
	
	protected void logRequest(String searchterm, String using, long start){
		log.info(iid+" suggest: ["+searchterm+"] using=["+using+"] in "+(System.currentTimeMillis()-start)+" ms");
	}
}
