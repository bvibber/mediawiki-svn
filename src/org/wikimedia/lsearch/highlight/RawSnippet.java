package org.wikimedia.lsearch.highlight;

import java.util.ArrayList;
import java.util.Set;

import org.wikimedia.lsearch.analyzers.Alttitles;
import org.wikimedia.lsearch.analyzers.ExtToken;
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
		for(int i=showBegin;i<showEnd;i++){
			ExtToken t = tokens.get(i);
			if(i == showBegin && t.getType() != ExtToken.Type.TEXT){
				// omit first nontext token
				if(t.getText().endsWith("\""))
					sb.append("\""); // hack to include initial "
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
		this.bestEnd = f.bestEnd - f.start;
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
	
	
}
