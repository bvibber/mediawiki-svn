package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.Functors;
import de.brightbyte.wikiword.integrator.FeatureSetSourceDescriptor;

public class FeatureSetValueMapper implements FeatureSetMangler {

	protected FeatureMapping mapping;
	
	public FeatureSetValueMapper() {
		this(new FeatureMapping());
	}
	
	public FeatureSetValueMapper(FeatureMapping mapping) {
		if (mapping==null) throw new NullPointerException();
		this.mapping = mapping;
	}

	public FeatureSet apply(FeatureSet features) {
		FeatureSet ft = new DefaultFeatureSet();
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(features, f, null); //XXX: extra type conversion?!
			
			ft.put(f, v);
		}
		
		return ft;
	}

	public <T> void addMapping(String field, FeatureSetSourceDescriptor source, String option, Class<T> type, Functor<?, ? extends Collection<?>> aggregator) {
		mapping.addMapping(field, source, option, type, aggregator);
	}

	public void addMapping(String field, PropertyAccessor<FeatureSet, ?> accessor) {
		mapping.addMapping(field, accessor);
	}

	public <T> void addMapping(String field, String feature, Class<T> type, Functor<?, ? extends Collection<?>> aggregator) {
		mapping.addMapping(field, feature, type, aggregator);
	}

	public <T> void addMapping(String field, String feature) {
		mapping.addMapping(field, feature);
	}
	
}
