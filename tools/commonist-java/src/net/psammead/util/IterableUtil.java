package net.psammead.util;

import java.util.Iterator;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.functional.Functions;
import net.psammead.functional.data.Option;
import net.psammead.util.annotation.FullyStatic;

/** {@link Iterable} utility functions; Iterable is a monad */
@FullyStatic
public final class IterableUtil {
	private IterableUtil() {}
	
//	private static <T> Iterable<T> wrap(final Iterator<T> it) {
//		return new Iterable<T>() {
//			public Iterator<T> iterator() {
//				return it;
//			}
//		};
//	}
	
	public static final <T> Function<Iterable<T>,Iterator<T>> iterator() {
		return new Function<Iterable<T>,Iterator<T>>() {
			public Iterator<T> apply(Iterable<T> source) {
				return source.iterator();
			}
		};
	};
	
	public static <T> boolean all(Function<? super T,Boolean> predicate, Iterable<T> values) {
		for (T element : values) {
			if (!predicate.apply(element))	return false;
		}
		return true;
	}
	
	public static <T> boolean any(Function<? super T,Boolean> predicate, Iterable<T> values) {
		for (T element : values) {
			if (predicate.apply(element))	return true;
		}
		return false;
	}
	
	public static <T> Option<T> firstItem(Iterable<T> values) {
		return IteratorUtil.nextItem(values.iterator());
	}
	
	public static <T> Option<T> findFirst(Function<? super T,Boolean> predicate, Iterable<T> values) {
		for (T item : values) {
			if (predicate.apply(item))	return Option.some(item);
		}
		return Option.none();
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> void forEach(Acceptor<? super T> effect, Iterable<T> values) {
		for (T element : values) 	effect.set(element);
	}
	
	public static <T> Iterable<T> filter(final Function<? super T,Boolean> predicate, final Iterable<T> values) {
		return new Iterable<T>() {
			public Iterator<T> iterator() {
				return IteratorUtil.filter(predicate, values.iterator());
			}
		};
	}
	
	public static <S,T> Iterable<T> map(final Function<? super S,? extends T> function, final Iterable<? extends S> values) {
		return new Iterable<T>() {
			public Iterator<T> iterator() {
				return IteratorUtil.map(function, values.iterator());
			}
		};
	}
	
	/** monadic bind */
	public static <S,T,TX extends T> Iterable<TX> flatMap(final Function<? super S,? extends Iterable<TX>> function, final Iterable<? extends S> values) {
		return new Iterable<TX>() {
			public Iterator<TX> iterator() {
				return IteratorUtil.flatMap(
						Functions.andThen(function, IterableUtil.<TX>iterator()), 
						values.iterator());
			}
		};
	}
	
	/** monadic join */
	public static <T> Iterable<T> flatten(final Iterable<? extends Iterable<T>> values) {
		return new Iterable<T>() {
			public Iterator<T> iterator() {
				return IteratorUtil.flatten(IteratorUtil.map(IterableUtil.<T>iterator(), values.iterator()));
			}
		};
	}
	
	/** foldRight would not make much sense here */
	public static <S,T> T foldLeft(Function<? super T,? extends Function<? super S,? extends T>> transformer, T initial, Iterable<? extends S> values) {
		return IteratorUtil.foldLeft(transformer, initial, values.iterator());
	}
	
	/** scanRight would not make much sense here */
	public static <S,T> Iterable<T> scanLeft(final Function<? super T,? extends Function<? super S,? extends T>> transformer, final T initial, final Iterable<? extends S> values) {
		return new Iterable<T>() {
			public Iterator<T> iterator() {
				return IteratorUtil.scanLeft(transformer, initial, values.iterator());
			}
		};
	}
	
	// TODO functional: zip, zipWithIndex, zipWith, nub, crossProduct, partition
}
