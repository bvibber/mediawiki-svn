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
		protected String meta1, meta2;
		protected EditDistance sd;
		protected EditDistance sdmeta1, sdmeta2;
		protected String word;
		
		public Metric(String word){
			this.word = word;
			meta1 = dmeta.doubleMetaphone(word);
			meta2 = dmeta.doubleMetaphone(word,true);
			sd = new EditDistance(word);
			sdmeta1 = new EditDistance(meta1);
			sdmeta2 = new EditDistance(meta2);
		}
		/** Edit distance */
		public int distance(String w){
			return sd.getDistance(w);
		}		
		/** Edit distance of primary metaphone of w */
		public int meta1Distance(String w){
			return sdmeta1.getDistance(dmeta.doubleMetaphone(w));
		}
		/** Edit distance of alternative metaphone of w */
		public int meta2Distance(String w){
			return sdmeta2.getDistance(dmeta.doubleMetaphone(w,true));
		}
		/** If string differs only in duplication of some letters */
		public boolean hasSameLetters(String w){
			return sd.hasSameLetters(w);
		}
	}
	
	/** Number of results to fetch */
	public static final int POOL = 800;
	
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
			log.info("Using stop words "+stopWords);
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
	 * 1) make a phrase suggestion if it will yield more search results
	 * 2) make a phrase or words suggestion (what goes with less edit distance) 
	 *    if there are too little hits
	 *    
	 * @return suggested query, or null if no suggestions 
	 * @throws IOException 
	 */
	@SuppressWarnings("unchecked")
	public SuggestQuery suggest(String searchterm, WikiQueryParser parser, SearchResults res) throws IOException{
		ArrayList<Token> tokens = parser.tokenizeBareText(searchterm);
		
		// collect words in titles, these shouldn't be spell-checked
		ArrayList<HashSet<String>> titles = new ArrayList<HashSet<String>>();
		HashSet<String> correctWords = new HashSet<String>();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,false);
		try {
			for(ResultSet r : res.getResults()){
				HashSet<String> title = new HashSet<String>();
				Token t = null;
				TokenStream ts = analyzer.tokenStream("title",r.title);				
				while( (t = ts.next()) != null ){
					correctWords.add(t.termText());
					title.add(t.termText());
				}				
				titles.add(title);
			}
		} catch (IOException e) {
			log.error("I/O error trying to get list of correct words : "+e.getMessage());
			e.printStackTrace();
		}

		// init suggestions
		// int minFreq = (numHits <  minHitsTitles)? 0 : numHits;
		int minFreq = 0;
		ArrayList<Change> suggestions = new ArrayList<Change>(); 
		ArrayList<Change> suggestionsTitle = new ArrayList<Change>();
		
		// add correct words
		/*for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			if(correctWords.contains(t.termText())){
				Change c = new Change(0,1,Change.Type.TITLE_WORD);
				c.preserves.put(i,t.termText());
				suggestions.add(c);
			}
		} */
		
		// check for exact title match
		if(tokens.size() == 1){
			String w = tokens.get(0).termText();
			if(correctWords.contains(w) && reader.docFreq(new Term("title",w)) != 0)
				return null;
		}

		HashSet<String> stemmedCorrectWords = stemSet(correctWords,parser.getBuilder().getFilters());
		ArrayList<ArrayList<SuggestResult>> wordSug = new ArrayList<ArrayList<SuggestResult>>();
		HashSet<Integer> correctIndex = new HashSet<Integer>();
		ArrayList<SuggestResult> possibleStopWords = new ArrayList<SuggestResult>();
		HashSet<Integer> correctPhrases = new HashSet<Integer>(); // indexes of words in correct phrases
		int numStopWords = 0;

		// generate list of correct phrases
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			String w = t.termText();
			if(correctWords.contains(w))
				correctIndex.add(i);
			if(stopWords.contains(w)){
				numStopWords ++;
				continue;
			}
			int i2 = i +1;
			String gap = "_";
			String w2 = null;
			for(;i2<tokens.size();i2++){
				if(stopWords.contains(tokens.get(i2).termText())){
					gap += tokens.get(i2).termText()+"_";
				} else{
					w2 = tokens.get(i2).termText();
				}
			}
			if(w2 == null)
				continue;
			
			if(correctWords.contains(w) && correctWords.contains(w2)){
				for(HashSet<String> title : titles){
					if(title.contains(w) && title.contains(w2)){
						correctPhrases.add(i);
						correctPhrases.add(i2);
						break;
					}
				}
			}
		}
		if(correctPhrases.size()+numStopWords >= tokens.size() 
				&& correctWords.size()+numStopWords >= tokens.size()){
			log.info("All correct!");
			return null; // everything is correct!
		}
		
		// suggest words, splits, joins
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			String w = t.termText();
			if(w.length() < 2 || stopWords.contains(w) || correctPhrases.contains(i)){ // || correctWords.contains(w)
				ArrayList<SuggestResult> sug = new ArrayList<SuggestResult>();
				sug.add(new SuggestResult(w,0,0));
				wordSug.add(sug);
				possibleStopWords.add(null);
				continue;
			} else if(correctWords.contains(w))
				correctIndex.add(i);
			// suggest word
			ArrayList<SuggestResult> sug;
			if(correctWords.contains(w))
				sug = suggestWords(w,w,POOL/2,POOL/2);
			else
				sug = suggestWords(w,POOL);
			if(sug.size() > 0){
				wordSug.add(sug);
				SuggestResult maybeStopWord = null;
				// see if this might be a stop word
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
			if(!correctWords.contains(w)){
				SuggestResult split = suggestSplit(w,minFreq);
				if(split != null){
					Change sc = new Change(split.dist,split.frequency,Change.Type.SPLIT);
					sc.substitutes.put(i,split.word.replace("_"," "));
					suggestions.add(sc);
				}
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
			boolean maybeStopWord = false; // the currecnt i2 might be a stop word, try to find phrases with it as stop word
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
				if(correctIndex.contains(i) && correctIndex.contains(i2))
					forTitlesOnly = true;
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
							if(s1.word.equals(w1))
								c.preserves.put(i,w1);
							else if(!good1 || (inTitle && diff1 <= 2 && !correctWords.contains(w1)))					
								c.substitutes.put(i,s1.word);
							else if(!good1 || (inTitle && diff1 <=2)){
								forTitlesOnly = true;
								c.substitutes.put(i,s1.word);
							} else
								accept = false;
							if(s2.word.equals(w2))
								c.preserves.put(i2,w2);
							else if(!good2 || (inTitle && diff2 <= 2 && !correctWords.contains(w2)))					
								c.substitutes.put(i2,s2.word);
							else if(!good2 || (inTitle && diff2 <= 2)){
								forTitlesOnly = true;
								c.substitutes.put(i2,s2.word);
							} else
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
		if(suggestionsTitle.size() > 0){
			log.info("Trying exact-title matches");
			Object[] ret = calculateChanges(suggestionsTitle,searchterm.length()/2);
			ArrayList<Entry<Integer,String>> proposedTitle = (ArrayList<Entry<Integer, String>>) ret[0];
			boolean madeChanges = false;
			String title = searchterm;
			String formated = searchterm;
			for(Entry<Integer,String> e : proposedTitle){
				Token t = tokens.get(e.getKey());
				String nt = e.getValue();
				// replace words if they don't stem to same word, of they stem to same, but the words is misspelled
				boolean stemNotSame = stemNotSameOrInSet(t.termText(),nt,parser.getBuilder().getFilters(),stemmedCorrectWords); 
				if(stemNotSame || (!stemNotSame && reader.docFreq(new Term("word",t.termText())) == 0)){
					formated = markSuggestion(formated,t,nt);
					title = applySuggestion(title,t,nt);
					madeChanges = true;
				}
				if(madeChanges){
					// check if some title exactly matches the spell-checked query
					if(reader.docFreq(new Term("title",title.toLowerCase())) != 0){
						log.info("Found title match for "+title);
						return new SuggestQuery(tidy(title),tidy(formated));		
					}
				}
			}
		} else if(tokens.size() == 1 && wordSug.get(0)!=null
				&& wordSug.get(0).size() > 0 && !correctWords.contains(tokens.get(0).termText())){ 
			// only one token, try different spell-checks for title
			log.info("Trying exact-title single word match");
			ArrayList<SuggestResult> sg = (ArrayList<SuggestResult>) wordSug.get(0).clone();
			Collections.sort(sg,new SuggestResult.ComparatorNoCommonMisspell());
			Token t = tokens.get(0);
			int maxdist = sg.get(0).getDist();
			if(maxdist != 0){
				for(SuggestResult r : sg){
					if(r.getDist() > maxdist)
						break;
					String title = r.getWord();
					if(reader.docFreq(new Term("title",title.toLowerCase())) != 0){
						log.info("Found title match for "+title);
						return new SuggestQuery(tidy(title),tidy(markSuggestion(searchterm,t,title)));		
					}
				}
			}
		}

		// find best suggestions so far
		HashSet<Integer> preserveTokens = new HashSet<Integer>();
		ArrayList<Entry<Integer,String>> proposedChanges = new ArrayList<Entry<Integer,String>>();
		if(suggestions.size() > 0){
			// found some suggestions
			log.info("Trying phrases ...");
			Object[] ret = calculateChanges(suggestions,searchterm.length()/2);
			proposedChanges = (ArrayList<Entry<Integer, String>>) ret[0];
			ArrayList<Entry<Integer,String>> preservedWords = (ArrayList<Entry<Integer, String>>) ret[1];
			for(Entry<Integer,String> e : preservedWords)
				preserveTokens.add(e.getKey());			
			for(Entry<Integer,String> e : proposedChanges)
				preserveTokens.add(e.getKey());
		}
		log.info("Adding words, preserve tokens: "+preserveTokens+", preserve correct phrases: "+correctPhrases);
		// last resort: go with individual word suggestions
		HashMap<Integer,String> wordChanges = new HashMap<Integer,String>();
		for(int i=0;i<tokens.size();i++){
			if(preserveTokens.contains(i) || correctPhrases.contains(i))
				continue;
			// TODO: maybe check for common misspells here?!
			ArrayList<SuggestResult> sug = wordSug.get(i);
			if(sug == null)
				continue;
			SuggestResult s = sug.get(0);
			if(s.dist!=0)
				wordChanges.put(i,s.word);
		}
		if(wordChanges.size() != 0)
			proposedChanges.addAll(wordChanges.entrySet());
		
		// sort in reverse order from that in query, i.e. first change in the last term
		Collections.sort(proposedChanges,new Comparator<Entry<Integer,String>>() {
			public int compare(Entry<Integer,String> o1, Entry<Integer,String> o2){
				return o2.getKey() - o1.getKey();
			}
		});
		// substitute
		if(proposedChanges.size() > 0){
			boolean madeChanges = false;
			String formated = searchterm;
			for(Entry<Integer,String> e : proposedChanges){
				Token t = tokens.get(e.getKey());
				String nt = e.getValue();
				// incorrect words, or doesn't stem to same
				boolean stemNotSame = stemNotSameOrInSet(t.termText(),nt,parser.getBuilder().getFilters(),stemmedCorrectWords); 
				if(stemNotSame || (!stemNotSame && reader.docFreq(new Term("word",t.termText())) == 0)){
					formated = markSuggestion(formated,t,nt);
					searchterm = applySuggestion(searchterm,t,nt);
					madeChanges = true;
				}
			}
			if(madeChanges)
				return new SuggestQuery(tidy(searchterm),tidy(formated));
		}

		return null;
	}

	/** try to figure out the case of original spell-checked word, and output the new word in that case */
	protected String simulateCase(String formated, Token t, String newWord) {
		String old = formated.substring(t.startOffset(),t.endOffset());
		if(old.equals(old.toLowerCase()))
			return newWord.toLowerCase();
		if(old.equals(old.toUpperCase()))
			return newWord.toUpperCase();
		if(old.length()>1 && old.equals(old.substring(0,1).toUpperCase()+old.substring(1)))
			return newWord.substring(0,1).toUpperCase()+newWord.substring(1).toLowerCase();
		return newWord;
	}

	protected String markSuggestion(String formated, Token t, String newWord){
		return formated.substring(0,t.startOffset()) 
		+ "<i>" + simulateCase(formated,t,newWord) + "</i>"
		+ formated.substring(t.endOffset());
	}
	
	protected String applySuggestion(String searchterm, Token t, String newWord){
		return searchterm.substring(0,t.startOffset())  
		+ simulateCase(searchterm,t,newWord)
		+ searchterm.substring(t.endOffset());
	}
	
	/** tidy the query, convert double spaces into single spaces, and such... */
	protected String tidy(String searchterm){
		return searchterm.replaceAll("<i></i>","").replaceAll(" +"," ").replaceAll(";","");
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
		ArrayList<Entry<Integer,String>> proposedChanges = new ArrayList<Entry<Integer,String>>();
		proposedChanges.addAll(accept.entrySet());
		// sort in reverse order from that in query, i.e. first change in the last term
		Collections.sort(proposedChanges,new Comparator<Entry<Integer,String>>() {
			public int compare(Entry<Integer,String> o1, Entry<Integer,String> o2){
				return o2.getKey() - o1.getKey();
			}
		});
		ArrayList<Entry<Integer,String>> preservedWords = new ArrayList<Entry<Integer,String>>();
		preservedWords.addAll(preserve.entrySet());
		return new Object[] {proposedChanges, preservedWords};
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

	/** Make an ngram query on fields with specific prefix, e.g. phrase_ngram2, etc ..  */
	public Query makeWordQuery(String word, String prefix){
		BooleanQuery bq = new BooleanQuery(true);
		int min = NgramIndexer.getMinNgram(word);
		int max = NgramIndexer.getMaxNgram(word);
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
	public HashSet<String> stemSet(HashSet<String> set, FilterFactory filters){
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
}
