package org.wikimedia.lsearch.ranks;

import java.util.ArrayList;
import java.util.HashSet;
import java.util.Hashtable;

import org.wikimedia.lsearch.util.Localization;

/**
 * Parse wiki-text into sentences. Each sentence will provide a
 * context for links within it.   
 * 
 * @author rainman
 *
 */
public class ContextParser {
	protected char[] text;
	protected int len;
	protected HashSet<String> imageLocalized = null;
	protected HashSet<String> categoryLocalized = null;
	protected HashSet<String> interwiki = null;
	
	protected ArrayList<Context> contexts = null;
	protected int conIn = 0; 
	
	public static class Context {
		int start;
		int end;
		String context = null;
		public Context(int start, int end) {
			this.start = start;
			this.end = end;
		}
		
		public String get(String text){
			if(context == null)
				context = text.substring(start,end);
			return context;
		}
		
	}
	
	public ContextParser(String text, HashSet<String> imageLocalized, HashSet<String> categoryLocalized, HashSet<String> interwiki){
		this.text = text.toCharArray();
		this.len = this.text.length;
		this.imageLocalized = imageLocalized;
		this.categoryLocalized = categoryLocalized;
		this.interwiki = interwiki;
		parse();
	}
	
	/** Get indexes of boundaries of contexts (usually different sentences) */
	public ArrayList<Context> getContexts(){
		return contexts;		
	}
	
	/** Get context by index in text, function should be called for incrementaly larger index */
	public Context getNext(int index){
		if(conIn >= contexts.size())
			return null;
		Context c = contexts.get(conIn);
		if(c.start > index)
			return null;
		else{
			for(;conIn<contexts.size();conIn++){
				c = contexts.get(conIn); 
				if(c.start <= index && index < c.end)
					return c;
				if(c.start > index)
					return null; // no context for this index
			}
		}
		return null;
	}

	/** fetch up to 128 chars of prefix */
	protected String fetchPrefix(int in){
		int count = 0;
		for(int i=in;i<len;i++,count++){
			if(count >= 128)
				return null;
			if(text[i] == ':'){
				return new String(text,in,count);
			}
		}
		return null;
	}
	
	protected void parse(){
		if(contexts != null)
			return;
		contexts = new ArrayList<Context>();
		int cur = 0;
		char c;
		boolean seenLetter = false;
		int topLinkLevel = 0;
		boolean inQuotes = false;
		int start = 0;
		for(;cur<len;cur++){
			c = text[cur];
			if(!seenLetter && Character.isLetterOrDigit(c))
				seenLetter = true;
			switch(c){
			case '[':
				if(cur+2>=len)
					continue; // EOF
				if(text[cur+1]=='['){
					boolean valid = false;
					int closingInx = -1;
					// seek to see if this is valid link opening
					for(int i=cur+2;i<len && i<cur+512;i++){
						if(text[i]=='[' && i+1<len && text[i+1]=='[')
							break; // bad internal link
						if(text[i]==']' && i+1<len && text[i+1]==']'){
							topLinkLevel++; // ok, valid internal link
							closingInx = i+2;
							valid = true;
							break;
						}
							
					}
					// begin of links
					String prefix = fetchPrefix(cur+2);
					if(prefix != null && isImage(prefix)){
						// take full image caption as one context
						int lastPipe = cur + 2 + prefix.length();
						int linkLevel = 0;
						int imageEnd = -1;
						for(int i=lastPipe;i<len;i++){
							if(text[i]=='|')
								lastPipe = i; 
							// internal link begin
							if(text[i]=='[' && i+1<len && text[i+1]=='[')
								linkLevel++;
							// internal link end
							if(text[i]==']' && i+1<len && text[i+1]==']'){
								if(linkLevel == 0){
									imageEnd = i+1;
									break;
								} else if(linkLevel != 0)
									linkLevel--;
							}
						}
						// add everything up to image as one context
						// and image caption as second context
						if(imageEnd != -1){
							contexts.add(new Context(lastPipe+1,imageEnd-2));
							start = imageEnd+1;
							cur = imageEnd;
						}
					} else if(valid && prefix != null && (isCategory(prefix) || isInterwiki(prefix))){
						// skip categories
						if(seenLetter)
							contexts.add(new Context(start,cur));
						start = cur;
						cur = closingInx;						
					} else if(valid){
						cur = closingInx;
					}
				}
				break;
			case 'h': case 'f':
				// check simple http/ftp links
				if(checkPrefix(cur,"http://") || checkPrefix(cur,"ftp://")){
					if(seenLetter && cur-start>2)
						contexts.add(new Context(start,cur-1));
					for(;cur<len;cur++){
						if(text[cur]==' ' || text[cur]==']'){ // seek to after link
							start = cur+1;
							seenLetter = false;
							break; 
						}
					}
				}
				break;
			case '<':
				if(checkPrefix(cur,"<tr>") || checkPrefix(cur,"</tr>")){
					if(seenLetter)
						contexts.add(new Context(start,cur-1));
					start = cur + 4;
				}
				break;
			case ']':
				if(cur+2>=len)
					continue; // EOF 
				if(text[cur+1]==']' && topLinkLevel!=0){
					topLinkLevel--;
				}
				break;
			/*case '"':
				// numbers like 6'5" 
				if(cur>0 && Character.isDigit(text[cur-1]))
					break;
				inQuotes = !inQuotes;
				break; */
			/*case '=':
			case '!':
			case '?': */ 
			case '{':
			case '}':
			/*case '*':
			case '#':
			case '|':
			case '.': */
			case '\n':
				// whole quote and link text is context 
				//if(inQuotes || topLinkLevel!=0)
				//	break;
				// only double == is separator (as in headings)
				if(c == '=' && !(cur+1<len && text[cur+1]=='='))
					break;
				// | is separator in tables, etc.. but not in link syntax like [[x|y]]
				if(c == '|' && topLinkLevel != 0 && (cur+1<len && text[cur+1]!='-'))
					break;
				// dot/comma between numbers
				if((c == '.' || c==',') && (cur>0 && Character.isDigit(text[cur-1]) && cur+1<len && Character.isDigit(text[cur+1])))
					break;
				// proceed only if this is not paragraph brake (i.e. \n\n)
				if(c == '\n' && !(cur+1<len && (text[cur+1]=='\n' || text[cur+1]==':' || text[cur+1]=='*' || text[cur+1]=='#')))
					break;
				
				if(seenLetter){
					contexts.add(new Context(start,cur));
					start = cur + 1;
					seenLetter = false;
				}
				break;
			}
		}
		if(seenLetter)
			contexts.add(new Context(start,len));
	}
	
	/** check text from cur position */
	private boolean checkPrefix(int cur, String prefix) {
		if(cur + prefix.length() < len){
			for(int i=0;i<prefix.length();i++){
				if(text[cur+i] != prefix.charAt(i))
					return false;
			}
			return true;
		}
		return false;
	}

	/** Check if this is an "image" keyword using localization */
	private final boolean isImage(String prefix){
		prefix = prefix.toLowerCase();
		if(prefix.equals("image"))
			return true;		
		if(imageLocalized!=null && imageLocalized.contains(prefix))
			return true;
		return false;
	}
	
	private final boolean isCategory(String prefix){
		prefix = prefix.toLowerCase();
		if(prefix.equals("category"))
			return true;
		if(categoryLocalized!=null && categoryLocalized.contains(prefix))
			return true;
		return false;
	}
	
	private final boolean isInterwiki(String prefix){
		if(interwiki!=null)
			return interwiki.contains(prefix);
		else
			return false;
	}
	
	
	
}
