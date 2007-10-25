package org.wikimedia.lsearch.analyzers;

import java.util.ArrayList;
import java.util.Set;

import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.snowball.SnowballFilter;
import org.apache.lucene.analysis.th.ThaiWordFilter;
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
		noStemmerFilterFactory = new FilterFactory(iid,lang,snowballName,false,useLangFilter,null,langFilter,additionalFilters); 
	}
		
	public FilterFactory(IndexId iid, String lang, String snowballName, boolean useStemmer, boolean useLangFilter, Class stemmer, Class langFilter, ArrayList<Class> additionalFilters) {
		this.iid = iid;
		this.lang = lang;
		this.snowballName = snowballName;
		this.useStemmer = useStemmer;
		this.useLangFilter = useLangFilter;
		this.stemmer = stemmer;
		this.langFilter = langFilter;
		this.additionalFilters = additionalFilters;
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
		else if(lang.equals("zh") || lang.equals("cjk") || lang.equals("ja") ||
				lang.equals("zh-classical") || lang.equals("zh-yue")){
			langFilter = CJKFilter.class;
			usingCJK = true;
		} else 
			useLangFilter = false;
		
		// additional filters
		if(type == Type.SPELL_CHECK){
			additionalFilters = new ArrayList<Class>();
			additionalFilters.add(PhraseFilter.class);
		}
		
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
	
	
	
	
}
