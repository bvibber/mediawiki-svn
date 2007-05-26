package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.lucene.analysis.Token;
import org.wikimedia.lsearch.util.Localization;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Wiki Tokenizer. Tokens are words and numbers. All letters are
 * lowercased and diacritics deleted using unicode compatibility
 * decomposition (i.e. č -> c). Parses some basic wiki syntax,
 * template names are skipped, from images captions are extracted,
 * categories and interwiki links are extracted ... 
 * 
 * Tokenizer will not take a Reader as input, but a String (for
 * optimal performance)
 * 
 * @author rainman
 *
 */
public class FastWikiTokenizerEngine {
	private static final int MAX_WORD_LEN = 255;
	private final char[] buffer = new char[MAX_WORD_LEN+1];
	private static final int IO_BUFFER_SIZE = 1024;
	private final char[] ioBuffer = new char[IO_BUFFER_SIZE];
	private char[] text;
	private String textString; // original text in string format
	private int textLength;
	private ArrayList<Token> tokens;
	protected ArrayList<String> categories;
	protected HashMap<String,String> interwikis;
	private int length = 0; // length of token
	private int start = 0; // start position of token
	private int cur = 0; // current position in the input string
	private char c, c1 = ' '; // current character, next char
	private int lookup; // lookup counter
	private char lc; // lookup char
	private char[] decomp; // unicode decomposition letters
	private int decompi;
	private char cl; // lowercased character
	private boolean numberToken; // if the buffer holds a number token
	
	private int prefixLen = 0;
	private final char[] prefixBuf = new char[MAX_WORD_LEN];
	private int semicolonInx = -1;
	
	/** language code */
	private String language;
	/** language code -> set (image namespace names) */
	private static Hashtable<String,HashSet<String>> imageLocalized = new Hashtable<String,HashSet<String>>();
	/** language code -> set (category namespace names) */
	private static Hashtable<String,HashSet<String>> categoryLocalized = new Hashtable<String,HashSet<String>>();
	private static HashSet<String> interwiki;
	
	private UnicodeDecomposer decomposer;
	
	enum ParserState { WORD, LINK_BEGIN, LINK_WORDS, LINK_END, 
		LINK_FETCH, IGNORE, EXTERNAL_URL, EXTERNAL_WORDS,
		TEMPLATE_BEGIN, TEMPLATE_WORDS, TEMPLATE_END,
		TABLE_BEGIN};
		
	enum FetchState { WORD, CATEGORY, INTERWIKI};
	
	
	private void init(){
		tokens = new ArrayList<Token>();
		categories = new ArrayList<String>();
		interwikis = new HashMap<String,String>();
		decomposer = UnicodeDecomposer.getInstance();
		numberToken = false;
	}
	
	/** Note: this will read only 1024 bytes of reader, it's
	 *  ment to be used only for parsing queries! 
	 * @param reader
	 */
	@Deprecated
	public FastWikiTokenizerEngine(Reader reader){
		try {
			reader.read(ioBuffer);
			text = ioBuffer;
			textLength = ioBuffer.length;	
			textString = new String(ioBuffer,0,ioBuffer.length);
			init();
		} catch (IOException e1) {
			e1.printStackTrace();
		}
	}
	
	public FastWikiTokenizerEngine(String text){
		this(text,null);
	}
	
	public FastWikiTokenizerEngine(String text, String lang){
		this.text = text.toCharArray();
		this.textString = text;
		this.language = lang;
		textLength = text.length();
		init();
	}
	
	/**
	 * Strip accents 
	 * @param c
	 * @return array of letters to which this is char is decomposed
	 */
	private final char[] decompose(char c){
		return decomposer.decompose(c);
	}
	
	/** 
	 * This function is called at word boundaries, it is used to 
	 * make a new token and add it to token stream
	 */
	private final void addToken(){
		if(length!=0){
			if(numberToken && (buffer[length-1]=='.' ||buffer[length-1]==','))
				length--; // strip trailing . and , in numbers
			tokens.add(new Token(
					new String(buffer, 0, length), start, start + length));
			length = 0;
			numberToken = false;
		}
	}
	
	/**
	 * Tries to add the current letter (variable c) to the
	 * buffer, if it's not a letter, new token is created
	 */
	private final void addLetter(){
		try{
			// add new character to buffer
			if(Character.isLetter(c)){
				if(numberToken) // we were fetching a number
					addToken();

				if(length == 0)
					start = cur;

				cl = Character.toLowerCase(c);
				decomp = decompose(cl);
				if(decomp == null){
					if(length<buffer.length)
						buffer[length++] = cl;
				}
				else{
					for(decompi = 0; decompi < decomp.length; decompi++){
						if(length<buffer.length)
							buffer[length++] = decomp[decompi];
					}
				}
				// add digits
			} else if(Character.isDigit(c)){				
				if(length == 0)
					start = cur;
				numberToken = true;

				if(length<buffer.length)
					buffer[length++] = c;
				// add dot and comma to digits if they are not at the beginning
			} else if(numberToken && (c == '.' || c == ',')){
				if(length<buffer.length)
					buffer[length++] = c; 
			} else{
				addToken();
			}
		} catch(Exception e){
			if(length >= buffer.length){
				System.out.println("Buffer overflow on token: "+new String(buffer,0,buffer.length));
			}
			e.printStackTrace();
			System.out.println("Error while processing char '"+c+"', hex:"+Integer.toString(c,16));
		}
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
	
	/** Returns true if the parsed article is a redirect page */
	public final boolean isRedirect(){
		if(textLength == 0 || text[0] != '#') // quick test
			return false;
		
		return Localization.getRedirectTarget(textString,language)!=null;			
	}
	
	/**
	 * Parse Wiki text, and produce an arraylist of tokens.
	 * Also fills the lists categories and interwikis.
	 * @return
	 */
	public ArrayList<Token> parse(){		
		ParserState state = ParserState.WORD;
		FetchState fetch = FetchState.WORD;
		String prefix = "";
		char ignoreEnd = ' '; // end of ignore block
		int pipeInx = 0;
		
		// before starting, make sure this is not a redirect
		if(isRedirect())
			return tokens;
		
		for(cur = 0; cur < textLength; cur++ ){
			c = text[cur];			
			
			// actions for various parser states
			switch(state){
			case WORD:
				switch(c){
				case '<':
					addToken();
					state = ParserState.IGNORE;
					ignoreEnd = '>';					
					continue;
				case '[':
					addToken();
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
					addToken();
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
					continue;
				case ']':
					state = ParserState.WORD;
					continue;
				}
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
						if( !imageCaptionSeek() )
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
					} else{
						// unrecognized, ignore
						cur--;
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
				if(length == 0 && c ==' ')
					continue; // ignore leading whitespaces
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
				
				if(length<buffer.length)
					buffer[length++] = c;
				continue;				
			case LINK_END:
				if(c == ']'){ // good link ending
					state = ParserState.WORD;
					
					switch(fetch){
					case WORD:
						addToken();
						continue;
					case CATEGORY:
						categories.add(new String(buffer,0,length));
						length = 0;
						fetch = FetchState.WORD;
						continue;
					case INTERWIKI:
						interwikis.put(prefix,
								         new String(buffer,0,length));
						length = 0;
						fetch = FetchState.WORD;
						continue;
					}
				} else{
					// bad syntax, ignore any categories, etc.. 
					state = ParserState.WORD;
					continue;
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
						cur = lookup;
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
		addToken();
		return tokens;
	}
	
	/** Check if this is an "image" keyword using localization */
	private final boolean isImage(String prefix){
		prefix = prefix.toLowerCase();
		if(prefix.equals("image"))
			return true;
		else if(language!=null && language.length()!=0){
			HashSet<String> loc = imageLocalized.get(language);
			if(loc == null){
				loc = Localization.getLocalizedImage(language);
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
				loc = Localization.getLocalizedCategory(language);
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

	public ArrayList<String> getCategories() {
		return categories;
	}

	public HashMap<String, String> getInterwikis() {
		return interwikis;
	}

	public ArrayList<Token> getTokens() {
		return tokens;
	}
}
