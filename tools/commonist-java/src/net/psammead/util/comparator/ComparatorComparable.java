package net.psammead.util.comparator;

import java.util.Comparator;

import net.psammead.util.ToString;

/** wraps an Object into a {@link Comparable} using a given {@link Comparator} */
public final class ComparatorComparable<T> implements Comparable<ComparatorComparable<T>> {
	public final T	target;
	
	private final Comparator<? super T>	comparator;
	
	public ComparatorComparable(T target, Comparator<? super T> comparator) {
		this.target = target;
		this.comparator = comparator;
	}
	
	public int compareTo(ComparatorComparable<T> that) {
		return comparator.compare(this.target, that.target);
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("target",		target)
				.append("comparator",	comparator)
				.toString();
	}
}
