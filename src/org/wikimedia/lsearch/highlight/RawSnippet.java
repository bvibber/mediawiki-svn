package org.wikimedia.lsearch.highlight;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.Set;

import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.ExtToken;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.highlight.Highlight.FragmentScore;

/**
 * Building material for snippets of highlighted text.
 * 
 * @author rainman
 *
 */
public class RawSnippet {
	protected double score = 0;
	protected ArrayList<ExtToken> tokens = null;
	protected int bestStart = -1;
	protected int bestEnd = -1;
	protected Set<String> highlight;
	protected Alttitles.Info alttitle;
	
	protected FragmentScore next, section, cur;
	protected Position pos;
	protected HashSet<String> found;
	protected int sequenceNum;
	
	/** number of chars in [start,end) */
	protected int charLen(int start, int end){
		int len = 0;
		for(int i=start;i<end;i++){
			ExtToken t = tokens.get(i);
			if(t.getPositionIncrement() != 0)
				len += t.getText().length();
		}
		return len;
	}
	
	/** find the token size chars after from */
	protected int findLastWithin(int from, int size){
		int len = 0;
		for(int i=from;i<tokens.size();i++){
			ExtToken t = tokens.get(i);
			if(t.getPositionIncrement() != 0)
				len += t.getText().length();
			if(len > size)
				return i;
		}
		return tokens.size();
	}
	
	/** reverse of findLastWithin */
	protected int findFirstWithin(int from, int size){
		int len = 0;
		for(int i=from-1;i>=0;i--){
			ExtToken t = tokens.get(i);
			if(t.getPositionIncrement() != 0)
				len += t.getText().length();
			if(len > size)
				return i;
		}
		return 0;
	}
	
	/** scan a range o tokens for a minor break */
	protected int findMinorBreak(int from, int to){
		for(int i=from;i<to;i++){
			if(tokens.get(i).getType() == ExtToken.Type.MINOR_BREAK)
				return i;
		}
		return -1;
	}
	
	/** 
	 * Construct a snippet of predefined length
	 * 
	 * @param context - max number of chars for the snippet
	 * @return
	 */ 
	public Snippet makeSnippet(int context){		
		int showBegin, showEnd;
		// check if beginning of the sentence is in context range
		int before = charLen(0,bestStart);
		int after = charLen(bestEnd,tokens.size());
		int best = charLen(bestStart,bestEnd);
		
		if(best > context){
			// more tokens than we can show
			showBegin = bestStart;
			showEnd = findLastWithin(showBegin,context);			
		} else if(before + best + after < context){
			// we can show everything!
			showBegin = 0;
			showEnd = tokens.size();
		} else if(before + best < context){
			// show from begin
			showBegin = 0;
			showEnd = findLastWithin(showBegin,context);
		} else if(after < before && after + best < context){
			// show till end
			showEnd = tokens.size();
			showBegin = findFirstWithin(showEnd,context);
		} else{
			// show some before/after, start at minor word break is some is near
			int radix = (context - best) / 2;
			int scanFrom = findFirstWithin(bestStart,radix);
			int minor = findMinorBreak(scanFrom,bestStart);
			if(minor != -1)
				showBegin = minor;
			else
				showBegin = scanFrom;
			showEnd = findLastWithin(showBegin,context);			
		}
		
		// make snippet in range showBegin,showEnd
		Snippet s = new Snippet();
		StringBuilder sb = new StringBuilder();
		int start=0, end=0; // range 
		if(showBegin > 0 && tokens.get(showBegin).getType() == ExtToken.Type.TEXT)
			showBegin--; // always start with nontext token to catch " and (
		if(showEnd == tokens.size())
			s.setShowsEnd(true);
		if(showBegin == 0 && showEnd == tokens.size())
			s.setShowsAll(true);
		// don't show the final space if any
		if(tokens.size()>1 && tokens.get(tokens.size()-1).getText().equals(" ")){
			tokens.remove(tokens.size()-1);
			showEnd--;
		}
		for(int i=showBegin;i<showEnd;i++){
			ExtToken t = tokens.get(i);
			if(i == showBegin && t.getType() != ExtToken.Type.TEXT){
				// omit first nontext token
				if(t.getText().endsWith("\""))
					sb.append("\""); // hack to include initial " 
				else if(t.getText().endsWith("("))
					sb.append("("); // hack to include initial (
				continue;
			}
			if(t.getPositionIncrement() != 0){
				start = sb.length();
				sb.append(t.getText());
				end = sb.length();
			}
			if(highlight.contains(t.termText())){
				s.addRange(new Snippet.Range(start,end));
			}
		}
		s.setText(sb.toString());
		if(alttitle != null)
			s.setOriginalText(alttitle.getTitle());
		return s;
	}
	
	public RawSnippet(ArrayList<ExtToken> tokens, FragmentScore f, Set<String> highlight){
		this.tokens = new ArrayList<ExtToken>();
		for(int i=f.start;i<f.end;i++)
			this.tokens.add(tokens.get(i));
		this.highlight = highlight;
		this.score = f.score;
		this.bestStart = f.bestStart - f.start;
		if(bestStart < 0)
			bestStart = 0;
		this.bestEnd = f.bestEnd - f.start;
		if(bestEnd < 0)
			bestEnd = 0;
		this.pos = f.pos;
		this.found = f.found;
		this.next = f.next;
		this.section = f.section;
		this.cur = f;
		this.sequenceNum = f.sequenceNum;
	}

	public int getBestEnd() {
		return bestEnd;
	}

	public void setBestEnd(int bestEnd) {
		this.bestEnd = bestEnd;
	}

	public int getBestStart() {
		return bestStart;
	}

	public void setBestStart(int bestStart) {
		this.bestStart = bestStart;
	}

	public Set<String> getHighlight() {
		return highlight;
	}

	public void setHighlight(Set<String> highlight) {
		this.highlight = highlight;
	}

	public double getScore() {
		return score;
	}

	public void setScore(double score) {
		this.score = score;
	}

	public ArrayList<ExtToken> getTokens() {
		return tokens;
	}

	public void setTokens(ArrayList<ExtToken> tokens) {
		this.tokens = tokens;
	}

	public Alttitles.Info getAlttitle() {
		return alttitle;
	}

	public void setAlttitle(Alttitles.Info alttitle) {
		this.alttitle = alttitle;
	}

	public FragmentScore getNext() {
		return next;
	}

	public void setNext(FragmentScore next) {
		this.next = next;
	}

	public Position getPos() {
		return pos;
	}

	public void setPos(Position pos) {
		this.pos = pos;
	}

	public FragmentScore getSection() {
		return section;
	}

	public void setSection(FragmentScore section) {
		this.section = section;
	}
	
	public FragmentScore getCur() {
		return cur;
	}

	public void setCur(FragmentScore cur) {
		this.cur = cur;
	}

	public HashSet<String> getFound() {
		return found;
	}

	public void setFound(HashSet<String> found) {
		this.found = found;
	}

	public int getSequenceNum() {
		return sequenceNum;
	}

	public void setSequenceNum(int sequenceNum) {
		this.sequenceNum = sequenceNum;
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((cur == null) ? 0 : cur.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final RawSnippet other = (RawSnippet) obj;
		if (cur == null) {
			if (other.cur != null)
				return false;
		} else if (!cur.equals(other.cur))
			return false;
		return true;
	}
	
	public String toString(){
		return "first="+Boolean.toString(cur.isFirstSentence)+", sequence="+sequenceNum+", score="+score+", bestStart="+bestStart+", bestEnd="+bestEnd+", next="+next+", tokens="+tokens;
	}
	
	
}
