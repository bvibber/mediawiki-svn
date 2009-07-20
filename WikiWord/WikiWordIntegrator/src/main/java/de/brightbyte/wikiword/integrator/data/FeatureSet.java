package de.brightbyte.wikiword.integrator.data;

import java.util.Collection;

import de.brightbyte.abstraction.AbstractAccessor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Aggregator;
import de.brightbyte.data.LabeledVector;

public interface FeatureSet {
	public interface Feature<V> {
		public V getValue();
		public Record getQualifiers();
	}

	public boolean overlaps(FeatureSet sourceItem, String sourceKeyField);
	public LabeledVector<Object> getHistogram(String key);

	public <V>void addFeature(String key, V value, Record qualifiers);

	public <V>Collection<? extends Feature<? extends V>> getFeatures(String key);

	public Iterable<String> keys();
	
	public void addAll(FeatureSet other);
}
