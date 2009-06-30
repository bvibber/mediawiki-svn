/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import java.text.ParsePosition;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.AnalyzerUtils;

public class BoxStripMangler implements Mangler, SuccessiveMangler {
	protected Matcher matcher;
	protected String[] beginnings;
	protected String[] ends;
	
	public BoxStripMangler(String[] beginnings, String[] ends, String stop) {
		this.beginnings = beginnings;
		this.ends = ends;
		
		if (beginnings.length!=ends.length) throw new IllegalArgumentException("must have the same number of ends and beginnings");
		
		StringBuilder regex = new StringBuilder();
		for (int i = 0; i < beginnings.length; i++) {
			if (i>0) regex.append('|');
			regex.append('(');
			regex.append(beginnings[i]);
			regex.append(')');
		}
		for (int i = 0; i < ends.length; i++) {
			regex.append('|');
			regex.append('(');
			regex.append(ends[i]);
			regex.append(')');
		}
		
		if (stop!=null) {
			regex.append('|');
			regex.append('(');
			regex.append(stop);
			regex.append(')');
		}
		
		Pattern pattern = Pattern.compile(regex.toString(), Pattern.CASE_INSENSITIVE | Pattern.MULTILINE);
		matcher = pattern.matcher("");
	}
	
	public CharSequence mangle(CharSequence text) {
		return mangle(text, null);
	}
	
	public CharSequence mangle(CharSequence text, ParsePosition pp) {
		StringBuilder out = new StringBuilder(text.length());
		
		int[] stack = new int[100];
		int level = 0;
		int last = pp == null ? 0 : pp.getIndex();
		
		//System.out.print("."); System.out.flush();
		
		matcher.reset(text);
		if (pp!=null) matcher.region(pp.getIndex(), text.length());
		while (matcher.find()) {
			if (level==0) out.append(text, last, matcher.start());
			if (pp!=null) pp.setIndex(matcher.start());
			
			int marker = getMarkerId(matcher);
			//System.out.println(marker+": "+m.group(0));
			
			if (marker==0) { //end
				if (level==0) {
					if (AnalyzerUtils.trim(out).length()>0 || matcher.group().trim().length()>0) {
						return out; //NOTE: hefty hack //TODO: min length?
					}
				}
			}
			else if (marker>0) { //push
				if (level<stack.length) {
					stack[level] = -marker;
					level++;
				}
			}
			else { //pop
				if (level>0) {
					if (marker==stack[level-1]) {
						level--;
					}
					else { //mismatching closing thingy!
						//scan up the stack for matching open block
						//XXX: maybe use precedence order (level ordinals?)
						int lvl = level;
						while (lvl>0 && marker!=stack[lvl-1]) {
							lvl--;
						}
						
						if (lvl>0) {
							level = lvl -1;
						}
					}
				}
			}
			
			last = matcher.end();
			if (pp!=null) pp.setIndex(last);
		}
		
		if (level==0) {
			out.append(text, last, text.length());
			if (pp!=null) pp.setIndex(text.length());
		}
		
		return out;
	}
	
	protected int getMarkerId(Matcher m) {
		for (int i=1; i<=beginnings.length*2; i++) {
			if (m.group(i)!=null) {
				if (i<=beginnings.length) return i;
				else return -i +beginnings.length;
			}
		}
		
		if (m.group(beginnings.length*2+1)!=null) {
			return 0;
		}
		
		throw new RuntimeException("no group matched (can't happen!)");
	}
	
}