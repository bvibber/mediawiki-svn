package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Map.Entry;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.CombinedPhraseQuery;
import org.apache.lucene.search.ConstMinScore;
import org.apache.lucene.search.CustomBoostQuery;
import org.apache.lucene.search.CustomPhraseQuery;
import org.apache.lucene.search.Explanation;
import org.apache.lucene.search.LogTransformScore;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.QueryOptions;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.WildcardQuery;
import org.apache.lucene.search.BooleanClause.Occur;
import org.apache.lucene.search.function.CustomScoreQuery;
import org.apache.lucene.search.function.FieldScoreQuery;
import org.apache.lucene.search.function.ValueSource;
import org.apache.lucene.search.function.ValueSourceQuery;
import org.apache.lucene.search.spans.SpanNearQuery;
import org.apache.lucene.search.spans.SpanQuery;
import org.apache.lucene.search.spans.SpanTermQuery;
import org.mediawiki.importer.ExactListFilter;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.search.AggregatePhraseInfo;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.search.RankField;
import org.wikimedia.lsearch.search.RankValue;
import org.wikimedia.lsearch.search.Wildcards;
import org.wikimedia.lsearch.search.RankField.RankFieldSource;
import org.wikimedia.lsearch.util.UnicodeDecomposer;

/**
 * Replacement for Lucene QueryParser, a subset of syntax is supported:
 * 
 * - Boolean operators (both AND, OR and +/-) are permitted, and 
 * clauses can be grouped via parenthesis.
 * - Phrase search, by enclosing in "" (e.g. "some phrase")
 * - Range, wildcard and fuzzy queries are disabled
 * 
 * Aliases (introduced by analyzer) are boosted by 0.5
 * 
 * The query for contents is rewritten as "contents:query title:query^2"
 * 
 * The class <b>IS NOT</b> thread safe, i.e. one instance of an object
 * cannot be used by multiple threads
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
	private String field; // current field
	private String defaultField; // the default field value
	private float defaultBoost = 1;
	private float defaultAliasBoost = ALIAS_BOOST;
	protected enum TokenType {WORD, FIELD, AND, OR, EOF };
	
	private TokenStream tokenStream; 
	private ArrayList<Token> tokens; // tokens from analysis
	protected ArrayList<String> words;
	
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
	public static int MAINPHRASE_SLOP = 50;
	public static float MAINPHRASE_BOOST = 2f;
	public static float RELATED_BOOST = 4f;	
	public static int RELATED_SLOP = 0;
	public static float ALT_PHRASES_BOOST = 0.4f;
	// additional to main phrase:
	public static float ADD_STEMTITLE_BOOST = 2;
	public static float ADD_ALTTITLE_BOOST = 2;
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
	protected HashSet<String> stopWords;
	protected Wildcards wildcards = null;
	
	/** default value for boolean queries */
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
			
			if(Character.isLetterOrDigit(c)){
				int start = cur;
				tokenType = fetchToken();
				if(tokenType == TokenType.WORD){
					analyzeBuffer();
					for(Token t : tokens){
						if(t.type().equals("word"))
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
			if(!Character.isWhitespace(ch) && ch != ':' && ch != '(' && ch != ')' && ch !='[' && ch != ']' && ch != '.' && ch != ',' && ch != ';' && ch != '"'){
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
		if(field == null)
			return new Term(defaultField,builder.isExactCase()? t : t.toLowerCase());
		else if(!field.equals("incategory") && 
				(namespacePolicy == NamespacePolicy.IGNORE || 
						namespacePolicy == NamespacePolicy.REWRITE))
			return new Term(defaultField,t);
		else if(field.equals("incategory")){
			String norm = t.replace("_"," "); // bug 10822
			return new Term("category",builder.isExactCase()? norm : norm.toLowerCase());
		} else
			return new Term(field,t);
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
			if(field!=null && field.equals("incategory"))
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
		if(!returnOnFieldDef && field == null && needsRewrite()){
			fieldQuery = namespaceRewriteQuery; 
		}
		
		mainloop: for( ; cur < queryLength; cur++ ){
			c = text[cur];
			
			if(c == ' ')
				continue;
			
			// terms, fields
			if(Character.isLetterOrDigit(c) || c == '[' ||  c=='*' || c=='?'){
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
					if(field == null || definedExplicitField){
						// set field name
						field = new String(buffer,0,length);
						if((defaultNamespaceName!=null && field.equals(defaultNamespaceName)) || field.equals(defaultField)){
							field = null;
							break; // repeated definition of field, ignore
						}
						definedExplicitField = true;
						
						fieldQuery = getNamespaceQuery(field); // depending on policy rewrite this field
						if(fieldQuery != null){
							// save field, we will need it to be set to null to fetch categories
							String myfield = field;
							field = null;
							// fetch the clause until the next field
							fieldsubquery = parseClause(level+1,true,myfield);
							field = myfield;
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
						String myfield = (topFieldName != null)? topFieldName : (field !=null)? field : (defaultNamespaceName!=null)? defaultNamespaceName : defaultField; 
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
			field = null;
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
		BooleanClause.Occur aliasOccur;
		BooleanQuery bq = null;
		TermQuery t;
	
		// categories should not be analyzed
		if(field != null && field.equals("incategory")){
			return new TermQuery(makeTerm());
		}
		
		// check for wildcard seaches, they are also not analyzed/stemmed, only for titles
		// wildcard signs are allowed only at the end of the word, minimum one letter word
		if(length>1 && wildcards != null && bufferIsWildCard()){
			Term term = makeTerm();
			if(term.field().equals("stemtitle") || term.field().equals("stemtitle_exact"))
				return null; // don't do wildcards for stemtitles
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
			aliasOccur = null; // do not add aliases
		else
			aliasOccur = BooleanClause.Occur.SHOULD;

		if(tokens.size() == 1){
			t = new TermQuery(makeTerm(tokens.get(0)));
			t.setBoost(defaultBoost);
			return t;
		} else{
			BooleanQuery cur;
			cur = bq = new BooleanQuery();
			// make a nested boolean query
			for(int i=0; i<tokens.size(); i++){
				Token token = tokens.get(i);
				if(token.getPositionIncrement() == 0){
					if(aliasOccur == null); // ignore stemmed/aliases if prefixed with MUST_NOT
					else if(token.type().equals("stemmed")){						
						// stemmed word
						t = new TermQuery(makeTerm(token));
						t.setBoost(defaultAliasBoost*defaultBoost);
						cur.add(t,aliasOccur);
					} else if(token.type().equals("alias")){
						// produced by alias engine (e.g. for sr)
						t = new TermQuery(makeTerm(token));
						t.setBoost(defaultAliasBoost*defaultBoost);
						cur.add(t,aliasOccur);
					} else if (token.type().equals("transliteration")){
						// if not in nested query make one
						if(cur == bq  && (i+1) < tokens.size() && tokens.get(i+1).getPositionIncrement()==0){
							t = new TermQuery(makeTerm(token));
							t.setBoost(defaultBoost);
							cur = new BooleanQuery();
							cur.add(t,BooleanClause.Occur.SHOULD);
							bq.add(cur,BooleanClause.Occur.SHOULD);
							continue;
						} else{
							// alternative transliteration
							t = new TermQuery(makeTerm(token));
							t.setBoost(defaultBoost);
							cur.add(t,aliasOccur);
							// fetch the next token to same query if it's transliteration
							if((i+1) < tokens.size() && tokens.get(i+1).getPositionIncrement()==0 && tokens.get(i+1).type().equals("transliteration"))
								continue;
						}
					}
					if( cur != bq) // returned from nested query
						cur = bq;
				} else{
					t = new TermQuery(makeTerm(token));
					t.setBoost(defaultBoost);
					if(tokens.size() > 2 && (i+1) < tokens.size() && tokens.get(i+1).getPositionIncrement()==0){
						// make nested query. this is needed when single word is tokenized
						// into many words of which they all have aliases
						// e.g. anti-hero => anti hero
						cur = new BooleanQuery();
						cur.add(t,BooleanClause.Occur.SHOULD);
						if(token.type().equals("unicode"))
							bq.add(cur,BooleanClause.Occur.SHOULD);
						else
							bq.add(cur,boolDefault);
					} else if((i+1) >= tokens.size() || tokens.get(i+1).getPositionIncrement()!=0)
						cur.add(t,boolDefault);
					else
						cur.add(t,BooleanClause.Occur.SHOULD); // add the original word with SHOULD					
				}
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
		field = null;	
		prev_cur = 0;
		explicitOccur = null;
	}
	
	/** Init parsing, call this function to parse text */ 
	private Query startParsing(){
		reset();
		return parseClause(0);
	}
	
	/** 
	 * Parse a string repesentation of query and return a Query object.
	 * Will not try to transform field names into namespace boolean queries.
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
	
	/** Duplicate a term query, setting "title" as field */
	private TermQuery makeTitleTermQuery(TermQuery tq){
		if(disableTitleAliases && tq.getBoost()==defaultAliasBoost)
			return null;
		Term term = tq.getTerm();
		if(term.field().equals(defaultField)){
			TermQuery tq2 = new TermQuery(
					new Term(fields.title(),term.text()));
			tq2.setBoost(tq.getBoost()*TITLE_BOOST);
			
			return tq2;
		}
		return null;
	}
	
	/** Duplicate a phrase query, setting "title" as field */
	private PhraseQuery makeTitlePhraseQuery(PhraseQuery pq){
		if(disableTitleAliases && pq.getBoost()==defaultAliasBoost)
			return null;
		PhraseQuery pq2 = new PhraseQuery();
		Term[] terms = pq.getTerms();
		if(terms.length > 0 && terms[0].field().equals(defaultField)){
			for(int j=0;j<terms.length;j++){
				pq2.add(new Term(fields.title(),terms[j].text()));
			}
			pq2.setBoost(pq.getBoost()*TITLE_BOOST);
			
			return pq2;
		}		
		return null;
	}
	
	/**
	 * Recursively add title term for each contents term.
	 * e.g. (foo bar) => (foo bar title:foo title:bar)
	 * 
	 * @param query
	 * @return
	 */
	@Deprecated
	protected Query rewriteQuery(Query query){
		BooleanQuery nq; // the returned new query
		
		if(query instanceof TermQuery){
			TermQuery tq = (TermQuery) query;			
			TermQuery tq2 = makeTitleTermQuery(tq);
			if(tq2 != null){				
				nq = new BooleanQuery();
				nq.add(tq,boolDefault);
				nq.add(tq2,BooleanClause.Occur.SHOULD);
				return nq;
			}
		} else if(query instanceof PhraseQuery){
			PhraseQuery pq = (PhraseQuery) query;
			PhraseQuery pq2 = makeTitlePhraseQuery(pq);
			if(pq2 != null){	
				nq = new BooleanQuery();
				nq.add(pq,boolDefault);
				nq.add(pq2,BooleanClause.Occur.SHOULD);
				return nq;
			}
		} else if(query instanceof BooleanQuery){
			BooleanQuery bq = (BooleanQuery) query;
			nq = new BooleanQuery();
			BooleanClause cs[] = bq.getClauses();
			ArrayList<BooleanClause> newClauses = new ArrayList<BooleanClause>();
			ArrayList<BooleanClause> oldClauses = new ArrayList<BooleanClause>();
			// loop over clauses, replace the nested clauses, others add
			for(BooleanClause clause : cs){
				Query q = clause.getQuery();
				Query q2;
				if(q instanceof BooleanQuery){
					q2 = rewriteQuery(q);
					oldClauses.add(new BooleanClause(q2,clause.getOccur()));
				} else{
					if(q instanceof TermQuery)
						q2 = makeTitleTermQuery((TermQuery)q);
					else if(q instanceof PhraseQuery)
						q2 = makeTitlePhraseQuery((PhraseQuery)q);
					else 
						q2 = null;

					if(q2 != null)
						newClauses.add(new BooleanClause(q2,clause.getOccur())); // titles are always with or!

					oldClauses.add(clause);
				}
			}
			
			// re-add clauses in a neat order
			for(BooleanClause clause : oldClauses)
				nq.add(clause);
			// add all new clauses in a single boolean clause
			if(newClauses.size() != 0){
				Query nquery = null; 
				// don't nest simple boolean quries
				if(newClauses.size() == 1)
					nquery = newClauses.get(0).getQuery();
				else{
					BooleanQuery newbq;
					nquery = newbq = new BooleanQuery();
					for(BooleanClause clause : newClauses)
						newbq.add(clause);
				}
				BooleanQuery oldq = nq;
				nq = new BooleanQuery();
				nq.add(oldq,BooleanClause.Occur.MUST);
				nq.add(nquery,BooleanClause.Occur.SHOULD);
			}
			return nq;
		}
		
		return query;
	}	
	
	/** Parse into query using namespace policy */
	@Deprecated
	public Query parse(String queryText, NamespacePolicy policy) throws ParseException{
		this.namespacePolicy = policy;
		return parse(queryText);
	}
	
	/** 
	 * Parse a string repesentation of query and returns a Query object
	 * Does all the necessary processing, adds "title" field. 
	 *  
	 * @throws ParseException 
	 * */
	@Deprecated
	public Query parse(String queryText) throws ParseException{		
		Query query = rewriteQuery( parseRaw( queryText ) );
		if(query == null)
			throw new ParseException("Parsing failed, returned null query");
		
		return query;		
	}
	
	protected boolean isNamespaceQuery(Query q){
		if(q instanceof TermQuery)
			return ((TermQuery)q).getTerm().field().equals("namespace");
		else if(q instanceof BooleanQuery){
			for(BooleanClause cl : ((BooleanQuery)q).getClauses()){
				if(cl.getQuery() instanceof TermQuery && 
						((TermQuery)cl.getQuery()).getTerm().field().equals("namespace"));
				else	
					return false;
			}
			return true;
		}
		return false;
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
	
	/** Extract all words from the query */
	public ArrayList<String> extractWords(Query query){
		ArrayList<String> list = new ArrayList<String>();
		if(query == null)
			return list;
		else if(query instanceof TermQuery || query instanceof PhraseQuery){
			addWords(list,query);
			return list;
		} else if(query instanceof BooleanQuery){
			BooleanQuery bq = (BooleanQuery)query;
			BooleanClause[] cl = bq.getClauses();
			// check if first clause is namespace rewrite part
			boolean ignoreFirst = false;
			if(cl.length > 1){
				Query qn = cl[0].getQuery();
				if(qn instanceof TermQuery && ((TermQuery)qn).getTerm().field().equals("namespace"))
					ignoreFirst = true;
				else if (qn instanceof BooleanQuery && ((BooleanQuery)qn).getClauses().length>1){
					Query q1 = ((BooleanQuery)qn).getClauses()[0].getQuery();
					if(q1 instanceof TermQuery && ((TermQuery)q1).getTerm().field().equals("namespace"))
						ignoreFirst = true;
				}
			}
			for(BooleanClause c : cl){
				if(ignoreFirst){
					ignoreFirst = false;
					continue;
				}
				Query q = c.getQuery();
				if(q instanceof TermQuery || q instanceof PhraseQuery)
					addWords(list,q);
				else if(q instanceof BooleanQuery){
					BooleanClause[] bcl = ((BooleanQuery)q).getClauses();
					if(bcl.length == 0);
					else if(bcl.length == 1 && bcl[0].getOccur() != Occur.MUST_NOT)
						addWords(list,bcl[0].getQuery());
					else if(bcl.length == 2){
						// TODO: this might break in some complex queries! (with some parenthesis and transliterations...)
						if(bcl[0].getOccur() == Occur.MUST && bcl[1].getOccur() == Occur.SHOULD)
							// second is alias
							addWords(list,bcl[0].getQuery());
						else if(bcl[0].getOccur() == Occur.MUST && bcl[1].getOccur() == Occur.MUST){
							// both are legal words
							addWords(list,bcl[0].getQuery());
							addWords(list,bcl[1].getQuery());
						} else if(bcl[0].getOccur() == Occur.SHOULD && bcl[1].getOccur() == Occur.SHOULD){
							// first is transliterated, other is alias
							addWords(list,bcl[0].getQuery());
						}
					} else{
						// multple-parsed, add all required
						for(BooleanClause b : bcl){
							if(b.getOccur() == Occur.MUST)
								addWords(list,b.getQuery());
						}
					}
				}				
			}
		}
		return list;	
	}
	
	/** 
	 * Doing some very simple analysis extract span queries to use for
	 * redirect field. Currently only extracts if all boolean clauses are
	 * required or if it's a phrase query. This is since making span
	 * queries in non-trivial in other cases. :(
	 * 
	 * The function heavily depends on the format of output of parser,
	 * especially for rewrite. 
	 * 
	 * @param query
	 * @param level - recursion level
	 * @return
	 */
	protected Query extractSpans(Query query, int level, String fieldName, float boost) {
		// phrase, or termquery just rewrite field name
		if(query instanceof TermQuery){
			TermQuery tq = (TermQuery)query;
			TermQuery ret = new TermQuery(new Term(fieldName,tq.getTerm().text()));
			ret.setBoost(boost);
			return ret;
		} else if(query instanceof PhraseQuery){
			PhraseQuery phrase = new PhraseQuery();
			for(Term term : ((PhraseQuery)query).getTerms()){
				phrase.add(new Term(fieldName,term.text()));				
			}
			phrase.setBoost(boost);
			return phrase;
		} else if(query instanceof BooleanQuery){
			BooleanQuery bq = (BooleanQuery)query;
			// check for rewritten queries, TODO: parse complex multi-part rewrites
			if(level==0 && namespacePolicy != null && namespacePolicy == NamespacePolicy.REWRITE){
				if(bq.getClauses().length == 2 && isNamespaceQuery(bq.getClauses()[0].getQuery())){
					BooleanQuery ret = new BooleanQuery();
					ret.add(bq.getClauses()[0]);
					// the second clause is always the query
					ret.add(extractSpans(bq.getClauses()[1].getQuery(),level+1,fieldName,boost),BooleanClause.Occur.MUST);
					return ret;
				} else
					return null;
			}
			// we can parse if all clauses are required
			boolean canTransform = true;
			for(BooleanClause cl : bq.getClauses()){
				if(cl.getOccur() != BooleanClause.Occur.MUST){
					canTransform = false;
					break;
				}
			}
			if(!canTransform)
				return null;
			// rewrite into span queries + categories
			ArrayList<SpanQuery> spans = new ArrayList<SpanQuery>();
			ArrayList<Query> categories = new ArrayList<Query>();
			for(BooleanClause cl : bq.getClauses()){
				Query q = cl.getQuery();
				if(q instanceof TermQuery){ // -> SpanTermQuery
					TermQuery tq = (TermQuery)q;
					Term t = tq.getTerm(); 
					if(t.field().equals("category")){
						categories.add(q);
					} else {
						SpanTermQuery stq = new SpanTermQuery(new Term(fieldName,t.text()));
						spans.add(stq);
					}
				} else if(q instanceof PhraseQuery){ // -> SpanNearQuery(slop=0,inOrder=true)
					PhraseQuery pq = (PhraseQuery)q;
					Term[] terms = pq.getTerms();
					if(terms == null || terms.length==0)
						continue;
					if(terms[0].field().equals("category")){
						categories.add(q);
					} else{
						SpanTermQuery[] spanTerms = new SpanTermQuery[terms.length];
						for(int i=0; i<terms.length; i++ ){
							spanTerms[i] = new SpanTermQuery(new Term(fieldName,terms[i].text()));
						}
						SpanNearQuery snq = new SpanNearQuery(spanTerms,0,true);
						snq.setBoost(boost);
						spans.add(snq);
					}
				} else // nested boolean or wildcard query
					return null;
			}
			// create the queries
			Query cat = null;
			SpanQuery span = null;
			if(categories.size() != 0){
				if(categories.size() == 1)					
					cat = categories.get(0);
				else{
					BooleanQuery b = new BooleanQuery();
					for(Query q : categories)
						b.add(q,BooleanClause.Occur.MUST);
					cat = b; // intersection of categories, bool query 
				}
			}
			if(spans.size() != 0){
				if(spans.size() == 1)
					span = spans.get(0);
				else{
					// make a span-near query that has a slop 1/2 of tokenGap
					span = new SpanNearQuery(spans.toArray(new SpanQuery[] {}),(KeywordsAnalyzer.TOKEN_GAP-1)/2,false);
					span.setBoost(boost);
				}
			}
			if(cat != null && span != null){
				BooleanQuery ret = new BooleanQuery();
				ret.add(span,BooleanClause.Occur.MUST);
				ret.add(cat,BooleanClause.Occur.MUST);
				return ret;
			} else if(span != null)
				return span;
			else // we don't want categories only
				return null; 

		}
		return null;
	}

	protected BooleanQuery multiplySpans(Query query, int level, String fieldName, float boost){
		BooleanQuery bq = new BooleanQuery(true);
		for(int i=1;i<=KeywordsAnalyzer.KEYWORD_LEVELS;i++){
			Query q = extractSpans(query,0,fieldName+i,boost/i);
			if(q != null)
				bq.add(q,BooleanClause.Occur.SHOULD);
		}
		
		if(bq.getClauses() == null || bq.getClauses().length==0)
			return null;
		else
			return bq;
	}
	
	/** Make a redirect query in format altitle1:query altitle2:query ... redirect:spanquery */
	protected BooleanQuery makeRedirectQuery(String queryText, Query qt) {
		BooleanQuery bq = new BooleanQuery(true);
		float olfDefaultBoost = defaultBoost;
		String contentField = defaultField;
		defaultBoost = ALT_TITLE_BOOST;
		defaultAliasBoost = ALT_TITLE_ALIAS_BOOST;
		for(int i=1;i<=WikiIndexModifier.ALT_TITLES;i++){
			defaultField = fields.alttitle()+i; 
			Query q = parseRaw(queryText);
			if(q != null)
				bq.add(q,BooleanClause.Occur.SHOULD);
		}
		// pop stack
		defaultField = contentField;
		defaultBoost = olfDefaultBoost;
		defaultAliasBoost = ALIAS_BOOST;
		
		if(bq.getClauses() == null || bq.getClauses().length==0)
			return null;
		else
			return bq;

	}
	
	/** Make title query in format: title:query stemtitle:stemmedquery
	 *  Also extract words from query (to be used for phrases additional scores)
	 *  @return { query, arraylist<string> of words } */
	protected Object[] makeTitleQuery(String queryText) {
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

		ArrayList<String> words = extractWords(qt);
		
		if(qt == qs) // either null, or category query
			return new Object[] {qt,words};
		if(qt == null)
			return new Object[] {qs,words};
		if(qs == null)
			return new Object[] {qt,words};
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(qt,BooleanClause.Occur.SHOULD);
		bq.add(qs,BooleanClause.Occur.SHOULD);
		return new Object[] {bq,words};
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
	
	/** make two-word queries for some simple queries */
	protected Query makeTitlePhrases(Query q){
		if(q instanceof BooleanQuery){
			boolean allReq = true;
			BooleanQuery bq = (BooleanQuery) q;
			for(BooleanClause bc : bq.getClauses()){
				if(!bc.getOccur().equals(BooleanClause.Occur.MUST) || !(bc.getQuery() instanceof TermQuery) ||
						!(((TermQuery)bc.getQuery()).getTerm().field().equals("title"))){
					allReq = false;
					break;
				}
			}
			if(allReq){
				BooleanQuery ret = new BooleanQuery(true);
				Term last = null;
				// make phrases '+very +long +query' => "very long" "long query"
				for(BooleanClause bc : bq.getClauses()){
					Term t = ((TermQuery)bc.getQuery()).getTerm();
					if(last != null){
						PhraseQuery pq = new PhraseQuery();
						pq.add(new Term("stemtitle",last.text()));
						pq.add(new Term("stemtitle",t.text()));
						pq.setBoost(TITLE_PHRASE_BOOST);
						pq.setSlop(2);
						ret.add(pq,BooleanClause.Occur.SHOULD);
					}
					last = t;
					
				}
				if(ret.getClauses() != null && ret.getClauses().length != 0)
					return ret;
			}
		} 
		
		return null;
		
	}
	
	/** Make the main phrase query, finds exact phrases, and sloppy phrases without stop words */
	public Query makeMainPhrase(ArrayList<String> words, String field, int slop, float boost, Query stemtitle, Query related, HashSet<String> preStopWords){
		RankValue val = new RankValue();
		boolean allStopWords = true;
		for(String w : words){
			if(!preStopWords.contains(w)){
				allStopWords = false;
				break;
			}
		}
		if(allStopWords){
			CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.ContentsExactOptions(val,stemtitle,related));
			for(String w : words){
				pq.add(new Term(field,w));
			}
			pq.setSlop(slop);
			pq.setBoost(boost);
			return pq;
		} else{
			CombinedPhraseQuery pq = new CombinedPhraseQuery(new QueryOptions.CombinedMainOptions(null,null),new QueryOptions.ContentsSloppyOptions(val,stemtitle,related),
					new QueryOptions.ContentsExactOptions(val,stemtitle,related),preStopWords);
			for(String w : words){
				pq.add(new Term(field,w));
			}
			pq.setSlop(slop);
			pq.setBoost(boost);
			return pq;
		}
	}
	
	/** make single phrase for related field */
	protected PhraseQuery makePhraseForRelated(ArrayList<String> words, int slop, AggregatePhraseInfo aphi){
		CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.RelatedOptions(aphi));
		for(String w : words){
			pq.add(new Term("related",w));
		}
		pq.setSlop(slop);
		return pq;
	}
	
	/** Make phrase queries for additional scores */
	public Query makePhrasesForRelated(ArrayList<String> words, int slop, float boost){
		if(words.size() <= 2){
			PhraseQuery pq = makePhraseForRelated(words,slop,new AggregatePhraseInfo());
			pq.setBoost(boost);
			return pq;
		} else{
			AggregatePhraseInfo aphi = new AggregatePhraseInfo();
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
					bq.add(makePhraseForRelated(phrase,slop,aphi),Occur.SHOULD);
			}
			// make word queries
			for(String w : words){
				if(!stopWords.contains(w)){
					phrase.clear();
					phrase.add(w);
					PhraseQuery pq = makePhraseForRelated(phrase,slop,aphi);
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
	
	public Query makeWordQueries(ArrayList<String> words, String field, float boost, float minScore){
		BooleanQuery bq = new BooleanQuery();
		if(words.size() == 1){
			TermQuery tq = new TermQuery(new Term(field,words.get(0)));
			tq.setBoost(boost);
			if(minScore != 0)
				return new ConstMinScore(tq,minScore);
			return tq;
		} else if(words.size() > 1){
			for(String w : words){
				TermQuery tq = new TermQuery(new Term(field,w));
				if(minScore != 0)
					bq.add(new ConstMinScore(tq,minScore),Occur.SHOULD);
				else
					bq.add(tq,Occur.SHOULD);
			}
			bq.setBoost(boost);
			return bq;
		}
		return null;		
	} 
	
	public Query makeStemtitlePhrase(ArrayList<String> words, String field){
		PhraseQuery pq = new PhraseQuery();
		for(String w : words){
			pq.add(new Term(field,w));
		}
		return pq;
	}
	
	/** Make the stemtitle query, words and phrases anchored in non-stopwords */
	public Query makeStemtitle(ArrayList<String> words, String field, float boost, float minScore){
		BooleanQuery bq = new BooleanQuery(true);
		if(words.size() == 1){
			TermQuery tq = new TermQuery(new Term(field,words.get(0)));
			tq.setBoost(boost);
			if(minScore != 0)
				return new ConstMinScore(tq,minScore);
			bq.add(tq,Occur.SHOULD);
		} else if(words.size() > 1){
			// add words
			for(String w : words){
				TermQuery tq = new TermQuery(new Term(field,w));
				if(minScore != 0)
					bq.add(new ConstMinScore(tq,minScore),Occur.SHOULD);
				else
					bq.add(tq,Occur.SHOULD);
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
					bq.add(makeStemtitlePhrase(phrase,field),Occur.SHOULD);
			}
		}
		bq.setBoost(boost);
		return bq;		
	} 
	
	public Query makeAlttitlePhrase(ArrayList<String> words, String field, AggregatePhraseInfo ap){
		CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.AlttitleOptions(ap));
		for(String w : words)
			pq.add(new Term(field,w));
		return pq;
	}
	
	/** Make the stemtitle query for additional score */
	public Query makeAlttitlePhrases(ArrayList<String> words, String field, float boost){		
		AggregatePhraseInfo ap = new AggregatePhraseInfo();
		if(words.size() == 1){
			CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.AlttitleOptions(ap));
			pq.add(new Term(field,words.get(0)));
			pq.setBoost(boost);			
			return pq;
		} else{
			BooleanQuery bq = new BooleanQuery(true);
			// add words
			for(String w : words){
				CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.AlttitleOptions(ap));
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
					bq.add(makeAlttitlePhrase(phrase,field,ap),Occur.SHOULD);
			}
			bq.setBoost(boost);
			return bq;
		}				
	} 
	
	/** Make the phrase that will match redirects, etc..  */
	public Query makeAlttitleWholePhrase(ArrayList<String> words, String field, int slop, float boost, HashSet<String> preStopWords){
		AggregatePhraseInfo ap = new AggregatePhraseInfo();
		boolean allStopWords = true;
		for(String w : words){
			if(!preStopWords.contains(w)){
				allStopWords = false;
				break;
			}
		}
		if(allStopWords){
			CustomPhraseQuery pq = new CustomPhraseQuery(new QueryOptions.AlttitleExactOptions(ap));
			for(String w : words){
				pq.add(new Term(field,w));
			}
			pq.setSlop(slop);
			pq.setBoost(boost);
			return pq;
		} else{
			CombinedPhraseQuery pq = new CombinedPhraseQuery(new QueryOptions.CombinedAlttitleOptions(), new QueryOptions.AlttitleSloppyOptions(ap),
					new QueryOptions.AlttitleExactOptions(ap),preStopWords);
			for(String w : words){
				pq.add(new Term(field,w));
			}
			pq.setSlop(slop);
			pq.setBoost(boost);
			return pq;
		}
			
	}
	
	/** Join a collection via a char/string */
	protected String join(Collection<String> col, String sep){
		StringBuffer sb = new StringBuffer();
		boolean first = true;
		for(String s : col){
			if(!first){
				sb.append(sep);
			} else
				first = false;
			sb.append(s);
		}
		return sb.toString();
	}
	
	/** Make a query to search grouped titles indexes */
	public Query parseForTitles(String queryText, Wildcards wildcards){
		this.wildcards = wildcards;
		String oldDefaultField = this.defaultField;
		NamespacePolicy oldPolicy = this.namespacePolicy;
		FieldBuilder.BuilderSet oldBuilder = this.builder;
		this.defaultField = "title";
		this.namespacePolicy = NamespacePolicy.IGNORE;
		
		Query q = parseRaw(queryText);
		
		this.defaultField = "title_exact";
		FieldBuilder fb = new FieldBuilder(builder.getFilters().getIndexId(),FieldBuilder.Case.EXACT_CASE);
		this.builder = fb.getBuilder(FieldBuilder.Case.EXACT_CASE);
		
		Query qe = parseRaw(queryText); 
		
		this.builder = oldBuilder;		
		this.defaultField = oldDefaultField;
		this.namespacePolicy = oldPolicy;
		
		BooleanQuery whole = new BooleanQuery(true);
		whole.add(q,Occur.SHOULD);
		whole.add(qe,Occur.SHOULD);
		return whole;
	}
	
	/**
	 * Main function for multi-pass parsing.
	 * 
	 * @param queryText
	 * @param policy
	 * @param makeRedirect
	 * @return
	 */
	@SuppressWarnings("unchecked")
	protected Query parseMultiPass(String queryText, NamespacePolicy policy, boolean makeRedirect, boolean makeKeywords, Wildcards wildcards){
		this.wildcards = wildcards;
		queryText = quoteCJK(queryText);
		if(policy != null)
			this.namespacePolicy = policy;		
		defaultBoost = CONTENTS_BOOST;
		defaultAliasBoost = ALIAS_BOOST;
		Query qc = parseRaw(queryText);		
		Object[] qtwords = makeTitleQuery(queryText);
		// qt = title query, qp = title phrase query
		Query qt = (Query) qtwords[0];
		words = (ArrayList<String>) qtwords[1];
		if(qc == null || qt == null)
			return new BooleanQuery();		
		if(qc.equals(qt))
			return qc; // don't duplicate (probably a query for categories only)
		
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(qc,BooleanClause.Occur.SHOULD);
		bq.add(qt,BooleanClause.Occur.SHOULD);
		
		if(words.size() == 0)
			return bq;

		HashSet<String> preStopWords = StopWords.getPredefinedSet(builder.getFilters().getIndexId());
		// queries: main phrase (main*) + additional queries (add*)
		Query addAlttitleQuery = null;		
		Query mainAlttitleQuery = null;
		Query mainRelatedQuery = makePhrasesForRelated(words,RELATED_SLOP,RELATED_BOOST);
		if(wildcards==null || !wildcards.hasWildcards()){
			addAlttitleQuery = makeAlttitleWholePhrase(words,fields.alttitle(),ADD_ALTTITLE_SLOP,ADD_ALTTITLE_BOOST,preStopWords);
			mainAlttitleQuery = makeAlttitlePhrases(words,fields.alttitle(),ALT_PHRASES_BOOST);
		}
		Query addStemtitleQuery = 
			new LogTransformScore(makeStemtitle(words,fields.stemtitle(),1,0.1f));
		addStemtitleQuery.setBoost(ADD_STEMTITLE_BOOST);		
		Query addRelatedQuery = new LogTransformScore(mainRelatedQuery);
		addRelatedQuery.setBoost(ADD_RELATED_BOOST);
		
		Query mainPhrase = makeMainPhrase(words,fields.contents(),MAINPHRASE_SLOP,MAINPHRASE_BOOST,mainAlttitleQuery,mainRelatedQuery,preStopWords);
		if(mainPhrase == null)
			return bq;
		// build the final query
		BooleanQuery coreQuery = new BooleanQuery(true);		
		BooleanQuery additional = new BooleanQuery(true);
		BooleanQuery addTitleQuery = new BooleanQuery(true); 
		
		if(mainPhrase != null)
			additional.add(mainPhrase,Occur.MUST);
		if(mainRelatedQuery != null)
			additional.add(addRelatedQuery,Occur.SHOULD);
		if(addStemtitleQuery != null)
			additional.add(addStemtitleQuery,Occur.SHOULD); 
		if(addAlttitleQuery != null)
			additional.add(addAlttitleQuery,Occur.SHOULD);		
		
		/* if(alttitle2Query != null)
			additional.add(alttitle2Query,Occur.SHOULD); */
		
		coreQuery.add(bq,Occur.MUST);
		coreQuery.add(additional,Occur.SHOULD);
		//coreQuery.add(addTitleQuery,Occur.SHOULD);
			
		return coreQuery;
		
	}
	
	public Query parseWithWildcards(String queryText, NamespacePolicy policy, Wildcards wildcards){
		return parseMultiPass(queryText,policy,false,false,wildcards);
	}
	
	/**
	 * Three parse pases: contents, title, redirect
	 * 
	 * @param queryText
	 * @param policy
	 * @return
	 * @throws ParseException
	 */
	public Query parseThreePass(String queryText, NamespacePolicy policy) throws ParseException{
		return parseMultiPass(queryText,policy,true,false,null);
	}
	
	/**
	 * Depending on settings for db, do all 4 passes of parsing:
	 * 1) contents
	 * 2) titles
	 * 3) redirects
	 * 4) keywords
	 */
	public Query parseFourPass(String queryText, NamespacePolicy policy, String dbname) throws ParseException{
		boolean makeKeywords = global.useKeywordScoring(dbname);
		return parseMultiPass(queryText,policy,true,makeKeywords,null);
	}
	
	public Query parseFourPass(String queryText, NamespacePolicy policy, boolean makeKeywords) throws ParseException{
		return parseMultiPass(queryText,policy,true,makeKeywords,null);
	}
	
	/** 
	 * Parse the query according to policy. Instead of rewrite phrase, simply pass 
	 * twice the query with different default fields. 
	 * 
	 * @param queryText
	 * @param policy
	 * @return
	 * @throws ParseException
	 */
	public Query parseTwoPass(String queryText, NamespacePolicy policy) throws ParseException{
		return parseMultiPass(queryText,policy,false,false,null);
	}
	
	public NamespacePolicy getNamespacePolicy() {
		return namespacePolicy;
	}
	public void setNamespacePolicy(NamespacePolicy namespacePolicy) {
		this.namespacePolicy = namespacePolicy;
	}
	public FieldBuilder.BuilderSet getBuilder() {
		return builder;
	}
	public void setBuilder(FieldBuilder.BuilderSet builder) {
		this.builder = builder;
	}
	
	public ArrayList<String> getWords(){
		return words;
	}
	
}
