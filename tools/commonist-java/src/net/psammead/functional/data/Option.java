package net.psammead.functional.data;

import java.util.Collections;
import java.util.Iterator;
import java.util.List;
import java.util.NoSuchElementException;
import java.util.Set;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Converter;
import net.psammead.functional.Function;
import net.psammead.util.ListUtil;
import net.psammead.util.SetUtil;
import net.psammead.util.annotation.ImmutableValue;

@ImmutableValue 
public abstract class Option<T> implements Iterable<T> {
	public static <T> Option<T> some(T value) {
		return new Some<T>(value);
	}
	
	public static <T> Option<T> none() {
		return new None<T>();
	}
	
	public static <T> Option<T> fromNullable(T value) {
		if (value != null)	return some(value);
		else				return none();
	}
	
	public static <T> Option<T> fromIterator(Iterator<T> iterator) {
		if (iterator.hasNext())	return some(iterator.next());
		else					return none();
	}
	
	public static <T> Option<T> fromFirst(Iterable<T> source) {
		return fromIterator(source.iterator());
	}
	
	public static <T> Option<T> maybe(Function<? super T,Boolean> predicate, T value) {
		if (predicate.apply(value))	return some(value);
		else						return none();
	}
	
	/** monadic join */
	@SuppressWarnings("unchecked")
	public static <T> Option<T> flatten(Option<? extends Option<? extends T>> source) {
		if (source.isSome())	return ((Some<Option<T>>)source).value;
		else					return none();
	}
	
	/** Option is covariant */
	@SuppressWarnings("unchecked")
	public static <T,TX extends T> Option<T> vary(Option<TX> orig) { return (Option<T>) orig; }
	
	public static <T> Converter<T,Option<T>> fromNullableConverter() {
		// Converters.functionConverter(_Option.<T>fromNullable(), _Option.toNullable());
		return new Converter<T,Option<T>>() {
			public Option<T> apply(T original) {
				return fromNullable(original);
			}
			public T unapply(Option<T> derived) {
				return derived.getOrNull();
			}
		};
	}
	public static <T> Converter<Option<T>,T> toNullableConverter() {
		// Converters.inverse(fromNullableConverter)
		return new Converter<Option<T>, T>() {
			public T apply(Option<T> original) {
				return original.getOrNull();
			}
			public Option<T> unapply(T derived) {
				return fromNullable(derived);
			}
		};
	}
	
	//-------------------------------------------------------------------------
	
	// TODO functional: add scanLeft, scanRight, zip and unzip
	
	private Option() {}
	
	public final Iterator<T> iterator() { return toList().iterator(); }
	
	public abstract boolean isNone();
	public abstract boolean isSome();
	/** throws a {@link NoSuchElementException} when {@link #isNone()} */
	public abstract T getOrThrow();
	/** returns null when  {@link #isNone()} */
	public abstract T getOrNull();
	/** returns #alternative when  {@link #isNone()} */
	public abstract T getOrElse(T alternative);
	public abstract List<T> toList();
	public abstract Set<T> toSet();
	public abstract void forEach(Acceptor<? super T> effect);
	public abstract Option<T> filter(Function<? super T,Boolean> predicate);
	public abstract <X> Option<X> map(Function<? super T,? extends X> function);
	/** monadic bind */
	public abstract <X> Option<X> flatMap(Function<? super T,? extends Option<X>> function);
	public abstract <X> X foldLeft(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial);
	public abstract <X> X foldRight(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial);
	public abstract void visit(OptionVisitor<? super T> visitor);
	
	//-------------------------------------------------------------------------
	
	@ImmutableValue private static final class None<T> extends Option<T> {
		public None() {}
		
		@Override public boolean isNone() { return true; }
		@Override public boolean isSome() {	return false; }
		@Override public T getOrThrow() { throw new NoSuchElementException("None doesn't have a value"); }
		@Override public T getOrNull() {	return null; }
		@Override public T getOrElse(T alternative) { return alternative; }
		@Override public List<T> toList() {	return Collections.emptyList(); }
		@Override public Set<T> toSet() {	return Collections.emptySet(); }
		@Override public void forEach(Acceptor<? super T> effect) {}
		@Override public Option<T> filter(Function<? super T,Boolean> predicate) { return none(); }
		@Override public <X> Option<X> map(Function<? super T,? extends X> function) { return none();	}
		@Override public <X> Option<X> flatMap(Function<? super T,? extends Option<X>> function) { return none(); }
		@Override public <X> X foldLeft(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial) { return initial; }
		@Override public <X> X foldRight(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial) { return initial; }
		@Override public void visit(OptionVisitor<? super T> visitor) { visitor.none(); }
		
		@Override
		public String toString() {
			return "None()";
		}
		@Override public int hashCode() {
			return 0;	
		}
		@Override public boolean equals(Object o) {	
			return o != null 
				&& o.getClass() == this.getClass(); 
		}
	}

	@ImmutableValue 
	private static final class Some<T> extends Option<T> {
		public final T value;
		
		public Some(T value) { this.value	= value; }

		@Override public boolean isNone() { return false; }
		@Override public boolean isSome() { return true; }
		@Override public T getOrThrow() { return value; }
		@Override public T getOrNull() {	return value; }
		@Override public T getOrElse(T alternative) { return value; }
		@Override public List<T> toList() {	return ListUtil.single(value); }
		@Override public Set<T> toSet() {	return SetUtil.single(value); }
		@Override public void forEach(Acceptor<? super T> effect) { effect.set(value); }
		@Override public Option<T> filter(Function<? super T,Boolean> predicate) { if (predicate.apply(value)) return this; else return none(); }
		@Override public <X> Option<X> map(Function<? super T,? extends X> function) { X x = function.apply(value); return some(x); }
		@Override public <X> Option<X> flatMap(Function<? super T,? extends Option<X>> function) { return function.apply(value); }
		@Override public <X> X foldLeft(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial) { return function.apply(initial).apply(value); }
		@Override public <X> X foldRight(Function<? super X, ? extends Function<? super T,? extends X>> function, X initial) { return function.apply(initial).apply(value); }
		@Override public void visit(OptionVisitor<? super T> visitor) { visitor.some(value); }
		
		@Override
		public String toString() {
			return "Some(" + value + ")";
		}
		@Override
		public int hashCode() {
			final int prime = 31;
			int result = 1;
			result = prime * result + ((value == null) ? 0 : value.hashCode());
			return result;
		}
		@SuppressWarnings("unchecked")
		@Override
		public boolean equals(Object obj) {
			if (this == obj) return true;
			if (obj == null) return false;
			if (getClass() != obj.getClass()) return false;
			Some other = (Some)obj;
			if (value == null) {
				if (other.value != null) return false;
			}
			else if (!value.equals(other.value)) return false;
			return true;
		}
	}
}
