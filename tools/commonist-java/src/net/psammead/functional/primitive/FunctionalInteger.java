package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalInteger {
	private FunctionalInteger() {}

	// arithmetic: - + - * / %
	
	public static final Function<Integer,Integer> neg = new Function<Integer,Integer>() { 
		public Integer apply(final Integer a) { 
			return -a; } };

	public static final Function<Integer,Function<Integer,Integer>> add = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() { 
				public Integer apply(final Integer b) { 
					return a + b; } }; } };

	public static final Function<Integer,Function<Integer,Integer>> subtract = new Function<Integer,Function<Integer,Integer>>() {
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) { 
					return a - b; } }; } };

	public static final Function<Integer,Function<Integer,Integer>> multiply = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() { 
				public Integer apply(final Integer b) { 
					return a * b; } }; } };

	public static final Function<Integer,Function<Integer,Integer>> divide = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a / b; } }; } };
					
	public static final Function<Integer,Function<Integer,Integer>> modulo = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a % b; } }; } };
					
	// bitwise: & | ^
					
	public static final Function<Integer,Integer> comp = new Function<Integer,Integer>() { 
		public Integer apply(final Integer a) {
			return ~a; } };
					
	public static final Function<Integer,Function<Integer,Integer>> and = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a & b; } }; } };					
					
	public static final Function<Integer,Function<Integer,Integer>> or = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a | b; } }; } };
					
	public static final Function<Integer,Function<Integer,Integer>> xor = new Function<Integer,Function<Integer,Integer>>() {
		public Function<Integer, Integer> apply(final Integer a) {
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a ^ b; } }; } };		
					
	// shift: << >> >>>
					
	public static final Function<Integer,Function<Integer,Integer>> shl = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a << b; } }; } };		
					
	public static final Function<Integer,Function<Integer,Integer>> shr = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a >> b; } }; } };		
					
	public static final Function<Integer,Function<Integer,Integer>> asr = new Function<Integer,Function<Integer,Integer>>() { 
		public Function<Integer,Integer> apply(final Integer a) { 
			return new Function<Integer,Integer>() {
				public Integer apply(final Integer b) {
					return a >>> b; } }; } };		
	
	// comparison: < > <= >=
					
	public static final Function<Integer,Function<Integer,Boolean>> lt = new Function<Integer,Function<Integer,Boolean>>() { 
		public Function<Integer,Boolean> apply(final Integer a) { 
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					return a < b; } }; } };		
					
	public static final Function<Integer,Function<Integer,Boolean>> gt = new Function<Integer,Function<Integer,Boolean>>() { 
		public Function<Integer,Boolean> apply(final Integer a) { 
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					return a > b; } }; } };		
					
	public static final Function<Integer,Function<Integer,Boolean>> le = new Function<Integer,Function<Integer,Boolean>>() { 
		public Function<Integer,Boolean> apply(final Integer a) { 
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					return a <= b; } }; } };		
					
	public static final Function<Integer,Function<Integer,Boolean>> ge = new Function<Integer,Function<Integer,Boolean>>() { 
		public Function<Integer,Boolean> apply(final Integer a) { 
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					return a >= b; } }; } };		
					
	// equality: == !=
					
	public static final Function<Integer,Function<Integer,Boolean>> eq = new Function<Integer,Function<Integer,Boolean>>() {
		public Function<Integer, Boolean> apply(final Integer a) {
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					// Integer is a reference type!
					return (int)a == (int)b; } }; } };
					
	public static final Function<Integer,Function<Integer,Boolean>> neq = new Function<Integer,Function<Integer,Boolean>>() {
		public Function<Integer, Boolean> apply(final Integer a) {
			return new Function<Integer,Boolean>() {
				public Boolean apply(final Integer b) {
					// Integer is a reference type!
					return (int)a != (int)b; } }; } };
}
	
	