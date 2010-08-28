package net.psammead.util;

import java.util.Comparator;

import net.psammead.util.annotation.FullyStatic;
import net.psammead.util.comparator.ChainComparator;
import net.psammead.util.comparator.ComparableComparator;
import net.psammead.util.comparator.InverseComparator;

 @FullyStatic 
 public final class Comparators {
	private Comparators() {}
	
	public static <T> Comparator<T> inverseComparator(Comparator<T> delegate) {
		return new InverseComparator<T>(delegate);
	}
	
	public static <T> Comparator<T> chainComparator(Comparator<? super T> high, Comparator<? super T> low) {
		return new ChainComparator<T>(high, low);
	}
	
	public static <T extends Comparable<? super T>> Comparator<T> comparableComparator() {
		return new ComparableComparator<T>();
	}
}
