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
	
	/** Construct case-insensitive field builder */
	public FieldBuilder(String lang){
		this(lang,false);
	}
	
	public FieldBuilder(String lang, boolean exactCase){
		if(exactCase){
			builders = new BuilderSet[2];
			// additional exact case factory
			builders[1] = new BuilderSet(
					new FilterFactory(lang).getNoStemmerFilterFactory(),
					new FieldNameFactory(FieldNameFactory.EXACT_CASE));
		} else
			builders = new BuilderSet[1];
		// default factory, lowercase all data
		builders[0] = new BuilderSet(
				new FilterFactory(lang),
				new FieldNameFactory());
		
	}

	public BuilderSet[] getBuilders() {
		return builders;
	}
	
	/** Get the case-insensitive builder */
	public BuilderSet getBuilder(){
		return getBuilder(false);
	}
	
	/** Get BuilderSet for exactCase value */
	public BuilderSet getBuilder(boolean exactCase){
		if(exactCase && builders.length > 1)
			return builders[1];
		else
			return builders[0];
	}
	
	
}
