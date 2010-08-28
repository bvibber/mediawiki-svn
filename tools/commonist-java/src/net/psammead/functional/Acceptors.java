package net.psammead.functional;

import java.util.List;

public final class Acceptors {
	private Acceptors() {}
	
	/** an {@link Acceptor} doing nothing */
	public static <T> Acceptor<T> ignore() {
		return new Acceptor<T>() {
			public void set(T value) {}
		};
	}
	
//	/** first class variant of {@link Acceptor#set()} */
//	public static <T> Function<Acceptor<T>,Function<T,Unit>> set() {
//		return new Function<Acceptor<T>,Function<T,Unit>>() {
//			public Function<T,Unit> apply(final Acceptor<T> effect) {
//				return asFunction(effect);
//			}
//		};
//	}
//	
//	/** convert an {@link Acceptor} into a {@link Function} */
//	public static <T> Function<T,Unit> asFunction(final Acceptor<T> effect) {
//		return new Function<T,Unit>() {
//			public Unit apply(T value){
//				effect.set(value);
//				return Unit.INSTANCE;
//			}
//		};
//	}
//	
	/** convert an {@link Acceptor} into a {@link Function} */
	public static <T> Acceptor<T> fromFunction(final Function<? super T,?> effect) {
		return new Acceptor<T>() {
			public void set(T value) {
				effect.apply(value);
			}
		};
	}
	
	// TODO acceptor: these two are equivalent
	//Acceptor<Rectangle> setter1 = Acceptors.setter(_Canvas.refresh()).apply(this);
	//Acceptor<Rectangle> setter2 = Acceptors.fromFunction(_Canvas.refresh().apply(this));
	
	//-------------------------------------------------------------------------
	
	// scgen-specific: run a parameterless method on a target when set
	public static <T> Function<T,Acceptor<Object>> runner(final Function<? super T,Unit> function) {
		return new Function<T,Acceptor<Object>>() {
			public Acceptor<Object> apply(T target) {
				return runner(target, function);
			}
		};
	}

	// scgen-specific: run a parameterless method on a specific target
	public static <T> Acceptor<Object> runner(final T target, final Function<? super T,Unit> function) {
		return new Acceptor<Object>() {
			public void set(Object value) {
				function.apply(target);
			}
		};
	}

	// scgen-specific: create a setter for a specific instance
	public static <S,T> Acceptor<T> setter(final S target, final Function<S,Function<T,Unit>> setFunction) {
		return new Acceptor<T>() {
			public void set(T value) {
				setFunction.apply(target).apply(value);
			}
		};
	}
	
	// scgen-specific: create a setter
	public static <S,T> Function<S,Acceptor<T>> setter(final Function<S,Function<T,Unit>> setFunction) {
		return new Function<S,Acceptor<T>>() {
			public Acceptor<T> apply(final S target) {
				return setter(target, setFunction);
			}
		};
	}
	
	//-------------------------------------------------------------------------
	
	// TODO acceptor: add flatten and flatMap
	
	public static <S,T> Acceptor<S> map(final Acceptor<? super T> target, final Function<? super S,? extends T> transformer) {
		return new Acceptor<S>() {
			public void set(S value) {
				target.set(transformer.apply(value));
			}
		};
	}
	
	public static <T> Acceptor<T> filter(final Acceptor<? super T> target, final Function<? super T,Boolean> predicate) {
		return new Acceptor<T>() {
			public void set(T value) {
				if (predicate.apply(value)) {
					target.set(value);
				}
			}
		};
	}
	
	//-------------------------------------------------------------------------
	
	public static <T> Acceptor<T> distribute(final Acceptor<? super T> a, final Acceptor<? super T> b) {
		return new Acceptor<T>() {
			public void set(T value) {
				a.set(value);
				b.set(value);
			}
		};
	}
	
	public static <T> Acceptor<T> distribute(final List<? extends Acceptor<? super T>> targets) {
		return new Acceptor<T>() {
			public void set(T value) {
				for (Acceptor<? super T> acceptor : targets) {
					acceptor.set(value);
				}
			}
		};
	}
	
	
	//-------------------------------------------------------------------------
	
    // TODO functional: variable is an Acceptor, but it's invariant!
    
    /** Acceptor is contravariant */
    @SuppressWarnings("unchecked")
	public static<T> Acceptor<T> vary(Acceptor<? super T> acceptor) {
    	return (Acceptor<T>)acceptor;
    }
}
