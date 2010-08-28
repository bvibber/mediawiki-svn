package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalReference {
	private FunctionalReference() {}
	
	// TODO functional: add instanceof
	
	// equality: == !=
	
	public static final <S,T> Function<S,Function<T,Boolean>> isSame() { 
		return new Function<S,Function<T,Boolean>>() {
			public Function<T, Boolean> apply(final S source1) {
				return new Function<T, Boolean>() {
					public Boolean apply(final T source2) {
						return source1 == source2; } }; } }; }
	
	public static final <S,T> Function<S,Function<T,Boolean>> isNotSame() { 
		return new Function<S,Function<T,Boolean>>() {
			public Function<T, Boolean> apply(final S source1) {
				return new Function<T, Boolean>() {
					public Boolean apply(final T source2) {
						return source1 != source2; } }; } }; }

	// nullity: ==null !=null
	
	public static final <T> Function<T,Boolean> isNull() { 
		return new Function<T,Boolean>() { 
			public Boolean apply(final T t) { return t == null; } }; }

	public static final <T> Function<T,Boolean> isNotNull() {
		return new Function<T,Boolean>() { 
			public Boolean apply(final T t) { 
				return t != null; } }; }
}
