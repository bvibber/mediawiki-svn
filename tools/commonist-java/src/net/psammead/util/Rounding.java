package net.psammead.util;

public enum Rounding {
	floor, ceil, rint;
	
	public double apply(double value) {
		switch (this) {
			case floor: return Math.floor(value);
			case ceil:	return Math.ceil(value);
			case rint:	return Math.rint(value);
			default: throw new RuntimeException("unexpected Rounding");
		}
	}
}
