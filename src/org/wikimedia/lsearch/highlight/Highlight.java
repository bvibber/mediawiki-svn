package org.wikimedia.lsearch.highlight;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.document.Document;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.StopWords;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.analyzers.ExtToken.Type;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.SearcherCache;

public class Highlight {
	protected static SearcherCache cache = null;
	static Logger log = Logger.getLogger(Highlight.class);
	
	public static final int SLOP = WikiQueryParser.MAINPHRASE_SLOP;
	/** maximal length of text that surrounds highlighted words */ 
	public static final int MAX_CONTEXT = 70;
	/** variability in all snippets length */
	public static final int TOLERANCE = 10;
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
	/**
	 * 
	 * @param hits - keys of articles that need to be higlighted
	 * @param iid - highlight index
	 * @param terms - terms to highlight
	 * @param df - their document frequencies
	 * @param words - in order words (from main phrase)
	 * @param exactCase - if these are results from exactCase search
	 * @throws IOException 
	 * @returns map: key -> what to highlight
	 */
	@SuppressWarnings("unchecked")
	public static HashMap<String,HighlightResult> highlight(ArrayList<String> hits, IndexId iid, Term[] terms, int df[], int maxDoc, ArrayList<String> words, HashSet<String> stopWords, boolean exactCase) throws IOException{
		if(cache == null)
			cache = SearcherCache.getInstance();
		
		// System.out.println("Highlighting: "+Arrays.toString(terms));
		
		FieldNameFactory fields = new FieldNameFactory(exactCase);
		
		if(stopWords == null)
			stopWords = new HashSet<String>();
		
		// terms weighted with idf
		HashMap<String,Double> weightTerm = new HashMap<String,Double>();
		for(int i=0;i<terms.length;i++){
			Term t = terms[i];
			if(t.field().equals(fields.contents())){
				double idf = idf(df[i],maxDoc); 
				weightTerm.put(t.text(),idf);
			}
		}
		// position within main phrase
		HashMap<String,Integer> wordIndex = new HashMap<String,Integer>();
		for(int i=0;i<words.size();i++)
			wordIndex.put(words.get(i),i);
		
		// process requested documents
		IndexReader reader = cache.getLocalSearcher(iid.getHighlight()).getIndexReader();
		HashMap<String,HighlightResult> res = new HashMap<String,HighlightResult>();
		for(String key : hits){
			Object[] ret = null;
			try{
				ret = getTokens(reader,key);			
			} catch(Exception e){
				
			}
			if(ret == null)
				continue;
			
			ArrayList<ExtToken> tokens = (ArrayList<ExtToken>) ret[0];
			Alttitles alttitles = (Alttitles) ret[1];
			preprocessTemplates(tokens);
			
			HashMap<String,Double> notInTitle = getTermsNotInTitle(weightTerm,alttitles);
			ArrayList<RawSnippet> textSnippets = getBestTextSnippets(tokens, weightTerm, wordIndex, 2, false, stopWords);
			ArrayList<RawSnippet> titleSnippets = getBestTextSnippets(alttitles.getTitle().getTokens(),weightTerm,wordIndex,1,true,stopWords);
			RawSnippet redirectSnippets = getBestAltTitle(alttitles.getRedirects(),weightTerm,notInTitle,stopWords,wordIndex,1);
			RawSnippet sectionSnippets = null;
			if(redirectSnippets == null){
				// remove stop words for section higlighting
				for(String s : stopWords){
					if(notInTitle.containsKey(s))
						notInTitle.remove(s);
				}
				sectionSnippets = getBestAltTitle(alttitles.getSections(),weightTerm,notInTitle,stopWords,wordIndex,0);
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
					added = extendSnippet(raw,hr,raw.size()-1,tokens,addSection);
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
						extendSnippet(raw,hr,0,tokens,true);
						if(more(hr.textLength()))
							extendSnippet(raw,hr,raw.size()-1,tokens,true);
					} else {
						extendSnippet(raw,hr,1,tokens,true);
						if(more(hr.textLength()))
							extendSnippet(raw,hr,0,tokens,true);
					}
				}
				boolean added = true;
				while(added && more(hr.textLength())){
					// extend tokens one by one
					added = false;
					for(int i=0;i<hr.getText().size() && more(hr.textLength());i++){
						boolean addedNow = false;
						if(hr.getText().get(i).isExtendable()){
							addedNow = extendSnippet(raw,hr,i,tokens,false);
							if(addedNow)
								i++;
						}
						added = added || addedNow;
					}
				}
			}
			
			if(titleSnippets.size() > 0){
				hr.setTitle(titleSnippets.get(0).makeSnippet(256));
			}
			
			if(redirectSnippets != null){
				hr.setRedirect(redirectSnippets.makeSnippet(MAX_CONTEXT));
			}
			
			if(sectionSnippets != null){
				hr.setSection(sectionSnippets.makeSnippet(MAX_CONTEXT));
			}
			res.put(key,hr);
			
		}
		return res;
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
				len += t.getText().length(); 
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
		else if(text.indexOf('.')==-1 && text.indexOf('|')==-1)
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
			ArrayList<ExtToken> tokens, boolean addSection){
		Snippet curS = hr.getText().get(index);
		RawSnippet curRs = raw.get(index);
		int len = hr.textLength();
		boolean added = false;
		// add section
		if(addSection && more(len)){
			RawSnippet rs = sectionSnippet(curRs,curS,tokens);
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
			RawSnippet rs = nextSnippet(curRs,curS,tokens);
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
	
	protected static RawSnippet nextSnippet(RawSnippet rs, Snippet s, ArrayList<ExtToken> tokens){
		if(rs.next == null)
			return null;
		return new RawSnippet(tokens,rs.next,rs.highlight);
	}
	
	protected static RawSnippet sectionSnippet(RawSnippet rs, Snippet s, ArrayList<ExtToken> tokens){
		if(rs.section == null)
			return null;
		if(s.length() < SHORT_SNIPPET)
			return new RawSnippet(tokens,rs.section,rs.highlight);
		return null;
	}
	
	/** Implemented as <code>log(numDocs/(docFreq+1)) + 1</code>. */
	protected static double idf(int docFreq, int numDocs) {
		return Math.log(numDocs/(double)(docFreq+1)) + 1.0;
	}
	
	@SuppressWarnings("unchecked")
	protected static HashMap<String,Double> getTermsNotInTitle(HashMap<String,Double> weightTerm, Alttitles alttitles){
		Alttitles.Info info = alttitles.getTitle();
		ArrayList<ExtToken> tokens = info.getTokens();
		HashMap<String,Double> ret = (HashMap<String, Double>) weightTerm.clone();
		// delete all terms from title
		for(ExtToken t : tokens){
			if(ret.containsKey(t.termText()))
				ret.remove(t.termText());
		}
		return ret;
		
	}
	
	/** Alttitle and sections highlighting */	
	protected static RawSnippet getBestAltTitle(ArrayList<Alttitles.Info> altInfos, HashMap<String,Double> weightTerm, 
			HashMap<String,Double> notInTitle, HashSet<String> stopWords, HashMap<String,Integer> wordIndex, int minAdditional){
		ArrayList<RawSnippet> res = new ArrayList<RawSnippet>();
		for(Alttitles.Info ainf : altInfos){			
			double matched = 0, additionalScore = 0;
			int additional = 0;
			ArrayList<ExtToken> tokens = ainf.getTokens();
			boolean completeMatch=true;
			for(int i=0;i<tokens.size();i++){
				ExtToken t = tokens.get(i);
				if(t.getPositionIncrement() == 0 || t.getType() != Type.TEXT)
					continue; // skip aliases
				
				if(weightTerm.containsKey(t.termText()))
					matched += weightTerm.get(t.termText());
				else if(!stopWords.contains(t.termText()))
					completeMatch = false;
				
				if(notInTitle.containsKey(t.termText())){
					additional++;
					additionalScore += notInTitle.get(t.termText());
				}
			}
			if((completeMatch && additional >= minAdditional) || additional >= minAdditional+1 || (additional != 0 && additional == notInTitle.size())){
				ArrayList<RawSnippet> snippets = getBestTextSnippets(tokens, weightTerm, wordIndex, 1, false, stopWords);
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
			return "start="+start+", end="+end+", score="+score+", bestStart="+bestStart+", bestEnd="+bestEnd;
		}
	}
	
	/** Highlight text */
	protected static ArrayList<RawSnippet> getBestTextSnippets(ArrayList<ExtToken> tokens, HashMap<String, Double> weightTerms, 
			HashMap<String,Integer> wordIndex, int maxSnippets, boolean ignoreBreaks, HashSet<String> stopWords) {
		
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
		// length of text since first sentence
		int beginLen = 0;
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
					addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,i);
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
					res.add(new RawSnippet(tokens,firstFragment,weightTerms.keySet()));
					return res;					
				}
				fs.next = new FragmentScore(fs.end, sequence++); // link into list			
				fs = fs.next;
			}
			if(t == null)
				break;
			if(foundAllInFirst)
				beginLen += t.getText().length();

			Double weight = weightTerms.get(t.termText());
			if(weight != null){
				if(fs.found == null)
					fs.found = new HashSet<String>();
				fs.found.add(t.termText());				
				addToScore(fs,weight,i,i+1);
				addProximity(fs,weight,i,lastWeight,lastIndex);				
				
				Integer inx = wordIndex.get(t.termText());
				Integer lastInx = (lastText != null)? wordIndex.get(lastText.termText()) : null;
				if(t.getPositionIncrement() == 0); // FIXME: should do something
				else if((inx != null && lastInx == null) || phraseStart == -1){
					// begin of phrase
					phraseScore = weight;
					phraseStart = i;
					phraseCount = 1;
				} else if((inx == null && lastInx != null)){
					// end of phrase
					addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,i);
					phraseScore = 0;
					phraseStart = -1;
					phraseCount = 0;
				} else if(inx != null && lastInx != null){
					 if(lastInx + 1 != inx){
						 // end of last phrase, begin of new
						 addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,i);
						 phraseScore = weight;
						 phraseStart = i;
						 phraseCount = 1;
					 } else{
						 // continuation of phrase
						 phraseScore += weight;
						 phraseCount++;
					 }
				}
				
				lastIndex = i;
				lastWeight = weight;
			} else if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0){
				// end of phrase, unrecognized text token 
				addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,i);
				phraseScore = 0;
				phraseStart = -1;
				phraseCount = 0;
			}
			
			last = t;
			// FIXME: aliases won't get extra score for phrases
			if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0)
				lastText = t;
		}
		// flush phrase score stuff
		if(phraseScore != 0 && phraseStart != -1){
			addToScore(fs,boostPhrase(phraseScore,phraseCount),phraseStart,tokens.size());
		}

		// find fragments with best score
		Collections.sort(fragments,  new Comparator<FragmentScore>() {
			public int compare(FragmentScore o1, FragmentScore o2) {
				double d = o2.score - o1.score;
				if(d > 0)
					return 1;
				else if(d == 0)
					return 0;
				else return -1;
			}});
		
		ArrayList<RawSnippet> res = new ArrayList<RawSnippet>();
		Set<String> wordHighlight = weightTerms.keySet();
		HashSet<String> termsFound = new HashSet<String>();
		ArrayList<FragmentScore> resNoNew = new ArrayList<FragmentScore>();
		for(FragmentScore f : fragments){
			if(f.score == 0)
				continue;
			// check if the fragment has new terms
			boolean hasNew = false;
			HashSet<String> newTerms = new HashSet<String>();
			if(f.found != null){
				for(String w : f.found){
					if(!termsFound.contains(w) && !stopWords.contains(w)){
						hasNew = true;
						newTerms.add(w);
					}
				}
			}
			if(hasNew){
				if(f.found != null)
					termsFound.addAll(f.found);
				adjustBest(f,tokens,weightTerms,wordIndex,newTerms);
				RawSnippet s = new RawSnippet(tokens,f,wordHighlight);
				res.add(s);
			} else if(resNoNew.size() < maxSnippets)
				resNoNew.add(f);
			if(res.size() >= maxSnippets)
				break;			
		}
		/*if(res.size() < maxSnippets && (res.size()==0 || res.get(0).cur.pos != Position.FIRST_SECTION)){
			for(FragmentScore f : resNoNew){
				if(res.size() >= maxSnippets)
					break;
				res.add(new RawSnippet(tokens,f,wordHighlight));
			}
		} */
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
			HashMap<String, Integer> wordIndex, HashSet<String> requiredTerms) {
		f.bestScore = 0;
		
		double phraseScore=0;
		int phraseStart=-1;
		int requiredCount = 0;
		ExtToken lastText = null;
		for(int i=f.start;i<f.end;i++){
			ExtToken t = tokens.get(i);
			Double weight = weightTerms.get(t.termText());
			if(weight != null){
				// single word phrase
				if(requiredTerms.contains(t.termText()))
					updateBest(f,weight,i,i+1,1);
				
				Integer inx = wordIndex.get(t.termText());
				Integer lastInx = (lastText != null)? wordIndex.get(lastText.termText()) : null;
				if(t.getPositionIncrement() == 0); // FIXME: as above, ignores aliases
				else if((inx != null && lastInx == null) || phraseStart == -1){
					// begin of phrase
					phraseScore = weight;
					phraseStart = i;
					requiredCount = 0;
					if(requiredTerms.contains(t.termText()))
						requiredCount++;
				} else if((inx == null && lastInx != null)){
					// end of phrase
					updateBest(f,phraseScore,phraseStart,i,requiredCount);
					phraseScore = 0;
					phraseStart = -1;
					requiredCount = 0;
				} else if(inx != null && lastInx != null){
					 if(lastInx + 1 != inx){
						 // end of last phrase, begin of new
						 if(requiredTerms.contains(t.termText()))
							 requiredCount++;
						 updateBest(f,phraseScore,phraseStart,i,requiredCount);
						 phraseScore = weight;
						 phraseStart = i;
						 requiredCount = 0;
					 } else{
						 // continuation of phrase
						 if(requiredTerms.contains(t.termText()))
							 requiredCount++;
						 phraseScore += weight;
					 }
				}			
			} else if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0){
				// end of phrase, unrecognized text token 
				updateBest(f,phraseScore,phraseStart,i,requiredCount);
				phraseScore = 0;
				phraseStart = -1;
				requiredCount = 0;
			}
			
			if(t.getType() == Type.TEXT && t.getPositionIncrement() != 0)
				lastText = t;
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
	private static void addToScore(FragmentScore fs, double score, int start, int end) {
		fs.score += score;
		if(fs.bestScore < score){
			fs.bestScore = score;
			if(start == -1)
				throw new RuntimeException("Phrase start cannot be -1, score="+score);
			fs.bestStart = start;
			fs.bestEnd = end;
		}		
	}
	
	/** @return ArrayList<ExtToken> tokens, Altitles alttitles */
	protected static Object[] getTokens(IndexReader reader, String key) throws IOException{
		TermDocs td = reader.termDocs(new Term("key",key));
		if(td.next()){
			Document doc = reader.document(td.doc());
			ArrayList<ExtToken> tokens = ExtToken.deserialize(doc.getBinaryValue("text"));
			Alttitles alttitles  = Alttitles.deserializeAltTitle(doc.getBinaryValue("alttitle"));
			return new Object[] {tokens, alttitles};
		} else
			return null;
	}
}
