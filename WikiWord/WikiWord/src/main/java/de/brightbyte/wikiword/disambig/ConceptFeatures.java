package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;

public class ConceptFeatures<C extends WikiWordConcept> {
	protected LabeledVector<Integer> features;
	protected WikiWordConceptReference<C> reference;
	
	public ConceptFeatures(WikiWordConceptReference<C> reference, LabeledVector<Integer> features) {
		this.features = features;
		this.reference = reference;
	}
	
	public ConceptFeatures(WikiWordConceptReference<C> reference, byte[] features) {
		this(reference, unserializeVector(features));
	}
	
	public String toString() {
		return reference+ ":"+features;
	}
	public LabeledVector<Integer> getFeatureVector() {
		return features;
	}
	
	public WikiWordConceptReference<C> getConceptReference() {
		return reference;
	}
	
	public int getConceptId() {
		return reference.getId();
	}
	
	public byte[] getFeatureVectorData() {
		return serializeVector(features);
	}
	
	protected static byte[] serializeVector(LabeledVector<Integer> v) {
		int c = v.size();
		byte[] data = new byte[c*4 + c*8];
		
		int i = 0;
		for (Integer k: v.labels()) {
			int id = k.intValue();
			double d = v.get(k);
			long b = Double.doubleToLongBits(d);
			
			data[i++] = (byte)(id & 0xFF);
			data[i++] = (byte)(id >>> 8 & 0xFF);
			data[i++] = (byte)(id >>> 16 & 0xFF);
			data[i++] = (byte)(id >>> 24 & 0xFF);

			data[i++] = (byte)(b & 0xFF);
			data[i++] = (byte)(b >>> 8 & 0xFF);
			data[i++] = (byte)(b >>> 16 & 0xFF);
			data[i++] = (byte)(b >>> 24 & 0xFF);
			data[i++] = (byte)(b >>> 32 & 0xFF);
			data[i++] = (byte)(b >>> 40 & 0xFF);
			data[i++] = (byte)(b >>> 48 & 0xFF);
			data[i++] = (byte)(b >>> 56 & 0xFF);
		}
		
		return data;
	}

	protected static LabeledVector<Integer> unserializeVector(byte[] data) {
		LabeledVector<Integer> v = new MapLabeledVector<Integer>();
		
		for (int i = 0; i<data.length; ) {
			int id = (data[i++] & 0xFF) ;
			id |= (data[i++]  & 0xFF) << 8;
			id |= (data[i++]  & 0xFF) << 16;
			id |=  (data[i++]  & 0xFF) << 24;
			
			long b = (data[i++] & 0xFFL);
			b |= (data[i++] & 0xFFL) << 8;
			b |= (data[i++] & 0xFFL) << 16;
			b |= (data[i++] & 0xFFL) << 24;
			b |= (data[i++] & 0xFFL) << 32;
			b |= (data[i++] & 0xFFL) << 40; 
			b |= (data[i++] & 0xFFL) << 48; 
			b |= (data[i++] & 0xFFL) << 56; 
			
			double d = Double.longBitsToDouble(b);
			v.set(id, d);
		}
		
		return v;
	}
	
}
