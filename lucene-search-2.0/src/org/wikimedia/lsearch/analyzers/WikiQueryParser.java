package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.index.Term;
import org.apache.lucene.queryParser.ParseException;
import org.apache.lucene.search.BooleanClause;
import org.apache.lucene.search.BooleanQuery;
import org.apache.lucene.search.PhraseQuery;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.TermQuery;
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
	protected enum TokenType {WORD, FIELD, AND, OR, EOF };
		
	private TokenStream tokenStream; 
	private ArrayList<Token> tokens; // tokens from analysis
	
	/** sometimes the fieldsubquery takes the bool modifier, to retrieve it, use this variable,
	 *  this will always point to the last unused bool modifier */
	BooleanClause.Occur explicitOccur = null;  
	
	/** Wheather to include aliases during title rewrite */
	protected boolean disableTitleAliases;
	
	/** boost for alias words from analyzer */
	public final float ALIAS_BOOST = 0.5f; 
	/** boost for title field */
	public final float TITLE_BOOST = 2;
	
	/** Policies in treating field names:
	 * 
	 * LEAVE - don't mess with field rewriting
	 * IGNORE - convert all field names to contents (except category)
	 * REWRITE -  rewrite (help:searchterm) => (+namespace:12 contents:searchterm)
	 */
	public enum NamespacePolicy { LEAVE, IGNORE, REWRITE };
	protected HashMap<String,Integer> namespaceMapping;
	private String defaultNamespace;
	private NamespacePolicy namespacePolicy;
	
	/** default value for boolean queries */
	public BooleanClause.Occur boolDefault = BooleanClause.Occur.MUST;
	
	private UnicodeDecomposer decomposer;
	private char[] decomp; // unicode decomposition letters
	private int decompi;
	
	private void initNamespaces(){
		namespaceMapping = new HashMap<String,Integer>();
		namespaceMapping.put("main",new Integer(0));
		namespaceMapping.put("main_talk",new Integer(1));
		namespaceMapping.put("user",new Integer(2));
		namespaceMapping.put("user_talk",new Integer(3));
		namespaceMapping.put("project",new Integer(4));
		namespaceMapping.put("project_talk",new Integer(5));
		namespaceMapping.put("image",new Integer(6));
		namespaceMapping.put("image_talk",new Integer(7));
		namespaceMapping.put("mediawiki",new Integer(8));
		namespaceMapping.put("mediawiki_talk",new Integer(9));
		namespaceMapping.put("template",new Integer(10));
		namespaceMapping.put("template_talk",new Integer(11));
		namespaceMapping.put("help",new Integer(12));
		namespaceMapping.put("help_talk",new Integer(13));
		namespaceMapping.put("category_text",new Integer(14));
		namespaceMapping.put("category_talk",new Integer(15));
	}
	
	/**
	 * Construct using default policy (LEAVE), without any namespace rewriting
	 * @param field   default field name
	 * @param analyzer
	 */
	public WikiQueryParser(String field, Analyzer analyzer){
		this(field,null,analyzer,NamespacePolicy.LEAVE);
	}
	
	/**
	 * Construct with default field (e.g. contents), with default namespace
	 * (e.g. main), and with analyzer and namespace policy
	 * @param field
	 * @param namespace
	 * @param analyzer
	 * @param nsPolicy
	 */
	public WikiQueryParser(String field, String namespace, Analyzer analyzer, NamespacePolicy nsPolicy){
		defaultField = field;
		defaultNamespace = namespace;
		this.analyzer = analyzer;
		decomposer = UnicodeDecomposer.getInstance();
		tokens = new ArrayList<Token>();
		this.namespacePolicy = nsPolicy;
		disableTitleAliases = true;
		initNamespaces();
	}
	
	/** 
	 * Get a hashset of namespace numbers for fields that are
	 * valid namespace keys. 
	 * @param queryText
	 * @return
	 */
	public HashSet<Integer> getFieldNamespaces(String queryText){
		HashSet<String> fields = getFields(queryText);
		HashSet<Integer> ret = new HashSet<Integer>();
		for(String field : fields){
			field = field.toLowerCase();
			if(namespaceMapping.get(field) != null)
				ret.add(namespaceMapping.get(field));
			else if(field.equals("all"))
				ret.add(new Integer(Integer.MAX_VALUE));
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
			
			if(Character.isLetter(c)){
				tokenType = fetchToken();
				if(tokenType == TokenType.FIELD){
					fieldLevel = level;
					fields.add(new String(buffer,0,length));
				} else if(tokenType == TokenType.WORD){
					if(fieldLevel == -1)
						fields.add(defaultField);
				}
			}
		}
		
		
		return fields;
	}
	
	/** rewrite field name (e.g. help) into a term query like namespace:12 */
	private Query getNamespaceQuery(String fieldName){
		if(fieldName == null || namespacePolicy != NamespacePolicy.REWRITE)
			return null;
		
		Integer i;
		if((i = namespaceMapping.get(fieldName))!=null){
			return new TermQuery(new Term("namespace",i.toString()));
		} else
			return null;
	}
	
	/**
	 * Fetch token into <code>buffer</code> starting from current position (<code>cur</code>)
	 * 
	 * Similar to <code>FastWikiTokenizerEngine</code>, automatically
	 * normalizes (strip accents) and lowercases the words
	 * @return type of the token in buffer
	 */
	private TokenType fetchToken(){
		char ch;
		prev_cur = cur;
		for(length = 0; cur < queryLength; cur++){
			ch = text[cur];
			if(length == 0 && ch == ' ')
				continue; // ignore whitespaces
			
			// pluses and minuses, underscores can be within words
			if(Character.isLetterOrDigit(ch) || ch=='-' || ch=='+' || ch=='_'){
				// unicode normalization -> delete accents
				decomp = decomposer.decompose(ch);
				if(decomp == null)
					buffer[length++] = ch;
				else{
					for(decompi = 0; decompi < decomp.length; decompi++)
						buffer[length++] = decomp[decompi];
				}				
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
				cur = lookup;
				return TokenType.FIELD;
			} else
				break;
		}
		
		return TokenType.WORD; 
	}
	
	/** Go back one token */
	private void backToken(){
		cur = prev_cur;
	}

	/** make <code>tokenStream</code> from lowercased <code>buffer</code> via analyzer */
	private void analyzeBuffer(){
		String analysisField = defaultField;
		tokenStream = analyzer.tokenStream(analysisField, 
				new String(buffer,0,length).toLowerCase());
		
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
			return new Term(defaultField,t.toLowerCase());
		else if(!field.equals("category") && 
				(namespacePolicy == NamespacePolicy.IGNORE || 
						namespacePolicy == NamespacePolicy.REWRITE))
			return new Term(defaultField,t.toLowerCase());
		else
			return new Term(field,t.toLowerCase());
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
			else
				buffer[length++] = text[cur];
		}
		if(length != 0){
			query = new PhraseQuery();
			// if it's a category don't tokenize it, we want whole category name
			if(field!=null && field.equals("category"))
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
		return defaultNamespace != null && namespacePolicy == NamespacePolicy.REWRITE; 
	}
	
	/** Parses a clause:  (in regexp notation)
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
			fieldQuery = getNamespaceQuery(defaultNamespace); 
		}
		
		mainloop: for( ; cur < queryLength; cur++ ){
			c = text[cur];
			
			if(c == ' ')
				continue;
			
			// terms, fields
			if(Character.isLetterOrDigit(c)){
				tokenType = fetchToken();
				
				switch(tokenType){
				case FIELD:
					// this is where the function returns if called from the
					// next if (i.e. some 10 lines down)
					if(returnOnFieldDef){
						String newfield = new String(buffer,0,length); 
						if(!newfield.equals("category") && !newfield.equals(topFieldName)){
							backToken(); cur--;
							break mainloop;
						}
					}
					if(field == null || definedExplicitField){
						// set field name
						field = new String(buffer,0,length);
						if((defaultNamespace!=null && field.equals(defaultNamespace)) || field.equals(defaultField)){
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
						String myfield = (topFieldName != null)? topFieldName : (field !=null)? field : (defaultNamespace!=null)? defaultNamespace : defaultField; 
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
		if(field != null && field.equals("category")){
			return new TermQuery(makeTerm());
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
						t = new TermQuery(new Term("stemmed",token.termText()));
						t.setBoost(ALIAS_BOOST*defaultBoost);
						cur.add(t,aliasOccur);
					} else if(token.type().equals("alias")){
						// produced by alias engine (e.g. for sr)
						t = new TermQuery(makeTerm(token));
						t.setBoost(ALIAS_BOOST*defaultBoost);
						cur.add(t,aliasOccur);
					}
					if( cur != bq) // returned from nested query
						cur = bq;
				} else{
					t = new TermQuery(makeTerm(token));
					t.setBoost(defaultBoost);
					if(tokens.size() > 2 && (i+1) < tokens.size() && tokens.get(i+1).getPositionIncrement()==0){
						// make nested query. this is needed when single word is tokenized
						// into many words of which they all have aliases
						// e.g. anti-hero => anti stemmed:anti hero stemmed:hero
						cur = new BooleanQuery();
						cur.add(t,BooleanClause.Occur.SHOULD);
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
		if(disableTitleAliases && tq.getBoost()==ALIAS_BOOST)
			return null;
		Term term = tq.getTerm();
		if(term.field().equals(defaultField)){
			TermQuery tq2 = new TermQuery(
					new Term("title",term.text()));
			tq2.setBoost(tq.getBoost()*TITLE_BOOST);
			
			return tq2;
		}
		return null;
	}
	
	/** Duplicate a phrase query, setting "title" as field */
	private PhraseQuery makeTitlePhraseQuery(PhraseQuery pq){
		if(disableTitleAliases && pq.getBoost()==ALIAS_BOOST)
			return null;
		PhraseQuery pq2 = new PhraseQuery();
		Term[] terms = pq.getTerms();
		if(terms.length > 0 && terms[0].field().equals(defaultField)){
			for(int j=0;j<terms.length;j++){
				pq2.add(new Term("title",terms[j].text()));
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
		if(policy != null)
			this.namespacePolicy = policy;
		float olfDefaultBoost = defaultBoost;
		defaultBoost = 1;
		Query qc = parseRaw(queryText);
		String contentField = defaultField;
		defaultField = "title"; // now parse the title part
		defaultBoost = TITLE_BOOST;
		Query qt = parseRaw(queryText);
		// pop stack
		defaultField = contentField;
		defaultBoost = olfDefaultBoost;
		if(qc.equals(qt))
			return qc; // don't duplicate (probably a query for categories only)
		BooleanQuery bq = new BooleanQuery();
		bq.add(qc,BooleanClause.Occur.SHOULD);
		bq.add(qt,BooleanClause.Occur.SHOULD);
		return bq;
	}

	
}
