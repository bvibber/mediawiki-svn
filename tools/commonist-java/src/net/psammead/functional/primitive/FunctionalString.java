package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalString {
	private FunctionalString() {}

	// +
	
	public static final Function<String,Function<String,String>> concat = new Function<String,Function<String,String>>() {
		public Function<String, String> apply(final String a) {
			return new Function<String,String>() {
				public String apply(final String b) {
					return a + b; } }; } };
}
