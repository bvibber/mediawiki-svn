package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.ByteSequence;
import de.brightbyte.data.ByteString;
import de.brightbyte.data.CodecException;
import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;
import de.brightbyte.io.BlockCodec;
import de.brightbyte.wikiword.model.ConceptFeatures;

public class IntFeatureCodec implements BlockCodec<LabeledVector<Integer>> {

	public LabeledVector<Integer> decode(byte[] data, int offset, int length)
			throws CodecException {
		
		LabeledVector<Integer> v = ConceptFeatures.newIntFeaturVector();
		
		for (int i = 0; i<length; ) {
			int id = (data[offset + i++] & 0xFF) ;
			id |= (data[offset + i++]  & 0xFF) << 8;
			id |= (data[offset + i++]  & 0xFF) << 16;
			id |=  (data[offset + i++]  & 0xFF) << 24;
			
			long b = (data[offset + i++] & 0xFFL);
			b |= (data[offset + i++] & 0xFFL) << 8;
			b |= (data[offset + i++] & 0xFFL) << 16;
			b |= (data[offset + i++] & 0xFFL) << 24;
			b |= (data[offset + i++] & 0xFFL) << 32;
			b |= (data[offset + i++] & 0xFFL) << 40; 
			b |= (data[offset + i++] & 0xFFL) << 48; 
			b |= (data[offset + i++] & 0xFFL) << 56; 
			
			double d = Double.longBitsToDouble(b);
			v.set(id, d);
		}
		
		return v;
	}

	public LabeledVector<Integer> decode(ByteSequence b) throws CodecException {
		return decode(b.getBytes(), 0, b.length());
	}

	public int getRequiredBufferSize(LabeledVector<Integer> v) {
		int c = v.size();
		return c*4 + c*8;
	}
	
	public ByteString encode(LabeledVector<Integer> v) {
		int c = getRequiredBufferSize(v);
		byte[] data = new byte[c];
		encode(v, data, 0);
		return new ByteString(data);
	}

	public int encode(LabeledVector<Integer> v, byte[] data, int offset) {
		int c = getRequiredBufferSize(v);
		if (data.length < c + offset) throw new IllegalArgumentException("need " + c + " bytes to store vector, only "+(data.length - offset)+" left in buffer" );
		
		int i = 0;
		for (Integer k: v.labels()) {
			int id = k.intValue();
			double d = v.get(k);
			long b = Double.doubleToLongBits(d);
			
			data[offset + i++] = (byte)(id & 0xFF);
			data[offset + i++] = (byte)(id >>> 8 & 0xFF);
			data[offset + i++] = (byte)(id >>> 16 & 0xFF);
			data[offset + i++] = (byte)(id >>> 24 & 0xFF);

			data[offset + i++] = (byte)(b & 0xFF);
			data[offset + i++] = (byte)(b >>> 8 & 0xFF);
			data[offset + i++] = (byte)(b >>> 16 & 0xFF);
			data[offset + i++] = (byte)(b >>> 24 & 0xFF);
			data[offset + i++] = (byte)(b >>> 32 & 0xFF);
			data[offset + i++] = (byte)(b >>> 40 & 0xFF);
			data[offset + i++] = (byte)(b >>> 48 & 0xFF);
			data[offset + i++] = (byte)(b >>> 56 & 0xFF);
		}
		
		return i;
	}

}
