/**
 * 
 */
package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.Functor;

final class SquareBooster implements Functor.Double {

	public static final SquareBooster instance = new SquareBooster();
	
	/**
	 * @param disambiguator
	 */
	public SquareBooster() {
	}

	public double apply(double a) {
		return  a * a; 
	}
}