package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
import java.util.Collection;
import java.util.Collections;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.List;
import java.util.Map.Entry;

import javax.swing.plaf.multi.MultiPopupMenuUI;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.Term;
import org.apache.lucene.search.AggregateInfo;
import org.apache.lucene.search.ArticleQueryWrap;
import org.apache.lucene.search.ArticleScaling;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.LogTransformScore;
import org.apache.lucene.search.MultiPhraseQuery;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.PositionalMultiQuery;
import org.apache.lucene.search.PositionalOptions;
import org.apache.lucene.search.PositionalQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.RelevanceQuery;
import org.apache.lucene.search.TermQuery;
import org.apache.lucene.search.BooleanClause.Occur;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.config.IndexId.AgeScaling;
import org.wikimedia.lsearch.search.AggregateInfoImpl;
import org.wikimedia.lsearch.search.ArticleInfoImpl;
import org.wikimedia.lsearch.search.Fuzzy;
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
	protected ArrayList<String> words, wordsFromParser, wordsClean;
	protected ArrayList<ArrayList<String>> expandedWordsFromParser; // with all aliases, wildcards and fuzzy stuff
	protected ArrayList<ArrayList<String>> expandedWordsContents, expandedWordsTitle;
	protected ArrayList<ArrayList<Float>> expandedBoostContents, expandedBoostTitle, expandedBoostFromParser;
	protected ArrayList<ExpandedType> expandedTypes, expandedTypesFromParser;
	protected enum ExpandedType {WORD, WILDCARD, FUZZY, PHRASE};
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
	public static float CONTENTS_BOOST = 0.2f;
	
	// main phrase stuff:
	public static int MAINPHRASE_SLOP = 100;
	public static float MAINPHRASE_BOOST = 2f;
	public static float RELEVANCE_RELATED_BOOST = 8f;	
	public static float RELEVANCE_ALTTITLE_BOOST = 2.5f;
	public static float SECTIONS_BOOST = 0.25f;
	public static float ALTTITLE_BOOST = 0.5f;
	public static float RELATED_BOOST = 1f;
	// additional to main phrase:
	public static float ADD_RELATED_BOOST = 4f;
	
	public static float WILDCARD_BOOST = 2f;
	public static float FUZZY_BOOST = 4f;
	
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
	protected Fuzzy fuzzy = null;
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
	public ArrayList<Token> tokenizeForSpellCheck(String queryText){
		int level = 0; // parenthesis count
		int fieldLevel = -1;
		TokenType tokenType;
		boolean inPhrase = false;
		
		Analyzer oldAnalyzer = this.analyzer;
		this.analyzer = Analyzers.getReusableAnalyzer(filters,new TokenizerOptions.SpellCheckSearch());
		
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
			}
			
			if(inPhrase);
			else if(c == ')'){
				level--;
				if(level < fieldLevel)
					fieldLevel = -1;
				continue;
			} else if(c == '('){
				level++;	
				continue;
			} else if(fieldLevel != -1 && level>fieldLevel)
				continue;
			
			if(isTermChar(c) && text[cur]!='-'){
				int start = cur;
				tokenType = fetchToken(inPhrase);
				if(tokenType == TokenType.WORD && (start==0 || text[start-1]!='-')){
					String type = "word";
					if(bufferIsWildCard())
						type = "wildcard";
					else if(bufferIsFuzzy())
						type = "fuzzy";
					analyzeBuffer();
					for(Token t : tokens){
						if(t.getPositionIncrement() > 0){
							ret.add(new Token(t.termText(),start+t.startOffset(),start+t.endOffset(),type));
						}
					}					
				}
			} else if(c == '[' && !inPhrase){
				fetchGenericPrefix();
			}
		}
		
		this.analyzer = oldAnalyzer;
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
	
	private NamespaceFilter getNamespaceFilter(String fieldName){
		if(fieldName == null)
			return defaultNamespaceFilter;
		else if(namespaceFilters.contains(fieldName))
			return namespaceFilters.get(fieldName);
		else if(fieldName.startsWith("["))
			return new NamespaceFilter(fieldName.substring(1,fieldName.length()-1));
		else
			return defaultNamespaceFilter;
	}
	
	private final boolean isTermChar(char ch){
		return !Character.isWhitespace(ch) && ch != ':' && ch != '(' && ch != ')' && ch !='[' && ch != ']' && ch != ',' && ch != ';' && ch != '"'; 
	}
	
	/**
	 * Fetch token into <code>buffer</code> starting from current position (<code>cur</code>)
	 * 
	 * @return type of the token in buffer
	 */
	private TokenType fetchToken(){
		return fetchToken(false);
	}
	private TokenType fetchToken(boolean termOnly){
		char ch;
		prev_cur = cur;
		for(length = 0; cur < queryLength; cur++){
			ch = text[cur];
			if(length == 0 && ch == ' ')
				continue; // ignore whitespaces
			
			// pluses and minuses, underscores can be within words (to prevent to be missinterpeted), *,? are for wildcard queries
			if(isTermChar(ch)){
				if(length<buffer.length)
					buffer[length++] = ch;
			} else{
				cur--; // position before the nonletter character
				break;
			}
		}
		if(length == 0)
			return TokenType.EOF;
		
		if(termOnly)
			return TokenType.WORD;		
		
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
	private Query parsePhrase(){				
		// special case for incategory 
		if(currentField!=null && currentField.equals("incategory")){
			length = 0;
			for(; cur < queryLength ; cur++ ){
				if(text[cur] == '"')
					break;
				else if(length < buffer.length)
					buffer[length++] = text[cur];
			}
			if(length > 0){
				// no tokenization, we want whole category name
				return new TermQuery(makeTerm());
			}
			return null;
		} 
		//PositionalMultiQuery query = new PositionalMultiQuery(new PositionalOptions.PhraseQueryFallback());
		MultiPhraseQuery query = new MultiPhraseQuery();
		for(; cur < queryLength ; cur++ ){
			length = 0;
			// fetch next word
			while(cur<queryLength && isTermChar(text[cur]) && length<buffer.length){
				buffer[length++] = text[cur++];
			}
			
			// add to phrase
			if(length > 0){
				boolean added = false;
				if(bufferIsWildCard()){
					Term term = makeTerm();
					Term[] terms = wildcards.makeTerms(term.text(),term.field());
					if(terms != null){
						query.add(terms);
						ArrayList<String> words = wildcards.getWords(term.text());
						expandedWordsFromParser.add(words);
						expandedTypesFromParser.add(ExpandedType.WILDCARD);
						ArrayList<Float> boosts = new ArrayList<Float>();
						for(int i=0;i<words.size();i++) boosts.add(1f);
						expandedBoostFromParser.add(boosts);
						added = true;
					}
				}
				if(bufferIsFuzzy()){
					Term term = makeTerm();
					NamespaceFilter nsf = getNamespaceFilter(currentField);
					Term[] terms = fuzzy.makeTerms(term.text(),term.field(),nsf);
					if(terms != null){
						//query.add(terms,fuzzy.getBoosts(term.text(),nsf,terms));
						query.add(terms);
						ArrayList<String> words = fuzzy.getWords(term.text(),nsf);
						expandedWordsFromParser.add(words);
						expandedTypesFromParser.add(ExpandedType.FUZZY);
						expandedBoostFromParser.add(fuzzy.getBoosts(term.text(),nsf,words));
						added = true;
					}
				}
				if(!added){
					// fallback to ordinary words
					analyzeBuffer();
					for(Token token : tokens){
						if(token.getPositionIncrement()>0){ // ignore aliases and stemmed words
							Term t = makeTerm(token);
							addToWords(t.text(),1,ExpandedType.PHRASE);
							query.add(t);
						}
					}				
				}
			}			
			// end of phrase query
			if(cur < queryLength && text[cur] == '"')
				break;
		}
		if(query.getPositions().length > 0){
			query.setBoost(defaultBoost);
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
	
	private boolean bufferIsFuzzy(){
		return length>1 && (buffer[0]=='~' || buffer[length-1]=='~');
	}
	
	private void addToWords(String w){
		addToWords(w,1,ExpandedType.WORD);
	}
	private void addToWords(String w, float boost, ExpandedType type){
		wordsFromParser.add(w);
		ArrayList<String> ew = new ArrayList<String>();
		ew.add(w);
		expandedWordsFromParser.add(ew);
		expandedTypesFromParser.add(type);
		ArrayList<Float> eb = new ArrayList<Float>();
		eb.add(boost);
		expandedBoostFromParser.add(eb);
	}
	
	private void addToWordsAsAlias(String w){
		ArrayList<String> ew = expandedWordsFromParser.get(expandedWordsFromParser.size()-1);
		ew.add(w);
		ArrayList<Float> eb = expandedBoostFromParser.get(expandedBoostFromParser.size()-1);
		eb.add(0.5f);
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
			Query ret = wildcards.makeQuery(term.text(),term.field());
			if(ret != null){
				ArrayList<String> words = wildcards.getWords(term.text());
				expandedWordsFromParser.add(words);
				expandedTypesFromParser.add(ExpandedType.WILDCARD);
				ArrayList<Float> boosts = new ArrayList<Float>();
				for(int i=0;i<words.size();i++) boosts.add(1f);
				expandedBoostFromParser.add(boosts);
				ret.setBoost(WILDCARD_BOOST);
				return ret;
			} else{
				// something is wrong, try making normal query
				addToWords(term.text());
				return new TermQuery(term);
			}
		}
		// parse fuzzy queries
		if(length>1 && fuzzy != null && bufferIsFuzzy()){
			Term term = makeTerm();
			String termText = term.text().replaceAll("~","");
			NamespaceFilter nsf = getNamespaceFilter(currentField);
			Query ret = fuzzy.makeQuery(termText,term.field(),nsf);
			if(ret != null){
				ArrayList<String> words = fuzzy.getWords(termText,nsf);
				expandedWordsFromParser.add(words);
				expandedTypesFromParser.add(ExpandedType.FUZZY);
				expandedBoostFromParser.add(fuzzy.getBoosts(termText,nsf,words));
				ret.setBoost(FUZZY_BOOST);
				return ret;
			}
		}
		
		if(toplevelOccur == BooleanClause.Occur.MUST_NOT)
			addAliases = false;

		if(tokens.size() == 1){		
			t = new TermQuery(makeTerm(tokens.get(0)));
			t.setBoost(defaultBoost);
			if(toplevelOccur != Occur.MUST_NOT)
				addToWords(t.getTerm().text());
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
					addToWords(t.getTerm().text());
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
						t.setBoost(defaultAliasBoost*defaultBoost);
						query.add(t,Occur.SHOULD);
						addToWordsAsAlias(alias.termText());
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
		expandedWordsFromParser = new ArrayList<ArrayList<String>>();
		expandedTypesFromParser = new ArrayList<ExpandedType>();
		expandedBoostFromParser = new ArrayList<ArrayList<Float>>();
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
		/** fuzzy queries interface */
		Fuzzy fuzzy = null;
		
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
		public ParsingOptions(NamespacePolicy policy, Wildcards wildcards, Fuzzy fuzzy){
			this.policy = policy;
			this.wildcards = wildcards;
			this.fuzzy = fuzzy;
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
		this.fuzzy = options.fuzzy;
		queryText = quoteCJK(queryText);
		NamespacePolicy defaultPolicy = this.namespacePolicy;
		if(options.policy != null)
			this.namespacePolicy = options.policy;		
		defaultBoost = CONTENTS_BOOST;
		defaultAliasBoost = ALIAS_BOOST;
		Query qc = parseRaw(queryText);		
		expandedWordsContents = expandedWordsFromParser;
		expandedBoostContents = expandedBoostFromParser;
		Query qt = makeTitlePart(queryText);
		words = wordsFromParser;
		wordsClean = cleanupWords(words);
		expandedWordsTitle = expandedWordsFromParser;
		expandedBoostTitle = expandedBoostFromParser;
		expandedTypes = expandedTypesFromParser;
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
		
		if(options.coreQueryOnly || words == null || (expandedWordsContents.size()==0 && expandedWordsTitle.size()==0))
			return bq;
		
		// filter out stop words to SHOULD (this enables queries in form of question)
		if(!allStopWords(words)){
			filterStopWords(bq);
		}
		
		// work out singular forms of words
		ArrayList<String> singularWords = makeSingularWords(words);
		
		// work out stemmed words
		ArrayList<String> stemmedWords = filters.stem(words);
		if(stemmedWords != null && (stemmedWords.equals(words) || stemmedWords.equals(singularWords) || stemmedWords.size()==0))
			stemmedWords = null;

		// main phrase combined with relevance meatrics
		Query mainPhrase = makeMainPhraseWithRelevance(words,expandedWordsContents,expandedWordsTitle,expandedTypes,expandedBoostContents,expandedBoostTitle);
		if(mainPhrase == null)
			return bq;

		// additional queries
		Query related = new LogTransformScore(makeRelatedRelevance(expandedWordsTitle,expandedBoostTitle,expandedTypes,ADD_RELATED_BOOST));
		
		// full query
		BooleanQuery additional = new BooleanQuery(true);
		additional.add(mainPhrase,Occur.MUST);
		if(related != null)
			additional.add(related,Occur.SHOULD); 
		
		BooleanQuery full = new BooleanQuery(true);
		full.add(bq,Occur.MUST);
		full.add(additional,Occur.SHOULD);
		
		// redirect match (when redirect is not contained in contents or title)
		if(hasWildcards() || hasFuzzy()){
			Query redirectsMulti = makeAlttitleForRedirectsMulti(expandedWordsTitle,expandedBoostTitle,expandedTypes,20,1f);
			if(redirectsMulti != null)
				full.add(redirectsMulti,Occur.SHOULD);
		} else{
			Query redirects = makeAlttitleForRedirects(words,20,1);
			if(redirects != null)
				full.add(redirects,Occur.SHOULD);
			if(singularWords != null){
				Query redirectsSing = makeAlttitleForRedirects(singularWords,20,0.8f);
				if(redirectsSing != null)
					full.add(redirectsSing,Occur.SHOULD);
			}		
		}
		
		
		BooleanQuery wrap = new BooleanQuery(true);
		wrap.add(full,Occur.SHOULD);
		wrap.add(makeComplete(expandedWordsTitle,expandedBoostTitle,expandedTypes),Occur.SHOULD);
		if(forbidden != null)
			wrap.add(forbidden,Occur.MUST_NOT);
		
		// init global scaling of articles 
		ArticleScaling scale = new ArticleScaling.None();
		// based on age
		AgeScaling age = iid.getAgeScaling();
		if(age != AgeScaling.NONE){
			switch(age){
			case STRONG: scale = new ArticleScaling.StepScale(0.3f,1); break;
			case MEDIUM: scale = new ArticleScaling.StepScale(0.6f,1); break;
			case WEAK: scale = new ArticleScaling.StepScale(0.9f,1); break;
			default: throw new RuntimeException("Unsupported age scaling "+age);
			}  
			
		}
		// additional rank
		AggregateInfo rank = iid.useAdditionalRank()? new AggregateInfoImpl() :  null; 
		return new ArticleQueryWrap(wrap,new ArticleInfoImpl(),scale,iid.getNamespacesWithSubpages(),rank);
			
	}
	
	private ArrayList<String> makeSingularWords(ArrayList<String> words){		
		if(filters.hasSingular()){
			ArrayList<String> singularWords = new ArrayList<String>();
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
			if(diff) // all words are same as original words
				return singularWords;
		}
		return null;
	}
	
	/** Make alternate "complete" query that will match redirects not in contents like los angles -> los angeles */
	private Query makeComplete(ArrayList<ArrayList<String>> expanded, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types) {
		return makePositionalMulti(expanded,boosts,types,fields.alttitle(),new PositionalOptions.RedirectComplete(),0,1);
		/* PositionalQuery pq = new PositionalQuery(new PositionalOptions.RedirectComplete());
		for(int i=0;i<expanded.size();i++){
			for(String w : expanded.get(i)){
				pq.add(new Term(fields.alttitle(),w),i,stopWords.contains(w));
			}
		}
		return pq; */
	}

	private ArrayList<String> cleanupWords(ArrayList<String> words) {
		ArrayList<String> ret = new ArrayList<String>();
		for(String w : words){
			ret.add(FastWikiTokenizerEngine.clearTrailing(w));
		}
		return ret;
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
	 *  @return query */
	protected Query makeTitlePart(String queryText) {
		// push on stack
		String contentField = defaultField;
		float olfDefaultBoost = defaultBoost;

		// stemmed title
		Query qs = null;
		if(ADD_STEM_TITLE && builder.getFilters().hasStemmer()){
			defaultField = fields.stemtitle(); 
			defaultBoost = STEM_TITLE_BOOST;
			defaultAliasBoost = STEM_TITLE_ALIAS_BOOST;
			qs = parseRaw(queryText);
		}
		// title
		defaultField = fields.title(); 
		defaultBoost = (qs!= null)? TITLE_BOOST : TITLE_BOOST+STEM_TITLE_BOOST; 
		defaultAliasBoost = TITLE_ALIAS_BOOST;		
		Query qt = parseRaw(queryText);
		
		// pop stack
		defaultField = contentField;
		defaultBoost = olfDefaultBoost;
		defaultAliasBoost = ALIAS_BOOST;

		
		if(qt==qs || qt.equals(qs)) // either null, or category query
			return qt;
		if(qt == null)
			return qs;
		if(qs == null)
			return qt;
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(qt,Occur.SHOULD);
		bq.add(qs,Occur.SHOULD);
		return bq;
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
	
	/** Get phrase words, valid only after parse() call - trailing chars cleared */
	public ArrayList<String> getWordsClean(){		
		return wordsClean;
	}
	/** Valid after parse(), returns if the last query had phrases in it */
	public boolean hasPhrases(){
		if(expandedTypes == null)
			return false;
		for(ExpandedType t : expandedTypes){
			if(t == ExpandedType.PHRASE)
				return true;
		}
		return false;
	}
	
	/** Make the main phrases with relevance metrics */ 
	protected Query makeMainPhraseWithRelevance(ArrayList<String> words, ArrayList<ArrayList<String>> expandedWordsContents, ArrayList<ArrayList<String>> expandedWordsTitle, 
			ArrayList<ExpandedType> expandedTypes, ArrayList<ArrayList<Float>> expandedBoostContents, ArrayList<ArrayList<Float>> expandedBoostTitle){
		Query main = null;
						
		// all words as entered into the query
		Query exact = (!hasFuzzy() && !hasWildcards())? makePositional(words,fields.contents(),new PositionalOptions.Exact(),0,0.8f) :  null;
		// words in right order (not stemmed)
		Query inorder = makePositionalMulti(expandedWordsTitle,expandedBoostTitle,expandedTypes,fields.contents(),new PositionalOptions.Exact(),0,exact==null? 1f : 0.2f);
		// words + stemmed + singulars + transliterations + wildcards + fuzzy - with slop factor
		Query sloppy = makePositionalMulti(expandedWordsContents,expandedBoostContents,expandedTypes,fields.contents(),new PositionalOptions.Sloppy(),MAINPHRASE_SLOP,1,false);
		
		Query sections = makeSectionsQuery(expandedWordsTitle,expandedBoostTitle,expandedTypes,SECTIONS_BOOST);
		Query alt = null; // makeAlttitleRelevance(expandedWordsTitle,expandedTypes,ALTTITLE_BOOST);
		Query rel = null; // makeRelatedRelevance(expandedWordsTitle,expandedTypes,RELATED_BOOST);
		// wordnet synonyms
		ArrayList<ArrayList<String>> wordnet = WordNet.replaceOne(words,iid.getLangCode());
				
		BooleanQuery combined = new BooleanQuery(true);
		if(exact != null)
			combined.add(exact,Occur.SHOULD);
		if(inorder!=null)
			combined.add(inorder,Occur.SHOULD);
		// combined various queries into mainphrase 
		if(sloppy != null){			
			combined.add(sloppy,Occur.SHOULD);
			// wordnet			
			if(wordnet != null){
				for(ArrayList<String> wnwords : wordnet){
					if(!allStopWords(wnwords))
						combined.add(makePositional(wnwords,fields.contents(),new PositionalOptions.Sloppy(),MAINPHRASE_SLOP,1),Occur.SHOULD);
				}
			}
		}
		if(sections!=null)
			combined.add(sections,Occur.SHOULD);
		if(alt != null)
			combined.add(alt,Occur.SHOULD);
		if(rel != null)
			combined.add(rel,Occur.SHOULD);
		
		if(combined.getClauses().length == 1)
			main = combined.getClauses()[0].getQuery();
		else
			main = combined;
			
				
		main.setBoost(MAINPHRASE_BOOST);
		
		// relevance: alttitle
		Query alttitle = makeAlttitleRelevance(expandedWordsTitle,expandedBoostTitle,expandedTypes,RELEVANCE_ALTTITLE_BOOST);
		ArrayList<Query> altAdd = new ArrayList<Query>();
		if(wordnet!=null)
			for(ArrayList<String> wnwords : wordnet)
				if(!allStopWords(wnwords))
					altAdd.add(makeAlttitleRelevance(wnwords,RELEVANCE_ALTTITLE_BOOST));
		alttitle = simplify(combine(alttitle,altAdd));
		
		// relevance: related
		Query related = makeRelatedRelevance(expandedWordsTitle,expandedBoostTitle,expandedTypes,RELEVANCE_RELATED_BOOST);
		ArrayList<Query> relAdd = new ArrayList<Query>();
		if(wordnet!=null)
			for(ArrayList<String> wnwords : wordnet)
				if(!allStopWords(wnwords))
					relAdd.add(makeRelatedRelevance(wnwords,RELEVANCE_RELATED_BOOST));
		related = simplify(combine(related,relAdd));
		
		BooleanQuery relevances = new BooleanQuery(true);
		relevances.add(alttitle,Occur.SHOULD);
		relevances.add(related,Occur.SHOULD);
		
		RelevanceQuery whole = new RelevanceQuery(main);
		whole.addRelevanceMeasure(relevances);
		
		return whole;
	}
	
	/** Combine one main query with a number of other queries into a boolean query */
	private Query combine(Query query, ArrayList<Query> additional) {
		if(additional.size()==0)
			return query;
		BooleanQuery bq = new BooleanQuery(true);
		bq.add(query,Occur.SHOULD);
		for(Query q : additional){
			if(q != null)
				bq.add(q,Occur.SHOULD);
		}
		if(bq.clauses().size()==1)
			return query;
		return bq;
	}	
	
	/** Convert multiple OR-like queries into one with larger boost */
	protected Query simplify(Query q){
		if(q instanceof BooleanQuery){
			BooleanQuery bq = (BooleanQuery)q;
			if(!allShould(bq))
				return q;
			// query -> boost
			HashMap<Query,Float> map = new HashMap<Query,Float>();
			extractAndSimplify(bq,map,1);
			
			// simplify
			BooleanQuery ret = new BooleanQuery(true);
			for(Entry<Query,Float> e : map.entrySet()){
				Query qt = (Query) e.getKey();
				qt.setBoost(e.getValue());
				ret.add(qt,Occur.SHOULD);
			}
			return ret;
		}
		return q;
	}
	
	private boolean allShould(BooleanQuery bq){
		for(BooleanClause cl : bq.getClauses()){
			if(!cl.getOccur().equals(Occur.SHOULD))
				return false;
			if(cl.getQuery() instanceof BooleanQuery){
				if(!allShould((BooleanQuery)cl.getQuery()))
					return false;
			}
		}
		return true;
	}
	
	private void extractAndSimplify(BooleanQuery bq, HashMap<Query,Float> map, float parentBoost){
		for(BooleanClause cl : bq.getClauses()){
			Query q = cl.getQuery();
			if(q instanceof BooleanQuery)
				extractAndSimplify((BooleanQuery)q,map,parentBoost*bq.getBoost());
			else{
				Float boost = map.get(q);
				float b = boost==null? 0 : boost;
				b += q.getBoost()*bq.getBoost()*parentBoost;
				map.put(q,b);
			}
		}
	}
	
	/** Make positional query by including all of the stop words */
	protected PositionalQuery makePositional(ArrayList<String> words, String field, PositionalOptions options, int slop, float boost){
		return makePositional(words,field,options,slop,boost,true);
	}
	
	/** Make generic positional query */
	protected PositionalQuery makePositional(ArrayList<String> words, String field, PositionalOptions options, int slop, float boost, boolean includeStopWords){
		PositionalQuery pq = new PositionalQuery(options);
		int pos = 0;
		for(String w : words){
			boolean isStop = stopWords.contains(w);
			if(!(isStop && !includeStopWords))
				pq.add(new Term(field,w),pos,isStop);
			pos++;
		}
		if(slop != 0)
			pq.setSlop(slop);
		pq.setBoost(boost);
		if(pq.getPositions().length > 0)
			return pq;
		else return null;
	}
	/** Make generic multi query with all stopwords */
	protected Query makePositionalMulti(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, String field, PositionalOptions options, int slop, float boost){
		return makePositionalMulti(words,boosts,types,field,options,slop,boost,true);
	}
	
	/** Make generic multipositional query */
	protected Query makePositionalMulti(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, String field, PositionalOptions options, int slop, float boost, boolean includeStopWords){
		PositionalMultiQuery mq = new PositionalMultiQuery(options);
		int pos = 0;
		for(int i=0;i<words.size();i++){
			ArrayList<String> ws = words.get(i);
			ArrayList<Float> wb = boosts.get(i);
			if(ws.size() == 1){
				String w = ws.get(0);
				boolean stop = stopWords.contains(w);
				if(!(stop && !includeStopWords))
					mq.add(new Term(field,w),pos,stop);
			} else if(ws.size() > 1){				
				if(!(!includeStopWords && stopWords.contains(ws.get(0)) && types!=null && types.get(i)==ExpandedType.WORD)){
					ArrayList<Term> expanded = new ArrayList<Term>();
					ArrayList<Float> expBoost = new ArrayList<Float>();
					for(int j=0;j<ws.size();j++){
						String w = ws.get(j);
						if(!includeStopWords && stopWords.contains(w))
							continue;
						expanded.add(new Term(field,w));
						expBoost.add(wb.get(j));
					}
					if(expanded.size() > 0)
						mq.add(expanded.toArray(new Term[]{}),pos,expBoost);
				}
			}
			pos++;
		}
		mq.setSlop(slop);
		mq.setBoost(boost);
		if(mq.getPositions().length > 0)
			return mq;
		else return null;
	}

	/** Make query with short subphrases anchored in non-stop words */
	protected Query makeAnchoredQuery(ArrayList<String> words, String field, 
			PositionalOptions options, PositionalOptions whole, PositionalOptions wholeSloppy,
			float boost, int slop){
		BooleanQuery bq = new BooleanQuery(true);
		if(words.size() == 1){
			PositionalQuery pq = makePositional(words,field,options,0,1f);
			bq.add(pq,Occur.SHOULD);
		} else{
			// add words
			for(String w : words){
				PositionalQuery pq = new PositionalQuery(options);
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
					bq.add(makePositional(phrase,field,options,0,phrase.size()),Occur.SHOULD);
			}
		}
		// add the whole-only query
		if(whole != null)
			bq.add(makePositional(words,field,whole,slop,1),Occur.SHOULD);
		if(wholeSloppy != null){
			Query ws = makePositional(words,field,wholeSloppy,slop,1,false);
			if(ws != null)
				bq.add(ws,Occur.SHOULD);
		}
		bq.setBoost(boost);
		
		return bq;
	}
	
	private int countNonStopWords(ArrayList<String> words){
		int count = 0;
		for(String w : words){
			if(!stopWords.contains(w))
				count++;
		}
		return count;
	}
	
	/** Make query with short subphrases anchored in non-stop words */
	protected Query makeAnchoredQueryMulti(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, 
			String field, PositionalOptions options, PositionalOptions whole, PositionalOptions wholeSloppy, 
			float boost, int slop){
		BooleanQuery bq = new BooleanQuery(true);
		if(words.size() == 1){
			Query pq = makePositionalMulti(words,boosts,types,field,options,0,1f);
			bq.add(pq,Occur.SHOULD);
		} else{
			// add words
			for(int i=0;i<words.size();i++){
				ArrayList<ArrayList<String>> wrap = new ArrayList<ArrayList<String>>();
				wrap.add(words.get(i));
				ArrayList<ArrayList<Float>> boostWrap = new ArrayList<ArrayList<Float>>();
				boostWrap.add(boosts.get(i));
				bq.add(makePositionalMulti(wrap,boostWrap,types,field,options,0,1f),Occur.SHOULD);
			}
			// phrases
			int i =0;
			ArrayList<ArrayList<String>> phrase = new ArrayList<ArrayList<String>>();
			ArrayList<ArrayList<Float>> phraseBoost = new ArrayList<ArrayList<Float>>();
			while(i < words.size()){
				phrase.clear();
				phraseBoost.clear();
				for(;i<words.size();i++){
					// make phrases anchored in non-stop words
					ArrayList<String> ww = words.get(i);
					if(phrase.size() == 0){
						phrase.add(ww);
						phraseBoost.add(boosts.get(i));
					} else if(types.get(i) == ExpandedType.WORD && stopWords.contains(ww.get(0))){
						phrase.add(ww);
						phraseBoost.add(boosts.get(i));
					} else{
						phrase.add(ww);
						phraseBoost.add(boosts.get(i));
						break;
					}
				}
				if(phrase.size() > 1){
					bq.add(makePositionalMulti(phrase,phraseBoost,null,field,options,0,phrase.size()),Occur.SHOULD);
				}
			}
		}
		// add the whole-only query
		if(whole != null)
			bq.add(makePositionalMulti(words,boosts,types,field,whole,slop,1),Occur.SHOULD);
		if(wholeSloppy != null){
			Query ws = makePositionalMulti(words,boosts,types,field,wholeSloppy,slop,0.5f,false);
			if(ws != null)
				bq.add(ws,Occur.SHOULD);
		}
		bq.setBoost(boost);
		
		return bq;
	}
	
	/** Query for section headings */
	protected Query makeSectionsQuery(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, float boost){
		return makeAnchoredQueryMulti(words,boosts,types,fields.sections(),new PositionalOptions.Sections(),null,null,boost,0);
	}
	
	/** Relevance metrics based on rank (of titles and redirects) */
	protected Query makeAlttitleRelevance(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, float boost){
		return makeAnchoredQueryMulti(words,boosts,types,fields.alttitle(),new PositionalOptions.Alttitle(),new PositionalOptions.AlttitleWhole(),new PositionalOptions.AlttitleWholeSloppy(),boost,20);
	}
	
	/** Make relevance metrics based on context via related articles */
	protected Query makeRelatedRelevance(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, float boost){
		return makeAnchoredQueryMulti(words,boosts,types,fields.related(),new PositionalOptions.Related(),null,null,boost,0);
	}
	
	/** Relevance metrics based on rank (of titles and redirects) */
	protected Query makeAlttitleRelevance(ArrayList<String> words, float boost){
		return makeAnchoredQuery(words,fields.alttitle(),new PositionalOptions.Alttitle(),new PositionalOptions.AlttitleWhole(), new PositionalOptions.AlttitleWholeSloppy(),boost,20);
	}

	
	/** Make relevance metrics based on context via related articles */
	protected Query makeRelatedRelevance(ArrayList<String> words, float boost){
		return makeAnchoredQuery(words,fields.related(),new PositionalOptions.Related(),null,null,boost,0);
	}

		
	/** Additional query to match words in redirects that are not in title or article */
	protected Query makeAlttitleForRedirects(ArrayList<String> words, int slop, float boost){
		return makePositional(words,fields.alttitle(),new PositionalOptions.RedirectMatch(),slop,boost);
	}

	protected Query makeAlttitleForRedirectsMulti(ArrayList<ArrayList<String>> words, ArrayList<ArrayList<Float>> boosts, ArrayList<ExpandedType> types, int slop, float boost){
		return makePositionalMulti(words,boosts,types,fields.alttitle(),new PositionalOptions.RedirectMatch(),slop,boost);		
	}
		
	/** Make alttitle phrase for titles indexes  */
	public Query makeAlttitleForTitles(List<String> words){
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
		
		sloppy.setSlop(10);
		main.add(exact,Occur.SHOULD);
		main.add(sloppy,Occur.SHOULD);
		main.setBoost(1);
		return main;
			
	}
	
	/** Make a query to search grouped titles indexes */
	public Query parseForTitles(String queryText){
		String oldDefaultField = this.defaultField;
		NamespacePolicy oldPolicy = this.namespacePolicy;
		FieldBuilder.BuilderSet oldBuilder = this.builder;
		this.defaultField = "alttitle";
		this.namespacePolicy = NamespacePolicy.IGNORE;
		
		Query q = parseRaw(queryText);
		
		ArrayList<String> words = wordsFromParser;		
		
		this.builder = oldBuilder;		
		this.defaultField = oldDefaultField;
		this.namespacePolicy = oldPolicy;
		
		words = wordsFromParser;
		expandedWordsTitle = expandedWordsFromParser;
		expandedBoostTitle = expandedBoostFromParser;
		expandedTypes = expandedTypesFromParser;

		BooleanQuery forbidden = extractForbidden(q);
		
		BooleanQuery full = new BooleanQuery(true);
		full.add(q,Occur.MUST);

		if(expandedWordsTitle.size() == 0)
			return full;
		
		// fuzzy & wildcards
		// NOTE: for these to work parseForTitles needs to called after parse()
		Query redirectsMulti = makeAlttitleForRedirectsMulti(expandedWordsTitle,expandedBoostTitle,expandedTypes,20,1f);
		if(redirectsMulti != null)
			full.add(redirectsMulti,Occur.SHOULD);

		// add another for complete matches
		BooleanQuery wrap = new BooleanQuery(true);
		wrap.add(full,Occur.SHOULD);
		wrap.add(makeComplete(expandedWordsTitle,expandedBoostTitle,expandedTypes),Occur.SHOULD);
		if(forbidden != null)
			wrap.add(forbidden,Occur.MUST_NOT);
		
		return wrap;
		
	}
	
	/** check if all the words in the array are stop words */
	private boolean allStopWords(ArrayList<String> words){
		if(words == null || words.size() == 0)
			return false;
		for(String w : words){
			if(!stopWords.contains(w)){
				return false;
			}
		}
		return true;
	}

	/** Valid after parse() call - contents terms to be highlighted */
	public Term[] getHighlightTerms() {
		return highlightTerms;
	}
	
	/** @return if last parsed query had wildcards in it */
	public boolean hasWildcards(){
		return wildcards!=null && wildcards.hasWildcards();
	}
	/** @return if last parsed query has fuzzy words in it */
	public boolean hasFuzzy(){
		return fuzzy!=null && fuzzy.hasFuzzy();
	}
	
	public void setNamespacePolicy(NamespacePolicy namespacePolicy) {
		this.namespacePolicy = namespacePolicy;
	}



}
