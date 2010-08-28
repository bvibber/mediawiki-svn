package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalBoolean {
	private FunctionalBoolean() {}

	// ! && || ^
	
	public static final Function<Boolean,Boolean> not = new Function<Boolean,Boolean>() { 
		public Boolean apply(final Boolean a) { return !a; } };

	public static final Function<Boolean,Function<Boolean,Boolean>> and = new Function<Boolean,Function<Boolean,Boolean>>() {
		public Function<Boolean, Boolean> apply(final Boolean a) {
			return new Function<Boolean,Boolean>() {
				public Boolean apply(final Boolean b) {
					return a && b; } }; } };

	public static final Function<Boolean,Function<Boolean,Boolean>> or = new Function<Boolean,Function<Boolean,Boolean>>() {
		public Function<Boolean, Boolean> apply(final Boolean a) {
			return new Function<Boolean,Boolean>() {
				public Boolean apply(final Boolean b) {
					return a || b; } }; } };
					
	public static final Function<Boolean,Function<Boolean,Boolean>> xor = new Function<Boolean,Function<Boolean,Boolean>>() {
		public Function<Boolean, Boolean> apply(final Boolean a) {
			return new Function<Boolean,Boolean>() {
				public Boolean apply(final Boolean b) {
					return a ^ b; } }; } };			
					
	// == !=
					
	public static final Function<Boolean,Function<Boolean,Boolean>> eq = new Function<Boolean,Function<Boolean,Boolean>>() {
		public Function<Boolean, Boolean> apply(final Boolean a) {
			return new Function<Boolean,Boolean>() {
				public Boolean apply(final Boolean b) {
					// Boolean is a reference type!
					return (boolean)a == (boolean)b; } }; } };
					
	public static final Function<Boolean,Function<Boolean,Boolean>> neq = new Function<Boolean,Function<Boolean,Boolean>>() {
		public Function<Boolean, Boolean> apply(final Boolean a) {
			return new Function<Boolean,Boolean>() {
				public Boolean apply(final Boolean b) {
					// Boolean is a reference type!
					return (boolean)a != (boolean)b; } }; } };
	
	// ? :
			
	// TODO functional: vary
	public static final <T> Function<Boolean,Function<T,Function<T,T>>> choose() {
		return new Function<Boolean,Function<T,Function<T,T>>>() {
			public Function<T, Function<T,T>> apply(final Boolean condition) {
				return new Function<T, Function<T,T>>() {
					public Function<T, T> apply(final T trueValue) {
						return new  Function<T,T>() {
							public T apply(final T falseValue) {
								return condition ? trueValue : falseValue; } }; } }; }  }; }	
	
//	public static final <T> Function<T,Function<T,Function<Boolean,T>>> chooseLast() {
//		return new Function<T,Function<T,Function<Boolean,T>>>() {
//			public Function<T,Function<Boolean,T>> apply(final T trueValue) {
//				return new Function<T,Function<Boolean,T>>() {
//					public Function<Boolean,T> apply(final T falseValue) {
//						return chooseLast(trueValue, falseValue);
//					}
//				};
//			}
//		};
//	}
//	
//	public static final <T> Function<Boolean,T> chooseLast(final T trueValue, final T falseValue) {
//		return new Function<Boolean,T>() {
//			public T apply(Boolean source) {
//				return source ? trueValue : falseValue;
//			}
//		};
//	}
}
	
	