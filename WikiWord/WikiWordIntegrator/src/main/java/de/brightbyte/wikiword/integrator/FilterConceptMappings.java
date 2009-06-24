package de.brightbyte.wikiword.integrator;

import java.util.Collection;

import de.brightbyte.abstraction.AbstractedAccessor;
import de.brightbyte.abstraction.Abstractor;
import de.brightbyte.abstraction.ConvertingAccessor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.CollapsingMappingCandidateCursor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSets;
import de.brightbyte.wikiword.integrator.data.FilteredMappingCandidateCursor;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.data.filter.BestMappingCandidateSelector;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateFilter;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidatePropertyScorer;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateScorer;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateSelector;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateSelectorFilter;
import de.brightbyte.wikiword.integrator.data.filter.MappingCandidateThresholdFilter;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;

public class FilterConceptMappings extends BuildConceptMappings {
	
	@Override
	protected void run() throws Exception {
		AssociationFeatureStoreBuilder store = getStoreBuilder();
		this.propertyProcessor = createProcessor(store); 

		section("-- fetching properties --------------------------------------------------");
		DataCursor<Association> asc = openAssociationCursor(); 

		DataCursor<MappingCandidates> cursor = 
			new CollapsingMappingCandidateCursor(asc, 
					sourceDescriptor.getTweak("foreign-id-field", (String)null), 
					sourceDescriptor.getTweak("concept-id-field", (String)null) );
		
		cursor = new FilteredMappingCandidateCursor(cursor, createMappingCandidateFilter(sourceDescriptor));
		
		section("-- process properties --------------------------------------------------");
		store.prepareImport();
		
		this.propertyProcessor.processMappings(cursor);
		cursor.close();

		store.finalizeImport();
	}	
	

	protected MappingCandidateFilter createMappingCandidateFilter(FeatureSetSourceDescriptor sourceDescriptor) {
		MappingCandidateFilter filter = sourceDescriptor.getTweak("mapping-filter", null);
		MappingCandidateSelector selector = sourceDescriptor.getTweak("mapping-selector", null);
		MappingCandidateScorer scorer = sourceDescriptor.getTweak("mapping-filter-scorer", null);
		PropertyAccessor<FeatureSet, ? extends Number>  accessor = sourceDescriptor.getTweak("mapping-filter-field-accessor", null);
		String field = sourceDescriptor.getTweak("mapping-filter-field", null);

		if (filter==null && selector==null && scorer==null && accessor ==null && field!=null) {
			Functor<? extends Number, Collection<Number>> aggregator = sourceDescriptor.getTweak("mapping-filter-aggregator", null);

			if (aggregator==null) {
				String f = sourceDescriptor.getTweak("mapping-filter-aggregator-function", "sum");
				if (f.equals("sum")) aggregator = (Functor<? extends Number, Collection<Number>> )Functors.Double.sum;
				else if (f.equals("max")) aggregator = (Functor<? extends Number, Collection<Number>> )Functors.Double.max;
				else throw new IllegalArgumentException("unknwon aggregator function: "+f);
			}
			
			AbstractedAccessor<FeatureSet, ? extends Collection<Number>> a = 
				new AbstractedAccessor<FeatureSet,Collection<Number>>(field, (Abstractor<FeatureSet>)FeatureSets.abstractor);

			accessor =  new ConvertingAccessor<FeatureSet,  Collection<Number>, Number>(a, aggregator, Number.class);
		}
		
		if (filter==null && selector==null && scorer==null && accessor!=null) {
			scorer = new MappingCandidatePropertyScorer(accessor);
		}
		
		if (filter==null && selector==null && scorer!=null) {
			int threshold = sourceDescriptor.getTweak("mapping-filter-threshold", -1);
			if (threshold>0)  {
				filter = new MappingCandidateThresholdFilter(scorer, threshold);
			} else {
				selector = new BestMappingCandidateSelector(scorer);
			}
		}

		if (filter==null && selector!=null) filter = new MappingCandidateSelectorFilter(selector);
		
		return filter;
		
		/*
		Functor<Number, ? extends Collection<? extends Number>> aggregator = sourceDescriptor.getTweak("optimization-aggregator", null);
		
		if (aggregator==null) {
			String f = sourceDescriptor.getTweak("optimization-aggregator-function", "sum");
			if (f.equals("sum")) aggregator = Functors.Double.sum;
			else if (f.equals("max")) aggregator = Functors.Double.max;
			else throw new IllegalArgumentException("unknwon aggregator function: "+f);
		}
		
		Class<? extends Number> type = sourceDescriptor.getTweak("optimization-class", null);

		if (type==null) {
			String c = sourceDescriptor.getTweak("optimization-type", "double");
			if (c.equals("double")) type = Double.class;
			else if (c.equals("int")) type = Integer.class;
			else if (c.equals("long")) type = Long.class;
			else if (c.equals("bigint")) type = BigInteger.class;
			else if (c.equals("decimal") || c.equals("bigdecimal")) type = BigDecimal.class;
			else throw new IllegalArgumentException("unknwon comparator type: "+c);
		}
		
		Comparator<? extends Number> comp = sourceDescriptor.getTweak("optimization-comparator", null);

		if (comp==null) {
			if (type==Double.class) comp = Functors.Double.comparator;
			else if (type==Integer.class) comp = Functors.Integer.comparator;
			else if (type==Long.class) comp = Functors.Long.comparator;
			else if (type==BigInteger.class) comp = Functors.BigInteger.comparator;
			else if (type==BigDecimal.class) comp = Functors.BigDecimal.comparator;
			else throw new IllegalArgumentException("unknwon comparator function: "+type);
		}*/
	}
	

	@Override
	protected String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialect) {
		String fields = StringUtils.join(", ", getDefaultFields(dialect));
		return "SELECT " + fields + " FROM " + dialect.quoteName(getQualifiedTableName(table)) ;
	}

	public static void main(String[] argv) throws Exception {
		FilterConceptMappings app = new FilterConceptMappings();
		app.launch(argv);
	}
}