package net.psammead.functional;

import net.psammead.functional.data.Pair;
import net.psammead.util.ObjectUtil;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class Functions {
	private Functions() {}
	
	/** create an identity function returning the input */
	public static <T> Function<T,T> identity() {
		return new Function<T,T>() {
			public T apply(T source) {
				return source;
			}
		};
	}
	
	/** create a constant function returning the same value for every input */
	public static <S,T> Function<S,T> constant(final T value) {
		return new Function<S,T>() {
			public T apply(S source) {
				return value;
			}
		};
	}
	
	/** aka map */
	public static <R,S,T> Function<R,T> compose(final Function<? super S,? extends T> function2, final Function<? super R,? extends S> function1) {
		return new Function<R,T>() {
			public T apply(R source) {
				return function2.apply(function1.apply(source));
			}
		};
	}
	
	public static <S1,S2,S3,T> Function<S1,T> compose2(final Function<? super S3,? extends T> function3, final Function<? super S2,? extends S3> function2, final Function<? super S1,? extends S2> function1) {
		return compose(function3, compose(function2, function1));
	}
	public static <S1,S2,S3,S4,T> Function<S1,T> compose3(final Function<? super S4,? extends T> function4, final Function<? super S3,? extends S4> function3, final Function<? super S2,? extends S3> function2, final Function<? super S1,? extends S2> function1) {
		return compose(function4, compose(function3, compose(function2, function1)));
	}
	
	/** aka |> */
	public static <R,S,T> Function<R,T> andThen(final Function<? super R,? extends S> function1, final Function<? super S,? extends T> function2) {
		return compose(function2, function1);
	}
	
	public static <S1,S2,S3,T> Function<S1,T> andThen2(final Function<? super S1,? extends S2> function1, final Function<? super S2,? extends S3> function2, final Function<? super S3,? extends T> function3) {
		return andThen(function1, andThen(function2, function3));
	}
	public static <S1,S2,S3,S4,T> Function<S1,T> andThen3(final Function<? super S1,? extends S2> function1, final Function<? super S2,? extends S3> function2, final Function<? super S3,? extends S4> function3, final Function<? super S4,? extends T> function4) {
		return andThen(function1, andThen(function2, andThen(function3, function4)));
	}
	
	/** apply two functions to a value and apply a curried 2-arg function to the outcomes */
	public static <T,X,Y> Function<T,Pair<X,Y>> both(final Function<? super T,? extends X> a, final Function<? super T,? extends Y> b) {
		return new Function<T,Pair<X,Y>>() {
			public Pair<X,Y> apply(T source) {
				return Pair.create(
						(X)a.apply(source),
						(Y)b.apply(source));
			}
		};
	}
	
//	/** apply two functions to a value and apply a curried 2-arg function to the outcomes */
//	public static <S,X,Y,T> Function<S,T> bothAndThen(final Function<? super S,? extends X> a, final Function<? super S,? extends Y> b, final Function<? super X,? extends Function<? super Y,? extends T>> x) {
//		// simple version
//		return andThen(both(a,b), uncurry(x));
////		return new Function<S,T>() {
////			public T apply(S in) {
////				return x.apply(a.apply(in)).apply(b.apply(in));
////			}
////		};
//	}
	
	/** make a curried function from a Pair-arg function */
	public static <R,S,T> Function<R,Function<S,T>> curry(final Function<Pair<R,S>,T> in) {
		return new Function<R,Function<S,T>>() {
			public Function<S,T> apply(final R r) {
				return new Function<S,T>() {
					public T apply(final S s) {
						return in.apply(new Pair<R,S>(r,s));
					}
				};
			}
		};
	}
	
	/** make a Pair-arg function from a curried function */
	public static <R,S,T> Function<Pair<R,S>,T> uncurry(final Function<? super R,? extends Function<? super S,? extends T>> in) {
		return new Function<Pair<R,S>,T>() {
			public T apply(Pair<R,S> source) {
				return in.apply(source.first).apply(source.second);
			}
		};
	}
	
	/** swap the first and second argument in a curried function */
	public static <A,B,T> Function<B,Function<A,T>> flip(final Function<? super A,? extends Function<? super B,? extends T>> function) {
		return new Function<B,Function<A,T>>() {
			public Function<A,T> apply(final B b) {
				return new Function<A,T>() {
					public T apply(A a) {
						return function.apply(a).apply(b);
					}
				};
			}
		};
	}
	
	/** swap the second and third argument in a curried function */
	public static <A,B,C,T> Function<A,Function<C,Function<B,T>>> flip23(final Function<A,Function<B,Function<C,T>>> function) {
		return new Function<A,Function<C,Function<B,T>>>() {
			public Function<C, Function<B, T>> apply(A a) {
				return flip(function.apply(a));
			}
		};
	}
	
	/** swap the first and third argument in a curried function */
	public static <A,B,C,T> Function<C,Function<B,Function<A,T>>> flip13(final Function<A,Function<B,Function<C,T>>> function) {
		return new Function<C,Function<B,Function<A,T>>>() {
			public Function<B,Function<A,T>> apply(final C c) {
				return new Function<B,Function<A,T>>() {
					public Function<A,T> apply(final B b) {
						return new Function<A,T>() {
							public T apply(final A a) {
								return function.apply(a).apply(b).apply(c);
							}
						};
					}
				};
			}
		};
	}
	
	/** create a function applying another function if the input value is not null or returning null whenn the input is null */
	public static <S,T> Function<S,T> propagateNull(final Function<S,T> function) {
		return new Function<S,T>() {
			public T apply(S source) {
				if (source == null)	return null;
				return function.apply(source);
			}
		};
	}
	
	public static <S,T> Function<S,T> nullSafe(final Function<S,T> function, final T nullReplacement) {
//		return Functions.andThen(
//				Functions.propagateNull(function), 
//				Functions.replace((T)null, nullReplacement));
		return new Function<S,T>() {
			public T apply(S source) {
				if (source == null)	return nullReplacement;
				return function.apply(source);
			}
		};
	}
	
	
	public static <T> Function<T,T> replace(final T originalValue, final T derivedValue) {
		return new Function<T,T>() {
			public T apply(T original) {
				return ObjectUtil.equals(original, originalValue) ? derivedValue : original;
			}
		};
	}
	
	// TODO functional: check these two
//	public static <S,T> Function<S,T> variant(Function<? super S,? extends T> function) {
//		return (Function<S,T>)function;
//	}
//	public static <S,T> Function<? super S,? extends T> vary(Function<S,T> function) {
//		return function;
//	}
//	@SuppressWarnings("unchecked")
//	public static <S,T,X extends T> Function<S,X> upcast(Function<S,T> function) {
//		return (Function<S, X>) function;
//	}
	
	/** vary type parameters: input is contravariant, output is covariant */
	public static <S,T> Function<S,T> vary(final Function<? super S,? extends T> function) {
		return new Function<S,T>() {
			public T apply(final S a) {
				return function.apply(a);
			}
		};
	}
	
	// TODO functional: simplify
	public static <T,S extends T> Function<S,T> upcast() {
		return Functions.vary(Functions.<T>identity());
	}
	
	// this is Donors#getter(function)
//	public static <S,T> Function<S,Donor<T>> lazy(final Function<S,T> function) {
//		return new Function<S,Donor<T>>() {
//			public Donor<T> apply(final S source) {
//				return new Donor<T>() {
//					public T get() {
//						return function.apply(source);
//					}
//				};
//			}
//		};
//	}
}
