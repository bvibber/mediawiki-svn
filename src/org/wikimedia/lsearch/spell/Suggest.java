package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
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
import org.wikimedia.lsearch.spell.api.NamespaceFreq;
import org.wikimedia.lsearch.spell.api.NgramIndexer;
import org.wikimedia.lsearch.spell.dist.DoubleMetaphone;
import org.wikimedia.lsearch.spell.dist.EditDistance;

public class Suggest {
	static Logger log = Logger.getLogger(Suggest.class);
	protected IndexId iid;
	protected IndexSearcher words;
	protected IndexSearcher titles;	
	protected IndexReader titlesReader;
	protected int minHitsWords;
	protected int minHitsTitles;
	protected static WeakHashMap<IndexSearcher,Set<String>> stopWordsIndexes = new WeakHashMap<IndexSearcher,Set<String>>();
	protected Set<String> stopWords;
	
	/** Distance an metaphone metrics */
	static class Metric {
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
	}
	
	/** Number of results to fetch */
	public static final int POOL = 800;
	
	/** Lower limit to hit rate for joining */
	public static final int JOIN_FREQ = 1;
	
	public Suggest(IndexId iid) throws IOException{
		SearcherCache cache = SearcherCache.getInstance();
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		this.iid = iid;
		this.words = cache.getLocalSearcher(iid.getSpellWords());
		this.titles = cache.getLocalSearcher(iid.getSpellTitles());
		this.titlesReader = titles.getIndexReader();
		this.minHitsWords = global.getIntDBParam(iid.getDBname(),"spell_words","minHits",20);
		this.minHitsTitles = global.getIntDBParam(iid.getDBname(),"spell_titles","minHits",20);
		
		synchronized(stopWordsIndexes){
			if(!stopWordsIndexes.containsKey(titles)){
				Set<String> s = Collections.synchronizedSet(new HashSet<String>());
				stopWordsIndexes.put(titles,s);
				TermDocs d = titles.getIndexReader().termDocs(new Term("metadata_key","stopWords"));
				if(d.next()){
					String val = titles.doc(d.doc()).get("metadata_value");
					for(String sw : val.split(" ")){
						s.add(sw);
					}
				}				
			}
			this.stopWords = new HashSet<String>();
			this.stopWords.addAll(stopWordsIndexes.get(titles)); 			
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
			return "dist:"+dist+"-freq:"+freq+"-sub:"+substitutes+"-pres:"+preserves;
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
	public SuggestQuery suggest(String searchterm, WikiQueryParser parser, NamespaceFilter nsf, SearchResults res) throws IOException{
		ArrayList<Token> tokens = parser.tokenizeBareText(searchterm);
		int numHits = res.getNumHits();
		
		//if(numHits >= minHitsTitles)
		//return null;
		
		// collect words in titles, these shouldn't be spell-checked
		HashSet<String> correctWords = new HashSet<String>();
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,false);
		try {
			for(ResultSet r : res.getResults()){
				Token t = null;
				TokenStream ts = analyzer.tokenStream("title",r.title);				
				while( (t = ts.next()) != null ){
					correctWords.add(t.termText());
				}				
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
		for(int i=0;i<tokens.size();i++){
			Token t = tokens.get(i);
			if(correctWords.contains(t.termText())){
				Change c = new Change(0,1,Change.Type.TITLE_WORD);
				c.preserves.put(i,t.termText());
				suggestions.add(c);
			}
		}

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
			
			String phrase = w+gap+w2;
			if(titlesReader.docFreq(new Term("phrase",phrase)) != 0){
				correctPhrases.add(i);
				correctPhrases.add(i2);
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
				sug = suggestWordsFromTitle(w,w,nsf,POOL/2,POOL/2);
			else
				sug = suggestWordsFromTitle(w,nsf,POOL);
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
				// detect common misspells
				if(sug.size() > 1){
					SuggestResult r1 = sug.get(0);
					SuggestResult r2 = sug.get(1);
					if(r1.dist == 1 && r2.dist == 0 && r1.frequency > 100 * r2.frequency){
						Change c = new Change(r1.dist,r1.frequency,Change.Type.WORD);
						c.substitutes.put(i,r1.word);
						suggestions.add(c);
					}
				}
			} else{
				wordSug.add(null);
				possibleStopWords.add(null);
			}
			// suggest split
			SuggestResult split = suggestSplitFromTitle(w,nsf,minFreq);
			if(split != null){
				Change sc = new Change(split.dist,split.frequency,Change.Type.SPLIT);
				sc.substitutes.put(i,split.word.replace("_"," "));
				suggestions.add(sc);
			}
			// suggest join
			if(i-1 >= 0 
					&& (wordSug.get(i-1)==null || wordSug.get(i-1).get(0).dist!=0)
					&& (wordSug.get(i)==null || wordSug.get(i).get(0).dist!=0)){
				SuggestResult join = suggestJoinFromTitle(tokens.get(i-1).termText(),w,nsf,minFreq);
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
			boolean good1 = sug1.get(0).getDist() == 0; // w1 is spellchecked right
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
				boolean good2 = sug2.get(0).getDist() == 0; // w2 is spellchecked right
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
						TermDocs td = titlesReader.termDocs(new Term("phrase",phrase));
						if(td.next()){
							int docid = td.doc();
							String f = titlesReader.document(docid).get("freq");
							freq = Integer.parseInt(f.substring(2,f.length()-1));
							String it = titlesReader.document(docid).get("intitle");
							if(it!=null && it.equals("1"))
								inTitle = true;

						}
						//log.info("Checking "+phrase);
						if(freq > 0){
							log.info("Found "+phrase+" at dist="+(s1.dist+s2.dist)+", freq="+freq+" inTitle="+inTitle);
							int dist = s1.dist + s2.dist + distOffset;
							boolean accept = true;
							Change c = new Change(dist,freq,Change.Type.PHRASE);
							if(s1.word.equals(w1))
								c.preserves.put(i,w1);
							else if(!good1 || inTitle)					
								c.substitutes.put(i,s1.word);
							else
								accept = false;
							if(s2.word.equals(w2))
								c.preserves.put(i2,w2);
							else if(!good2 || inTitle)					
								c.substitutes.put(i2,s2.word);
							else
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
			} while(maybeStopWord);
		}
		// try to construct a valid title by spell-checking all words
		if(suggestionsTitle.size() > 0){
			Object[] ret = calculateChanges(suggestionsTitle,searchterm.length()/2);
			ArrayList<Entry<Integer,String>> proposedTitle = (ArrayList<Entry<Integer, String>>) ret[0];
			boolean madeChanges = false;
			String title = searchterm;
			String formated = searchterm;
			for(Entry<Integer,String> e : proposedTitle){
				Token t = tokens.get(e.getKey());
				String nt = e.getValue();					
				if(!stemsToSame(t.termText(),nt,parser.getBuilder().getFilters())){
					formated = markSuggestion(formated,t,nt);
					title = applySuggestion(title,t,nt);
					madeChanges = true;
				}
				if(madeChanges){
					// check if some title exactly matches the spell-checked query
					if(titlesReader.docFreq(new Term("title",title.toLowerCase())) != 0){
						log.info("Found title match for "+title);
						return new SuggestQuery(tidy(title),tidy(formated));		
					}
				}
			}
		} else if(tokens.size() == 1 && wordSug.get(0)!=null
				&& wordSug.get(0).size() > 0 && !correctWords.contains(tokens.get(0).termText())){ 
			// only one token, try different spell-checks for title
			ArrayList<SuggestResult> sg = wordSug.get(0);
			Collections.sort(sg,new SuggestResult.ComparatorNoCommonMisspell());
			Token t = tokens.get(0);
			int maxdist = sg.get(0).getDist();
			if(maxdist != 0){
				for(SuggestResult r : sg){
					if(r.getDist() > maxdist)
						break;
					String title = r.getWord();
					if(titlesReader.docFreq(new Term("title",title.toLowerCase())) != 0){
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
			Object[] ret = calculateChanges(suggestions,searchterm.length()/2);
			proposedChanges = (ArrayList<Entry<Integer, String>>) ret[0];
			ArrayList<Entry<Integer,String>> preservedWords = (ArrayList<Entry<Integer, String>>) ret[1];
			for(Entry<Integer,String> e : preservedWords)
				preserveTokens.add(e.getKey());			
			for(Entry<Integer,String> e : proposedChanges)
				preserveTokens.add(e.getKey());
		}

		// last resort: go with individual word suggestions
		HashMap<Integer,String> wordChanges = new HashMap<Integer,String>();
		for(int i=0;i<tokens.size();i++){				
			if(preserveTokens.contains(i))
				continue;
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
				if(!stemsToSame(t.termText(),nt,parser.getBuilder().getFilters())){
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
	
	protected boolean addPhraseSuggestion(ArrayList<Token> tokens, int i1, int i2, ArrayList<Change> suggestions, NamespaceFilter nsf, int minFreq) {
		Token t1 = tokens.get(i1);
		Token t2 = tokens.get(i2);
		if(t2.type().equals(t1.type())){
			String word1 = t1.termText();
			String word2 = t2.termText();
			if(stopWords.contains(word1) || stopWords.contains(word2))
				return false;
			log.info("spell-check phrase \""+word1+" "+word2+"\"");
			// phrase
			ArrayList<SuggestResult> r = suggestPhraseFromTitle(word1,word2,1,nsf,minFreq);
			if(r.size() > 0){
				SuggestResult res = r.get(0);
				String[] ph = res.word.split("_");						
				if(ph.length == 2){
					// figure out which words need to be changed
					Change sc = new Change(res.dist,res.frequency,Change.Type.PHRASE);
					if(!ph[0].equals(word1))
						sc.substitutes.put(i1,ph[0]);
					else
						sc.preserves.put(i1,ph[0]);
					if(!ph[1].equals(word2))
						sc.substitutes.put(i2,ph[1]);
					else
						sc.preserves.put(i2,ph[1]);
					suggestions.add(sc);
				} else
					log.error("Unexpected phrase in suggest result "+res);
			}
			// join
			SuggestResult join = suggestJoinFromTitle(word1,word2,nsf,minFreq);
			if(join != null){
				Change sc = new Change(join.dist,join.frequency,Change.Type.JOIN);
				sc.substitutes.put(i1,""); 
				sc.substitutes.put(i2,join.word);
				suggestions.add(sc);
			}
			return true;
		}
		return false;		
	}

	protected String markSuggestion(String formated, Token t, String newWord){
		return formated.substring(0,t.startOffset()) 
		+ "<i>" + newWord	+ "</i>"
		+ formated.substring(t.endOffset());
	}
	
	protected String applySuggestion(String searchterm, Token t, String newWord){
		return searchterm.substring(0,t.startOffset())  
		+ newWord
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
	
	/** Suggest some words from the words index */
	public ArrayList<SuggestResult> suggestWords(String word, int num){
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		addQuery(bq,"metaphone1",metric.meta1,2);
		addQuery(bq,"metaphone2",metric.meta2,2);
		bq.add(makeWordQuery(word,""),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = words.search(bq,null,POOL);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = words.doc(sc.doc);
				String w = d.get("word");
				SuggestResult r = new SuggestResult(w,
						Integer.parseInt(d.get("freq")),
						metric);
				if(word.equals(r.word)){
					minfreq = r.frequency;
				}				
				if(acceptWord(r,metric))				
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
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
	
	public ArrayList<SuggestResult> suggestWordsFromTitle(String word, NamespaceFilter nsf, int num){
		ArrayList<SuggestResult> r1 = suggestWordsFromTitle(word,word,nsf,POOL,POOL);
		if(r1 != null && r1.size() > 0){
			if(r1.get(0).dist == 0)
				return r1;
			ArrayList<SuggestResult> r2 = suggestWordsFromTitle(word,r1.get(0).word,nsf,POOL/2,POOL/2);
			if(r2 != null && r2.size() > 0){
				HashSet<SuggestResult> hr = new HashSet<SuggestResult>();
				hr.addAll(r1); hr.addAll(r2);
				ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
				res.addAll(hr);
				Collections.sort(res,new SuggestResult.Comparator());
				return res;
			}
			return r1;
		} else
			return r1;
	}
	
	public ArrayList<SuggestResult> suggestWordsFromTitle(String word, String searchword, NamespaceFilter nsf, int num, int pool_size){
		Metric metric = new Metric(word);
		BooleanQuery bq = new BooleanQuery();		
		bq.add(makeWordQuery(searchword,"word"),BooleanClause.Occur.SHOULD);
		
		try {
			TopDocs docs = titles.search(bq,new NamespaceFilterWrapper(nsf),pool_size);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = -1;
			// fetch results, calculate various edit distances
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = titles.doc(sc.doc);
				String w = d.get("word");
				String f = d.get("freq");
				String meta1 = d.get("meta1");
				String meta2 = d.get("meta2");
				SuggestResult r = new SuggestResult(w, // new NamespaceFreq(d.get("freq")).getFrequency(nsf),
						Integer.parseInt(f.substring(2,f.length()-1)),
						metric, meta1, meta2);
				if(word.equals(r.word)){
					minfreq = r.frequency;
				}				
				if(acceptWord(r,metric))				
					res.add(r);
			}
			// filter out 
			/*if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
			} */
			// suggest simple inversion since it probably won't be found
			/* if(word.length() == 2){
				String inv = NgramIndexer.reverse(word);
				TermDocs td = titlesReader.termDocs(new Term("word",inv));
				int freq = 0;
				if(td.next()){
					freq = new NamespaceFreq(titlesReader.document(td.doc()).get("freq")).getFrequency(nsf);
					SuggestResult r = new SuggestResult(inv,
							freq,
							metric);
					//if(acceptWord(r,metric))				
					res.add(r);
				}
			} */
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
	public SuggestResult suggestSplitFromTitle(String word, NamespaceFilter nsf, int minFreq){
		int freq = 0;
		Hits hits;
		ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
		try {
			// find frequency
			hits = titles.search(new TermQuery(new Term("word",word)),new NamespaceFilterWrapper(nsf));
			if(hits.length() == 1)
				freq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);

			// try different splits 
			for(int i=1;i<word.length()-1;i++){
				String phrase = word.substring(0,i) + "_" + word.substring(i);
				hits = titles.search(new TermQuery(new Term("phrase",phrase)),new NamespaceFilterWrapper(nsf));
				if(hits.length() > 0){
					int pfreq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);
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
	public SuggestResult suggestJoinFromTitle(String word1, String word2, NamespaceFilter nsf, int minFreq){
		try {
			Hits hits = titles.search(new TermQuery(new Term("word",word1+word2)),new NamespaceFilterWrapper(nsf));
			if(hits.length() > 0){
				int freq = new NamespaceFreq(hits.doc(0).get("freq")).getFrequency(nsf);
				if(freq >= minFreq)
					return new SuggestResult(word1+word2,freq,1);
			}
		} catch (IOException e) {
			log.warn("I/O error while suggesting join on "+iid+" : "+e.getMessage());
			e.printStackTrace();
		}
		return null;
	}
	
	/** Suggest phrase from a titles index, if the phrase is correct will return it as first result */
	public ArrayList<SuggestResult> suggestPhraseFromTitle(String word1, String word2, int num, NamespaceFilter nsf, int minFreq){
		String phrase = word1+"_"+word2;		
		Query q = makeWordQuery(phrase,"phrase");
		Metric m1 = new Metric(word1);
		Metric m2 = new Metric(word2);
		Metric metric = new Metric(phrase);
		try {
			TopDocs docs = titles.search(q,new NamespaceFilterWrapper(nsf),POOL/2);			
			ArrayList<SuggestResult> res = new ArrayList<SuggestResult>();
			int minfreq = (minFreq == 0)? -1 : minFreq;
			// fetch results
			for(ScoreDoc sc : docs.scoreDocs){		
				Document d = titles.doc(sc.doc);
				String p = d.get("phrase");
				int freq = new NamespaceFreq(d.get("freq")).getFrequency(nsf); 
				SuggestResult r = new SuggestResult(p,freq,metric);
				if(phrase.equals(r.word) && minfreq == -1){
					minfreq = r.frequency;
				}
				String[] words = p.split("_");
				SuggestResult r1 = new SuggestResult(words[0],freq,m1);
				SuggestResult r2 = new SuggestResult(words[1],freq,m2);
				if(r.dist < phrase.length() / 2 && acceptWord(r1,m1) && acceptWord(r2,m2)) // don't add if it will change more than half of the phrase
					res.add(r);
			}
			// filter out 
			if(minfreq != -1){
				for(int i=0;i<res.size();){
					if(res.get(i).frequency < minfreq ){
						res.remove(i);
					} else
						i++;
				}
			}
			// sort
			Collections.sort(res,new SuggestResult.Comparator());
			// get first num results
			while(res.size() > num){
				res.remove(res.size()-1);
			}
			return res;
		} catch (IOException e) {
			log.error("Cannot get suggestions for "+phrase+" at "+iid+" : "+e.getMessage());
			e.printStackTrace();
			return new ArrayList<SuggestResult>();
		}		
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
			log.error("Cannot stemm words "+word1+", "+word2+" : "+e.getMessage());			
		}
		return false;
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
