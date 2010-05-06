/**
 * 
 */
package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.Functor2;

final class ProductCombiner implements Functor2.Double {

	public static final ProductCombiner instance = new ProductCombiner();
	
	/**
	 * @param disambiguator
	 */
	public ProductCombiner() {
	}

	public double apply(double a, double b) {
		return  a * b; 
		//return =  Math.sqrt( popf * simf ); //normalized produkt
	}
}