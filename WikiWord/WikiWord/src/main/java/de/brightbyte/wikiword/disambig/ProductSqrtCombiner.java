/**
 * 
 */
package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.Functor2;

final class ProductSqrtCombiner implements Functor2.Double {

	public static final ProductSqrtCombiner instance = new ProductSqrtCombiner();

	/**
	 * @param disambiguator
	 */
	public ProductSqrtCombiner() {
	}

	public double apply(double a, double b) {
		if (a<0 || a>1) throw new IllegalArgumentException("ProductSqrt is only defined for values 0 <= x <= 1");
		if (b<0 || b>1) throw new IllegalArgumentException("ProductSqrt is only defined for values 0 <= x <= 1");
		return Math.sqrt( a * b ); //normalized produkt
	}
}