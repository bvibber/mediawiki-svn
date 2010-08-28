package net.psammead.util;

import java.util.Collections;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.Set;
import java.util.WeakHashMap;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.functional.Functions;
import net.psammead.functional.data.Pair;
import net.psammead.util.annotation.FullyStatic;

/** {@link Set} utility functions */
@FullyStatic 
public final class SetUtil {
	private SetUtil() {}
	
	public static <T> Set<T> createVA(T... values) {
		final Set<T> out	= new HashSet<T>();
		for (T element : values)	out.add(element);
		return out;
	}
	
	public static <T> Set<T> create(Iterable<? extends T> values) {
		final Set<T> out	= new HashSet<T>();
		for (T element : values)	out.add(element);
		return out;
	}
	
	public static <T,X extends T> Set<T> single(X element) {
		final Set<T> out	= new HashSet<T>();
		out.add(element);
		return out;
	}
	
	public static <T,X extends T> Set<T> immutable(Iterable<? extends T> values) {
		return Collections.unmodifiableSet(create(values));
	}
	
	public static <T> Set<T> weakHashSet() {
		return Collections.newSetFromMap(new WeakHashMap<T,Boolean>());
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> void forEach(Acceptor<? super T> effect, Set<T> values) {
		for (T element : values) {
			effect.set(element);
		}
	}
	
	public static <T> Set<T> filter(Function<? super T,Boolean> predicate, Set<? extends T> values) {
		final Set<T> out	= new HashSet<T>();
		for (T element : values) {
			if (predicate.apply(element)) {
				out.add(element);
			}
		}
		return out;
	}
	
	public static <S,T> Set<T> map(Function<? super S,? extends T> function, Set<? extends S> values) {
		final Set<T> out	= new HashSet<T>();
		for (S element : values) {
			out.add(function.apply(element));
		}
		return out;
	}
	
	/** monadic bind */
	public static <S,T> Set<T> flatMap(Function<? super S, ? extends Set<? extends T>> function, Set<? extends S> values) {
		final Set<T> out	= new HashSet<T>();
		for (S element : values) {
			out.addAll(function.apply(element));
		}
		return out;
	}
	
	/** monadic join */
	public static <T> Set<T> flatten(Set<? extends Set<? extends T>> values) {
		final Set<T> out	= new HashSet<T>();
		for (Set<? extends T> element : values) {
			out.addAll(element);
		}
		return out;
	}
	
	public static <S,T> T fold(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, Set<? extends S> values) {
		return IterableUtil.foldLeft(function, initial, values);
	}
	
	public static <S,T> Set<T> scan(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, Set<? extends S> values) {
		final Set<T>	out	= new HashSet<T>();
		T	value	= initial;
		out.add(value);
		for (S element : values) {
			value	= function.apply(value).apply(element);
			out.add(value);
		}
		return out;
	}
	
	//-------------------------------------------------------------------------
	 
	public static <T> Set<T> union(Set<? extends T> a, Set<? extends T> b) {
		final Set<T> out	= new HashSet<T>();
		out.addAll(a);
		out.addAll(b);
		return out;
	}
	
	public static <T> Set<T> intersection(Set<? extends T> a, Set<? extends T> b) {
		final Set<T> out	= new HashSet<T>();
		out.addAll(a);
		out.retainAll(b);
		return out;
	}
	
	public static <T> Set<T> without(Set<? extends T> a, Set<? extends T> b) {
		final Set<T> out	= new HashSet<T>();
		out.addAll(a);
		out.removeAll(b);
		return out;
	}
	
	public static <T> Set<T> difference(Set<? extends T> a, Set<? extends T> b) {
		return without(union(a,b), intersection(a,b));
	}
	
	public static <A,B> Set<Pair<A,B>> crossProduct(Set<? extends A> a, Set<? extends B> b) {
		final Set<Pair<A,B>>	out	= new HashSet<Pair<A,B>>();
		for (A aa : a)	for (B bb : b)	out.add(Pair.create(aa, bb));
		return out;
	}
	
	//-------------------------------------------------------------------------
	
	public static <S,T> Set<Pair<S,T>> zip(Set<? extends S> first, Set<? extends T> second) {
		final Set<Pair<S,T>>	out	= new HashSet<Pair<S,T>>();
		final Iterator<? extends S>	it1	= first.iterator();
		final Iterator<? extends T>	it2	= second.iterator();
		while (it1.hasNext() && it2.hasNext()) { 
			out.add(new Pair<S,T>(it1.next(), it2.next()));
		}
		return out;
	}
	
	public static <S,T> Pair<Set<S>,Set<T>> unzip(Set<? extends Pair<? extends S,? extends T>> values) {
		final Set<S>	first	= new HashSet<S>();
		final Set<T>	second	= new HashSet<T>();
		for (Pair<? extends S,? extends T> pair : values) {
			first.add(pair.first);
			second.add(pair.second);
		}
		return Pair.create(first, second);
	}
	
	public static <R,S,T> Set<T> zipWith(Function<? super R,? extends Function<? super S, ? extends T>> combine, Set<? extends R> first, Set<? extends S> second) {
		return map(Functions.<R,S,T>uncurry(combine), zip(first, second));
	}
	
//	public static <T> Set<Pair<T,Integer>> zipWithIndex(Set<? extends T> list) {
//		final Set<Pair<T,Integer>>	out	= new HashSet<Pair<T,Integer>>();
//		int i	= 0;
//		for (T item : list) {
//			out.add(Pair.create(item, i++));
//		}
//		return out;
//	}
	
	public static <T> Pair<Set<T>,Set<T>> partition(Function<? super T,Boolean> predicate, List<? extends T> values) {
		final Set<T>	xs	= new HashSet<T>();
		final Set<T>	ys	= new HashSet<T>();
		for (T item : values) {
			if (predicate.apply(item))	xs.add(item);
			else						ys.add(item);
		}
		return Pair.create(xs, ys);
	}
	
}
