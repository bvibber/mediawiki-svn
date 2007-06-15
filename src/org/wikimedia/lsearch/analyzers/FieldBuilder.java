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
		public BuilderSet(FilterFactory filters, FieldNameFactory fields) {
			this.filters = filters;
			this.fields = fields;
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
	}
	
	protected BuilderSet[] builders = new BuilderSet[2];
	
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
	
	
}
