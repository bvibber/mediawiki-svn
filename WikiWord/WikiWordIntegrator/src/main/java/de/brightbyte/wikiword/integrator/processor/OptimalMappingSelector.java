package de.brightbyte.wikiword.integrator.processor;

import java.util.Collection;
import java.util.Comparator;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.Optimum;
import de.brightbyte.data.PropertyComparator;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.MappingCandidates;
import de.brightbyte.wikiword.integrator.store.AssociationFeatureStoreBuilder;

public class OptimalMappingSelector extends ConceptMappingPassThrough {

	protected Optimum<FeatureSet> optimum;
	
	public <T>OptimalMappingSelector(AssociationFeatureStoreBuilder store, String property, Functor<T, ? extends Collection<T>> aggregator, Comparator<T> comp, Class<T> type) {
		this(store, (Comparator<FeatureSet>)(Object)PropertyComparator.newMultiMapEntryComparator(property, comp, aggregator, type));
	}
	
	public OptimalMappingSelector(AssociationFeatureStoreBuilder store, PropertyAccessor<FeatureSet, Number> accessor) {
		this(store, new Optimum<FeatureSet>(accessor));
	}
	
	public OptimalMappingSelector(AssociationFeatureStoreBuilder store, Comparator<FeatureSet> comp) {
		this(store, new Optimum<FeatureSet>(comp));
	}
	
	public OptimalMappingSelector(AssociationFeatureStoreBuilder store, Optimum<FeatureSet> optimum) {
		super(store);
		
		if (optimum==null) throw new NullPointerException();
		this.optimum = optimum;
	}

	protected  void processMappingCandidates(MappingCandidates m) throws PersistenceException {
			FeatureSet f = optimum.apply(m.getCandidates());
			if (f!=null) store.storeAssociationFeatures(m.getSubject(), f, f);
	}

}
