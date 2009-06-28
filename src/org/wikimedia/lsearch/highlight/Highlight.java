package org.wikimedia.lsearch.highlight;

import java.io.IOException;
import java.io.Serializable;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.BitSet;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.analyzers.ExtToken.Type;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.SearcherCache;
import org.wikimedia.lsearch.util.Utf8Set;

public class Highlight {
	protected static SearcherCache cache = null;
	static Logger log = Logger.getLogger(Highlight.class);
	
	public static final int SLOP = WikiQueryParser.MAINPHRASE_SLOP;
	/** maximal length of text that surrounds highlighted words */ 
	public static final int MAX_CONTEXT = 70;
	/** variability in all snippets length */
	public static final int TOLERANCE = 15;
	/** too small snippets that will be extended */
	public static final int SHORT_SNIPPET = 50;
	/** coefficient for words that match and are close one to the other */
	public static final double PROXIMITY = 2;
	
	public static final double FIRST_SENTENCE_BOOST = 10;
	
	public static final double PHRASE_BOOST = 1;
	
	/** boost (preference) factors for varius parts of the text */
	public static final HashMap<Position,Double> BOOST = new HashMap<Position,Double>(); 
	static {
		BOOST.put(Position.FIRST_SECTION,5.0);
		BOOST.put(Position.HEADING,2.0);
		BOOST.put(Position.NORMAL,1.0);
		BOOST.put(Position.BULLETINS,0.5);
		BOOST.put(Position.TABLE,0.5);
		BOOST.put(Position.TEMPLATE,0.1);		
		BOOST.put(Position.IMAGE_CAT_IW,0.01);
		BOOST.put(Position.EXT_LINK,0.05);
		BOOST.put(Position.REFERENCE,0.05);
	}	
	
	/** Results from higlighting multiple articles */
	public static class ResultSet implements Serializable {
		public HashMap<String,HighlightResult> highlighted;
		/** phrases matched in document, word1_word2 (i.e. separated by underscore) */
		public HashSet<String> phrases;
		/** words found with some other query word in a sentence */
		public HashSet<String> foundInContext;
		/** If all words are found within one article title */
		public boolean foundAllInTitle;
		/** Rank of the first hit, used as title-suggestion threshold */
		public int firstHitRank = 0;
		/** Words found in titles */
		public HashSet<String> foundInTitles;
		/** If we found all words in title or redirect */
		public boolean foundAllInAltTitle;
		public ResultSet(HashMap<String, HighlightResult> highlighted, HashSet<String> phrases, HashSet<String> foundInContext, boolean foundAllInTitle, int firstHitRank, HashSet<String> foundInTitles, boolean foundAllInAltTitle) {
			this.highlighted = highlighted;
			this.phrases = phrases;
			this.foundInContext = foundInContext;
			this.foundAllInTitle = foundAllInTitle;
			this.firstHitRank = firstHitRank;
			this.foundInTitles = foundInTitles;
			this.foundAllInAltTitle = foundAllInAltTitle;
		}
	}
	/**
	 * 
	 * @param hits - keys of articles that need to be higlighted
	 * @param iid - highlight index
	 * @param terms - terms to highlight
	 * @param df - their document frequencies
	 * @param words - in order words (from main phrase)
	 * @param exactCase - if these are results from exactCase search
	 * @throws IOException 
	 * @returns resultset
	 */
	@SuppressWarnings("unchecked")
	public static ResultSet highlight(ArrayList<String> hits, IndexId iid, Term[] terms, int df[], int maxDoc, 
			ArrayList<String> words, HashSet<String> stopWords, boolean exactCase, IndexReader reader, 
			boolean sortByPhrases, boolean alwaysIncludeFirstLine) throws IOException{
		if(cache == null)
			cache = SearcherCache.getInstance();
		
		if(stopWords == null)
			stopWords = new HashSet<String>();
		
		HashSet<String> phrases = new HashSet<String>();
		HashSet<String> inContext = new HashSet<String>();
		boolean foundAllInTitle = false, foundAllInAltTitle = false;
		int firstHitRank = 0;
		HashSet<String> inTitle = new HashSet<String>();
		boolean isCJK = iid.getDB().isCJK();
		
		//System.out.println("Terms: "+Arrays.toString(terms));
		//System.out.println("Words: "+words);
		
		// terms weighted with idf
		HashMap<String,Double> weightTerm = new HashMap<String,Double>();
		for(int i=0;i<terms.length;i++){
			Term t = terms[i];
			double idf = idf(df[i],maxDoc);
			weightTerm.put(t.text(),idf);
		}
		// position within main phrase
		HashMap<String,Integer> wordIndex = new HashMap<String,Integer>();
		for(int i=0;i<words.size();i++){
			String w = words.get(i);
			if(!wordIndex.containsKey(w))
				wordIndex.put(w,i);
		}
		
		// process requested documents
		if(reader == null) // get new reader if none is supplied
			reader = cache.getLocalSearcher(iid.getHighlight()).getIndexReader();
		HashMap<String,HighlightResult> res = new HashMap<String,HighlightResult>();
		Set<String> allTerms = weightTerm.keySet();
		FieldNameFactory fields = new FieldNameFactory(exactCase);
		for(int hi=0;hi<hits.size();hi++){
			String key = hits.get(hi);
			Object[] ret = null;
			try{
				ret = getTokens(reader,key,allTerms,fields);			
			} catch(Exception e){
				log.error("Error geting tokens: "+e.getMessage(),e);
				e.printStackTrace();
			}
			if(ret == null)
				continue;
			
			ArrayList<ExtToken> tokens = (ArrayList<ExtToken>) ret[0];
			Alttitles alttitles = (Alttitles) ret[1];
			String date = (String) ret[2];
			Long size = (Long) ret[3];
			preprocessTemplates(tokens);
			
			if(hi == 0)  
				firstHitRank = alttitles.getTitle().getRank();
			
			HashMap<String,Double> notInTitle = getTermsNotInTitle(weightTerm,alttitles,wordIndex);
			ArrayList<RawSnippet> textSnippets = getBestTextSnippets(tokens, weightTerm, words, wordIndex, 2, false, stopWords, true, phrases, inContext, sortByPhrases, alwaysIncludeFirstLine, isCJK );
			ArrayList<RawSnippet> titleSnippets = getBestTextSnippets(alttitles.getTitle().getTokens(),weightTerm,words,wordIndex,1,true,stopWords,false,phrases,inContext,false,false,isCJK);
			RawSnippet redirectSnippet = null;
			// don't show redirect if we matched whole title
			if(! (titleSnippets.size()>0 && titleSnippets.get(0).countPositions()==titleSnippets.get(0).noAliasLength())){
				redirectSnippet = getBestAltTitle(alttitles.getRedirects(),weightTerm,notInTitle,stopWords,words,wordIndex,0,phrases,inContext,isCJK);
			}
			RawSnippet sectionSnippet = null;
			if(redirectSnippet == null){
				// remove stop words for section higlighting
				for(String s : stopWords){
					if(notInTitle.containsKey(s))
						notInTitle.remove(s);
				}
				sectionSnippet = getBestAltTitle(alttitles.getSections(),weightTerm,notInTitle,stopWords,words,wordIndex,0,phrases,inContext,isCJK);
			}
			
			HighlightResult hr = new HighlightResult();
			ArrayList<RawSnippet> raw = new ArrayList<RawSnippet>();
			if(textSnippets.size() == 1){
				RawSnippet rs1 = textSnippets.get(0); 
				Snippet s1 = rs1.makeSnippet(MAX_CONTEXT*2);
				raw.add(rs1);
				hr.addTextSnippet(s1);				
				boolean addSection = true, added = true;
				while(added && more(hr.textLength())){
					// add more snippets if there is still space					
					added = extendSnippet(raw,hr,raw.size()-1,tokens,addSection,stopWords,isCJK);
					addSection = false;					
				}				
			} else if(textSnippets.size() >= 2){
				RawSnippet rs1 = textSnippets.get(0);
				RawSnippet rs2 = textSnippets.get(1);
				Snippet s1 = null, s2 = null;
				if(rs1.cur.isFirstSentence)
					s1 = rs1.makeSnippet((int)(MAX_CONTEXT*1.2));
				else
					s1 = rs1.makeSnippet(MAX_CONTEXT);
				s2 = rs2.makeSnippet(diff(s1.length()));
				raw.add(rs1);
				raw.add(rs2);
				hr.addTextSnippet(s1);
				hr.addTextSnippet(s2);
				if(s1.isShowsEnd() && rs1.next == rs2.cur)
					setSuffix(s1,rs1); // sequence of found snippets
				if(more(hr.textLength())){
					// first pass of snippet extension, extend shortest first
					if(s1.length() < s2.length()){
						extendSnippet(raw,hr,0,tokens,true,stopWords,isCJK);
						if(more(hr.textLength()))
							extendSnippet(raw,hr,raw.size()-1,tokens,true,stopWords,isCJK);
					} else {
						extendSnippet(raw,hr,1,tokens,true,stopWords,isCJK);
						if(more(hr.textLength()))
							extendSnippet(raw,hr,0,tokens,true,stopWords,isCJK);
					}
				}
				boolean added = true;
				while(added && more(hr.textLength())){
					// extend tokens one by one
					added = false;
					for(int i=0;i<hr.getText().size() && more(hr.textLength());i++){
						boolean addedNow = false;
						if(hr.getText().get(i).isExtendable()){
							addedNow = extendSnippet(raw,hr,i,tokens,false,stopWords,isCJK);
							if(addedNow)
								i++;
						}
						added = added || addedNow;
					}
				}
			}
			
			if(titleSnippets.size() > 0){
				hr.setTitle(titleSnippets.get(0).makeSnippet(256,true));		
				if(titleSnippets.get(0).found.containsAll(words))
					foundAllInTitle = true;
				inTitle.addAll(titleSnippets.get(0).found);
			}
			
			if(redirectSnippet != null){
				hr.setRedirect(redirectSnippet.makeSnippet(MAX_CONTEXT,true));
				if(redirectSnippet.found.containsAll(words))
					foundAllInAltTitle = true;
				inTitle.addAll(redirectSnippet.found);
			}
			
			if(sectionSnippet != null){
				hr.setSection(sectionSnippet.makeSnippet(MAX_CONTEXT,true));
			}
			
			// date
			hr.setDate(date);
			
			// word count
			hr.setWordCount(textTokenLength(tokens));
			
			// byte count
			hr.setSize(size);
			
			res.put(key,hr);
			
		}
		return new ResultSet(res,phrases,inContext,foundAllInTitle,firstHitRank,inTitle,foundAllInAltTitle);
	}	
	
	/** Number of tokens excluding aliases and glue stuff */
	private static int textTokenLength(ArrayList<ExtToken> tokens) {
		int len = 0;
		for(ExtToken t : tokens){
			if(t.getType() == Type.TEXT && t.getPositionIncrement()!=0)
				len++;			
		}
		return len;
	}

	/**
	 * Since we are not expanding templates, use a heuristic to accept
	 * some templates as in-text stuff, e.g. language templates like
	 * {{lang|la|Ipsum Factum}}
	 * 
	 * FIXME at some point
	 * @param tokens
	 */
	private static void preprocessTemplates(ArrayList<ExtToken> tokens) {
		Position lastPos=null;
		for(int i=0;i<tokens.size();i++){
			ExtToken t = tokens.get(i);
			Position pos = t.getPosition();
			if(pos == Position.TEMPLATE && (lastPos == Position.FIRST_SECTION || lastPos == Position.NORMAL)){
				int[] ret = getTemplateLength(tokens,i);
				int len = ret[0];
				int lastInx = ret[1];
				if(len < MAX_CONTEXT/2){
					// upgrade position from TEMPLATE to FIRST_SECTION or NORMAL
					for(int j=i;j<lastInx;j++)
						tokens.get(j).setPosition(lastPos);
				}
				i = lastInx-1;
				pos = tokens.get(i).getPosition();
			}
			lastPos = pos;
		}
	}
	/** length of text in template, returns {len,lastIndex} */
	private static int[] getTemplateLength(ArrayList<ExtToken> tokens, int start){
		int len =0;
		int i = start;
		for(;i<tokens.size();i++){
			ExtToken t = tokens.get(i);
			if(t.getPosition() == Position.TEMPLATE){
				len += t.getTextLength(); 
			} else
				break;
		}
		return new int[] {len,i};
	}

	/** suffix between continous snippets */
	private static void setSuffix(Snippet s, RawSnippet rs) {
		String text = s.getText();
		int len = text.length();
		if(len < 2)
			s.setSuffix(" ");
		else if(rs.pos == Position.HEADING)
			s.setSuffix(": ");
		else if(s.isShowsEnd() && text.indexOf('.')==-1 && text.indexOf('|')==-1 
				&& Character.isLetter(text.charAt(text.length()-1)))
			s.setSuffix(". ");
		else
			s.setSuffix(" ");
	}
	/** if we should fetch more snippets */
	private static boolean more(int totalLen){
		return totalLen < 2*MAX_CONTEXT - TOLERANCE;
	}
	/** length of new snippet */
	private static int diff(int totalLen){
		return 2*MAX_CONTEXT - totalLen;
	}
	
	private static boolean extendSnippet(ArrayList<RawSnippet> raw, HighlightResult hr, int index, 
			ArrayList<ExtToken> tokens, boolean addSection, HashSet<String> stopWords, boolean isCJK){
		Snippet curS = hr.getText().get(index);
		RawSnippet curRs = raw.get(index);
		int len = hr.textLength();
		boolean added = false;
		// add section
		if(addSection && more(len)){
			RawSnippet rs = sectionSnippet(curRs,curS,tokens,stopWords,isCJK);
			if(rs != null && !raw.contains(rs)){
				Snippet s = rs.makeSnippet(diff(len));
				setSuffix(s,rs);
				hr.insertTextSnippet(s,index);
				raw.add(index,rs);
				len += s.length();
				curS.setExtendable(false);
				added = true;
				index++;
			}
		}
		// see if this snippet can be resized
		if(!curS.isShowsAll() && more(len)){
			Snippet s = curRs.makeSnippet(curS.length()+diff(len));
			hr.replaceTextSnippet(s,index);
			len = hr.textLength();
		}
		// add next snippet
		if(more(len)){										
			RawSnippet rs = nextSnippet(curRs,curS,tokens,stopWords,isCJK);
			if(rs != null && !raw.contains(rs)){
				Snippet s = rs.makeSnippet(diff(len));
				setSuffix(curS,curRs);
				hr.insertTextSnippet(s,index+1);
				raw.add(index+1,rs);
				len += s.length();
				curS.setExtendable(false);
				added = true;
			}
		}
		return added;
	}
	
	protected static RawSnippet nextSnippet(RawSnippet rs, Snippet s, ArrayList<ExtToken> tokens, HashSet<String> stopWords, boolean isCJK){
		if(rs.next == null)
			return null;
		return new RawSnippet(tokens,rs.next,rs.highlight,new HashSet<String>(),stopWords,isCJK);
	}
	
	protected static RawSnippet sectionSnippet(RawSnippet rs, Snippet s, ArrayList<ExtToken> tokens, HashSet<String> stopWords, boolean isCJK){
		if(rs.section == null)
			return null;
		if(s.length() < SHORT_SNIPPET)
			return new RawSnippet(tokens,rs.section,rs.highlight,new HashSet<String>(),stopWords,isCJK);
		return null;
	}
	
	/** Implemented as <code>log(numDocs/(docFreq+1)) + 1</code>. */
	protected static double idf(int docFreq, int numDocs) {
		return Math.log(numDocs/(double)(docFreq+1)) + 1.0;
	}
	
	@SuppressWarnings("unchecked")
	protected static HashMap<String,Double> getTermsNotInTitle(HashMap<String,Double> weightTerm, Alttitles alttitles, HashMap<String,Integer> wordIndex){
		Alttitles.Info info = alttitles.getTitle();
		ArrayList<ExtToken> tokens = info.getTokens();
		HashMap<String,Double> ret = new HashMap<String, Double>();
		for(Entry<String,Double> e : weightTerm.entrySet()){
			if(wordIndex.containsKey(e.getKey()))
					ret.put(e.getKey(),e.getValue());
		}
		// delete all terms from title
		for(ExtToken t : tokens){
			if(ret.containsKey(t.termText()))
				ret.remove(t.termText());
		}
		return ret;
		
	}
	
	/** Alttitle and sections highlighting */	
	protected static RawSnippet getBestAltTitle(ArrayList<Alttitles.Info> altInfos, HashMap<String,Double> weightTerm, 
			HashMap<String,Double> notInTitle, HashSet<String> stopWords, ArrayList<String> words, HashMap<String,Integer> wordIndex, 
			int minAdditional, HashSet<String> phrases, HashSet<String> inContext, boolean isCJK){
		ArrayList<RawSnippet> res = new ArrayList<RawSnippet>();
		for(Alttitles.Info ainf : altInfos){			
			double matched = 0, additionalScore = 0;
			int additional = 0;
			ArrayList<ExtToken> tokens = ainf.getTokens();
			HashSet<Integer> matchedPositions = new HashSet<Integer>();
			int count = 0;
			int length = textTokenLength(tokens);
			for(int i=0;i<tokens.size();i++){
				ExtToken t = tokens.get(i);
				if(t.getType() != Type.TEXT)
					continue;
				if(t.getPositionIncrement() != 0)
					count++;
				
				if(weightTerm.containsKey(t.termText())){
					matched += weightTerm.get(t.termText());
					matchedPositions.add(count);
				} 
				
				if(notInTitle.containsKey(t.termText()) && !stopWords.contains(t.termText())){
					additional++;
					additionalScore += notInTitle.get(t.termText());
				}
			}
			if(length == matchedPositions.size() || additional > minAdditional || (additional != 0 && additional == notInTitle.size())){
				ArrayList<RawSnippet> snippets = getBestTextSnippets(tokens, weightTerm, words, wordIndex, 1, false, stopWords, false, phrases, inContext, false, false, isCJK);
				if(snippets.size() > 0){
					RawSnippet snippet = snippets.get(0);
					snippet.setAlttitle(ainf);
					snippet.setScore(snippet.getScore()+2*additional);
					res.add(snippet);
				}
			}
		}
		if(res.size() > 0){
			if(res.size() == 1){
				return res.get(0);
			} else{
				// get snippet with best score
				Collections.sort(res,  new Comparator<RawSnippet>() {
					public int compare(RawSnippet o1, RawSnippet o2) {
						// most terms found
						int f = o2.found.size() - o1.found.size();
						if(f != 0)
							return f;
						// in shortest overall text
						int l = o1.textLength - o2.textLength;
						if(l != 0)
							return l;
						// with best rank
						int r = o2.alttitle.getRank() - o1.alttitle.getRank();
						if(r != 0)
							return r;
						// and max score
						double d = o2.score - o1.score;
						if(d > 0)
							return 1;
						else if(d == 0)
							return 0;
						else return -1;
					}});
				return res.get(0);
			}			
		}
		return null;
	}
	
	/** Text highlighting */
	  
	public static class FragmentScore {
		int start = 0;
		int end = 0;
		double score = 0;
		// best match in this fragment
		int bestStart = -1;
		int bestEnd = -1;
		double bestScore = 0;
		int bestCount = 0;
		int sequenceNum = 0;
		
		FragmentScore next = null; // next in text
		Position pos = null; // position of this fs
		FragmentScore section = null; // current section for this fragment
		boolean isFirstSentence = false;
		
		HashSet<String> found = null; // terms found in this fragment
		
		FragmentScore(int start, int sequenceNum){
			this.start = start;
			this.sequenceNum = sequenceNum;
		}
		
		public String toString(){
			return "start="+start+", end="+end+", score="+score+", bestStart="+bestStart+", bestEnd="+bestEnd+", found="+found;
		}
	}
	
	/** Highlight text */
	protected static ArrayList<RawSnippet> getBestTextSnippets(ArrayList<ExtToken> tokens, HashMap<String, Double> weightTerms, 
			ArrayList<String> words, HashMap<String,Integer> wordIndex, int maxSnippets, boolean ignoreBreaks, HashSet<String> stopWords, 
			boolean showFirstIfNone, HashSet<String> phrases, HashSet<String> foundInContext, 
			final boolean sortByPhrases, final boolean alwaysIncludeFirstLine, final boolean isCJK) {
		
		// pieces of text to ge highlighted
		ArrayList<FragmentScore> fragments = new ArrayList<FragmentScore>();
		//System.out.println("TOKENS: "+tokens);
		FragmentScore fs = null, section=null;
		ExtToken last = null;
		// next three are for in-order matched phrases		
		ExtToken lastText = null;
		double phraseScore = 0;
		int phraseStart = -1;
		int phraseCount = 0;
		// number in sequence of sentences
		int sequence = 0;
		int lastIndex = -1;
		double lastWeight = 0;
		// indicator for first sentence
		boolean seenFirstSentence = false;
		// if first sentence has all the terms
		boolean foundAllInFirst = false;
		FragmentScore firstFragment = null;
		// index in words of last in phrase
		Integer lastInPhraseInx = null;
		// length of text since first sentence
		int beginLen = 0;
		HashSet<String> availableWeight = new HashSet<String>();
		availableWeight.addAll(weightTerms.keySet());
		HashSet<String> wordsInSentence = new HashSet<String>();
		for(int i=0;i<=tokens.size();i++){
			ExtToken t = null;
			if(i < tokens.size())
				t = tokens.get(i);
			if(last == null){
				fs = new FragmentScore(i, sequence++);
			} else if(t==null || positionChange(t,last) || (!ignoreBreaks && t.getType() == Type.SENTENCE_BREAK)){
				Position pos = last.getPosition();
				// finalize fragment
				if(phraseScore != 0 && phraseStart != -1){
					addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,i,phraseCount);
					phraseScore = 0;
					phraseStart = -1;
					phraseCount = 0;
				}
				if(t != null && !ignoreBreaks && t.getType() == Type.SENTENCE_BREAK)
					fs.end = i + 1;
				else 
					fs.end = i;
				fs.score *= BOOST.get(pos);
				fragments.add(fs);
				// work out words that are close to some other in a sentence (for spellcheck)
				if(wordsInSentence.size() > 1)
					foundInContext.addAll(wordsInSentence);
				wordsInSentence.clear();
					
				if(!ignoreBreaks && pos == Position.FIRST_SECTION && !seenFirstSentence){
					// boost for first sentence
					fs.score *= FIRST_SENTENCE_BOOST;
					fs.isFirstSentence = true;
					seenFirstSentence = true;
					firstFragment = fs;
					if(fs.found != null && fs.found.size() == weightTerms.size())
						foundAllInFirst = true;
				}
				fs.section = section;
				fs.pos = pos;
				if(pos == Position.HEADING){
					fs.section = null; // don't show previous section for section headers
					section = fs; // new section
				}
				normalizeScore(fs);
				if(foundAllInFirst && beginLen > 2*MAX_CONTEXT && firstFragment!=null){
					// made enough snippets, return the first one					
					ArrayList<RawSnippet> res = new ArrayList<RawSnippet>();
					res.add(new RawSnippet(tokens,firstFragment,weightTerms.keySet(),firstFragment.found,stopWords,isCJK));
					return res;					
				}
				fs.next = new FragmentScore(fs.end, sequence++); // link into list			
				fs = fs.next;
			}
			if(t == null)
				break;
			if(foundAllInFirst)
				beginLen += t.getTextLength();

			Integer inx = null;
			ExtToken phraseToken = null;
			int phraseIndex = i;
			// fetch index within a phrase
			if(t.getPositionIncrement() != 0 && t.getType() == Type.TEXT){
				if(!t.isStub() && t.getType() == Type.TEXT)
					inx = getIndex(t,words,wordIndex,lastInPhraseInx);
				if(inx != null){
					wordsInSentence.add(t.termText());
					phraseIndex = i;					 
				} else{
					// try within aliases
					for(int j=i+1;j<tokens.size();j++){
						ExtToken alias = tokens.get(j);
						if(alias.getPositionIncrement() != 0)
							break;
						if(!alias.isStub()){
							inx = getIndex(alias,words,wordIndex,lastInPhraseInx);
							if(inx != null){
								wordsInSentence.add(alias.termText());
								phraseIndex = j;
								break;
							}
						}
					}
				}
				if(inx != null)
					phraseToken = tokens.get(phraseIndex);					
				
				
				// process phrases				
				Double phraseWeight = inx != null? weightTerms.get(phraseToken.termText()) : null;
				Integer lastInx = null;
				if(lastText != null && lastText.termText()!=null){
					lastInx = (lastInPhraseInx!=null)? lastInPhraseInx : getIndex(lastText,words,wordIndex,null);
				}
				
				if(phraseWeight != null){
					if(inx != null && lastInx == null && phraseStart == -1){
						// begin of phrase
						phraseScore = phraseWeight;
						phraseStart = phraseIndex;
						phraseCount = 1;						
					} else if(inx != null && lastInx == null && phraseStart != -1){
						// end of phrase, begin of new
						addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,phraseIndex,phraseCount);
						phraseScore = phraseWeight;
						phraseStart = phraseIndex;
						phraseCount = 1;
					} else if(inx != null && lastInx != null && phraseStart != -1){
						if(lastInx + 1 != inx){
							// end of last phrase, begin of new
							addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,phraseIndex,phraseCount);
							phraseScore = phraseWeight;
							phraseStart = phraseIndex;
							phraseCount = 1;
						} else{
							// continuation of phrase
							phraseScore += phraseWeight;
							phraseCount++;
							phrases.add(FastWikiTokenizerEngine.clearTrailing(lastText.termText())+"_"+FastWikiTokenizerEngine.clearTrailing(phraseToken.termText()));
						}
					}
				} else if(t.getType() == Type.TEXT && phraseStart != -1 && phraseScore != 0){
					// end of phrase, unrecognized text token 
					addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,phraseIndex,phraseCount);
					phraseScore = 0;
					phraseStart = -1;
					phraseCount = 0;
				}
				lastInPhraseInx = inx;
				lastIndex = phraseIndex;
			} 
						
			// proximity and other per-token scores
			Double weight = null;
			if(!t.isStub() && t.getType() == Type.TEXT && availableWeight.contains(t.termText()))
				weight = weightTerms.get(t.termText());
			if(weight != null){
				if(fs.found == null)
					fs.found = new HashSet<String>();
				fs.found.add(t.termText());				
				addToScore(fs,weight,i,i+1,1);
				addProximity(fs,weight,i,lastWeight,lastIndex);
				lastWeight = weight;
			}
			
			last = t;
			if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0)
				lastText = phraseToken!=null? phraseToken : t;
		}
		// flush phrase score stuff
		if(phraseScore != 0 && phraseStart != -1){
			addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,tokens.size(),phraseCount);
		}
		
		// beginning of fragment list
		FragmentScore fragmentsBeginning = null;
		if(fragments.size()>0)
			fragmentsBeginning = fragments.get(0);
		// find fragments with best score
		Collections.sort(fragments,  new Comparator<FragmentScore>() {
			public int compare(FragmentScore o1, FragmentScore o2) {
				if(alwaysIncludeFirstLine){
					if(o1.isFirstSentence)
						return -1;
					if(o2.isFirstSentence)
						return 1;
				}
				// sort via longest phrase found
				int c = o2.bestCount - o1.bestCount;
				if(sortByPhrases && c != 0)
					return c;
				// or if phrases are of same length, then total score
				double d = o2.score - o1.score;
				if(d > 0)
					return 1;
				else if(d == 0)
					return 0;
				else return -1;
			}});
		
		boolean allStopWords = false;
		if(stopWords!=null && stopWords.size()>0 && wordIndex.size()>0){
			allStopWords = true;
			for(String w : wordIndex.keySet()){
				if(!stopWords.contains(w)){
					allStopWords = false;
					break;
				}
			}
		}
		
		ArrayList<RawSnippet> res = new ArrayList<RawSnippet>();
		Set<String> wordHighlight = weightTerms.keySet();
		HashSet<String> termsFound = new HashSet<String>();
		ArrayList<FragmentScore> resNoNew = new ArrayList<FragmentScore>();
		for(FragmentScore f : fragments){
			if(f.score == 0 && !(alwaysIncludeFirstLine && f.isFirstSentence))
				break;
			// check if the fragment has new terms
			boolean hasNew = false;
			HashSet<String> newTerms = new HashSet<String>();
			if(f.found != null){
				for(String w : f.found){
					if(!termsFound.contains(w) && (!stopWords.contains(w) || allStopWords)){
						hasNew = true;
						newTerms.add(w);
					}
				}
			}
			if(hasNew || (alwaysIncludeFirstLine && f.isFirstSentence)){
				if(f.found != null)
					termsFound.addAll(f.found);
				adjustBest(f,tokens,weightTerms,words,wordIndex,newTerms); 
				RawSnippet s = new RawSnippet(tokens,f,wordHighlight,newTerms,stopWords,isCJK);
				res.add(s);
			} else if(resNoNew.size() < maxSnippets)
				resNoNew.add(f);
			if(res.size() >= maxSnippets)
				break;			
		}
		// if text doesn't match show some body text
		if(showFirstIfNone && res.size() == 0 && fragmentsBeginning != null){
			res.add(new RawSnippet(tokens,fragmentsBeginning,wordHighlight,wordHighlight,stopWords,isCJK));
		} 
		// always show snippet that is before in the text first 
		Collections.sort(res,  new Comparator<RawSnippet>() {
			public int compare(RawSnippet o1, RawSnippet o2) {
				return o1.sequenceNum - o2.sequenceNum;
			}});
		
		return res;
	}
	/** boost the phrase score */
	private static double boostPhrase(double baseScore, int phraseCount){
		return baseScore * Math.pow(2,phraseCount);
	}
	/** Get best guess of index of the word within a phrase */
	private static final Integer getIndex(ExtToken t, ArrayList<String> words, HashMap<String,Integer> wordIndex, Integer lastInPhraseInx){
		String w = FastWikiTokenizerEngine.clearTrailing(t.termText());
		// check if it's next in phrase
		if(lastInPhraseInx != null && lastInPhraseInx + 1 < words.size() && words.get(lastInPhraseInx+1).equals(w))
			return lastInPhraseInx + 1;
		// guess based on word -> position map (which might not be 1-1)
		return wordIndex.get(w);
	}

	/** Have we moved to new position ? */
	private static boolean positionChange(ExtToken current, ExtToken lastToken) {
		Position cur = current.getPosition();
		Position last = lastToken.getPosition();
		// return true on all changes, except FIRST_SECTION -> NORMAL
		return cur != last && !(cur == Position.NORMAL && last == Position.FIRST_SECTION);
	}

	private static void addProximity(FragmentScore fs, Double weight, int i, double lastWeight, int lastIndex) {
		if(lastIndex != -1 && i > lastIndex){
			fs.score += PROXIMITY*(lastWeight+weight)/(i-lastIndex);
		}		
	}

	private static void normalizeScore(FragmentScore fs) {
		// fs.score /= fs.end - fs.start;
	}

	/** Recalculate the best score for fragment, but requiring that the best phrase has some terms */
	private static void adjustBest(FragmentScore f, ArrayList<ExtToken> tokens, HashMap<String, Double> weightTerms, 
			ArrayList<String> words, HashMap<String, Integer> wordIndex, HashSet<String> requiredTerms) {
		f.bestScore = 0;
		
		double phraseScore=0;
		int phraseStart=-1;
		int requiredCount = 0;
		ExtToken lastText = null;
		Integer lastInPhraseInx = null;
		for(int i=f.start;i<f.end;i++){
			ExtToken t = tokens.get(i);
			
			Integer inx = null;
			ExtToken phraseToken = null;
			int phraseIndex = i;
			// fetch index within a phrase
			if(t.getPositionIncrement() != 0 && t.getType() == Type.TEXT){
				if(!t.isStub() && t.getType() == Type.TEXT)
					inx = getIndex(t,words,wordIndex,lastInPhraseInx);
				if(inx != null){
					phraseIndex = i;					 
				} else{
					// try within aliases
					for(int j=i+1;j<tokens.size();j++){
						ExtToken alias = tokens.get(j);
						if(alias.getPositionIncrement() != 0)
							break;
						if(!alias.isStub()){
							inx = getIndex(alias,words,wordIndex,lastInPhraseInx);
							if(inx != null){
								phraseIndex = j;
								break;
							}
						}
					}
				}
				if(inx != null)
					phraseToken = tokens.get(phraseIndex);	
				Double phraseWeight = inx != null? weightTerms.get(phraseToken.termText()) : null;
				Integer lastInx = null;
				if(lastText != null && lastText.termText()!=null){
					lastInx = (lastInPhraseInx!=null)? lastInPhraseInx : getIndex(lastText,words,wordIndex,null);
				}
				
				if(phraseWeight != null){
					if(inx != null && lastInx == null && phraseStart == -1){
						// begin of phrase
						phraseScore = phraseWeight;
						phraseStart = phraseIndex;
						requiredCount = 0;
						if(requiredTerms.contains(phraseToken.termText()))
							requiredCount++;
					} else if(inx != null && lastInx == null && phraseStart != -1){
						// end of phrase, begin of new
						updateBest(f,phraseScore,phraseStart,phraseIndex,requiredCount);
						phraseScore = phraseWeight;
						phraseStart = phraseIndex;
						requiredCount = 0;
						if(requiredTerms.contains(phraseToken.termText()))
							requiredCount++;
					} else if(inx != null && lastInx != null){
						if(lastInx + 1 != inx){
							// end of last phrase, begin of new							
							updateBest(f,phraseScore,phraseStart,phraseIndex,requiredCount);
							phraseScore = phraseWeight;
							phraseStart = phraseIndex;
							requiredCount = 0;
							if(requiredTerms.contains(phraseToken.termText()))
								requiredCount++;
						} else{
							// continuation of phrase
							if(requiredTerms.contains(phraseToken.termText()))
								 requiredCount++;
							 phraseScore += phraseWeight;
						}
					}
				} else if(t.getType() == Type.TEXT && phraseStart != -1 && phraseScore != 0){
					// end of phrase, unrecognized text token 
					updateBest(f,phraseScore,phraseStart,phraseIndex,requiredCount);
					phraseScore = 0;
					phraseStart = -1;
					requiredCount = 0;
				}
				lastInPhraseInx = inx;
			} 
				
			Double weight = weightTerms.get(t.termText());
			if(weight != null){
				// single word phrase
				if(requiredTerms.contains(t.termText()))
					updateBest(f,weight,i,i+1,1);
			}
			
			if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0)
				lastText = phraseToken!=null? phraseToken : t;
		}
		
	}

	private static void updateBest(FragmentScore fs, double score, int start, int end, int boost) {
		score *= boost;
		if(fs.bestScore < score){
			fs.bestScore = score;
			if(start == -1)
				throw new RuntimeException("Phrase start cannot be -1, score="+score);
			fs.bestStart = start;
			fs.bestEnd = end;
		}		
	}

	/** update best segment in the fragment */
	private static void addToScore(FragmentScore fs, double score, int start, int end, int count) {
		fs.score += score;
		if(count >= fs.bestCount && fs.bestScore < score){
			fs.bestScore = score;
			if(start == -1)
				throw new RuntimeException("Phrase start cannot be -1, score="+score);
			fs.bestStart = start;
			fs.bestEnd = end;
			fs.bestCount = count;
		}		
	}
	
	/** @return ArrayList<ExtToken> tokens, Altitles alttitles, String date, long size */
	protected static Object[] getTokens(IndexReader reader, String key, Set<String> termSet, FieldNameFactory fields) throws IOException{
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Utf8Set terms = new Utf8Set(termSet);
			HashMap<Integer,Position> posMap = new HashMap<Integer,Position>();
			for(Position p : Position.values())
				posMap.put(p.ordinal(),p);
			
			Document doc = reader.document(td.doc());
			ArrayList<ExtToken> tokens = ExtToken.deserialize(doc.getBinaryValue(fields.hl_text()),terms,posMap);
			Alttitles alttitles  = Alttitles.deserializeAltTitle(doc.getBinaryValue(fields.hl_alttitle()),terms,posMap);
			String date = doc.get("date");
			String sizeStr = doc.get("size");
			Long size = sizeStr != null? Long.parseLong(sizeStr) : 0;
			return new Object[] {tokens, alttitles, date, size};
		} else
			return null;
	}
}
