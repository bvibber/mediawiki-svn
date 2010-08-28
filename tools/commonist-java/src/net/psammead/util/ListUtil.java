package net.psammead.util;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.HashSet;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;
import java.util.Set;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.functional.Functions;
import net.psammead.functional.data.Option;
import net.psammead.functional.data.Pair;
import net.psammead.util.annotation.FullyStatic;

/** {@link List} utility functions */
@FullyStatic 
public final class ListUtil {
	private ListUtil() {}
	
	public static <T> List<T> createVA(T... values) {
		final List<T> out	= new ArrayList<T>();
		for (T element : values)	out.add(element);
		return out;
	}
	
	public static <T> List<T> create(Iterable<? extends T> values) {
		final List<T> out	= new ArrayList<T>();
		for (T element : values)	out.add(element);
		return out;
	}
	
	public static <T,X extends T> List<T> single(X element) {
		final List<T> out	= new ArrayList<T>();
		out.add(element);
		return out;
	}
	
	public static <T,X extends T> List<T> immutable(Iterable<? extends T> values) {
		return Collections.unmodifiableList(create(values));
	}
	
	public static <T> List<T> fromOptions(List<Option<T>> options) {
		final List<T>	out	= new ArrayList<T>();
		for (Option<T> option : options) {
			for (T value : option) {
				out.add(value);
			}
		}
		return out;
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> void forEach(Acceptor<? super T> effect, List<T> values) {
		for (T element : values) {
			effect.set(element);
		}
	}
	
	public static <T> List<T> filter(Function<? super T,Boolean> predicate, List<? extends T> values) {
		final List<T> out	= new ArrayList<T>();
		for (T element : values) {
			if (predicate.apply(element)) {
				out.add(element);
			}
		}
		return out;
	}
	
	public static <S,T> List<T> map(Function<? super S,? extends T> function, List<? extends S> values) {
		final List<T> out	= new ArrayList<T>();
		for (S element : values) {
			out.add(function.apply(element));
		}
		return out;
	}
	
	/** monadic bind */
	public static <S,T> List<T> flatMap(Function<? super S, ? extends List<? extends T>> function, List<? extends S> values) {
		final List<T> out	= new ArrayList<T>();
		for (S element : values) {
			out.addAll(function.apply(element));
		}
		return out;
	}
	
	/** monadic join */
	public static <T> List<T> flatten(List<? extends List<? extends T>> values) {
		final List<T> out	= new ArrayList<T>();
		for (List<? extends T> element : values) {
			out.addAll(element);
		}
		return out;
	}
	
	public static <S,T> T foldLeft(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, List<? extends S> values) {
		return IterableUtil.foldLeft(function, initial, values);
	}
	
	public static <S,T> T foldRight(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, List<? extends S> values) {
		T	value	= initial;
		for (ListIterator<? extends S> it=lastIterator(values); it.hasPrevious();) {
			value	= function.apply(value).apply(it.previous());
		}
		return value;
	}
	
	
	public static <S,T> List<T> scanLeft(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, List<? extends S> values) {
		final List<T>	out	= new ArrayList<T>();
		T	value	= initial;
		out.add(value);
		for (S element : values) {
			value	= function.apply(value).apply(element);
			out.add(value);
		}
		return out;
	}
	
	public static <S,T> List<T> scanRight(Function<? super T,? extends Function<? super S,? extends T>> function, T initial, List<? extends S> values) {
		final List<T>	out	= new ArrayList<T>();
		T	value	= initial;
		out.add(value);
		for (ListIterator<? extends S> it=lastIterator(values); it.hasPrevious();) {
			value	= function.apply(value).apply(it.previous());
			out.add(value);
		}
		// could add at index 0 instead
		Collections.reverse(out);
		return out;
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> List<T> concat(List<? extends T> a, List<? extends T> b) {
		final List<T> out	= new ArrayList<T>();
		out.addAll(a);
		out.addAll(b);
		return out;
	}
	
	public static <T> List<T> intersection(List<? extends T> a, List<? extends T> b) {
		final List<T> out	= new ArrayList<T>();
		out.addAll(a);
		out.retainAll(b);
		return out;
	}
	
	public static <T> List<T> without(List<? extends T> a, List<? extends T> b) {
		final List<T> out	= new ArrayList<T>();
		out.addAll(a);
		out.removeAll(b);
		return out;
	}
	
	public static <T> List<T> difference(List<? extends T> a, List<? extends T> b) {
		return without(concat(a,b), intersection(a,b));
	}
	
	public static <A,B> List<Pair<A,B>> crossProduct(List<? extends A> a, List<? extends B> b) {
		final List<Pair<A,B>>	out	= new ArrayList<Pair<A,B>>();
		for (A aa : a)	for (B bb : b)	out.add(Pair.create(aa, bb));
		return out;
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> List<T> reverse(List<? extends T> values) {
		final List<T>	out	= new ArrayList<T>(values);
		Collections.reverse(out);
		return out;
	}
	
	public static <T extends Comparable<? super T>> List<T> sort(List<? extends T> values) {
		final List<T> out	= new ArrayList<T>(values);
		Collections.sort(out);
		return out;
	}
	
	public static <T> List<T> sort(Comparator<? super T> comparator, List<? extends T> values) {
		final List<T> out	= new ArrayList<T>(values);
		Collections.sort(out, comparator);
		return out;
	} 
	
	public static final <T> List<T> nubLeft(List<? extends T> values) {
		final Set<T>	done	= new HashSet<T>();
		final List<T>	out		= new ArrayList<T>();
		for (T element : values) {
			if (done.contains(element))	continue;
			out.add(element);
			done.add(element);
		}
		return out;
	}
	
	public static final <T> List<T> nubRight(List<? extends T> values) {
		final Set<T>	done	= new HashSet<T>();
		final List<T>	out		= new ArrayList<T>();
		for (ListIterator<? extends T> it=lastIterator(values); it.hasPrevious();) {
			T	element	= it.previous();
			if (done.contains(element))	continue;
			out.add(element);
			done.add(element);
		}
		return out;
	}
	
	public static <S,T> List<Pair<S,T>> zip(List<? extends S> first, List<? extends T> second) {
		final List<Pair<S,T>>	out	= new ArrayList<Pair<S,T>>();
		final Iterator<? extends S>	it1	= first.iterator();
		final Iterator<? extends T>	it2	= second.iterator();
		while (it1.hasNext() && it2.hasNext()) { 
			out.add(new Pair<S,T>(it1.next(), it2.next()));
		}
		return out;
	}
	
	public static <S,T> Pair<List<S>,List<T>> unzip(List<? extends Pair<? extends S,? extends T>> values) {
		final List<S>	first	= new ArrayList<S>();
		final List<T>	second	= new ArrayList<T>();
		for (Pair<? extends S,? extends T> pair : values) {
			first.add(pair.first);
			second.add(pair.second);
		}
		return Pair.create(first, second);
	}
	
	public static <R,S,T> List<T> zipWith(Function<? super R,? extends Function<? super S, ? extends T>> combine, List<? extends R> first, List<? extends S> second) {
		return map(Functions.<R,S,T>uncurry(combine), zip(first, second));
	}
	
	public static <T> List<Pair<T,Integer>> zipWithIndex(List<? extends T> values) {
		final List<Pair<T,Integer>>	out	= new ArrayList<Pair<T,Integer>>();
		int i	= 0;
		for (T item : values) {
			out.add(Pair.create(item, i++));
		}
		return out;
	}
	
	public static <T> Pair<List<T>,List<T>> partition(Function<? super T,Boolean> predicate, List<? extends T> values) {
		final List<T>	xs	= new ArrayList<T>();
		final List<T>	ys	= new ArrayList<T>();
		for (T item : values) {
			if (predicate.apply(item))	xs.add(item);
			else						ys.add(item);
		}
		return Pair.create(xs, ys);
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> Option<T> findFirst(Function<? super T,Boolean> predicate, List<T> values) {
		for (T item : values) {
			if (predicate.apply(item))	return Option.some(item);
		}
		return Option.none();
	}
	
	public static <T> Option<T> findLast(Function<? super T,Boolean> predicate, List<T> values) {
		for (ListIterator<T> it=lastIterator(values); it.hasPrevious();) {
			final T item = it.previous();
			if (predicate.apply(item))	return Option.some(item);
		}
		return Option.none();
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> T first(List<? extends T> values) {
		return values.get(0);
	}
	
	public static <T> T last(List<? extends T> values) {
		return values.get(lastIndex(values));
	}
	
	public static int lastIndex(List<?> values) {
		return values.size()-1;
	} 
	
	public static <T> ListIterator<T> lastIterator(List<T> values) {
		return values.listIterator(values.size());
	}
	
	public static boolean containsIndex(int index, List<?> values) {
		return index >= 0 && index < values.size();
	}
	
	public static <T> T getOrElse(int index, T elseValue, List<? extends T> values) {
		if (containsIndex(index, values))	return values.get(index);
		else							return elseValue;
	}
	
	//-------------------------------------------------------------------------
	
//	@SuppressWarnings("unchecked")
//	public static <T> T[] toArray(List<T> list, Class<? extends T> clazz) {
//		T[]	out	= (T[])Array.newInstance(clazz, list.size());
//		return list.toArray(out);
//	}
	
	public static <T> Option<Integer> itemIndexOf(T item, List<T> values) {
		return itemIndex(values.indexOf(item));
	}
	
	public static <T> Option<Integer> itemLastIndexOf(T item, List<T> values) {
		return itemIndex(values.lastIndexOf(item));
	}
	
	public static <T> Option<T> firstItem(List<T> values) {
		return itemAt(0, values);
	}
	
	public static <T> Option<T> lastItem(List<T> values) {
		return itemAt(lastIndex(values), values);
	}
	
	public static <T> List<Option<T>> itemsAt(List<Integer> indizes, List<T> values) {
		final List<Option<T>>	out	= new ArrayList<Option<T>>();
		for (int index : indizes) {
			out.add(itemAt(index, values));
		}
		return out;
	}
	
	public static <T> Option<T> itemAt(int index, List<T> values) {
		if (ListUtil.containsIndex(index, values))	return Option.some(values.get(index));
		else										return Option.none();
	}
	
	public static Option<Integer> itemIndex(int index) {
		if (index != -1)	return Option.some(index);
		else				return Option.none();
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> Function<Integer,T> getter(final List<T> values) {
		return new Function<Integer,T>() {
			public T apply(final Integer source) {
				return values.get(source);
			}
		};
	}
	
	public static <T> Function<Integer,Option<T>> optionGetter(final List<T> values) {
		return new Function<Integer,Option<T>>() {
			public Option<T> apply(final Integer index) {
				return containsIndex(index, values) 
						? Option.some(values.get(index)) 
						: Option.<T>none();
			}
		};
	}
}
