package net.psammead.functional.versatz;

import net.psammead.functional.Functions;
import net.psammead.functional.Function;

// TODO functional: is this useful?
public final class FunctionWrapper<S,T> implements Function<S,T> {
	public static final <S,T> FunctionWrapper<S,T> $(Function<S,T> delegate) {
		return new FunctionWrapper<S,T>(delegate);
	}
	
	private final Function<? super S, ? extends T>	delegate;

	private FunctionWrapper(Function<? super S,? extends T> delegate) {
		this.delegate	= delegate;
	}
	
	public T apply(S source) {
		return delegate.apply(source);
	}
	
	// TODO functional: add map and flatMap

	public <R> FunctionWrapper<R,T> compose(Function<? super R, ? extends S> that) {
		return $(Functions.<R,S,T>compose(this, that));
	}
	
	public <U> FunctionWrapper<S,U> antThen(Function<? super T, ? extends U> that) {
		return $(Functions.<S,T,U>andThen(this, that));
	}
	
	public FunctionWrapper<S,T> propagateNull() {
		return $(Functions.propagateNull(this));
	}

	public FunctionWrapper<S,T> nullSafe(T nullReplacement) {
		return $(Functions.nullSafe(this, nullReplacement));
	}

	public FunctionWrapper<? super S,? extends T> vary() {
		return $(this);
	}
//	public FunctionWrapper<? super S, ? extends T> vary() {
//		return $(Functions.vary(this));
//	}
}
