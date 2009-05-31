package de.brightbyte.wikiword.integrator.data;

import java.util.List;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MultiMap;

public interface FeatureSet extends MultiMap<String, Object, List<Object>> {

	public boolean overlaps(FeatureSet sourceItem, String sourceKeyField);
	public LabeledVector<Object> getHistogram(String key);

}
