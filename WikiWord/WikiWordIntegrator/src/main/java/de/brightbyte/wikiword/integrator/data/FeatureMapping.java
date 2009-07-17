package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;

import de.brightbyte.abstraction.MultiMapAbstractor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.wikiword.integrator.FeatureSetSourceDescriptor;

public class FeatureMapping extends PropertyMapping<FeatureSet> {
	public <T>void addMapping(String field, String feature, Class<T> type, Functor<?, ? extends Collection<?>> aggregator) {
		PropertyAccessor<FeatureSet, T> accessor;
		
		if (aggregator==null) accessor = FeatureSets.fieldAccessor(feature, type);
		else accessor = (PropertyAccessor<FeatureSet, T>)(Object)MultiMapAbstractor.accessor(feature, (Functor<T, ? extends Collection<T>>)aggregator, type);
		
		addMapping(field, accessor);
	}

	public <T>void addMapping(String field, FeatureSetSourceDescriptor source, String option, Class<T> type, Functor<?, ? extends Collection<?>> aggregator) {
		String feature = source.getTweak(option, null);		
		if (feature!=null) addMapping(field, feature, type, aggregator);
	}
}
