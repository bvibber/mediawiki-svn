package org.wikimedia.lsearch.highlight;

import java.util.HashSet;
import java.util.Hashtable;

import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;

/**
 * Parser that cleans up wiki markup, and tidies a text a bit so
 * it's more readable when highlighted. 
 * 
 * @author rainman
 *
 */
public class CleanupParser {
	private static final int MAX_WORD_LEN = 255;
	private char[] out;
	private int length = 0; // length of out buffer
	private char[] text;
	private String textString; // original text in string format
	private int textLength;
	private int cur = 0; // current position in the input string
	private char c, c1 = ' '; // current character, next char
	private int lookup; // lookup counter
	private char lc; // lookup char
	private int headings = 0; // how many headings did we see
	
	private int prefixLen = 0;
	private final char[] prefixBuf = new char[MAX_WORD_LEN];
	private int semicolonInx = -1;
	
	/** This many tokens from begining of text are eligable for keywords */ 
	public static final int KEYWORD_TOKEN_LIMIT = 250;
	
	/** language code */
	private String language;
	private IndexId iid;
	/** language code -> set (image namespace names) */
	private static Hashtable<String,HashSet<String>> imageLocalized = new Hashtable<String,HashSet<String>>();
	/** language code -> set (category namespace names) */
	private static Hashtable<String,HashSet<String>> categoryLocalized = new Hashtable<String,HashSet<String>>();
	private static HashSet<String> interwiki;
	
	enum ParserState { WORD, LINK_BEGIN, LINK_WORDS, LINK_END, LINK_KEYWORD, 
		LINK_FETCH, IGNORE, EXTERNAL_URL, EXTERNAL_WORDS,
		TEMPLATE_BEGIN, TEMPLATE_WORDS, TEMPLATE_END,
		TABLE_BEGIN};
		
	enum FetchState { WORD, CATEGORY, INTERWIKI, KEYWORD };
	
	public CleanupParser(String text, IndexId iid){
		this.text = text.toCharArray();
		this.textString = text;
		this.iid = iid;
		this.language = iid.getLangCode();
		textLength = text.length();
		out = new char[textLength];
	}
		
	/** 
	 * Lookup for pipe (|) before the closed wiki link
	 * It will also extract prefix (i.e. Image in  [[Image:Blah.jpg]]) 
	 *  
	 */
	private final int pipeLookup(){
		
		prefixLen = 0;
		semicolonInx = -1;
		
		fetchPrefix: for(lookup = cur ; lookup<textLength ; lookup++ ){
			lc = text[lookup];
			switch(lc){
			case ']':
				return -1;
			case '|':
				return lookup;
			case ':':
				semicolonInx = lookup;
				break fetchPrefix;
			}
			
			if(prefixLen >= MAX_WORD_LEN){
				prefixLen = 0;
				semicolonInx = -1;
				break;
			}
			if(Character.isLetter(lc)){
				prefixBuf[ prefixLen++ ] = Character.toLowerCase(lc);
			} 
		}
		
		// we fetched the prefix, now just continue the lookup
		for(; lookup<textLength ; lookup++ ){
			lc = text[lookup];
			switch(lc){
			case ']':
				return -1;
			case '|':
				return lookup;
			}
		}
		return -1;
	}
		
	/**
	 *  Move cur to the beginning of the image caption
	 * @return true if caption is found, false if this is the end of link
	 */
	private final boolean imageCaptionSeek(){
		int lastPipe = -1;
		for(lookup = cur; lookup<textLength ; lookup++ ){
			lc = text[lookup];
			switch(lc){
			case '|':
				lastPipe = lookup;
				break;
			case ']':
				if(lastPipe == -1){
					cur = lookup;
					return false;
				}
				else{
					cur = lastPipe;
					return true;
				}				
			}
		}
		return false;	
	}
	
	private final boolean imageIgnore(){
		for(lookup = cur; lookup<textLength ; lookup++ ){
			if(text[lookup]==']'){
				cur = lookup;
				return true;
			}
		}
		return false;	
	}
	
	/** Returns true if the parsed article is a redirect page */
	public final boolean isRedirect(){
		if(textLength == 0 || text[0] != '#') // quick test
			return false;
		
		return Localization.getRedirectTarget(textString,language)!=null;			
	}
	
	/** When encountering '=' check if this line is actually a heading */ 
	private void checkHeadings() {
		// make sure = is at a begining of a line
		if(cur == 0 || text[cur-1]=='\n' || text[cur-1]=='\r'){
			int endOfLine;
			// find end of line/text
			for(endOfLine = cur ;  endOfLine < textLength ; endOfLine++ ){
				lc = text[endOfLine];
				if(lc == '\n' || lc =='\r')
					break;
			}
			int start=0, end=0; // number of ='s at begining and end of line
			// find first sequence of =
			for(lookup = cur ; lookup < textLength && lookup < endOfLine ; lookup++ ){
				if(text[lookup] == '=')
					start++;
				else
					break;
			}
			// find the last squence of =
			for(lookup = endOfLine-1 ; lookup > cur ; lookup-- ){
				if(text[lookup] == '=')
					end++;
				else
					break;
			}
			// check
			if(start == end && start != 0 && start+end<endOfLine-cur && start>=2 && start<=4){
				headings++;
			}
		}		
	}

	protected final void addLetter(){
		out[length++] = c;
	}
	
	/**
	 * Parse Wiki text, and produce an arraylist of tokens.
	 * Also fills the lists categories and interwikis.
	 * @return
	 */
	public String parse(){		
		ParserState state = ParserState.WORD;
		FetchState fetch = FetchState.WORD;
		String prefix = "";
		char ignoreEnd = ' '; // end of ignore block
		int pipeInx = 0;
				
		for(cur = 0; cur < textLength; cur++ ){
			c = text[cur];			
			
			// actions for various parser states
			switch(state){
			case WORD:
				switch(c){
				case '=':
					checkHeadings();
					continue;
				case '#':
				case '*':
					if(length == 0 || out[length-1] == '\n' || out[length-1]=='\r')
						continue; // lists, skip
					break;
				case '\'':
					if(cur + 1 < textLength ){
						c1 = text[cur+1];
						if(c == c1){
							while(cur < textLength && text[cur] == '\'') cur++; // skip ''' marks
							cur--;						
						} else
							addLetter();
					} else 
						addLetter(); // last in stream
					continue;
				case '|':
					if(cur + 1 < textLength ){
						c1 = text[cur+1];
						if(c1 == '-'){
							cur++;
							continue;
						}
					}
					addLetter();
					continue;
				case '<':
					state = ParserState.IGNORE;
					ignoreEnd = '>';					
					continue;
				case '[':
					if(cur + 1 < textLength )
						c1 = text[cur+1];
					else 
						continue; // last char in stream					
					// wiki-link
					if(c1 == '['){
						cur++; // skip this char
						state = ParserState.LINK_BEGIN;
						continue;
					} else{ // external link
						state = ParserState.EXTERNAL_URL;
						continue;
					}
				case '{':
					if(cur + 1 < textLength )
						c1 = text[cur+1];
					else 
						continue; // last char in stream
					
					if(c1 == '{'){
						cur++;
						state = ParserState.TEMPLATE_BEGIN;
						continue;
					} else if(c1 == '|'){
						// it's table only at the beginning of the line
						if(cur == 0 || text[cur-1] == '\n' || text[cur-1]=='\r'){
							cur++;
							state = ParserState.TABLE_BEGIN;
						}
						continue;
					} else
						continue;
				}
				addLetter();
				continue;
			case IGNORE:
				if(c == ignoreEnd){
					state = ParserState.WORD;
				}
				continue;
			case EXTERNAL_URL:
				switch(c){
				case ' ':
					state = ParserState.EXTERNAL_WORDS;
					addLetter();
					continue;
				case ']':
					state = ParserState.WORD;
					continue;
				}
				addLetter();
				continue;
			case EXTERNAL_WORDS:
				if(c == ']')
					state = ParserState.WORD;
				else
					addLetter();
				continue;
			case LINK_BEGIN:
				pipeInx = pipeLookup();
				state = ParserState.LINK_WORDS; // default next state
				
				// process prefixes!
				if( semicolonInx != -1 ){
					if(prefixLen == 0)
						continue; // syntax [[:Something]], i.e. with leading semicolon
					
					prefix = new String(prefixBuf,0,prefixLen);
					if(isImage(prefix)){
						if( imageIgnore() )
							state = ParserState.LINK_END;
						continue;
					} else if(isCategory(prefix)){
						cur = semicolonInx;
						fetch = FetchState.CATEGORY;
						state = ParserState.LINK_FETCH;
						continue;
					} else if(isInterwiki(prefix)){
						cur = semicolonInx;
						fetch = FetchState.INTERWIKI;
						state = ParserState.LINK_FETCH;
						continue;
					}
				}
				
				// no semicolon, search for pipe:
				if(pipeInx != -1){
					cur = pipeInx; // found pipe, ignore everything up to pipe
					continue;
				} else{
					addLetter();
					continue;
				}
			case LINK_WORDS:
				if(c == ']'){
					state = ParserState.LINK_END;
					continue;
				}
				addLetter();
				continue;
			case LINK_FETCH:
				if(c == ']'){
					state = ParserState.LINK_END;
					continue;
				} else if(c == '|'){ // ignore everything up to ]
					link_end_lookup: for( lookup = cur + 1 ; lookup < textLength ; lookup++ ){
						switch(text[lookup]){
						case ']':
							state = ParserState.LINK_END;
							cur = lookup;
							break link_end_lookup;
						// bad syntax:
						case '|':
						case '}':
						case '[':
						case '{':
							state = ParserState.WORD;
							fetch = FetchState.WORD;
							break link_end_lookup;
						}						
					}
					continue;
				} 
				continue;
			case LINK_END:
				if(c == ']'){ // good link ending
					state = ParserState.WORD;
					fetch = FetchState.WORD;					
				} else{
					addLetter();
					// bad syntax, ignore any categories, etc.. 
					state = ParserState.WORD;
					fetch = FetchState.WORD;
				}
				continue;
			case TABLE_BEGIN:
				// ignore everything up to the newspace, since they are table display params
				while(cur < textLength && (text[cur]!='\r' && text[cur]!='\n'))
					cur++;
				state = ParserState.WORD;
				continue;
			case TEMPLATE_BEGIN:
				// ignore name of the template, index parameters
				template_lookup: for( lookup = cur ; lookup < textLength ; lookup++ ){
					switch(text[lookup]){
					case '|':
					case '}':
						state = ParserState.WORD;
						cur = lookup+1;
						break template_lookup;
					// bad syntax, prevents text from being eaten up by lookup
					case '[':
					case ']':
					case '{':
						state = ParserState.WORD;
						addLetter();
						break template_lookup;
					}					
				}
				continue;
			default:
				System.out.println("Parser Internal error, near '"+c+"' at index "+cur);
			}		
		}
		return new String(out,0,length);
	}

	/** Check if this is an "image" keyword using localization */
	private final boolean isImage(String prefix){
		prefix = prefix.toLowerCase();
		if(prefix.equals("image"))
			return true;
		else if(language!=null && language.length()!=0){
			HashSet<String> loc = imageLocalized.get(language);
			if(loc == null){
				loc = Localization.getLocalizedImage(language,iid.getDBname());
				imageLocalized.put(language,loc);
			}
			if(loc.contains(prefix))
				return true;
		}
		return false;
	}
	
	/** Check if this is a "category" keyword using localization */
	private final boolean isCategory(String prefix){
		prefix = prefix.toLowerCase();
		if(prefix.equals("category"))
			return true;
		else if(language!=null && language.length()!=0){
			HashSet<String> loc = categoryLocalized.get(language);
			if(loc == null){
				loc = Localization.getLocalizedCategory(language,iid.getDBname());
				categoryLocalized.put(language,loc);
			}
			if(loc.contains(prefix))
				return true;
		}
		return false;
	}
	
	private final boolean isInterwiki(String prefix){
		if(interwiki == null)
			interwiki = Localization.getInterwiki();
		return interwiki.contains(prefix.toLowerCase());
	}

	
}
