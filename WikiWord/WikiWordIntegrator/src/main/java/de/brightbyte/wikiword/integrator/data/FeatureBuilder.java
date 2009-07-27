package de.brightbyte.wikiword.integrator.data;

public interface FeatureBuilder<R> {

	public void addFeatures(R rec, FeatureSet features);
	public boolean hasSameSubject(R prev, R next);

}