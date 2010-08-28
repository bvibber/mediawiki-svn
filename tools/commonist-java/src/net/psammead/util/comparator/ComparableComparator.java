package net.psammead.util.comparator;

import java.util.Comparator;

import net.psammead.util.ToString;
import net.psammead.util.annotation.ImmutableValue;

/** a {@link Comparator} for {@link Comparable}s */
@ImmutableValue 
public final class ComparableComparator<T extends Comparable<? super T>> implements Comparator<T> {
	public int compare(T o1, T o2) {
		return o1.compareTo(o2);
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.toString();
	}
}
