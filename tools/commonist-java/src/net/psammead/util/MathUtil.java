package net.psammead.util;

import net.psammead.util.annotation.FullyStatic;

/** math helper functions */
@FullyStatic 
public final class MathUtil {
	/** function collection, shall not be instantiated */
	private MathUtil() {}
	
	//-------------------------------------------------------------------------
	//## clamp
	
	/** clamp an integer between min and max (both inclusive) */
	public static int clamp(int value, int min, int max) {
		return Math.max(min, Math.min(max, value));
	}
	
	/** clamp a long between min and max (both inclusive) */
	public static long clamp(long value, long min, long max) {
		return Math.max(min, Math.min(max, value));
	}
	
	/** clamp a float between min and max (both inclusive) */
	public static float clamp(float value, float min, float max) {
		return Math.max(min, Math.min(max, value));
	}
	
	/** clamp a double between min and max (both inclusive) */
	public static double clamp(double value, double min, double max) {
		return Math.max(min, Math.min(max, value));
	}
	
	//-------------------------------------------------------------------------
	//## masking
	
	/** true iff bits of value and test selected with the mask are equal */
	public static final boolean maskTest(int mask, int test, int value) {
		return ((test ^ value) & mask) == 0;
	}
	
	/** true iff bits of value and test selected with the mask are equal */
	public static final boolean maskTest(long mask, long test, long value) {
		return ((test ^ value) & mask) == 0;
	}
	
	//-------------------------------------------------------------------------
	//## rounding
	
	/** 
	 * if the division has no remainder, the result is the same as an integer division.
	 * if there is a remainder, the result is the next higher value.
	 */
	public static int divUp(int dividend, int divisor) {
		return (dividend+divisor-1)/divisor;
	}
	
	//-------------------------------------------------------------------------
	//## log/exp
	
	/** constant ln 2 */
	public static final double	LOG2	= Math.log(2);
	
	/** logarithm to base 2 */
	public static double log2(double value) {
		return Math.log(value) / LOG2;
	}
	
	/** raise to power of 2 */
	public static double exp2(double value) {
		return Math.pow(2, value);	// Math.exp(value * LOG2);
	}
	
	/** raise to power of 10 */
	public static double exp10(double value) {
		return Math.pow(10, value);
	}

	//-------------------------------------------------------------------------
	//## dB
	
	/** convert from an amplitude multiplication factor to a dB value */
	public static double gain2db(double gain) {
		// roughly Math.log(gain) * 6.0 / Math.log(2);
		return 20 * Math.log10(gain);
	}
	
	/** convert from a dB value to an amplitude multiplication factor */
	public static double db2gain(double dB) {
		// roughly Math.exp(dB * Math.log(2) / 6.0);
		return exp10(dB / 20);
	}
	
	/** convert from an amplitude multiplication factor to a dB value */
	public static double intensity2db(double gain) {
		return 10 * Math.log10(gain);
	}
	
	/** convert from a dB value to an amplitude multiplication factor */
	public static double db2intensity(double dB) {
		return exp10(dB / 10);
	}
	
//	public static void main(String[] args) throws Exception {
//		double	gain1	= 0.5;
//		double	db1		= gain2db(gain1);
//		double	gain2	= db2gain(db1);
//		double	db2		= gain2db(gain2);
//		double	gain3	= db2gain(db2);
//		System.err.println((String.format("gain1=%f", gain1)));
//		System.err.println((String.format("db1=%f", db1)));
//		System.err.println((String.format("gain2=%f", gain2)));
//		System.err.println((String.format("db2=%f", db2)));
//		System.err.println((String.format("gain3=%f", gain3)));
//	}
}
