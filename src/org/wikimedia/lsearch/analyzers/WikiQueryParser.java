package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Collection;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.List;
import java.util.Map.Entry;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.LogTransformScore;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.PositionalOptions;
import org.apache.lucene.search.PositionalQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.RelevanceQuery;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.BooleanClause.Occur;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.Wildcards;

/**
 * Parser for wiki query syntax
 * 
 * @author rainman
 *
 */
public class WikiQueryParser {
	private static final int MAX_TERM_LEN = 255;
	private final char[] buffer = new char[MAX_TERM_LEN+1];
	private int length; // length of the token in the buffer
	private Analyzer analyzer;	
	private char[] text; // text that is being parsed
	private int cur; // current position in text
	private int prev_cur; // cur before parsing this token (for backToken())
	private char c; // current character
	private int queryLength; // length of the parsed text
	private int lookup; // lookahead index
	private String currentField; // current field
	private String defaultField; // the default field value
	private float defaultBoost = 1;
	private float defaultAliasBoost = ALIAS_BOOST;
	protected enum TokenType {WORD, FIELD, AND, OR, EOF };
	
	private TokenStream tokenStream; 
	private ArrayList<Token> tokens; // tokens from analysis
	protected ArrayList<String> words, wordsFromParser;
	protected Term[] highlightTerms = null;
	
	/** sometimes the fieldsubquery takes the bool modifier, to retrieve it, use this variable,
	 *  this will always point to the last unused bool modifier */
	BooleanClause.Occur explicitOccur = null;  
	
	/** Wheather to include aliases during title rewrite */
	protected boolean disableTitleAliases;
	
	/** boost for alias words from analyzer */
	public static float ALIAS_BOOST = 0.5f; 
	/** boost for title field */
	public static float TITLE_BOOST = 6;	
	public static float TITLE_ALIAS_BOOST = 0.2f;
	public static float TITLE_PHRASE_BOOST = 2;
	public static float STEM_TITLE_BOOST = 0.8f;	
	public static float STEM_TITLE_ALIAS_BOOST = 0.4f;
	public static float ALT_TITLE_BOOST = 4;
	public static float ALT_TITLE_ALIAS_BOOST = 0.4f;
	public static float KEYWORD_BOOST = 0.02f;
	public static float CONTENTS_BOOST = 0.2f;
	
	// main phrase stuff:
	public static int MAINPHRASE_SLOP = 100;
	public static float MAINPHRASE_BOOST = 2f;
	public static float RELATED_BOOST = 4f;	
	public static int RELATED_SLOP = 0;
	public static float ALTTITLE_RELEVANCE_BOOST = 2f;
	// additional to main phrase:
	public static float ADD_STEMTITLE_BOOST = 2;
	public static float ADD_ALTTITLE_BOOST = 1;
	public static int ADD_ALTTITLE_SLOP = 10;
	public static float ADD_RELATED_BOOST = 2;
	
	public static float WHOLE_TITLE_BOOST = 8f;
	public static float EXACT_CONTENTS_BOOST = 1f;
	public static float ANCHOR_BOOST = 0.02f;
	public static float WILDCARD_BOOST = 2f;
	
	public static boolean ADD_STEM_TITLE = true;
	public static boolean ADD_TITLE_PHRASES = true;
	
	/** Policies in treating field names:
	 * 
	 * LEAVE - don't mess with field rewriting
	 * IGNORE - convert all field names to contents (except category)
	 * REWRITE -  rewrite (help:searchterm) => (+namespace:12 contents:searchterm)
	 */
	public enum NamespacePolicy { LEAVE, IGNORE, REWRITE };
	/** Rewritten namespace queries. prefix => query */
	static protected Hashtable<String,Query> namespaceQueries = null;
	/** The 'all' keyword */
	static protected String namespaceAllKeyword = null;
	/** Prefixes and associated filters. prefix -> filter */
	static protected Hashtable<String,NamespaceFilter> namespaceFilters = null;
	/** nsfilter -> prefix (reverse table to namespaceFilters */
	static protected Hashtable<NamespaceFilter,String> namespacePrefixes = null;
	private String defaultNamespaceName;
	private Query namespaceRewriteQuery;
	private NamespacePolicy namespacePolicy;
	protected NamespaceFilter defaultNamespaceFilter;
	protected static GlobalConfiguration global=null;
	protected FieldBuilder.BuilderSet builder;
	protected FieldNameFactory fields;
	protected FilterFactory filters;
	protected HashSet<String> stopWords;
	protected Wildcards wildcards = null;
	protected IndexId iid;
	
	/** default operator (must = AND, should = OR) for boolean queries */
	public BooleanClause.Occur boolDefault = BooleanClause.Occur.MUST;
	
	/** Init namespace queries */
	protected void initNamespaces(){
		if(namespaceQueries != null)
			return;
		if(global == null)
		 global = GlobalConfiguration.getInstance();
		namespaceAllKeyword = global.getNamespacePrefixAll();
		namespaceQueries = new Hashtable<String,Query>();
		namespacePrefixes = new Hashtable<NamespaceFilter,String>();
		namespaceFilters = global.getNamespacePrefixes();
		for(Entry<String,NamespaceFilter> prefix : namespaceFilters.entrySet()){
			namespaceQueries.put(prefix.getKey(),generateRewrite(prefix.getValue()));
			namespacePrefixes.put(prefix.getValue(),prefix.getKey());
		}
	}
	
	/**
	 * Construct using default policy (LEAVE), without any namespace rewriting
	 * @param field   default field name
	 * @param analyzer
	 */
	public WikiQueryParser(String field, Analyzer analyzer, FieldBuilder.BuilderSet builder, Collection<String> stopWords){
		this(field,(NamespaceFilter)null,analyzer,builder,NamespacePolicy.LEAVE,stopWords);
	}
	
	/**
	 * Construct with default field (e.g. contents), with default namespace
	 * (e.g. main), and with analyzer and namespace policy
	 * @param field
	 * @param namespace
	 * @param analyzer
	 * @param nsPolicy
	 */
	public WikiQueryParser(String field, String namespace, Analyzer analyzer, FieldBuilder.BuilderSet builder, NamespacePolicy nsPolicy, Collection<String> stopWords){
		this(field,new NamespaceFilter(namespace),analyzer,builder,nsPolicy,stopWords);
	}
	
	public WikiQueryParser(String field, String namespace, Analyzer analyzer, FieldBuilder.BuilderSet builder, NamespacePolicy nsPolicy){
		this(field,new NamespaceFilter(namespace),analyzer,builder,nsPolicy,null);
	}
	
	public WikiQueryParser(String field, NamespaceFilter nsfilter, Analyzer analyzer, FieldBuilder.BuilderSet builder, NamespacePolicy nsPolicy, Collection<String> stopWords){
		defaultField = field;		
		this.analyzer = analyzer;
		this.builder = builder;
		this.fields = builder.getFields();
		this.filters = builder.getFilters();
		this.iid = filters.getIndexId();
		tokens = new ArrayList<Token>();
		this.namespacePolicy = nsPolicy;
		disableTitleAliases = true;
		initNamespaces();
		this.stopWords = new HashSet<String>();
		if(stopWords != null)
			this.stopWords.addAll(stopWords);
		this.defaultNamespaceFilter=nsfilter;
		if(nsfilter != null){
			namespaceRewriteQuery = generateRewrite(nsfilter);			
			if(namespaceRewriteQuery != null && namespacePrefixes.containsKey(nsfilter))
				defaultNamespaceName = namespacePrefixes.get(nsfilter);
			else
				defaultNamespaceName = null;
		}
		else{
			namespaceRewriteQuery = null;
			defaultNamespaceName = null;
		}
	}
	
	/** Generate a rewrite query for a collection of namespaces */
	public static Query generateRewrite(NamespaceFilter nsfilter){
		if(nsfilter.cardinality() == 0)
			return null;
		else if(nsfilter.cardinality() == 1)
			return new TermQuery(new Term("namespace",Integer.toString(nsfilter.getNamespace())));
		
		BooleanQuery bq = new BooleanQuery();
		BitSet bs = nsfilter.getIncluded();
		// iterate over set bits
		for(int i=bs.nextSetBit(0); i>=0; i=bs.nextSetBit(i+1)){
			bq.add(new TermQuery(new Term("namespace",Integer.toString(i))),
					BooleanClause.Occur.SHOULD);
			bq.add(new TermQuery(new Term("redirect_namespace",Integer.toString(i))),
					BooleanClause.Occur.MUST_NOT);
		}
		return bq;
	}
	
	/** Generate a rewrite query for a collection of namespaces */
	public static Query generateRedirectRewrite(NamespaceFilter nsfilter){
		if(nsfilter.cardinality() == 0)
			return null;
		else if(nsfilter.cardinality() == 1)
			return new TermQuery(new Term("redirect_namespace",Integer.toString(nsfilter.getNamespace())));
		
		BooleanQuery bq = new BooleanQuery();
		BitSet bs = nsfilter.getIncluded();
		// iterate over set bits
		for(int i=bs.nextSetBit(0); i>=0; i=bs.nextSetBit(i+1)){
			bq.add(new TermQuery(new Term("redirect_namespace",Integer.toString(i))),
					BooleanClause.Occur.SHOULD);
		}
		return bq;
	}
	
	/** 
	 * Get a hashset of namespace numbers for fields that are
	 * valid namespace keys. 
	 * @param queryText
	 * @return
	 */
	public HashSet<NamespaceFilter> getFieldNamespaces(String queryText){
		HashSet<String> fields = getFields(queryText);
		HashSet<NamespaceFilter> ret = new HashSet<NamespaceFilter>();
		for(String field : fields){
			field = field.toLowerCase();
			if(namespaceFilters.containsKey(field))
				ret.add(namespaceFilters.get(field));
			else if(field.equals(namespaceAllKeyword))
				ret.add(new NamespaceFilter());
			else if(field.equals(defaultField) && defaultNamespaceFilter != null)
				ret.add(defaultNamespaceFilter);
			else if(field.startsWith("[")){
				ret.add(new NamespaceFilter(field.substring(1,field.length()-1)));
			}
		}
		
		return ret;
	}
	
	/** get all fields that appear in a query */
	public HashSet<String> getFields(String queryText){
		int level = 0; // parenthesis count
		HashSet<String> fields = new HashSet<String>();
		int fieldLevel = -1;
		TokenType tokenType;
		boolean inPhrase = false;
		
		reset();
		
		queryLength = queryText.length(); 
		text = queryText.toCharArray();
						
		for(cur = 0; cur < text.length; cur++ ){
			c = text[cur];
			if(c == '"'){
				inPhrase = !inPhrase;
				if(inPhrase && fieldLevel == -1)
					fields.add(defaultField);
			}
			
			if(inPhrase)
				continue; // ignore stuff between ""
			
			if(c == ')'){
				level--;
				if(level < fieldLevel)
					fieldLevel = -1;
				continue;
			} else if(c == '('){
				level++;	
				continue;
			} else if(fieldLevel != -1 && level>fieldLevel)
				continue;
			
			if(Character.isLetterOrDigit(c)){
				tokenType = fetchToken();
				if(tokenType == TokenType.FIELD){
					fieldLevel = level;
					fields.add(new String(buffer,0,length));
				} else if(tokenType == TokenType.WORD){
					if(fieldLevel == -1)
						fields.add(defaultField);
				}
			} else if(c == '['){
				if(fetchGenericPrefix()){
					fieldLevel = level;
					fields.add(new String(buffer,0,length));
				}
			}
		}
		
		
		return fields;
	}
	
	/** Find and delete all valid prefixes, return search terms in tokens */
	public ArrayList<Token> tokenizeBareText(String queryText){
		int level = 0; // parenthesis count
		int fieldLevel = -1;
		TokenType tokenType;
		boolean inPhrase = false;
		
		ArrayList<Token> ret = new ArrayList<Token>();
		
		reset();
		
		queryLength = queryText.length(); 
		text = queryText.toCharArray();
		String oldDefault = defaultField;
		defaultField = "title"; // no stemming
						
		for(cur = 0; cur < text.length; cur++ ){
			c = text[cur];
			if(c == '"'){
				inPhrase = !inPhrase;
				if(inPhrase)
					length = 0;
				else{ // end of phrase
					int start = cur - length;
					analyzeBuffer();					
					for(Token t : tokens){
						if(t.type().equals("word"))
							ret.add(new Token(t.termText(),start+t.startOffset(),start+t.endOffset(),"phrase"));
					}
				}
			}
			
			if(inPhrase){
				buffer[length++] = c;
				continue;
			}
			
			if(c == ')'){
				level--;
				if(level < fieldLevel)
					fieldLevel = -1;
				continue;
			} else if(c == '('){
				level++;	
				continue;
			} else if(fieldLevel != -1 && level>fieldLevel)
				continue;
			
			if(Character.isLetterOrDigit(c) || c=='?' || c=='*'){
				int start = cur;
				tokenType = fetchToken();
				if(tokenType == TokenType.WORD && (start==0 || text[start-1]!='-') && !bufferIsWildCard()){
					analyzeBuffer();
					for(Token t : tokens){
						if(t.getPositionIncrement()!=0)
							ret.add(new Token(t.termText(),start+t.startOffset(),start+t.endOffset(),"word"));
					}					
				}
			} else if(c == '['){
				fetchGenericPrefix();
			}
		}
		
		defaultField = oldDefault;
		
		return ret;
		
	}
	
	/** rewrite field name (e.g. help) into a term query like namespace:12 */
	private Query getNamespaceQuery(String fieldName){
		if(fieldName == null || namespacePolicy != NamespacePolicy.REWRITE)
			return null;
		
		Query q;
		if((q = namespaceQueries.get(fieldName))!=null){
			return q;
		} else if(fieldName.startsWith("[")){
			return generateRewrite(new NamespaceFilter(fieldName.substring(1,fieldName.length()-1)));
		} else
			return null;
	}
	
	/**
	 * Fetch token into <code>buffer</code> starting from current position (<code>cur</code>)
	 * 
	 * @return type of the token in buffer
	 */
	private TokenType fetchToken(){
		char ch;
		prev_cur = cur;
		for(length = 0; cur < queryLength; cur++){
			ch = text[cur];
			if(length == 0 && ch == ' ')
				continue; // ignore whitespaces
			
			if(ch == '\'')
				continue; // ignore single quotes (it's -> its)
			
			// pluses and minuses, underscores can be within words (to prevent to be missinterpeted), *,? are for wildcard queries
			if(!Character.isWhitespace(ch) && ch != ':' && ch != '(' && ch != ')' && ch !='[' && ch != ']' && ch != ',' && ch != ';' && ch != '"'){
				if(length<buffer.length)
					buffer[length++] = ch;
			} else{
				cur--; // position before the nonletter character
				break;
			}
		}
		if(length == 0)
			return TokenType.EOF;
		
		// check for keywords
		if(length == 3 && buffer[0]=='A' && buffer[1]=='N' && buffer[2]=='D')
			return TokenType.AND;
		else if(length == 2 && buffer[0]=='O' && buffer[1]=='R')
			return TokenType.OR;
		
		
		// lookahead to see if this is a field
		for(lookup = cur+1; lookup < queryLength; lookup++ ){
			ch = text[lookup];
			if(ch == ' ')
				continue;
			else if(ch == ':'){				
				// check if it's a valid field
				String f = new String(buffer,0,length);
				if(f.equals(namespaceAllKeyword) || f.equals("incategory") || namespaceFilters.containsKey(f) || namespacePolicy == NamespacePolicy.LEAVE){
					cur = lookup;
					return TokenType.FIELD;
				} else
					break;
			} else
				break;
		}
		
		return TokenType.WORD; 
	}
	
	/**
	 * Fetches prefixes like [0,1,2] (in [0,1,2]:query) 
	 * 
	 * @return true if search prefixes is successfully fetched
	 */
	private boolean fetchGenericPrefix(){
		char ch;
		prev_cur = cur;
		if(text[cur] != '[')
			return false; // sanity check
		buffer[0] = '[';
		for(length = 1, cur++; cur < queryLength; cur++){
			ch = text[cur];
			if(Character.isDigit(ch) || ch ==',')
				buffer[length++] = ch;
			else if(ch == ']' && cur+1 < queryLength && text[cur+1]==':'){
				cur++; // position on :
				buffer[length++] = ch;
				return true;
			} else
				break; // bad format, only numbers and commas are allowed
		}
		cur = prev_cur; // traceback
		return false;
		
	}
	
	/** Go back one token */
	private void backToken(){
		cur = prev_cur;
	}

	/** make <code>tokenStream</code> from <code>buffer</code> via analyzer */
	private void analyzeBuffer(){
		String analysisField = defaultField;
		tokenStream = analyzer.tokenStream(analysisField, 
				new String(buffer,0,length));
		
		Token token;
		tokens.clear();
		try{
			while((token = tokenStream.next()) != null){
				tokens.add(token);
			}
		} catch (IOException e){
			e.printStackTrace();
		}		
	}
	
	
	/** Make term form lucene token */
	private Term makeTerm(Token token){
		return makeTerm(token.termText());
	}
	
	/** Make term form <code>buffer</code> */
	private Term makeTerm(){
		return makeTerm(new String(buffer,0,length));
	}
	
	/** Make a lucene term from string */
	private Term makeTerm(String t){
		if(currentField == null)
			return new Term(defaultField,builder.isExactCase()? t : t.toLowerCase());
		else if(!currentField.equals("incategory") && 
				(namespacePolicy == NamespacePolicy.IGNORE || 
						namespacePolicy == NamespacePolicy.REWRITE))
			return new Term(defaultField,t);
		else if(currentField.equals("incategory")){
			String norm = t.replace("_"," "); // bug 10822
			return new Term("category",builder.isExactCase()? norm : norm.toLowerCase());
		} else
			return new Term(currentField,t);
	}
	
	/** Parses a phrase query (i.e. between ""), the cur
	 *  should be set to the char just after the first
	 *  quotation mark
	 *   
	 * @return a query, or null if the query is empty
	 */
	private PhraseQuery parsePhrase(){
		PhraseQuery query = null;
		
		length = 0;
		for(; cur < queryLength ; cur++ ){
			// end of phrase query
			if(text[cur] == '"')
				break;
			else if(length < buffer.length)
				buffer[length++] = text[cur];
		}
		if(length != 0){
			query = new PhraseQuery();
			// if it's a category don't tokenize it, we want whole category name
			if(currentField!=null && currentField.equals("incategory"))
				query.add(makeTerm()); 
			else{
				analyzeBuffer();
				for(Token token : tokens){
					if(token.type().equals("word")) // ignore aliases and stemmed words
						query.add(makeTerm(token));
				}
				query.setBoost(defaultBoost);
			}			
			return query;	
		} else
			return null;
	}
	
	final private Query parseClause(int level){
		return parseClause(level,false,null);
	}
	
	private final boolean needsRewrite(){
		return namespaceRewriteQuery != null && namespacePolicy == NamespacePolicy.REWRITE; 
	}
	
	/** Parses a clause:  (in regexp-like notation)
	 * 
	 *  Clause := ([+-]? (<field>:)? <term> | [AND,OR] | \( Clause \) )+
	 *  
	 *  @param level - level of recurstion
	 *  @param returnOnFieldDef - if this is a nested field rewrite call
	 * @return
	 */
	private Query parseClause(int level, boolean returnOnFieldDef, String topFieldName){
		// the whole query
		Query query = null;		
		// reference to boolean query if one is constructed
		BooleanQuery boolquery = null;
		BooleanClause.Occur occur = boolDefault;
		// the first query
		BooleanClause.Occur firstOccur = boolDefault;		
		// state
		TokenType tokenType;
		Query subquery = null;				
		boolean definedField = false;
		boolean definedExplicitField = false;
		Query fieldQuery = null; // the namespace term, e.g. namespace:0
		Query fieldsubquery = null; // e.g. 'all:something else' will be parsed 'something else' 
		
		// assume default namespace value on rewrite
		if(!returnOnFieldDef && currentField == null && needsRewrite()){
			fieldQuery = namespaceRewriteQuery; 
		}
		
		mainloop: for( ; cur < queryLength; cur++ ){
			c = text[cur];
			
			if(c == ' ')
				continue;
			
			// terms, fields
			if(Character.isLetterOrDigit(c) || c=='.' || c == '[' ||  c=='*' || c=='?'){
				// check for generic namespace prefixes, e.g. [0,1]:
				if(c == '['){
					if(fetchGenericPrefix())
						tokenType = TokenType.FIELD;
					else
						continue;
				} else // fetch next token					
					tokenType = fetchToken();
				
				switch(tokenType){
				case FIELD:
					// this is where the function returns if called from the
					// next if (i.e. some 10 lines down)
					if(returnOnFieldDef){
						String newfield = new String(buffer,0,length); 
						if(!newfield.equals("incategory") && !newfield.equals(topFieldName)){
							backToken(); cur--;
							break mainloop;
						}
					}
					if(currentField == null || definedExplicitField){
						// set field name
						currentField = new String(buffer,0,length);
						if((defaultNamespaceName!=null && currentField.equals(defaultNamespaceName)) || currentField.equals(defaultField)){
							currentField = null;
							break; // repeated definition of field, ignore
						}
						definedExplicitField = true;
						
						fieldQuery = getNamespaceQuery(currentField); // depending on policy rewrite this field
						if(fieldQuery != null){
							// save field, we will need it to be set to null to fetch categories
							String myfield = currentField;
							currentField = null;
							// fetch the clause until the next field
							fieldsubquery = parseClause(level+1,true,myfield);
							currentField = myfield;
						}
					} else{
						// nested field names, don't allow, just add to query
						analyzeBuffer();
						subquery = makeQueryFromTokens(occur);
					}
					break;
				case WORD:
					if(fieldQuery != null){
						backToken();
						String myfield = (topFieldName != null)? topFieldName : (currentField !=null)? currentField : (defaultNamespaceName!=null)? defaultNamespaceName : defaultField; 
						fieldsubquery = parseClause(level+1,true,myfield);
					} else{
						analyzeBuffer();
						subquery = makeQueryFromTokens(explicitOccur!=null? explicitOccur : occur);
					}
					break;
				case AND:
					firstOccur = BooleanClause.Occur.MUST;
					occur = BooleanClause.Occur.MUST;
					if(returnOnFieldDef)
						explicitOccur = BooleanClause.Occur.MUST;
					continue;
				case OR:
					firstOccur = BooleanClause.Occur.SHOULD;
					occur = BooleanClause.Occur.SHOULD;
					if(returnOnFieldDef)
						explicitOccur = BooleanClause.Occur.SHOULD;
					continue;
				case EOF:
					break mainloop;					
				}				
			}
			
			// field subquery, the fetched clause while doing rewriting
			if(fieldsubquery != null){
				// this not the first field definition at this level
				if(definedField){
					// embed the old query
					BooleanQuery bq = new BooleanQuery();
					bq.add(query,BooleanClause.Occur.SHOULD);
					query = boolquery = bq;
				}
				
				BooleanQuery bq = new BooleanQuery();
				bq.add(fieldQuery,BooleanClause.Occur.MUST);
				bq.add(fieldsubquery,BooleanClause.Occur.MUST);
				
				// add to existing queries
				if(boolquery != null)
					boolquery.add(bq,BooleanClause.Occur.SHOULD);
				else if(query != null){
					boolquery = new BooleanQuery();
					boolquery.add(query,firstOccur);
					boolquery.add(bq,BooleanClause.Occur.SHOULD);
					query = boolquery;
				} else
					query = bq;
				
				fieldQuery = null;
				definedField = true;
				fieldsubquery = null;
			}
			
			// modifiers
			switch(c){
			case '+':
				occur = BooleanClause.Occur.MUST;
				if(returnOnFieldDef)
					explicitOccur = BooleanClause.Occur.MUST; 
				continue;
			case '-':
				occur = BooleanClause.Occur.MUST_NOT;
				if(returnOnFieldDef)
					explicitOccur = BooleanClause.Occur.MUST_NOT;
				continue;
			case '"':
				cur++;
				subquery = parsePhrase();
				break;
			case '(':
				cur++;
				subquery = parseClause(level+1);
				break;
			case ')':
				if(level > 0){
					break mainloop;
				}
				continue;
			}

			// if we fetched some tokens or a subquery add it to main query
			if(subquery != null){			
				if(query == null){
					query = subquery;
					firstOccur = occur; // save the boolean modifier
					occur = boolDefault; // return to default
				}
				else{
					if(explicitOccur != null)
						occur = explicitOccur;
					if(boolquery == null){
						// we have found the second term, make boolean query
						boolquery = new BooleanQuery();
						boolquery.add(query,firstOccur);
						boolquery.add(subquery,occur);
						query = boolquery;
					} else{
						boolquery.add(subquery,occur);
					}
					occur = boolDefault; // return to default
					explicitOccur = null;
				}
				subquery = null;
			}
		}
		
		if(definedExplicitField)
			currentField = null;
		return query;
	}
	
	/** return true if buffer is wildcard  */
	private boolean bufferIsWildCard(){
		if(length < 1)
			return false;
		boolean wild = false;
		int index = -1;
		for(int i=0;i<length;i++){
			if(buffer[i] == '*' || buffer[i] == '?'){
				wild = true;
				index = i;
				break;
			}
		}
		// check if it's a valid wildcard
		if(wild){
			if((buffer[0] == '*' || buffer[0] == '?') && (buffer[length-1]=='*' || buffer[length-1]=='?'))
				return false; // don't support patterns like *a*
			if(index == length-1 && buffer[index]=='?')
				return false; // probably just an ordinary question mark
			for(int i=0;i<length;i++){
				if(Character.isLetterOrDigit(buffer[i]))
					return true; // +card :P
			}
		}
		return false;
	}
	
	/** 
	 * Constructs either a termquery or a boolean query depending on
	 * analysis of the fetched token. A single "word" might be analyzed
	 * into many tokens, and some of them might be aliases 
	 * @return
	 */
	private Query makeQueryFromTokens(BooleanClause.Occur toplevelOccur){
		BooleanQuery bq = null;
		TermQuery t;
		boolean addAliases = true;
	
		// categories should not be analyzed
		if(currentField != null && currentField.equals("incategory")){
			return new TermQuery(makeTerm());
		}
		
		// check for wildcard seaches, they are also not analyzed/stemmed, only for titles
		// wildcard signs are allowed only at the end of the word, minimum one letter word
		if(length>1 && wildcards != null && bufferIsWildCard()){
			Term term = makeTerm();
			//if(term.field().equals("stemtitle") || term.field().equals("stemtitle_exact"))
			//	return null; // don't do wildcards for stemtitles
			Query ret = wildcards.makeQuery(term.text(),term.field());
			if(ret != null){
				ret.setBoost(WILDCARD_BOOST);
				return ret;
			} else{
				// something is wrong, try making normal query
				return new TermQuery(term);
			}
		}
		
		if(toplevelOccur == BooleanClause.Occur.MUST_NOT)
			addAliases = false;

		if(tokens.size() == 1){		
			t = new TermQuery(makeTerm(tokens.get(0)));
			t.setBoost(defaultBoost);
			if(toplevelOccur != Occur.MUST_NOT)
				wordsFromParser.add(t.getTerm().text());
			return t;
		} else{
			// make a nested boolean query
			ArrayList<BooleanQuery> queries = new ArrayList<BooleanQuery>();
			ArrayList<Token> aliases = new ArrayList<Token>();
			for(int i=0; i<tokens.size(); i++){
				BooleanQuery query = new BooleanQuery();
				// main token
				Token token = tokens.get(i);
				t = new TermQuery(makeTerm(token));
				t.setBoost(defaultBoost);
				if(toplevelOccur != Occur.MUST_NOT)
					wordsFromParser.add(t.getTerm().text());
				query.add(t,Occur.SHOULD);
				// group aliases together
				aliases.clear();
				for(int j=i+1;j<tokens.size();j++){
					if(tokens.get(j).getPositionIncrement() == 0){
						aliases.add(tokens.get(j));
						i = j;
					} else
						break;
				}				
				if(addAliases){
					for(Token alias : aliases){
						t = new TermQuery(makeTerm(alias));
						if (alias.type().equals("transliteration") || alias.type().equals("unicode"))
							t.setBoost(defaultBoost);
						else
							t.setBoost(defaultAliasBoost*defaultBoost);
						query.add(t,Occur.SHOULD);
					}
				}
				queries.add(query);
			}
			// don't returned nested if one query only
			if(queries.size() == 1){
				BooleanQuery q = (BooleanQuery)queries.get(0);
				// one nested clause
				if(q.getClauses().length == 1)
					return q.getClauses()[0].getQuery();
				return queries.get(0);
			}
			// multiple tokens, e.g. super-hero -> +super +hero
			bq = new BooleanQuery();
			for(BooleanQuery q : queries){
				if(q.getClauses().length == 1)
					bq.add(q.getClauses()[0].getQuery(),boolDefault);
				else
					bq.add(q,boolDefault);
			}
			return bq;
			
		}
	}
	
	public boolean isDisableTitleAliases() {
		return disableTitleAliases;
	}

	public void setDisableTitleAliases(boolean disableTitleAliases) {
		this.disableTitleAliases = disableTitleAliases;
	}

	/** Reset the parser state */
	private void reset(){
		cur = 0; 
		length = 0;
		currentField = null;	
		prev_cur = 0;
		explicitOccur = null;
		wordsFromParser = new ArrayList<String>();
	}
	
	/** Init parsing, call this function to parse text */ 
	private Query startParsing(){
		reset();
		return parseClause(0);
	}
	
	/** 
	 * Simple parse on one default field, no rewrites.
	 * 
	 * @param queryText
	 * @return
	 */
	public Query parseRaw(String queryText){
		queryLength = queryText.length(); 
		text = queryText.toCharArray();
		
		Query query = null;
		query = startParsing();
		
		return query;		
	}
	
	
	
	/* ======================= FULL-QUERY PARSING ========================= */
	
	public static class ParsingOptions {
		/** use a custom namespace-transformation policy */
		NamespacePolicy policy = null;
		/** only parse the main query (on contents and title) without relevance stuff */
		boolean coreQueryOnly = false;
		/** interface to fetch wildcard hits */
		Wildcards wildcards = null;
		
		public ParsingOptions() {}		
		public ParsingOptions(NamespacePolicy policy){
			this.policy = policy;
		}
		public ParsingOptions(boolean coreQueryOnly){
			this.coreQueryOnly = coreQueryOnly;
		}
		public ParsingOptions(Wildcards wildcards){
			this.wildcards = wildcards;
		}
		public ParsingOptions(NamespacePolicy policy, Wildcards wildcards){
			this.policy = policy;
			this.wildcards = wildcards;
		}
	}
	
	/** Parse a full query with default options */
	public Query parse(String queryText){
		return parse(queryText,new ParsingOptions());
	}
	
	/**
	 * Construct a full query on all the fields in the index from search text
	 * 
	 */
	@SuppressWarnings("unchecked")
	public Query parse(String queryText, ParsingOptions options){
		this.wildcards = options.wildcards;
		queryText = quoteCJK(queryText);
		NamespacePolicy defaultPolicy = this.namespacePolicy;
		if(options.policy != null)
			this.namespacePolicy = options.policy;		
		defaultBoost = CONTENTS_BOOST;
		defaultAliasBoost = ALIAS_BOOST;
		Query qc = parseRaw(queryText);		
		Object[] qtwords = makeTitlePart(queryText);
		Query qt = (Query) qtwords[0];
		words = (ArrayList<String>) qtwords[1];
		this.namespacePolicy = defaultPolicy;
		if(qc == null || qt == null)
			return new BooleanQuery();		
		if(qc.equals(qt))
			return qc; // don't duplicate (probably a query for categories only)
		
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(qc,Occur.SHOULD);
		bq.add(qt,Occur.SHOULD);
		// add additional must_not clause if needed
		BooleanQuery forbidden = extractForbidden(bq);
		if(forbidden != null)
			bq.add(forbidden,Occur.MUST_NOT);
		
		// extract terms that are going to be highlighted
		HashSet<Term> hterms = new HashSet<Term>();
		qc.extractTerms(hterms);
		HashSet<Term> forbiddenTerms = new HashSet<Term>();
		if(forbidden != null)
			forbidden.extractTerms(forbiddenTerms);
		hterms.removeAll(forbiddenTerms);
		highlightTerms = hterms.toArray(new Term[] {});
		
		if(options.coreQueryOnly || words == null || words.size()==0)
			return bq;
		
		// filter out stop words to SHOULD (this enables queries in form of question)
		if(!allStopWords(words,stopWords)){
			filterStopWords(bq);
		}
		
		// work out singular forms of words
		ArrayList<String> singularWords = null;
		if(filters.hasSingular()){
			Singular singular = filters.getSingular();
			singularWords = new ArrayList<String>();
			boolean diff = false;
			for(String w : words){
				String sw = singular.getSingular(w);
				if(sw != null){
					singularWords.add(sw);
					diff = true;
				} else
					singularWords.add(w);
			}
			if(!diff) // all words are same as original words
				singularWords = null;
		}
		
		// work out stemmed words
		ArrayList<String> stemmedWords = filters.stem(words);
		if(stemmedWords != null && (stemmedWords.equals(words) || stemmedWords.equals(singularWords) || stemmedWords.size()==0))
			stemmedWords = null;

		// main phrase combined with relevance meatrics
		Query mainPhrase = makeMainPhraseWithRelevance(words,singularWords,stemmedWords);
		if(mainPhrase == null)
			return bq;

		// additional queries
		Query related = new LogTransformScore(makeRelatedRelevance(words,RELATED_SLOP,RELATED_BOOST));
		
		// full query
		BooleanQuery additional = new BooleanQuery(true);
		additional.add(mainPhrase,Occur.MUST);
		if(related != null)
			additional.add(related,Occur.SHOULD); 
		
		BooleanQuery full = new BooleanQuery(true);
		full.add(bq,Occur.MUST);
		full.add(additional,Occur.SHOULD);
		
		// redirect match (when redirect is not contained in contents or title)
		if(wildcards == null || !wildcards.hasWildcards()){
			Query redirects = makeAlttitleForRedirects(words,20,1);
			if(redirects != null)
				full.add(redirects,Occur.SHOULD);
			if(singularWords != null){
				Query redirectsSing = makeAlttitleForRedirects(singularWords,20,0.8f);
				if(redirectsSing != null)
					full.add(redirectsSing,Occur.SHOULD);
			}
		}
		
		return full;
	}
	
	/** Recursively transverse queries and put stop words to SHOULD */
	private void filterStopWords(BooleanQuery bq) {
		if(stopWords==null && stopWords.size()==0)
			return;
		for(BooleanClause cl : bq.getClauses()){
			Query q = cl.getQuery();
			Occur o = cl.getOccur();
			if(q instanceof BooleanQuery){
				filterStopWords((BooleanQuery)q);
			} else if(q instanceof TermQuery && o.equals(Occur.MUST) 
					&& stopWords.contains(((TermQuery)q).getTerm().text())){
				cl.setOccur(Occur.SHOULD);
			}
		}
	}

	/** Quote CJK chars to avoid frequency-based analysis */
	protected String quoteCJK(String queryText){
		if(!builder.filters.isUsingCJK())
			return queryText;
		
		StringBuilder sb = new StringBuilder();
		int c;
		boolean prevCJK = false;
		int offset = 0;
		boolean closeQuote = false;
		boolean inQuotes = false;
		for(int i=0;i<queryText.length();i++){
			c = queryText.codePointAt(i);
			if(c == '"') inQuotes = !inQuotes;
			if(inQuotes)
				continue;
			if(CJKFilter.isCJKChar(c)){
				if(!prevCJK){ // begin of CJK stream
					if(i!=0)
						sb.append(queryText.substring(offset,i));
					offset = i;
					sb.append('"');
					closeQuote = true;
					prevCJK = true;
				}
			} else if(prevCJK){
				// end of CJK stream
				sb.append(queryText.substring(offset,i));
				offset = i;
				sb.append('"');
				closeQuote = true;
				prevCJK = false;
			}
		}
		if(offset == 0  && !closeQuote)
			return queryText;
		else{
			sb.append(queryText.substring(offset,queryText.length()));
			if(closeQuote)
				sb.append('"');
			return sb.toString();
		}
	}
	
	/** Make title query in format: title:query stemtitle:stemmedquery
	 *  Also extract words from query (to be used for phrases additional scores)
	 *  @return { query, arraylist<string> of words } */
	protected Object[] makeTitlePart(String queryText) {
		String contentField = defaultField;
		float olfDefaultBoost = defaultBoost;
		defaultField = fields.title(); // now parse the title part
		if(ADD_STEM_TITLE && builder.getFilters().hasStemmer())
			defaultBoost = TITLE_BOOST; // we have stem titles
		else
			defaultBoost = TITLE_BOOST+STEM_TITLE_BOOST; // no stem titles, add-up boosts
		defaultAliasBoost = TITLE_ALIAS_BOOST;
		Query qt = parseRaw(queryText);
		Query qs = null;
		// stemmed title
		if(ADD_STEM_TITLE && builder.getFilters().hasStemmer()){
			defaultField = fields.stemtitle(); 
			defaultBoost = STEM_TITLE_BOOST;
			defaultAliasBoost = STEM_TITLE_ALIAS_BOOST;
			qs = parseRaw(queryText);
		}
		// pop stack
		defaultField = contentField;
		defaultBoost = olfDefaultBoost;
		defaultAliasBoost = ALIAS_BOOST;

		//ArrayList<String> words = extractWords(qt);
		ArrayList<String> words = wordsFromParser;
		
		if(qt == qs) // either null, or category query
			return new Object[] {qt,words};
		if(qt == null)
			return new Object[] {qs,words};
		if(qs == null)
			return new Object[] {qt,words};
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(qt,Occur.SHOULD);
		bq.add(qs,Occur.SHOULD);
		return new Object[] {bq,words};
	}
	
	/** Extract MUST_NOT clauses form a query */
	protected static BooleanQuery extractForbidden(Query q){
		BooleanQuery bq = new BooleanQuery();
		extractForbiddenRecursive(bq,q);
		if(bq.getClauses().length == 0)
			return null;
		
		return bq;
	}
	/** Recursivily extract all MUST_NOT clauses from query */ 
	protected static void extractForbiddenRecursive(BooleanQuery forbidden, Query q){
		if(q instanceof BooleanQuery){
			BooleanQuery bq = (BooleanQuery)q;
			for(BooleanClause cl : bq.getClauses()){
				if(cl.getOccur() == Occur.MUST_NOT)
					forbidden.add(cl.getQuery(),Occur.SHOULD);
				else
					extractForbiddenRecursive(forbidden,cl.getQuery());
			}
		}
	}
	/** Extract forbidden terms from a query into a hashset */ 
	public static void extractForbiddenInto(Query q, HashSet<Term> forbidden){
		BooleanQuery bq = extractForbidden(q);
		if(bq != null)
			bq.extractTerms(forbidden);
	}
	
	protected void addWords(ArrayList<String> list, TermQuery tq){
		String f = tq.getTerm().field();
		if(!f.equals("category") && !f.equals("namespace"))
			list.add(tq.getTerm().text());
	}
	protected void addWords(ArrayList<String> list, PhraseQuery pq){
		for(Term term : pq.getTerms()){
			String f = term.field();
			if(!f.equals("category") && !f.equals("namespace"))
				list.add(term.text());
		}
	}	
	protected void addWords(ArrayList<String> list, Query q){
		if(q instanceof TermQuery){
			addWords(list,(TermQuery)q);
		} else if(q instanceof PhraseQuery){
			addWords(list,(PhraseQuery)q);
		}
	}
	
	/** Get phrase words, valid only after parse() call */
	public ArrayList<String> getWords(){		
		return words;
	}
	
	/** Make the main phrases with relevance metrics */ 
	protected Query makeMainPhraseWithRelevance(ArrayList<String> words, ArrayList<String> singularWords, ArrayList<String> stemmedWords){
		Query main = null;
						
		PositionalQuery exact = new PositionalQuery(new PositionalOptions.Exact());
		PositionalQuery sloppy = new PositionalQuery(new PositionalOptions.Sloppy());
		PositionalQuery stemmed = new PositionalQuery(new PositionalOptions.Sloppy());
		PositionalQuery singular = new PositionalQuery(new PositionalOptions.Sloppy());
		BooleanQuery combined = new BooleanQuery(true);
		
		ArrayList<ArrayList<String>> wordnet = WordNet.replaceOne(words,iid.getLangCode());
		
		// main phrases
		int pos = 0;
		for(int i=0;i<words.size();i++){
			String w = words.get(i);
			boolean stop = stopWords.contains(w); 
			Term term = new Term(fields.contents(),w);
			exact.add(term,stop);		
			if(!stop)
				sloppy.add(term,pos,stop); // maintain gaps
			if(!stop && singularWords!=null)
				singular.add(new Term(fields.contents(),singularWords.get(i)),pos,stop);
			if(!stop && stemmedWords!=null)
				stemmed.add(new Term(fields.contents(),stemmedWords.get(i)),pos,stop);
			pos++;
		}
		// combined exact and sloppy (if exists)
		if(sloppy.getTerms().length != 0){
			sloppy.setSlop(MAINPHRASE_SLOP);
			combined.add(exact,Occur.SHOULD);
			combined.add(sloppy,Occur.SHOULD);
			// singulars
			if(singularWords != null){
				singular.setSlop(MAINPHRASE_SLOP);
				combined.add(singular,Occur.SHOULD);
			}
			// stemmed
			if(stemmedWords != null){
				stemmed.setSlop(MAINPHRASE_SLOP);
				combined.add(stemmed,Occur.SHOULD);
			}
			// wordnet			
			if(wordnet != null){
				for(ArrayList<String> wnwords : wordnet){
					pos = 0;
					PositionalQuery wnquery = new PositionalQuery(new PositionalOptions.Sloppy());
					for(int i=0;i<wnwords.size();i++){
						String w = wnwords.get(i);
						boolean stop = stopWords.contains(w); 
						if(!stop)
							wnquery.add(new Term(fields.contents(),w),pos,stop);
						pos++;
					}
					wnquery.setSlop(MAINPHRASE_SLOP);
					combined.add(wnquery,Occur.SHOULD);
				}
			}
			main = combined;
		} else
			main = exact;
		
		main.setBoost(MAINPHRASE_BOOST);
		
		// relevance measures: alttitle
		Query alttitle = makeAlttitleRelevance(words,ALTTITLE_RELEVANCE_BOOST);
		ArrayList<Query> altAdd = new ArrayList<Query>();
		if(singularWords != null)
			altAdd.add(makeAlttitleRelevance(singularWords,ALTTITLE_RELEVANCE_BOOST));
		if(wordnet!=null)
			for(ArrayList<String> wnwords : wordnet)
				altAdd.add(makeAlttitleRelevance(wnwords,ALTTITLE_RELEVANCE_BOOST));
		alttitle = combine(alttitle,altAdd);
		
		// related
		Query related = makeRelatedRelevance(words,RELATED_SLOP,RELATED_BOOST);
		ArrayList<Query> relAdd = new ArrayList<Query>();
		if(singularWords != null)
			relAdd.add(makeRelatedRelevance(singularWords,RELATED_SLOP,RELATED_BOOST));
		if(wordnet!=null)
			for(ArrayList<String> wnwords : wordnet)
				relAdd.add(makeRelatedRelevance(wnwords,RELATED_SLOP,RELATED_BOOST));
		related = combine(related,relAdd);
		
		RelevanceQuery whole = new RelevanceQuery(main);
		whole.addRelevanceMeasure(alttitle);
		whole.addRelevanceMeasure(related);
		
		return whole;
	}
	
	private Query combine(Query query, ArrayList<Query> additional) {
		if(additional.size()==0)
			return query;
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(query,Occur.SHOULD);
		for(Query q : additional)
			bq.add(q,Occur.SHOULD);
		return bq;
	}

	
	/** Relevance metrics based on rank (of titles and redirects) */
	protected Query makeAlttitleRelevance(ArrayList<String> words, float boost){
		String field = fields.alttitle();
		BooleanQuery bq = new BooleanQuery(true);
		if(words.size() == 1){
			PositionalQuery pq = new PositionalQuery(new PositionalOptions.Alttitle());
			pq.add(new Term(field,words.get(0)));
			bq.add(pq,Occur.SHOULD);
		} else{
			// add words
			for(String w : words){
				PositionalQuery pq = new PositionalQuery(new PositionalOptions.Alttitle());
				pq.add(new Term(field,w));
				bq.add(pq,Occur.SHOULD);
			}
			// phrases
			int i =0;
			ArrayList<String> phrase = new ArrayList<String>();
			while(i < words.size()){
				phrase.clear();
				for(;i<words.size();i++){
					String w = words.get(i);
					if(phrase.size() == 0 || stopWords.contains(w))
						phrase.add(w);
					else{
						phrase.add(w);						
						break;
					}
				}
				if(phrase.size() > 1)
					bq.add(makeAlttitlePhrase(phrase,field,new PositionalOptions.Alttitle(),0),Occur.SHOULD);
			}
		}
		// add the whole-only query
		bq.add(makeAlttitlePhrase(words,field,new PositionalOptions.AlttitleWhole(),20),Occur.SHOULD);
		bq.setBoost(boost);
		return bq;

	}
	
	private Query makeAlttitlePhrase(ArrayList<String> words, String field, PositionalOptions options, int slop){
		PositionalQuery pq = new PositionalQuery(options);
		for(String w : words)
			pq.add(new Term(field,w),stopWords.contains(w));
		if(slop != 0)
			pq.setSlop(slop);
		pq.setBoost(words.size());
		return pq;
	}
	
	/** Make relevance metrics based on context via related articles */
	protected Query makeRelatedRelevance(ArrayList<String> words, int slop, float boost){
		if(words.size() <= 2){
			PhraseQuery pq = makePhraseForRelated(words,slop);
			pq.setBoost(boost);
			return pq;
		} else{
			BooleanQuery bq = new BooleanQuery(true);
			ArrayList<String> phrase = new ArrayList<String>();
			int i = 0;
			// make phrases with anchors in non-stopwords
			while(i < words.size()){
				phrase.clear();
				for(;i<words.size();i++){
					String w = words.get(i);
					if(phrase.size() == 0 || stopWords.contains(w))
						phrase.add(w);
					else{
						phrase.add(w);						
						break;
					}
				}
				if(phrase.size() > 1)
					bq.add(makePhraseForRelated(phrase,slop),Occur.SHOULD);
			}
			// make word queries
			for(String w : words){
				if(!stopWords.contains(w)){
					phrase.clear();
					phrase.add(w);
					PhraseQuery pq = makePhraseForRelated(phrase,slop);
					pq.setBoost(0.25f); // 1/4 of original related boost for words 
					bq.add(pq,Occur.SHOULD);
				}
			}
			bq.setBoost(boost);
			if(bq.getClauses() != null && bq.getClauses().length != 0)
				return bq;
			else
				return null;
		}
			
	}
	
	private PhraseQuery makePhraseForRelated(ArrayList<String> words, int slop){
		PositionalQuery pq = new PositionalQuery(new PositionalOptions.Related());
		for(String w : words){
			pq.add(new Term("related",w),stopWords.contains(w));
		}
		pq.setSlop(slop);
		return pq;
	}
	
	
	/** Additional query to match words in redirects that are not in title or article */
	protected Query makeAlttitleForRedirects(ArrayList<String> words, int slop, float boost){
		PositionalQuery pq = new PositionalQuery(new PositionalOptions.RedirectMatch());
		for(String w : words)
			pq.add(new Term(fields.alttitle(),w),stopWords.contains(w));
		pq.setSlop(slop);
		pq.setBoost(boost);
		return pq;
	}
	
	public void setNamespacePolicy(NamespacePolicy namespacePolicy) {
		this.namespacePolicy = namespacePolicy;
	}

	
	/** Make alttitle phrase for titles indexes  */
	public Query makeAlttitleForTitles(List<String> words, int slop, float boost){
		BooleanQuery main = new BooleanQuery(true);

		PositionalQuery exact = new PositionalQuery(new PositionalOptions.AlttitleExact());
		PositionalQuery sloppy = new PositionalQuery(new PositionalOptions.AlttitleSloppy());

		// make exact + sloppy
		int pos = 0;
		for(String w : words){
			Term term = new Term(fields.alttitle(),w);
			boolean isStop = stopWords.contains(w);
			exact.add(term,isStop);			 
			if(!isStop)
				sloppy.add(term,pos,isStop); // maintain gaps
			pos++;
		}
		if(sloppy.getTerms().length == 0)
			return exact;
		
		sloppy.setSlop(ADD_ALTTITLE_SLOP);
		main.add(exact,Occur.SHOULD);
		main.add(sloppy,Occur.SHOULD);
		main.setBoost(ADD_ALTTITLE_BOOST);
		return main;
			
	}
	
	/** Make a query to search grouped titles indexes */
	public Query parseForTitles(String queryText, Wildcards wildcards){
		this.wildcards = wildcards;
		String oldDefaultField = this.defaultField;
		NamespacePolicy oldPolicy = this.namespacePolicy;
		FieldBuilder.BuilderSet oldBuilder = this.builder;
		this.defaultField = "alttitle";
		this.namespacePolicy = NamespacePolicy.IGNORE;
		
		Query q = parseRaw(queryText);
		
		ArrayList<String> words = wordsFromParser;
		if(words == null || words.size() == 0)
			return q;
		
		Query alttitle = makeAlttitleForTitles(words,ADD_ALTTITLE_SLOP,ADD_ALTTITLE_BOOST);
		
		this.builder = oldBuilder;		
		this.defaultField = oldDefaultField;
		this.namespacePolicy = oldPolicy;

		if(alttitle == null)
			return q;
		
		BooleanQuery whole = new BooleanQuery(true);
		whole.add(q,Occur.MUST);
		whole.add(alttitle,Occur.SHOULD);
		return whole;
		
	}
	
	/** check if all the words in the array are stop words */
	private boolean allStopWords(ArrayList<String> words, HashSet<String> preStopWords){
		if(words == null || words.size() == 0)
			return false;
		for(String w : words){
			if(!preStopWords.contains(w)){
				return false;
			}
		}
		return true;
	}

	/** Valid after parse() call - contents terms to be highlighted */
	public Term[] getHighlightTerms() {
		return highlightTerms;
	}
	
	


}
