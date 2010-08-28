package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;
import net.psammead.util.range.IntegerRange;
import net.psammead.util.range.LongRange;

@FullyStatic 
public final class Ranges {
	private Ranges() {}
	
	public static final LongRange longRange(long start, long end) {
		return new LongRange(start, end);
	}
	
	public static final IntegerRange integerRange(int start, int end) {
		return new IntegerRange(start, end);
	}
}
