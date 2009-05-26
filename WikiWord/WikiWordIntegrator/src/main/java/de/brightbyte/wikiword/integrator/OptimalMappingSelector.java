package de.brightbyte.wikiword.integrator;

import java.util.Collection;
import java.util.Comparator;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.data.NaturalComparator;
import de.brightbyte.data.Optimum;
import de.brightbyte.data.PropertyComparator;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class OptimalMappingSelector implements MappingProcessor {

	protected Optimum<FeatureSet> optimum;
	
	public OptimalMappingSelector(String property, Functor<Number, ? extends Collection<Number>> aggregator) {
		this((Comparator<FeatureSet>)(Object)PropertyComparator.newMultiMapEntryComparator(property, (Comparator<Number>)(Object)NaturalComparator.instance, aggregator, Number.class));
	}
	
	public OptimalMappingSelector(PropertyAccessor<FeatureSet, Number> accessor) {
		this(new Optimum<FeatureSet>(accessor));
	}
	
	public OptimalMappingSelector(Comparator<FeatureSet> comp) {
		this(new Optimum<FeatureSet>(comp));
	}
	
	public OptimalMappingSelector(Optimum<FeatureSet> optimum) {
		if (optimum==null) throw new NullPointerException();
		this.optimum = optimum;
	}

	public void processMappings(DataCursor<MappingCandidates> cursor,
			MappingStore store) throws PersistenceException {
		
		MappingCandidates m;
		while ((m = cursor.next()) != null ) {
			FeatureSet f = optimum.apply(m.getCandidates());
			store.storeMapping(m.getSubject(), f, f);
		}

	}

}
