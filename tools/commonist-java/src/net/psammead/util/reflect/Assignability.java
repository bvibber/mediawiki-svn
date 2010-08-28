package net.psammead.util.reflect;

/** a measure for assigning a value to a parameter is possible */
public enum Assignability {
	exact(4),
	assignable(3),
	nullref(2),
	coercable(1),
	incompatible(0);
	
	private final int quality;
	
	private Assignability(int quality) {
		this.quality	= quality;
	}
	
	public static Assignability max(Assignability a, Assignability b) {
		return a.quality > b.quality ? a : b;
	}
	
	public static Assignability min(Assignability a, Assignability b) {
		return a.quality < b.quality ? a : b;
	}
	
	public boolean betterThan(Assignability that) {
		return this.quality > that.quality;
	}
	
	public boolean worseThan(Assignability that) {
		return this.quality < that.quality;
	}
}