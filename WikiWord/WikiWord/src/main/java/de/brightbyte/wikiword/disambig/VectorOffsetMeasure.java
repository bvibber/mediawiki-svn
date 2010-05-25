package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.data.measure.Distance;
import de.brightbyte.data.measure.EuclideanVectorDistance;
import de.brightbyte.data.measure.Measure;

public class VectorOffsetMeasure<K> implements Measure<LabeledVector<K>> {

	protected LabeledVector<K> reference;
	protected Distance<LabeledVector<K>> distance;
	
	public VectorOffsetMeasure() {
		this(new MapLabeledVector<K>());
	}
	
	public VectorOffsetMeasure(Distance<LabeledVector<K>> distance) {
		this(new MapLabeledVector<K>(), distance);
	}
	
	public VectorOffsetMeasure(LabeledVector<K> reference) {
		this(reference, new EuclideanVectorDistance<K>());
	}
	
	public VectorOffsetMeasure(LabeledVector<K> reference, Distance<LabeledVector<K>> distance) {
		if (reference == null) throw new NullPointerException();
		if (distance == null) throw new NullPointerException();
		
		this.reference = reference;
		this.distance = distance;
	}

	public double measure(LabeledVector<K> v) {
		double d = distance.distance(reference, v);
		return d;
	}
	
	public Comparator<LabeledVector<K>> comparator(boolean descending) {
		return new Measure.Comparator<LabeledVector<K>>(this, descending);
	}

}
