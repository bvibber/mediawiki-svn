package net.psammead.functional;

import static net.psammead.functional.Functions.*;

import java.util.Arrays;

import net.psammead.util.annotation.FullyStatic;


@FullyStatic 
public final class Predicates {
	private Predicates() {}
	
	/** accepts every value */
	public static <T> Function<T,Boolean> always() {
		return constant(true);
	}

	/** accepts no value */
	public static <T> Function<T,Boolean> never() { 
		return constant(false);
	}
	
	/** accepts if the sub does not accept */
	public static <T> Function<T,Boolean> not(final Function<? super T,Boolean> sub) { 
//		return andThen(sub, FunctionalBoolean.not);
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				return !sub.apply(element); 
			}
		};
	}
	
	/** accepts if both subs accept */
	public static <T> Function<T,Boolean> and(final Function<? super T,Boolean> a, final Function<? super T,Boolean> b) {
		// TODO functional: return bothAndThen(a, b, FunctionalBoolean.and);
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				return a.apply(element) 
					&& b.apply(element);
			}
		};
	}
	
	/** accepts if at least one of the subs accepts */
	public static <T> Function<T,Boolean> or(final Function<? super T,Boolean> a, final Function<? super T,Boolean> b) {
		// BETTER return bothAndThen(a, b, FunctionalBoolean.or);
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				return a.apply(element) 
					|| b.apply(element);
			}
		};
	}
	
	/** accepts if exactly one of the subs accepts */
	public static <T> Function<T,Boolean> xor(final Function<? super T,Boolean> a, final Function<? super T,Boolean> b) {
		// BETTER return bothAndThen(a, b, FunctionalBoolean.xor);
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				return a.apply(element) 
					^  b.apply(element);
			}
		};
	}
	
	/** accepts if all subs accept */
	public static <T> Function<T,Boolean> and(final Iterable<? extends Function<? super T,Boolean>> subs) {
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				for (Function<? super T,Boolean> sub : subs) {
					if (!sub.apply(element))	return false;
				}
				return true;
			}
		};
	}
	
	/** accepts if at least one sub accepts */
	public static <T> Function<T,Boolean> or(final Iterable<? extends Function<? super T,Boolean>> subs) { 
		return new Function<T,Boolean>() {
			public Boolean apply(T element) {
				for (Function<? super T,Boolean> sub : subs) { 
					if (sub.apply(element))	return true;
				}
				return false;
			}
		};
	}
	
	/** accepts if all subs accept */
	public static <T> Function<T,Boolean> andVA(final Function<? super T,Boolean>... subs) {
		return and(Arrays.asList(subs));
	}
	
	/** accepts if at least one sub accepts */
	public static <T> Function<T,Boolean> orVA(final Function<? super T,Boolean>... subs) { 
		return or(Arrays.asList(subs));
	}
}
