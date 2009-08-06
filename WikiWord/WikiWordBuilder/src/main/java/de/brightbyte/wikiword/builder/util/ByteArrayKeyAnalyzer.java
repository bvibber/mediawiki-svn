package de.brightbyte.wikiword.builder.util;

import org.ardverk.collection.KeyAnalyzer;

public class ByteArrayKeyAnalyzer implements KeyAnalyzer<byte[]> {

	private static final long serialVersionUID = -3056514701732470312L;

	public static final int BITS = 8;
	private static final int MSB = 0x80;
	
	public static final ByteArrayKeyAnalyzer INSTANCE = new ByteArrayKeyAnalyzer();

	public ByteArrayKeyAnalyzer() {
		super();
	}

	public int bitIndex(byte[] key, int offsetInBits, int lengthInBits, byte[] other, int otherOffsetInBits, int otherLengthInBits) {
        boolean allNull = true;
        
        if (offsetInBits % BITS != 0 || otherOffsetInBits % BITS != 0 
                || lengthInBits % BITS != 0 || otherLengthInBits % BITS != 0) {
            throw new IllegalArgumentException("offsets & lengths must be at byte boundaries");
        }
        
        int beginIndex1 = offsetInBits / BITS;
        int beginIndex2 =otherOffsetInBits / BITS;
        
        int endIndex1 = beginIndex1 + (lengthInBits / BITS);
        int endIndex2 = beginIndex2 + (otherLengthInBits / BITS);
        
        int length = Math.max(lengthInBits/ BITS, otherLengthInBits/ BITS);
        
        // Look at each character, and if they're different
        // then figure out which bit makes the difference
        // and return it.
        int k = 0, f = 0;
        for(int i = 0; i < length; i++) {
            int index1 = beginIndex1 + i;
            int index2 = beginIndex2 + i;
            
            if (index1 >= endIndex1) {
                k = 0;
            } else {
                k = key[index1];
            }
            
            if (other == null || index2 >= endIndex2) {
                f = 0;
            } else {
                f = other[index2];
            }
            
            if (k != f) {
               int x = k ^ f;
               return i * BITS + (Integer.numberOfLeadingZeros(x) - (Integer.SIZE-BITS));
            }
            
            if (k != 0) {
                allNull = false;
            }
        }
        
        if (allNull) {
            return KeyAnalyzer.NULL_BIT_KEY;
        }
        
        return KeyAnalyzer.EQUAL_BIT_KEY;
	}

	public boolean isPrefix(byte[] prefix, int offsetInBits, int lengthInBits, byte[] key) {
		if (offsetInBits % BITS>0) throw new IllegalArgumentException("index and offset must be multiple of "+BITS);
		if (lengthInBits % BITS>0) throw new IllegalArgumentException("index and offset must be multiple of "+BITS);

		int ofs = offsetInBits / BITS;
		int len = lengthInBits / BITS;
		
		for (int i=0; i<len; i++) {
			if (i>key.length) return false;
			if (prefix[ofs+i]!=key[i]) return false;
 		}
		
		return true;
	}

	public int bitsPerElement() {
		return BITS;
	}

	public boolean isBitSet(byte[] key, int offsetInBits, int lengthInBits) {
        if (key == null || offsetInBits >= lengthInBits) {
            return false;
        }
        
        int index = key.length - (int)(offsetInBits / BITS) -1;
        int bit = (int)(offsetInBits % BITS);
        
        return (key[index] & (MSB >>> bit)) != 0;
	}

	public int lengthInBits(byte[] key) {
		return key.length*BITS;
	}

	public int compare(byte[] o1, byte[] o2) {
		int i = 0;
		while (i<o1.length && i<o2.length) {
			if (o1[i]<o2[i]) return -1;
			else if (o1[i]>o2[i]) return 1;
			i++;
		}
		
		if (o1.length<o2.length) return -1;
		else if (o1.length<o2.length) return 1;
		else return 0;
	}
	

}
