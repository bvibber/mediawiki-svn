package de.brightbyte.wikiword.builder;

import org.ardverk.collection.KeyAnalyzer;
import org.ardverk.collection.StringKeyAnalyzer;

public class ReverseStringKeyAnalyzer extends StringKeyAnalyzer {

	private static final long serialVersionUID = -3056514701732470312L;

	private static final int MSB = 0x8000;
	
	public static final ReverseStringKeyAnalyzer INSTANCE = new ReverseStringKeyAnalyzer();

	public ReverseStringKeyAnalyzer() {
		super();
	}
	
	@Override
	public int bitIndex(String key, int offsetInBits, int lengthInBits, String other, int otherOffsetInBits, int otherLengthInBits) {
        boolean allNull = true;
       
        if (offsetInBits % LENGTH != 0 || otherOffsetInBits % LENGTH != 0 
                || lengthInBits % LENGTH != 0 || otherLengthInBits % LENGTH != 0) {
            throw new IllegalArgumentException("offsets & lengths must be at character boundaries");
        }
        
        int length1 = key.length();
        int length2 = other ==null ? otherLengthInBits / LENGTH : other.length();
        
        int beginIndex1 = length1 -1 - (offsetInBits / LENGTH);
        int beginIndex2 = length2 -1 - (otherOffsetInBits / LENGTH);
        
        int endIndex1 = beginIndex1 - (lengthInBits / LENGTH);
        int endIndex2 = beginIndex2 - (otherLengthInBits / LENGTH);
        
        int length = Math.max(lengthInBits/ LENGTH, otherLengthInBits/ LENGTH);
        
        // Look at each character, and if they're different
        // then figure out which bit makes the difference
        // and return it.
        char k = 0, f = 0;
        for(int i = 0; i < length; i++) {
            int index1 = beginIndex1 - i;
            int index2 = beginIndex2 - i;
            
            if (index1 <= endIndex1) {
                k = 0;
            } else {
                k = key.charAt(index1);
            }
            
            if (other == null || index2 <= endIndex2) {
                f = 0;
            } else {
                f = other.charAt(index2);
            }
            
            if (k != f) {
               int x = k ^ f;
               return i * LENGTH + (Integer.numberOfLeadingZeros(x) - LENGTH);
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

	@Override
	public boolean isBitSet(String key, int bitIndex, int lengthInBits) {
        if (key == null || bitIndex >= lengthInBits) {
            return false;
        }
        
        int index = key.length() - (int)(bitIndex / LENGTH) -1;
        int bit = (int)(bitIndex % LENGTH);
        
        return (key.charAt(index) & (MSB >>> bit)) != 0;
	}

	@Override
	public boolean isPrefix(String prefix, int offsetInBits, int lengthInBits, String key) {
        if (offsetInBits % LENGTH != 0 || lengthInBits % LENGTH != 0) {
            throw new IllegalArgumentException(
                    "Cannot determine prefix outside of Character boundaries");
        }
    
        int ofs = prefix.length() - ((offsetInBits + lengthInBits) / LENGTH);
        
        String s1 = prefix.substring(ofs, lengthInBits / LENGTH);
        return key.endsWith(s1);
    }

}
