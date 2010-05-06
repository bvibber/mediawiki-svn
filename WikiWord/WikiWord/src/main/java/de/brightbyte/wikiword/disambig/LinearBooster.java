/**
 * 
 */
package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.Functor;

final class LinearBooster implements Functor.Double {

	private double scale;

	/**
	 * @param disambiguator
	 */
	public LinearBooster(double scale) {
		this.scale = scale;
	}
	
	public LinearBooster() {
		this(1.0);
	}

	public double apply(double a) {
		return  a * scale;
	}
}