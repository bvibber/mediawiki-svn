package net.psammead.util;

import java.util.Iterator;
import java.util.NoSuchElementException;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.functional.data.Option;
import net.psammead.util.annotation.FullyStatic;

/** {@link Iterator} utility functions; Iterator is a monad */
@FullyStatic 
public final class IteratorUtil {
	private IteratorUtil() {}
	
	public static <T> void forEach(Acceptor<? super T> effect, Iterator<T> values) {
		while (values.hasNext())	effect.set(values.next());
	}
	
	public static <T> Iterator<T> filter(Function<? super T, Boolean> predicate, Iterator<T> values) {
		return new FilterIterator<T>(values, predicate);
	}
	
	public static <S,T> Iterator<T> map(final Function<? super S, ? extends T> function, final Iterator<S> values) {
		return new MapIterator<S,T>(values, function);
	}
	
	public static <T> Iterator<T> flatten(final Iterator<? extends Iterator<? extends T>> values) {
		return new FlattenIterator<T>(values);
	}

	public static <S,T> Iterator<T> flatMap(final Function<? super S, ? extends Iterator<? extends T>> function, final Iterator<S> values) {
		return flatten(map(function, values));
	}
	
	/** foldRight would not make much sense here */
	public static <S,T> T foldLeft(Function<? super T,? extends Function<? super S,? extends T>> transformer, T initial, Iterator<? extends S> values) {
		T	value	= initial;
		while (values.hasNext()) {
			S	element	= values.next();
			value	= transformer.apply(value).apply(element);
		}
		return value;
	}
	
	/** scanRight would not make much sense here */
	public static <S,T> Iterator<T> scanLeft(Function<? super T,? extends Function<? super S,? extends T>> transformer, T initial, Iterator<? extends S> values) {
		return new ScanIterator<S,T>(values, transformer, initial);
	}
	
	// TODO collection: zip, zipWith, zipWithIndex, nub, crossProduct, partition
	
	//-------------------------------------------------------------------------
	
	public static <T> Option<T> nextItem(Iterator<T> values) {
		return values.hasNext() ? Option.some(values.next()) : Option.<T>none();
	}
	
	public static <T> Option<T> findFirst(Function<? super T,Boolean> predicate, Iterator<T> values) {
		for (;;) {
			if (!values.hasNext())		return Option.none();
			T	item	= values.next();
			if (predicate.apply(item))	return Option.some(item);
		}
	}
	
	//-------------------------------------------------------------------------
	
	private static class FilterIterator<T> implements Iterator<T> {
		private final Iterator<T>					delegate;
		private final Function<? super T, Boolean>	predicate;
		
		private boolean	hasNext;
		private T		next;
		
		public FilterIterator(
				final Iterator<T> delegate, 
				final Function<? super T, Boolean> predicate) {
			this.delegate	= delegate;
			this.predicate	= predicate;
			run();
		}

		public boolean hasNext() {
			return hasNext;
		}

		public T next() {
			if (!hasNext())	throw new NoSuchElementException();
			T	old	= next;
			run();
			return old;
		}

		private void run() {
			for (;;) {
				hasNext	= delegate.hasNext();
				if (!hasNext)				break;
				next	= delegate.next();
				if (predicate.apply(next))	break;
			}
		}

		public void remove() { throw new UnsupportedOperationException(); }
	}
	
	private static class MapIterator<S,T> implements Iterator<T> {
		private final Iterator<S>	delegate;
		private final Function<? super S, ? extends T>	function;

		public MapIterator(
				final Iterator<S> delegate, 
				final Function<? super S, ? extends T> function) {
			this.delegate	= delegate;
			this.function	= function;
			
		}
		public boolean hasNext() {
			return delegate.hasNext();
		}

		public T next() {
			if (!hasNext())	throw new NoSuchElementException();
			return function.apply(delegate.next());
		}

		public void remove() { throw new UnsupportedOperationException(); }
	}
	
	private static class FlattenIterator<T> implements Iterator<T> {
		private Iterator<? extends Iterator<? extends T>>	outer;
		private boolean	outerHasNext;

		private Iterator<? extends T>	inner;
		private boolean	innerHasNext;
		
		private boolean	hasNext;
		private T		next;

		public FlattenIterator(final Iterator<? extends Iterator<? extends T>> delegates) {
			this.outer	= delegates;
			outerHasNext	= outer.hasNext();
			innerHasNext	= false;
			run();
		}

		public boolean hasNext() {
			return hasNext;
		}

		public T next() {
			if (!hasNext())	throw new NoSuchElementException();
			final T	old	= next;
			run();
			return old;
		}

		private void run() {
			for (;;) {
				if (innerHasNext) {
					hasNext	= true;
					next	= inner.next();
					innerHasNext	= inner.hasNext();
					break;
				}
				if (outerHasNext) {
					inner	= outer.next();
					innerHasNext	= inner.hasNext();
					outerHasNext	= outer.hasNext();
					continue;
				}
				hasNext	= false;
				break;
			}
//			while (outerHasNext && !innerHasNext) {
//				inner	= outer.next();
//				innerHasNext	= inner.hasNext();
//				outerHasNext	= outer.hasNext();
//			}
//			hasNext	= innerHasNext;
//			if (hasNext)	next	= inner.next();
		}

		public void remove() { throw new UnsupportedOperationException(); }
	}
	
	private static class ScanIterator<S,T> implements Iterator<T> {
		private final Iterator<? extends S>	delegate;
		private final Function<? super T, ? extends Function<? super S, ? extends T>>	transformer;
		
		private boolean	hasNext;
		private T		next;
		
		public ScanIterator(
				final Iterator<? extends S> delegate,
				final Function<? super T, ? extends Function<? super S, ? extends T>> transformer,
				final T initial) {
			this.delegate		= delegate;
			this.transformer	= transformer;
			
			next	= initial;
		}
		
		public boolean hasNext() {
			return hasNext;
		}

		public T next() {
			if (!hasNext())	throw new NoSuchElementException();
			T	old	= next;
			run();
			return old;
		}

		private void run() {
			for (;;) {
				hasNext	= delegate.hasNext();
				if (!hasNext)	break;
				next	= transformer.apply(next).apply(delegate.next());
			}
		}

		public void remove() { throw new UnsupportedOperationException(); }
	}
}
