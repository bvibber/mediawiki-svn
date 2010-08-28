package net.psammead.util;

import java.util.HashMap;
import java.util.Map;
import java.util.Set;
import java.util.Map.Entry;

import net.psammead.functional.Acceptor;
import net.psammead.functional.Function;
import net.psammead.functional.data.Option;
import net.psammead.functional.data.Pair;
import net.psammead.util.annotation.FullyStatic;

/** {@link Map} utility functions */
@FullyStatic 
public final class MapUtil {
	private MapUtil() {}
	
	/** create a Map from key/value-pairs */
	public static <S,T> Map<S,T> create(Iterable<Pair<S,T>> pairs) {
		final Map<S,T>	out = new HashMap<S,T>();
		for (Pair<S,T> pair : pairs) {
			out.put(pair.first, pair.second);
		}
		return out;
	}
	
	/** create a Map from entries */
	public static <S,T> Map<S,T> createFromEntries(Iterable<Entry<S,T>> entries) {
		final Map<S,T>	out = new HashMap<S,T>();
		for (Entry<S,T> entry : entries) {
			out.put(entry.getKey(), entry.getValue());
		}
		return out;
	}
	
	/** get some item, or none */
	public static <S,T> Option<T> itemAt(S key, Map<? super S,T> values) {
		if (values.containsKey(key))	return Option.some(values.get(key));
		else							return Option.none();
	}
	
	/** apply an effect for every key/value-pair */
	public static <S,T> void forEach(Acceptor<? super Pair<? super S,? super T>> effect, Map<S,T> values) {
		for (Pair<S,T> element : entryPairs(values)) {
			effect.set(element);
		}
	}
	
	/** provide key/value-pairs */
	public static <S,T> Set<Pair<S,T>> entryPairs(Map<S,T> values) {
		return SetUtil.map(MapUtil.<S,T>entryToPair(), values.entrySet());
	}
	
	public static <S,T> Function<Entry<S,T>,Pair<S,T>> entryToPair() {
		return new Function<Entry<S,T>,Pair<S,T>>() {
			public Pair<S,T> apply(Entry<S,T> source) {
				return Pair.create(source.getKey(), source.getValue());
			}
		};
	}
	
	/** convert a pair to an entry */
	public static <S,T> Function<Pair<S,T>,Entry<S,T>> pairToEntry() {
		return new Function<Pair<S,T>,Entry<S,T>>() {
			public Entry<S,T> apply(final Pair<S,T> source) {
				return ImmutableMapEntry.create(source.first, source.second);
			}
		};
	}
	
	/** partition into a pair of maps. the first contains entries for which the predicate is true */
	public static <S,T> Pair<Map<S,T>,Map<S,T>> partition(Function<? super S,Boolean> predicate, Map<S,T> values) {
		final Map<S,T>	xs	= new HashMap<S,T>();
		final Map<S,T>	ys	= new HashMap<S,T>();
		for (S key : values.keySet()) {
			final T value	= values.get(key);
			if (predicate.apply(key))	xs.put(key, value);
			else						ys.put(key, value);
		}
		return Pair.create(xs, ys);
	}
	
	/** turn a map into a function which returns null for missing keys */
	public static <S,T> Function<S,T> getter(final Map<S,T> values) {
		return new Function<S,T>() {
			public T apply(final S source) {
				return values.get(source);
			}
		};
	}
	
	/** turn a map into a function which returns some value for a key, or none */
	public static <S,T> Function<S,Option<T>> optionGetter(final Map<S,T> values) {
		return new Function<S,Option<T>>() {
			public Option<T> apply(final S key) {
				return values.containsKey(key) 
						? Option.some(values.get(key)) 
						: Option.<T>none();
			}
		};
	}
	
	private static class ImmutableMapEntry<S,T> implements Entry<S,T> {
		public static <S,T> Entry<S,T> create(S s, T t) {
			return new ImmutableMapEntry<S,T>(s, t);
		}
		private final S	s;
		private final T	t;
		
		public ImmutableMapEntry(S s, T t) {
			this.s = s;
			this.t = t;
		}
		
		public S getKey() {
			return s;
		}
		
		public T getValue() {
			return t;
		}
		
		public T setValue(T value) {
			throw new UnsupportedOperationException();
		}
	}
}
