package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Set;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.snowball.SnowballFilter;
import org.apache.lucene.analysis.th.ThaiWordFilter;
import org.wikimedia.lsearch.analyzers.EnglishSingularFilter.EnglishSingular;
import org.wikimedia.lsearch.analyzers.LanguageAnalyzer.ArrayTokens;
import org.wikimedia.lsearch.config.IndexId;


/**
 * Make a language-dependent pair of filters. The custom filter is to be applied before the stemmer.
 * 
 * @author rainman
 *
 */
public class FilterFactory {
	protected String lang;
	protected IndexId iid;
	protected String snowballName = null;
	protected boolean useStemmer,useLangFilter;
	protected Class stemmer = null;
	protected Class langFilter = null;
	protected boolean usingCJK = false;
	protected ArrayList<Class> additionalFilters = null;
	protected Singular singular = null;
	protected boolean hasCanonicalFilter = false;
	protected boolean hasLanguageVariants = false;
	
	protected FilterFactory noStemmerFilterFactory=null;
	protected Set<String> stopWords;
	
	public enum Type { FULL, NO_STEM, SPELL_CHECK };
	protected Type type = null;
	
	public FilterFactory(IndexId iid){
		this(iid,Type.FULL);
	}
		
	public FilterFactory(IndexId iid, Type type){
		this.lang = iid.getLangCode();
		this.iid = iid;
		this.type = type;
		init();
		noStemmerFilterFactory = new FilterFactory(iid,lang,snowballName,false,useLangFilter,null,langFilter,usingCJK,additionalFilters,singular,hasCanonicalFilter,stopWords,type); 
	}
		
	public FilterFactory(IndexId iid, String lang, String snowballName, boolean useStemmer, boolean useLangFilter, Class stemmer, Class langFilter, boolean usingCJK, ArrayList<Class> additionalFilters, Singular singular, boolean hasCanonicalFiler, Set<String> stopWords, Type type) {
		this.iid = iid;
		this.lang = lang;
		this.snowballName = snowballName;
		this.useStemmer = useStemmer;
		this.useLangFilter = useLangFilter;
		this.stemmer = stemmer;
		this.langFilter = langFilter;
		this.usingCJK = usingCJK;
		this.additionalFilters = additionalFilters;
		this.singular = singular;
		this.hasCanonicalFilter = hasCanonicalFiler;
		this.stopWords = stopWords;
		this.type = type;
	}
	
	public FilterFactory getNoStemmerFilterFactory() {
		if(noStemmerFilterFactory == null)
			return this;
		else
			return noStemmerFilterFactory;
	}

	protected void init(){
		if(lang == null)
			lang = "en";
		
		if(type == Type.FULL){
			useStemmer = true;
			// figure out stemmer
			if(lang.equals("en"))
				snowballName = "English";
			//stemmer = PorterStemFilter.class; // 2x faster but less accurate
			else if(lang.equals("da"))
				snowballName = "Danish";
			else if(lang.equals("nl"))
				snowballName = "Dutch";
			else if(lang.equals("fi"))
				snowballName = "Finnish";
			else if(lang.equals("de"))
				snowballName = "German";
			else if(lang.equals("it"))
				snowballName = "Italian";
			else if(lang.equals("no"))
				snowballName = "Norwegian";
			else if(lang.equals("pt"))
				snowballName = "Portuguese";
			else if(lang.equals("ru"))
				snowballName = "Russian";
			else if(lang.equals("es"))
				snowballName = "Spanish";
			else if(lang.equals("sv"))
				snowballName = "Swedish";
			else if(lang.equals("eo"))
				stemmer = EsperantoStemFilter.class;
			else 
				useStemmer = false;
		} else
			useStemmer = false;
		
		// figure out language-dependent filters
		useLangFilter = true;
		
		if(lang.equals("th"))
			langFilter = ThaiWordFilter.class;
		else if(lang.equals("sr"))
			langFilter = SerbianFilter.class;
		else if(lang.equals("vi"))
			langFilter = VietnameseFilter.class;
		else if(isCJKLanguage(lang)){
			langFilter = CJKFilter.class;
			usingCJK = true;
		} else 
			useLangFilter = false;
		
		// additional filters
		if(type == Type.SPELL_CHECK)
			addAdditionalFilter(PhraseFilter.class);
		
		if(type != Type.SPELL_CHECK && lang.equals("en"))
			addAdditionalFilter(EnglishSingularFilter.class);
		
		// singulars
		if(lang.equals("en"))
			singular = new EnglishSingular();
		else
			singular = null;
		
		// canonical filters
		if(lang.equals("sr"))
			hasCanonicalFilter = true;
		
		// variants (TODO: add zh)
		if(lang.equals("sr"))
			hasLanguageVariants = true;
	}
	
	public static boolean isCJKLanguage(String lang){
		return lang.equals("zh") || lang.equals("cjk") || lang.equals("ja") ||
		lang.equals("zh-classical") || lang.equals("zh-yue");
	}
	
	protected void addAdditionalFilter(Class filterClass){
		if(additionalFilters == null)
			additionalFilters = new ArrayList<Class>();
		additionalFilters.add(filterClass);
	}
	
	public TokenFilter makeStemmer(TokenStream in){
		if(!useStemmer)
			return null;
		else if(snowballName != null)
			return new SnowballFilter(in,snowballName);
		else if(stemmer != null){
			try {
				return (TokenFilter) stemmer.getConstructor(TokenStream.class).newInstance(in);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		
		return null;		
	}
	
	public TokenFilter makeCustomFilter(TokenStream in){
		if(!useLangFilter)
			return null;
		else if(langFilter != null){
			try {
				return (TokenFilter) langFilter.getConstructor(TokenStream.class).newInstance(in);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		
		return null;
	}
	
	public TokenStream makeAdditionalFilterChain(String field, TokenStream in){
		if(additionalFilters == null)
			return in;
		try {
			TokenStream chain = in;
			// nest additional filters, apply them as added to the list
			for(Class filter : additionalFilters){				
				chain = (TokenStream) filter.getConstructor(TokenStream.class).newInstance(chain);
				if(filter.getName().equals("org.wikimedia.lsearch.analyzers.PhraseFilter")){
					((PhraseFilter) chain).setStopWords(stopWords);
					((PhraseFilter) chain).setFilters(this);
				}
			}
			return chain;
		} catch (Exception e) {
			e.printStackTrace();
			return null;
		}
	}
	
	public boolean hasAdditionalFilters(){
		return additionalFilters != null;
	}
	
	public boolean hasStemmer(){
		return useStemmer;
	}
	
	public boolean isUsingCJK() {
		return usingCJK;
	}

	public boolean hasCustomFilter(){
		return useLangFilter;
	}
	
	public String getLanguage(){
		return lang;
	}
	
	public void setStopWords(Set<String> stopWords){
		this.stopWords = stopWords;
	}

	public IndexId getIndexId() {
		return iid;
	}
	
	public boolean hasSingular(){
		return singular != null;
	}
	
	public Singular getSingular() {
		return singular;
	}
	
	/** check if two words have same stemmed variants */
	public boolean stemsToSame(String word1, String word2){
		if(!hasStemmer())
			return false;
		ArrayList<String> in = new ArrayList<String>();
		in.add(word1); in.add(word2);
		TokenStream ts = makeStemmer(new StringsTokenStream(in));
		try {
			Token t1 = ts.next();
			Token t2 = ts.next();
			if(t1 != null && t2 != null && t1.termText().equals(t2.termText()))
				return true;
		} catch (IOException e) {
			e.printStackTrace();
		}
		return false;
	}
	
	/** stem a collection of words */
	public ArrayList<String> stem(Collection<String> list){
		if(!hasStemmer())
			return new ArrayList<String>();
		ArrayList<String> ret = new ArrayList<String>();
		TokenStream ts = makeStemmer(new StringsTokenStream(list));
		try {
			Token t;
			while((t = ts.next()) != null)
				ret.add(t.termText());
			return ret;
		} catch (IOException e) {
			e.printStackTrace();
			return new ArrayList<String>();
		}
	}
	/** Stem one word */
	public String stem(String word){
		if(!hasStemmer())
			return null;
		ArrayList<String> list = new ArrayList<String>();
		list.add(word);
		ArrayList<String> ret = stem(list);
		if(ret.size() == 0)
			return null;
		else
			return ret.iterator().next();
	}
	
	public static class StringsTokenStream extends TokenStream {
		Iterator<String> input;
		int count = 0;
		StringsTokenStream(Collection<String> input){
			this.input = input.iterator();
		}
		@Override
		public Token next() throws IOException {
			if(input.hasNext())
				return new Token(input.next(),count,count++);
			else
				return null;
		}
		
	}
	
	/** For languages with canonical form (e.g. latin for serbian) filter tokens 
	 * @throws IOException */
	public ArrayList<Token> canonicalFilter(ArrayList<Token> tokens) throws IOException{
		if(!hasCanonicalFilter)
			return tokens;
		if(lang.equals("sr")){
			TokenStream ts = makeCustomFilter(new ArrayTokens(tokens));
			ArrayList<Token> res = new ArrayList<Token>();
			Token t = null;
			while((t = ts.next()) != null)
				res.add(t);
			return res;
		} else
			return tokens;
	}
	/** For languages with canonical form 
	 * @return canonical token (or null if none) */
	public Token canonizeToken(Token t){
		if(!hasCanonicalFilter)
			return null;
		if(lang.equals("sr")){
			String nt = new SerbianFilter(null).convert(t.termText());
			if(!t.equals(nt)){
				Token tt = new Token(nt,t.startOffset(),t.endOffset());
				tt.setPositionIncrement(0);
				tt.setType("alias");
				return tt;
			}
		} 
		return null;
	}
	/** for lang with canonical forms
	 * @return canonical string (or null if none)  */
	public String canonizeString(String w){
		if(!hasCanonicalFilter)
			return null;
		if(lang.equals("sr")){
			if(w == null)
				return null;
			String nw = new SerbianFilter(null).convert(w);
			if(!w.equals(nw))
				return nw;
		} 
		return null;
	}
	/** for langs with cacnonical form: always return the canonical form, even if its identical to the input */
	public String canonicalStringFilter(String w){
		if(!hasCanonicalFilter)
			return w;
		if(lang.equals("sr")){
			if(w == null)
				return null;
			return new SerbianFilter(null).convert(w);
		} 
		return w;
	}
	
	public boolean isSpellCheck(){
		return type == Type.SPELL_CHECK;
	}
	
	/** Convert word into language variants if any */
	public ArrayList<String> getVariants(String word){
		if(!hasLanguageVariants)
			return null;
		if(lang.equals("sr")){
			return SerbianFilter.getVariants(word);
		} else 
			return null;
	}
}
