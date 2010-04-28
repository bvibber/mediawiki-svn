/**
 * 
 */
package de.brightbyte.wikiword.disambig;

import de.brightbyte.data.Functor2;

final class LinearCombiner implements Functor2.Double {

	private double bias;

	/**
	 * @param disambiguator
	 */
	public LinearCombiner(double bias) {
		if (bias<0 || bias>1) throw new IllegalArgumentException("bias must be >=0 and <=1, found "+bias);
		this.bias = bias;
	}
	
	public LinearCombiner() {
		this(0.5);
	}

	public double apply(double a, double b) {
		return  b * bias + b * ( 1 - bias ); 
		//return =  Math.sqrt( popf * simf ); //normalized produkt
	}
}