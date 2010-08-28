package net.psammead.functional;

public final class Donors {
	private Donors() {}
	
	/** a {@link Donor} returning a constant value */
	public static <T> Donor<T> constant(final T value) {
		return new Donor<T>() {
			public T get() {
				return value;
			}
		};
	}
	
//	/** first class variant of {@link Donor#get()} */
//	public static <T> Function<Donor<T>,T> get() {
//		return new Function<Donor<T>,T>() {
//			public T apply(Donor<T> donor) {
//				return donor.get();
//			}
//		};
//	}
	
	/** convert a {@link Donor} into a {@link Function} ignoring its argument */
	public static <S,T> Function<S,T> asFunction(final Donor<T> donor) {
		return new Function<S,T>() {
			public T apply(S source){
				return donor.get();
			}
		};
	}
	
	//-------------------------------------------------------------------------
	
	// scgen-specific: create a getter for a specific instance
	public static <S,T> Donor<T> getter(final S target, final Function<S,T> getFunction) {
		return new Donor<T>() {
			public T get() {
				return getFunction.apply(target);
			}
		};
	}
	
	// scgen-specific: create a getter
	public static <S,T> Function<S,Donor<T>> getter(final Function<S,T> getFunction) {
		return new Function<S,Donor<T>>() {
			public Donor<T> apply(final S target) {
				return getter(target, getFunction);
			}
		};
	}
	
	//-------------------------------------------------------------------------
	
//	public static Runnable asRunnable(final Donor<?> donor) {
//		return new Runnable() {
//			public void run() {
//				donor.get();
//			}
//		};
//	}
//
//	public static <T> Callable<T> asCallable(final Donor<T> donor) {
//		return new Callable<T>() {
//			public T call() throws Exception {
//				return donor.get();
//			}
//		};
//	}

	//-------------------------------------------------------------------------
	
	public static <S,T> Donor<T> map(final Donor<S> source, final Function<? super S, ? extends T> transformer) {
		return new Donor<T>() {
			public T get() {
				return transformer.apply(source.get()); 
			}
		};
	}
	
	/** aka join */
	public static <T> Donor<T> flatten(final Donor<? extends Donor<? extends T>> donor) {
		return new Donor<T>(){
			public T get() {
				return donor.get().get();
			}
		};
	}
	
	/** aka bind */
	public static <S,T> Donor<T> flatMap(Donor<S> source, Function<? super S,Donor<? extends T>> meta) {
		return flatten(map(source, meta));
	}
	
	//-------------------------------------------------------------------------
	
	/** Donor is covariant */
    @SuppressWarnings("unchecked")
	public static<T> Donor<T> vary(Donor<? extends T> donor) {
    	return (Donor<T>)donor;
    }
}
