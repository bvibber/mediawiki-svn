package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Reader;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.commons.lang.WordUtils;
import org.apache.lucene.analysis.Token;
import org.wikimedia.lsearch.analyzers.ExtToken.Position;
import org.wikimedia.lsearch.config.IndexId;
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
	private final char[] buffer = new char[MAX_WORD_LEN]; // buffer of text, e.g. gödel
	private final char[] aliasBuffer = new char[MAX_WORD_LEN]; // buffer for aliases, e.g. goedel
	private final char[] decompBuffer = new char[MAX_WORD_LEN]; // buffer for dedomposed text e.g. godel
	private final char[] glueBuffer = new char[MAX_WORD_LEN-1]; // buffer of spaces, etc.. that glues tokens together to produce the original (for highlight)
	private final char[] tempBuffer = new char[MAX_WORD_LEN-1]; // buffer for temp stuff
	private char[] text;
	private String textString; // original text in string format
	private int textLength;
	private ArrayList<Token> tokens;
	protected ArrayList<String> categories;
	protected HashMap<String,String> interwikis;
	protected HashSet<String> keywords;
	protected ArrayList<String> headingText;
	private int decompLength=0, aliasLength=0;
	private int glueLength = 0; // length for glue buffer
	private int tempLength = 0;
	private int length = 0; // length of token
	private int start = 0; // start position of token
	private int glueStart = 0; // start position of glue token
	private int cur = 0; // current position in the input string
	private char c, c1 = ' '; // current character, next char
	private int lookup; // lookup counter
	private char lc; // lookup char
	private char[] decomp; // unicode decomposition letters
	private int decompi;
	private char cl; // lowercased character
	private boolean numberToken; // if the buffer holds a number token
	private int headings = 0; // how many headings did we see
	private int templateLevel = 0; // level of nestedness of templates	
	private int gap = 1;
	private ArrayList<Token> nonContentTokens; // tokens from the beginning of the article that are not content, but templates, images, etc..
	private ArrayList<Token> references; // stuff between <ref></ref> tags should go to the end
	private boolean inRef = false, firstRef = false; // if we are within a ref tag
	private boolean inHeading = false; // if we are in section heading
	private boolean inBulletin = false; // if we are in ordered lists and such
	private boolean inImageCategoryInterwiki = false; // if we are in any of these
	private boolean inExternalLink = false, inUrl = false; // parts of url
	private boolean inLink = false; // if we are parsing internal links
	private boolean lastImgLinkCatWord = false; // if we are parsing the last word in a link, even when inImageCat.. is false if this is true, the word will be in the link
	private int tableLevel = 0;
	
	private int prefixLen = 0;
	private final char[] prefixBuf = new char[MAX_WORD_LEN];
	private int semicolonInx = -1;
	private final char[] keywordBuf = new char[MAX_WORD_LEN];
	private int keywordLen = 0;
	private int keywordTokens = 0; // number of tokens which can be keywords (i.e. which are not in templates)
	
	/** This many tokens from begining of text are eligable for keywords */ 
	public static int KEYWORD_TOKEN_LIMIT = 250;
	
	/** Token gap at first section break */ 
	public static int FIRST_SECTION_GAP = 500;	
	/** Token gap at any section break */ 
	public static int SECTION_GAP = 20;	
	/** Token gap at new paragraphs */ 
	public static int PARAGRAPH_GAP = 10;	
	/** Token gap at new bulletins (like *, #...) */ 
	public static int BULLETIN_GAP = 10;
	/** Gap between sentences */ 
	public static int SENTENCE_GAP = 2;
	/** Gap between references */ 
	public static int REFERENCE_GAP = 20;
	
	/** language code */
	private String language;
	private IndexId iid;
	/** language code -> set (image namespace names) */
	private static Hashtable<String,HashSet<String>> imageLocalized = new Hashtable<String,HashSet<String>>();
	/** language code -> set (category namespace names) */
	private static Hashtable<String,HashSet<String>> categoryLocalized = new Hashtable<String,HashSet<String>>();
	private static HashSet<String> interwiki;
	
	private UnicodeDecomposer decomposer;	
	private TokenizerOptions options;
	
	enum ParserState { WORD, LINK_BEGIN, LINK_WORDS, LINK_END, LINK_KEYWORD, 
		LINK_FETCH, IGNORE, EXTERNAL_URL, EXTERNAL_WORDS,
		TEMPLATE_BEGIN, TEMPLATE_WORDS, TEMPLATE_END,
		TABLE_BEGIN, CATEGORY_WORDS };
		
	enum FetchState { WORD, CATEGORY, INTERWIKI, KEYWORD };
	
	
	private void init(){
		tokens = null;
		categories = new ArrayList<String>();
		interwikis = new HashMap<String,String>();
		decomposer = UnicodeDecomposer.getInstance();
		keywords = new HashSet<String>();
		numberToken = false;
		headingText = new ArrayList<String>();
		nonContentTokens = new ArrayList<Token>();
		inRef = false;
		firstRef = false;
		references = new ArrayList<Token>();
	}
		
	public FastWikiTokenizerEngine(String text, IndexId iid, TokenizerOptions options){
		this.text = text.toCharArray();
		this.textString = text;
		this.language = iid.getLangCode();
		this.iid = iid;
		this.options = options;
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
	
	/** Add transliteration to token alias, create alias if it doesn't exist */
	private final void addToTokenAlias(String transliteration) {
		if(aliasLength == 0){
			System.arraycopy(decompBuffer,0,aliasBuffer,0,decompLength);
			aliasLength = decompLength;
		}
		for(char cc : transliteration.toCharArray())
			if(aliasLength < aliasBuffer.length)
				aliasBuffer[aliasLength++] = cc;
	}
	
	/** 
	 * This function is called at word boundaries, it is used to 
	 * make a new token and add it to token stream
	 * 
	 * Does unicode decomposition, and will make alias token with 
	 * alternative transliterations (e.g. ö -> oe)
	 */
	private final void addToken(){
		addToken(true);
	}	
		
	/** foundNonLetter - if nonLetter char is encountered */
	private final void addToken(boolean foundNonLetter){
		if(length!=0){
			if(options.highlightParsing && glueLength != 0){
				// we collected some glue, flush
				ExtToken t = makeGlueToken();
				if(t != null)
					addToTokens(t);
				glueLength = 0;
			}
			if(numberToken && (buffer[length-1]=='.' || buffer[length-1]==',')){
				length--; // strip trailing . and , in numbers
				if(options.highlightParsing){
					glueStart = cur; // we know glueLength will be 0
					glueBuffer[glueLength++] = buffer[length];
				}
			}
			// decompose token, maintain alias if needed
			decompLength = 0;
			aliasLength = 0;
			boolean addToAlias;
			boolean addDecomposed = false;
			for(int i=0;i<length;i++){
				if(decomposer.isCombiningChar(buffer[i])){
					addDecomposed = true;
					continue; // skip 
				}
				
				addToAlias = true;
				if( ! options.exactCase )
					cl = Character.toLowerCase(buffer[i]);
				else{
					cl = buffer[i];
					// check additional (uppercase) character aliases
					if(cl == 'Ä' ){
						addToTokenAlias("Ae");
						addToAlias = false;
					} else if(cl == 'Ö'){
						addToTokenAlias("Oe");
						addToAlias = false;
					} else if(cl == 'Ü'){
						addToTokenAlias("Ue");
						addToAlias = false;
					} else if(cl == 'Ñ'){
						addToTokenAlias("Nh");
						addToAlias = false;
					} else if(cl == 'Å'){
						addToTokenAlias("Aa");
						addToAlias = false;
					} else if(cl == 'Ø'){
						addToTokenAlias("O");
						addToAlias = false;
					} else if(cl == 'Æ'){
						addToTokenAlias("AE");
						addToAlias = false;
					} else if(cl == 'Œ'){
						addToTokenAlias("OE");
						addToAlias = false;
					}
				}
				// special alias transliterations ä -> ae, etc ... 
				if(cl == 'ä' ){
					addToTokenAlias("ae");
					addToAlias = false;
				} else if(cl == 'ö'){
					addToTokenAlias("oe");
					addToAlias = false;
				} else if(cl == 'ü'){
					addToTokenAlias("ue");
					addToAlias = false;
				} else if(cl == 'ß'){
					addToTokenAlias("ss");
					addToAlias = false;
				} else if(cl == 'ñ'){
					addToTokenAlias("nh");
					addToAlias = false;
				} else if(cl == 'å'){
					addToTokenAlias("aa");
					addToAlias = false;
				} else if(cl == 'ø'){
					addToTokenAlias("o");
					addToAlias = false;
				} else if(cl == 'æ'){
					addToTokenAlias("ae");
					addToAlias = false;
				} else if(cl == 'œ'){
					addToTokenAlias("oe");
					addToAlias = false;
				} 
				
				// delete single quotes in aliases
				if(cl == '\''){
					addToTokenAlias("");
					addToAlias = false;
				}
								
				decomp = decompose(cl);
				// no decomposition
				if(decomp == null){
					if(decompLength<decompBuffer.length)
						decompBuffer[decompLength++] = cl;
					if(addToAlias && aliasLength!=0 && aliasLength<aliasBuffer.length)
						aliasBuffer[aliasLength++] = cl;
				} else{
					addDecomposed = true; // there are differences to the original version 
					for(decompi = 0; decompi < decomp.length; decompi++){
						if(decompLength<decompBuffer.length)
							decompBuffer[decompLength++] = decomp[decompi];
						if(addToAlias && aliasLength!=0 && aliasLength<aliasBuffer.length)
							aliasBuffer[aliasLength++] = decomp[decompi];
					}
				}			
			}
			// make the original buffered version
			// TODO: maybe do this optionally for some languages
			/* if(!("de".equals(language) && aliasLength!=0)){ 
				Token exact;
				if(exactCase)
					exact = new Token(
							new String(buffer, 0, length), start, start + length);
				else
					exact = new Token(
							new String(buffer, 0, length).toLowerCase(), start, start + length);
				if(addDecomposed && decompLength!=0)
					exact.setType("unicode");
				tokens.add(exact);
			} */
			if(templateLevel == 0)
				keywordTokens+=gap; // inc by gap (usually 1, can be more before paragraphs and sections)
			
			// add decomposed token to stream
			if(decompLength!=0){
				Token t = makeToken(new String(decompBuffer, 0, decompLength), start, start + length, true);
				/*if(!"de".equals(language)){
					t.setPositionIncrement(0);
					t.setType("transliteration");
				} */
				t.setPositionIncrement(gap);
				if(gap != 1)
					gap = 1; // reset token gap
				addToTokens(t);
			}
			// add alias (if any) token to stream
			if(aliasLength!=0){
				Token t = makeToken(new String(aliasBuffer, 0, aliasLength), start, start + length, false);
				t.setPositionIncrement(0);
				t.setType("transliteration");
				addToTokens(t);
			}
			length = 0;
			numberToken = false;
			
		}
		// keep track of "glue"
		if(options.highlightParsing && foundNonLetter){		
			if(glueLength == 0)
				glueStart = cur;
			if(glueLength < glueBuffer.length){
				glueBuffer[glueLength++] = c;  
			}
		}
		if(lastImgLinkCatWord)
			lastImgLinkCatWord = false; // always reset
	}

	/** empty the glue buffer */
	private final void flushGlue(){
		if(glueLength > 0 ){
			ExtToken t = makeGlueToken();
			if(t != null)
				addToTokens(t);
			glueLength = 0;
		}
	}
	
	/** Make regular text token */
	private final Token makeToken(String text, int start, int end, boolean addOriginal){
		if(!options.highlightParsing)
			return new Token(text,start,end);
		else{
			ExtToken.Type type = ExtToken.Type.TEXT;
			Position pos = getPosition();
			
			String original = new String(buffer,0,length);
			if(!addOriginal || original.equals(text))
				original = null;
			
			return new ExtToken(text,start,end,type,pos,original,text);
		}			
	}
	
	/** get current position in parsing */
	private final Position getPosition(){
		ExtToken.Position pos = Position.NORMAL;
		if(inRef)
			pos = Position.REFERENCE;
		else if(inImageCategoryInterwiki || lastImgLinkCatWord)
			pos = Position.IMAGE_CAT_IW;
		else if(inExternalLink)
			pos = Position.EXT_LINK;
		else if(templateLevel > 0)
			pos = Position.TEMPLATE;
		else if(inBulletin)
			pos = Position.BULLETINS;
		else if(tableLevel > 0)
			pos = Position.TABLE;
		else if(headings == 0 && templateLevel == 0 && keywordTokens <= KEYWORD_TOKEN_LIMIT)
			pos = Position.FIRST_SECTION;
		else if(inHeading)
			pos = Position.HEADING;		
		return pos;
	}

	/** tidy the glue buffer, and return the token */
	private final ExtToken makeGlueToken(){
		tempLength = 0;
		char last = '\0', top ='\0';
		ExtToken.Type type = ExtToken.Type.GLUE;		
		for(int i=0;i<glueLength;i++,last=lc){
			lc = glueBuffer[i];
			
			if(options.simplifyGlue){
				if(top == lc && lc !='.' && !Character.isLetterOrDigit(lc) && lc != '/') // last two are for external links, e.g. http://blahblah.com
					continue;
				if((last == '-' && lc == '{') || (last == '}' && lc == '-') || (i<glueLength-1 && lc=='-' && glueBuffer[i+1]=='{'))
					continue; 
				if(last == '|' && (lc=='-' || lc=='|'))
					continue;

				// work out breaks
				if(lc == '.' && !inLink)
					type = ExtToken.Type.SENTENCE_BREAK;
				if(last == '\n' && (lc == ':' || lc == '*' || lc=='#' || lc=='\n'))
					type = ExtToken.Type.SENTENCE_BREAK;

				if(lc == '{' || lc == '}' || lc == '[' || lc == ']' 
					|| lc == '<' || lc == '>' || lc=='*' || lc=='#' 
						|| lc == '\n' || lc == '\r' || lc == '=')
					continue; // forbidden chars

				if(lc == '\'' && (last == '\'' || (i<glueLength-1 && glueBuffer[i+1]=='\'')))
					continue; // more than one '
				
				// always put spaces before/after |
				if(tempLength+1 < tempBuffer.length){
					if(last != ' ' && lc =='|')
						tempBuffer[tempLength++] = ' ';
					if(last == '|' && lc != ' ')
						tempBuffer[tempLength++] = ' ';
				}
			}
			// work out minor breaks within sentences
			if(type != ExtToken.Type.SENTENCE_BREAK && isMinorBreak(lc)){
				type = ExtToken.Type.MINOR_BREAK;
			}
			if(tempLength < tempBuffer.length)
				tempBuffer[tempLength++] = lc;
			top = lc;
		}
		String glue = null;
		if(tempLength != 0)
			glue = new String(tempBuffer,0,tempLength);
		else
			glue = " ";
		
		if(inUrl) // always overrides everything else
			type = ExtToken.Type.URL;
		return new ExtToken(glue,glueStart,glueStart+glueLength,type,getPosition());
	}
	
	/** Check if char is a small break in the sentence structure, but not end of sentence */
	public static final boolean isMinorBreak(char ch){
		return ch == '\"' || ch == ':' || ch == ';' || ch == ',' || ch == '(' || ch == ')';
	}
	

	/** 
	 * Add a token to tokens list, in case of initial templates adds to special 
	 * nonContentToken list, and later reintroduces them 
	 * @param t
	 */
	private final void addToTokens(Token t){
		if(inRef){ // handle references
			if(firstRef){ // delimiter whole references from each other
				firstRef = false;
				t.setPositionIncrement(REFERENCE_GAP);
			}
			references.add(t);
			return;
		}
		if(!options.relocationParsing){
			tokens.add(t);
			return;
		}
		// and now, relocation parsing:
		if((templateLevel > 0 || inImageCategoryInterwiki) && keywordTokens < FIRST_SECTION_GAP){
			nonContentTokens.add(t);
			return;
		} else if(t.getPositionIncrement() == FIRST_SECTION_GAP){
			boolean first = true;
			for(Token tt : nonContentTokens){
				if(first){
					tt.setPositionIncrement(FIRST_SECTION_GAP);
					first = false;
				}
				tokens.add(tt); // re-add nonconent tokens
			}
			nonContentTokens.clear();
			t.setPositionIncrement(PARAGRAPH_GAP);
			keywordTokens += PARAGRAPH_GAP; 
		}
		tokens.add(t);
	}

	/**
	 * Tries to add the current letter (variable c) to the
	 * buffer, if it's not a letter, new token is created
	 */
	private final void addLetter(){
		try{			
			// add new character to buffer
			if(Character.isLetter(c) || (c == '\'' && cur>0 && Character.isLetter(text[cur-1]) && cur+1<textLength && Character.isLetter(text[cur+1]) ) || decomposer.isCombiningChar(c)){				
				if(numberToken) // we were fetching a number
					addToken(false);

				if(length == 0)
					start = cur;

				if(length < buffer.length)
					buffer[length++] = c;
			// add digits
			} else if(Character.isDigit(c)){				
				if(length == 0)
					start = cur;
				numberToken = true;

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
		int linkLevel = 0;
		for(lookup = cur; lookup<textLength ; lookup++ ){
			lc = text[lookup];
			switch(lc){
			case '[':
				if(lookup+1 < textLength && text[lookup+1]=='['){
					linkLevel++;
				}
				break;
			case '|':
				if(linkLevel == 0){
					lastPipe = lookup;
				}
				break;
			case ']':
				if(lookup+1 < textLength && text[lookup+1]==']'){
					if(linkLevel > 0){
						linkLevel--;
					} else{
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
				break;
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
	 * Decide if link that is currently being processed is to be appended to list of keywords
	 * 
	 * Criterion: link is within first KEYWORD_TOKEN_LIMIT words, before the 
	 * first heading and not within a template
	 * 
	 */
	protected boolean isGoodKeywordLink(){
		return headings == 0 && templateLevel == 0 && keywordTokens <= KEYWORD_TOKEN_LIMIT;		
	}
	
	/** When encountering '=' check if this line is actually a heading */ 
	private boolean checkHeadings() {
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
			if(start == end && start != 0 && start+end<endOfLine-cur && start>=2 && start<=5){
				headings++;
				headingText.add(deleteRefs(new String(text,cur+start,endOfLine-(cur+start+end))));
				return true;
			}
		}		
		return false;
	}
	
	/** Delete <ref></ref> text from a string */ 
	protected String deleteRefs(String str){
		int start;
		while((start = str.indexOf("<ref>")) != -1){
			int end = str.indexOf("</ref>",start+1);
			if(end == -1)
				break;
			str = str.substring(0,start)+((end+6<str.length())? str.substring(end+6) : "");
		}
		return str;
	}
	
	/** Check if starting from current position a string is matched */
	protected boolean matchesString(String target){
		if(cur + target.length() >= textLength)
			return false;
		for(lookup=cur,lc=0;lc<target.length();lookup++,lc++){
			if(target.charAt(lc) != Character.toLowerCase(text[lookup]))
				return false;
		}
		return true;
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
		int fetchStart = -1; // start index if link fetching
		ParserState returnToState = null; // for nested states
		
		if(tokens == null)
			tokens = new ArrayList<Token>();
		else 
			return tokens; // already parsed
		
		// before starting, make sure this is not a redirect
		//if(isRedirect())
		//	return tokens;
		
		for(cur = 0; cur < textLength; cur++ ){
			c = text[cur];			
			
			if(cur == 0){
				// special case for begin bulletins
				if(c=='*' || c==':' || c=='#'){
					gap = BULLETIN_GAP; 
					inBulletin = true;
					continue;
				}
			}
			
			// actions for various parser states
			switch(state){			
			case WORD:
				switch(c){
				case '\'':
					if(cur + 1 < textLength ){
						c1 = text[cur+1];
						if(Character.isLetter(c1) && length>0 && Character.isLetter(buffer[length-1])){
							addLetter(); // collect single quotes
							continue;
						}
					}
					addToken();
					continue; 
				case '=':
					addToken();
					if( checkHeadings() ){
						inHeading = true;
					}
					if(options.relocationParsing){
						if(headings == 1)
							gap = FIRST_SECTION_GAP;
						else if(headings > 1)
							gap = SECTION_GAP;
					}
					continue;
				case '|':
					addToken();
					// table params
					if(templateLevel==0 && cur + 1 < textLength){
						// check for "|| colspan = 3|" kind of syntax
						if(text[cur+1] == '|'){
							table_col : for( lookup = cur + 2 ; lookup < textLength ; lookup++ ){
								switch(text[lookup]){
								case '\n': break table_col;
								case '|': cur = lookup; break table_col;
								}
							} 
						} else if(text[cur+1] == '-'){
							// |- align="center" kind of syntax, ignore till end of line
							for(lookup = cur+2 ; lookup < textLength ; lookup++){
								if(text[lookup] == '\n'){
									cur = lookup-1;
									break;
								}
							}
						}
					}
					continue;
				case '\n':
					if(inHeading){
						flushGlue();
						inHeading = false;
					}
					addToken();
					// check table end and table params
					if(templateLevel==0 && cur + 1 < textLength){
						if(cur+2 < textLength && text[cur+1]=='|' && text[cur+2]=='}'){
							if(tableLevel > 0)
								tableLevel--;
						} else{
							switch(text[cur+1]){
							case '|': 
							case '!':
								boolean seenNonPipe = false; // seen any other character than pipe
								table_params : for( lookup = cur + 2 ; lookup < textLength ; lookup++ ){
									switch(text[lookup]){
									case '\n': break table_params;
									case '|': 
										if(seenNonPipe){
											cur = lookup; 
											break table_params;
										} 
										break;
									default: 
										if(!seenNonPipe) 
											seenNonPipe = true;
									}
								}
							}
						}
					}
					// adjust gaps & figure out bulletins
					gap = 1; inBulletin = false;
					if(cur + 1 < textLength){
						switch(text[cur+1]){
						case '\n': 
							if(options.relocationParsing)
								gap = PARAGRAPH_GAP; 
							break;
						case '*': case ':': case '#': 
							if(options.relocationParsing)
								gap = BULLETIN_GAP; 
							inBulletin = true; 
							break;
						}
					}					
					continue;
				case '.':
				case '(':
				case ')':
				case '?':
				case '!':
				case ':':
				case ';':
					addToken();
					if(options.relocationParsing && gap == 1)
						gap = SENTENCE_GAP;
					continue;
				case '<':
					addToken();
					if(matchesString("<ref")){ // TODO: should probably be a regexp <ref.*?>
						inRef = true;
						firstRef = true;
					}					
					if(matchesString("</ref>")){
						flushGlue();
						inRef = false;
						gap = 1;
					}					
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
				case '}':
					addToken();
					if(cur + 1 < textLength )
						c1 = text[cur+1];
					else 
						continue; // last char in stream
					
					if(c1 == '}' && templateLevel>0){ // register end of previously started template
						state = ParserState.TEMPLATE_END;
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
				inExternalLink = true;
				inUrl = true;
				switch(c){
				case ' ':
					flushGlue();
					addToken();
					state = ParserState.EXTERNAL_WORDS;
					continue;
				case ']':
					addToken();
					state = ParserState.WORD;
					continue;
				default:
					addToken(); // for glue
				}
				continue;
			case EXTERNAL_WORDS:
				inUrl = false;
				if(c == ']'){
					addToken();
					state = ParserState.WORD;
					inExternalLink = false;
				} else
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
						inImageCategoryInterwiki = true;
						continue;
					} else if(isCategory(prefix)){
						cur = semicolonInx;
						fetch = FetchState.CATEGORY;
						state = ParserState.LINK_FETCH;
						fetchStart = cur;
						inImageCategoryInterwiki = true;
						continue;
					} else if(isInterwiki(prefix)){
						cur = semicolonInx;
						fetch = FetchState.INTERWIKI;
						state = ParserState.LINK_FETCH;
						inImageCategoryInterwiki = true;
						continue;
					}
				}
				// add this link to keywords?
				if(isGoodKeywordLink()){
					fetch = FetchState.KEYWORD;
					state = ParserState.LINK_KEYWORD;
					if(pipeInx != -1)
						cur = pipeInx; // ignore up to pipe
					else
						cur--; // return the first character of link 
					continue;
				}
				
				// no semicolon, search for pipe:
				if(pipeInx != -1){
					cur = pipeInx; // found pipe, ignore everything up to pipe
					continue;
				} else{
					addLetter();
					continue;
				}
			case LINK_KEYWORD:
				if(keywordLen < keywordBuf.length && c!=']'){
					keywordBuf[keywordLen++] = c;
				}
				// fall-thru
			case LINK_WORDS:
				if(!inLink)
					inLink = true;
				if(c == '['){
					addToken();
					if(cur + 1 < textLength )
						c1 = text[cur+1];
					else 
						continue; // last char in stream					
					// wiki-link
					if(c1 == '['){
						cur++; // skip this char
						returnToState = state;
						state = ParserState.LINK_BEGIN;						 
						continue;
					} else{ // external link
						state = ParserState.EXTERNAL_URL;
						continue;
					}
				} else if(c == ']'){
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
					flushGlue();
					inLink = false;
					if(returnToState != null){
						state = returnToState; // nested states
						returnToState = null;
					} else{
						state = ParserState.WORD;
						lastImgLinkCatWord = inImageCategoryInterwiki;
						inImageCategoryInterwiki = false;
					}
					
					switch(fetch){
					case WORD:						
						// don't add token to get syntax like [[bean]]s
						continue;
					case CATEGORY:						
						categories.add(new String(buffer,0,length).replace("_"," "));
						length = 0;
						fetch = FetchState.WORD;
						// index category words
						if(fetchStart != -1){
							cur = fetchStart;
							state = ParserState.CATEGORY_WORDS;
						} else
							System.err.print("ERROR: Inconsistent parser state, attepmted category backtrace for uninitalized fetchStart.");
						fetchStart = -1;
						continue;
					case INTERWIKI:
						interwikis.put(prefix,
								         new String(buffer,0,length));
						length = 0;
						fetch = FetchState.WORD;
						continue;
					case KEYWORD:
						keywords.add(new String(keywordBuf,0,keywordLen));
						keywordLen = 0;
						fetch = FetchState.WORD;
						continue;						
					}
				} else{
					// bad syntax, ignore any categories, etc.. 
					state = ParserState.WORD;
					fetch = FetchState.WORD;
					continue;
				}
				continue;
			case CATEGORY_WORDS:
				if(c == ']'){
					state = ParserState.WORD; // end of category
					continue;
				} else if(c == '|'){ // ignore everything up to ]
					for( lookup = cur + 1 ; lookup < textLength ; lookup++ ){
						if(text[lookup] == ']'){ // we know the syntax is correct since we checked it in LINK_FETCH
							state = ParserState.WORD;
							cur = lookup;
							break;
						}
					}
					continue;
				}
				addLetter();
				continue;
			case TABLE_BEGIN:
				tableLevel++;
				// ignore everything up to the newspace, since they are table display params
				while(cur < textLength && (text[cur]!='\r' && text[cur]!='\n'))
					cur++;
				state = ParserState.WORD;
				continue;
			case TEMPLATE_BEGIN:
				state = ParserState.WORD; // default next state in case of bad syntax
				// ignore name of the template, index parameters
				template_lookup: for( lookup = cur ; lookup < textLength ; lookup++ ){
					switch(text[lookup]){
					case '|':
						templateLevel++;
						state = ParserState.WORD;
						cur = lookup;
						break template_lookup;
					case '}':
						templateLevel++;
						state = ParserState.TEMPLATE_END;
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
			case TEMPLATE_END:
				if(c == '}'){
					if(templateLevel > 0)
						templateLevel--;
					state = ParserState.WORD;
					continue;
				} else // not really end of template
					state = ParserState.WORD;
				continue;
			default:
				System.out.println("Parser Internal error, near '"+c+"' at index "+cur);
			}		
		}
		addToken(false);
		flushGlue();
		if(nonContentTokens.size() != 0){
			boolean first = true;
			// flush any remaning tokens from initial templates, etc..
			for(Token tt : nonContentTokens){
				if(first){
					tt.setPositionIncrement(FIRST_SECTION_GAP);
					first = false;
				}
				tokens.add(tt);
			}
			nonContentTokens.clear();
		}
		// add references to end
		if(references.size() != 0){
			for(Token tt : references){
				tokens.add(tt);
			}
		}
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

	public ArrayList<String> getCategories() {
		return categories;
	}

	public HashMap<String, String> getInterwikis() {
		return interwikis;
	}

	public ArrayList<Token> getTokens() {
		return tokens;
	}

	public HashSet<String> getKeywords() {
		return keywords;
	}
	
	public ArrayList<String> getHeadingText() {
		return headingText;
	}
	
	/** Delete everything that is not being indexes, decompose chars */
	public static String stipTitle(String title){
		UnicodeDecomposer decomposer = UnicodeDecomposer.getInstance();
		char[] str = title.toCharArray();
		char[] buf = new char[256];
		int len = 0;
		for(int i=0;i<str.length;i++){
			char ch = str[i];
			if(ch == ':' || ch == '(' || ch == ')' || ch =='[' || ch == ']' || ch == '.' || ch == ',' 
				|| ch == ';' || ch == '"' || ch=='-' || ch=='+' || ch=='*' || ch=='!' || ch=='~' || ch=='$' 
					|| ch == '%' || ch == '^' || ch == '&' || ch == '_' || ch=='=' || ch=='|' || ch=='\\'){
				if(len > 0 && buf[len-1]!=' '){
					if(len >= buf.length){ // extend buf
						char[] n = new char[buf.length*2];
						System.arraycopy(buf,0,n,0,buf.length);
						buf = n;
					}
					buf[len++] = ' '; // replace the special char with space
				}
			} else{
				char[] decomp = decomposer.decompose(ch);
				if(decomp == null){
					// no decomposition add char, but don't double spaces
					if(ch!=' ' || (len>0 && buf[len-1]!=' ')){
						if(len >= buf.length){ 
							char[] n = new char[buf.length*2];
							System.arraycopy(buf,0,n,0,buf.length);
							buf = n;
						}
						buf[len++] = ch;
					}
				} else{
					// add decomposed chars
					for(int j = 0; j < decomp.length; j++){
						if(len >= buf.length){ 
							char[] n = new char[buf.length*2];
							System.arraycopy(buf,0,n,0,buf.length);
							buf = n;
						}
						buf[len++] = decomp[j];
					}
				}					
			}
		}
		return new String(buf,0,len);	
	}	
}
