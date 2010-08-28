package net.psammead.util;

import java.util.regex.Matcher;
import java.util.regex.Pattern;

/** a immutable Integer fraction */
public final class Fraction extends Number implements Comparable<Fraction> {
	public static final Fraction ZERO	= new Fraction(0,1);
	public static final Fraction ONE	= new Fraction(1,1);
	
	public final int	numerator;
	public final int	denominator;

	private static final Pattern	PARSE_PATTERN	= Pattern.compile("(-?\\d+)/(-?\\d+)");
	
	/** parses the canonical String representation, inverse of {@link #toString()} */
	public static Fraction parse(String s) {
		final Matcher matcher = PARSE_PATTERN.matcher(s);
		if (!matcher.matches())	throw new NumberFormatException("not a Fraction: " + s);
		return new Fraction(
				Integer.parseInt(matcher.group(1)),
				Integer.parseInt(matcher.group(2)));
	}
	
	/** constructs and normalizes a Fraction */
	public Fraction(int numerator, int denominator) {
		this(numerator, denominator, true);
	}

	/** constructs and normalizes a Fraction, optionally with normalization  */
	public Fraction(int numerator, int denominator, boolean normalize) {
		if (denominator == 0)	throw new ArithmeticException("denominator may not be null");
		
		if (normalize) {
			// normalize denominator to a positive value
			if (denominator < 0) {
				denominator	= -denominator;
				numerator	= -numerator;
			}
	
			// calculate the greatest common divisor
			final int	a	= Math.abs(numerator);
			final int	b	= denominator;
			final int	m	= a == 0 ? b : a > b ? euklid(a, b) : euklid(b, a);

			// divide both parts by their gcs
			numerator	/= m;
			denominator	/= m;
		}
		
		this.numerator		= numerator;
		this.denominator	= denominator;
	}
	
	//------------------------------------------------------------------------------
	//## mathematical stuff
	
	/** add another Fraction to this one */
	public Fraction add(Fraction o) {
		if (numerator   == 0)	return o;
		if (o.numerator == 0)	return this;
		return new Fraction(
			(int)((long)numerator   * (long)o.denominator + (long)denominator * (long)o.numerator), 
			(int)((long)denominator * (long)o.denominator)
		);
	}

	/** subtract another Fraction from this one */
	public Fraction sub(Fraction o) {
		if (numerator   == 0)	return o.neg();
		if (o.numerator == 0)	return this;
		return new Fraction(
			(int)((long)numerator   * (long)o.denominator - (long)denominator * (long)o.numerator), 
			(int)((long)denominator * (long)o.denominator)
		);
	}
		
	/** multiply this Fraction with another one */
	public Fraction mul(Fraction o) {
		return new Fraction(
			(int)((long)numerator   * (long)o.numerator), 
			(int)((long)denominator * (long)o.denominator)
		);
	}
	
	/** divide this Fraction by another one */
	public Fraction div(Fraction o) {
		return new Fraction(
			(int)((long)numerator   * (long)o.denominator), 
			(int)((long)denominator * (long)o.numerator)
		);
	}
	
	/** additive inverse: negate this Fraction */
	public Fraction neg() {
		return new Fraction(-numerator, denominator, false);
	} 

	/** multiplicative inverse: invert this Fraction */
	public Fraction inv() {
		return new Fraction(denominator, numerator, false);
	} 
	
	//------------------------------------------------------------------------------
	//## Number implementation
	
	@Override
	public double doubleValue() {
		return (double)numerator / (double)denominator;
	}
	
	@Override
	public float floatValue() {
		return (float)doubleValue();
	}
	
	@Override
	public int intValue() {
		return (int)doubleValue();
	}
	
	@Override
	public long longValue() {
		return (long)doubleValue();
	}
	
	//------------------------------------------------------------------------------
	//## value object
	
	/** 
	 * returns
	 * -1 when o is smaller, 
	 * +1 when o is greater and
	 *  0 when o equals us
	 */
	public int compareTo(Fraction f) {
	    if (f == null)	throw new NullPointerException("comparing to null is forbidden");
		if (f.numerator == numerator && f.denominator == denominator)	return 0;
		final long	a	= (long)numerator   * (long)f.denominator;
		final long	b	= (long)f.numerator * (long)denominator;
		return a > b ? 1 : a < b ? -1 : 0;
	}
	
	/** returns true if o is an identical Fraction */
	@Override
	public boolean equals(Object o) {
		if (o == this)					return true;
		if (o == null)					return false;
		if (o.getClass() != getClass())	return false;
		Fraction	f	= (Fraction)o;
		return f.numerator   == numerator
			&& f.denominator == denominator;
	}

	/** returns the hashCode */
	@Override
	public int hashCode() {
		return (17 * 37 + numerator) * 53 + denominator;
	}
	
	/** returns the canonical String representation, inverse of {@link #parse(String)} */
	@Override
	public String toString() {
		return numerator + "/" + denominator;
	}
	
	//------------------------------------------------------------------------------
	//## private helper
	
	/** find the greatest common divisor between a bigger a and a smaller b */
	private static int euklid(int a, int b) {
		int	m;
		for (;;) {
			m	= a % b;
			if (m == 0)	return b;
			a	= b;
			b	= m;
		}	
	}
}
