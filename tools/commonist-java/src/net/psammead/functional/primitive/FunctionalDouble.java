package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalDouble {
	private FunctionalDouble() {}

	// arithmetic: - + - * /
	
	public static final Function<Double,Double> neg = new Function<Double,Double>() { 
		public Double apply(final Double a) { 
			return -a; } };

	public static final Function<Double,Function<Double,Double>> add = new Function<Double,Function<Double,Double>>() { 
		public Function<Double,Double> apply(final Double a) { 
			return new Function<Double,Double>() { 
				public Double apply(final Double b) { 
					return a+b; } }; } };

	public static final Function<Double,Function<Double,Double>> subtract = new Function<Double,Function<Double,Double>>() {
		public Function<Double,Double> apply(final Double a) { 
			return new Function<Double,Double>() {
				public Double apply(final Double b) { 
					return a-b; } }; } };

	public static final Function<Double,Function<Double,Double>> multiply = new Function<Double,Function<Double,Double>>() { 
		public Function<Double,Double> apply(final Double a) { 
			return new Function<Double,Double>() { 
				public Double apply(final Double b) { 
					return a*b; } }; } };

	public static final Function<Double,Function<Double,Double>> divide = new Function<Double,Function<Double,Double>>() { 
		public Function<Double,Double> apply(final Double a) { 
			return new Function<Double,Double>() {
				public Double apply(final Double b) {
					return a/b; } }; } };
		
	// comparison: < > <= >=
	
	public static final Function<Double,Function<Double,Boolean>> lt = new Function<Double,Function<Double,Boolean>>() { 
		public Function<Double,Boolean> apply(final Double a) { 
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					return a < b; } }; } };		
					
	public static final Function<Double,Function<Double,Boolean>> gt = new Function<Double,Function<Double,Boolean>>() { 
		public Function<Double,Boolean> apply(final Double a) { 
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					return a > b; } }; } };		
					
	public static final Function<Double,Function<Double,Boolean>> le = new Function<Double,Function<Double,Boolean>>() { 
		public Function<Double,Boolean> apply(final Double a) { 
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					return a <= b; } }; } };		
					
	public static final Function<Double,Function<Double,Boolean>> ge = new Function<Double,Function<Double,Boolean>>() { 
		public Function<Double,Boolean> apply(final Double a) { 
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					return a >= b; } }; } };		
									
	// == !=
					
	public static final Function<Double,Function<Double,Boolean>> eq = new Function<Double,Function<Double,Boolean>>() {
		public Function<Double, Boolean> apply(final Double a) {
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					// Double is a reference type!
					return (double)a == (double)b; } }; } };
					
	public static final Function<Double,Function<Double,Boolean>> neq = new Function<Double,Function<Double,Boolean>>() {
		public Function<Double, Boolean> apply(final Double a) {
			return new Function<Double,Boolean>() {
				public Boolean apply(final Double b) {
					// Double is a reference type!
					return (double)a != (double)b; } }; } };
}
	
	