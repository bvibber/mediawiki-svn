package org.wikimedia.lsearch.analyzers;

import org.apache.lucene.analysis.PorterStemFilter;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.analysis.de.GermanStemFilter;
import org.apache.lucene.analysis.snowball.SnowballFilter;
import org.apache.lucene.analysis.th.ThaiWordFilter;

/**
 * Make a language-dependent pair of filters. The custom filter is to be applied before the stemmer.
 * 
 * @author rainman
 *
 */
public class FilterFactory {
	protected String lang;
	protected String snowballName = null;
	protected boolean useStemmer,useCustomFilter;
	protected Class stemmer = null;
	protected Class customFilter = null;
	
	protected FilterFactory noStemmerFilterFactory=null;
	
	public FilterFactory(String lang){
		this.lang = lang;
		init();
		noStemmerFilterFactory = new FilterFactory(lang,snowballName,false,useCustomFilter,null,customFilter); 
	}
		
	public FilterFactory(String lang, String snowballName, boolean useStemmer, boolean useCustomFilter, Class stemmer, Class customFilter) {
		this.lang = lang;
		this.snowballName = snowballName;
		this.useStemmer = useStemmer;
		this.useCustomFilter = useCustomFilter;
		this.stemmer = stemmer;
		this.customFilter = customFilter;
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
		
		// figure out stemmer
		useStemmer = true;		
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
		
		// figure out custom filter
		useCustomFilter = true;
		if(lang.equals("th"))
			customFilter = ThaiWordFilter.class;
		else if(lang.equals("sr"))
			customFilter = SerbianFilter.class;
		else if(lang.equals("vi"))
			customFilter = VietnameseFilter.class;
		else if(lang.equals("zh") || lang.equals("cjk") || lang.equals("ja") ||
				lang.equals("ko") || lang.equals("zh-classical") || lang.equals("zh-yue"))
			customFilter = CJKFilter.class;
		else 
			useCustomFilter = false;
		
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
		if(!useCustomFilter)
			return null;
		else if(customFilter != null){
			try {
				return (TokenFilter) customFilter.getConstructor(TokenStream.class).newInstance(in);
			} catch (Exception e) {
				e.printStackTrace();
			}
		}
		
		return null;
	}
	
	public boolean hasStemmer(){
		return useStemmer;
	}
	
	public boolean hasCustomFilter(){
		return useCustomFilter;
	}
	
	public String getLanguage(){
		return lang;
	}
}
