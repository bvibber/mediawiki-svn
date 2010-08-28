package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalFloat {
	private FunctionalFloat() {}

	// arithmetic: - + - * /

	public static final Function<Float,Float> neg = new Function<Float,Float>() { 
		public Float apply(final Float a) { 
			return -a; } };

	public static final Function<Float,Function<Float,Float>> add = new Function<Float,Function<Float,Float>>() { 
		public Function<Float,Float> apply(final Float a) { 
			return new Function<Float,Float>() { 
				public Float apply(final Float b) { 
					return a+b; } }; } };

	public static final Function<Float,Function<Float,Float>> subtract = new Function<Float,Function<Float,Float>>() {
		public Function<Float,Float> apply(final Float a) { 
			return new Function<Float,Float>() {
				public Float apply(final Float b) { 
					return a-b; } }; } };

	public static final Function<Float,Function<Float,Float>> multiply = new Function<Float,Function<Float,Float>>() { 
		public Function<Float,Float> apply(final Float a) { 
			return new Function<Float,Float>() { 
				public Float apply(final Float b) { 
					return a*b; } }; } };

	public static final Function<Float,Function<Float,Float>> divide = new Function<Float,Function<Float,Float>>() { 
		public Function<Float,Float> apply(final Float a) { 
			return new Function<Float,Float>() {
				public Float apply(final Float b) {
					return a/b; } }; } };
			
	// comparison: < > <= >=
	
	public static final Function<Float,Function<Float,Boolean>> lt = new Function<Float,Function<Float,Boolean>>() { 
		public Function<Float,Boolean> apply(final Float a) { 
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
					return a < b; } }; } };		
					
	public static final Function<Float,Function<Float,Boolean>> gt = new Function<Float,Function<Float,Boolean>>() { 
		public Function<Float,Boolean> apply(final Float a) { 
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
					return a > b; } }; } };		
					
	public static final Function<Float,Function<Float,Boolean>> le = new Function<Float,Function<Float,Boolean>>() { 
		public Function<Float,Boolean> apply(final Float a) { 
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
					return a <= b; } }; } };		
					
	public static final Function<Float,Function<Float,Boolean>> ge = new Function<Float,Function<Float,Boolean>>() { 
		public Function<Float,Boolean> apply(final Float a) { 
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
									return a >= b; } }; } };		
									
	// == !=
					
	public static final Function<Float,Function<Float,Boolean>> eq = new Function<Float,Function<Float,Boolean>>() {
		public Function<Float, Boolean> apply(final Float a) {
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
					// Float is a reference type!
					return (float)a == (float)b; } }; } };
					
	public static final Function<Float,Function<Float,Boolean>> neq = new Function<Float,Function<Float,Boolean>>() {
		public Function<Float, Boolean> apply(final Float a) {
			return new Function<Float,Boolean>() {
				public Boolean apply(final Float b) {
					// Float is a reference type!
					return (float)a != (float)b; } }; } };
}
	
	