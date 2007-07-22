package org.wikimedia.lsearch.analyzers;

/**
 * Agregate class for FilterFactory and FieldNameFactory. This class
 * contains methods used to build various fields of the index, 
 * it contains field names to be used, filter that are to be applied...  
 * 
 * @author rainman
 *
 */
public class FieldBuilder {
	public class BuilderSet{
		FilterFactory filters;
		FieldNameFactory fields;
		boolean addKeywords; // wether to add keywords from beginning of article
		
		public BuilderSet(FilterFactory filters, FieldNameFactory fields) {
			this.filters = filters;
			this.fields = fields;
			this.addKeywords = false;
		}
		public FieldNameFactory getFields() {
			return fields;
		}
		public FilterFactory getFilters() {
			return filters;
		}
		public boolean isExactCase() {
			return fields.isExactCase();
		}
		public boolean isAddKeywords() {
			return addKeywords;
		}
		public void setAddKeywords(boolean addKeywords) {
			this.addKeywords = addKeywords;
		}
		
	}
	
	protected BuilderSet[] builders = new BuilderSet[2];
	
	/** default is ignore case (upper/lower), use exact_case for wiktionaries, etc */
	public static enum Case { IGNORE_CASE, EXACT_CASE };
	/** use stemmer if available, of force no stemming */
	public static enum Stemmer { USE_STEMMER, NO_STEMMER }; 
	
	/** Construct case-insensitive field builder with stemming */
	public FieldBuilder(String lang){
		this(lang,Case.IGNORE_CASE,Stemmer.USE_STEMMER);
	}
	
	public FieldBuilder(String lang, Case useCase){
		this(lang,useCase,Stemmer.USE_STEMMER);
	}
	
	public FieldBuilder(String lang, Case useCase, Stemmer useStemmer){
		// additional exact case factory
		if(useCase == Case.EXACT_CASE){
			builders = new BuilderSet[2];		
			builders[1] = new BuilderSet(
					new FilterFactory(lang).getNoStemmerFilterFactory(),
					new FieldNameFactory(FieldNameFactory.EXACT_CASE));
		} else
			builders = new BuilderSet[1];
		
		// default factory, lowercase all data
		if(useStemmer == Stemmer.USE_STEMMER){
			builders[0] = new BuilderSet(
					new FilterFactory(lang),
					new FieldNameFactory());
		} else{
			builders[0] = new BuilderSet(
					new FilterFactory(lang).getNoStemmerFilterFactory(),
					new FieldNameFactory());
		}
		
	}

	public BuilderSet[] getBuilders() {
		return builders;
	}
	
	/** Get the case-insensitive builder */
	public BuilderSet getBuilder(){
		return getBuilder(Case.IGNORE_CASE);
	}
	
	/** Get BuilderSet for exactCase value */
	public BuilderSet getBuilder(Case dCase){
		if(dCase == Case.EXACT_CASE && builders.length > 1)
			return builders[1];
		else
			return builders[0];
	}
	
	
}
