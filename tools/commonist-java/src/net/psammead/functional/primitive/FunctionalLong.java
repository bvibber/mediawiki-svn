package net.psammead.functional.primitive;

import net.psammead.functional.Function;
import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class FunctionalLong {
	private FunctionalLong() {}

	// arithmetic: - + - * / %
	
	public static final Function<Long,Long> neg = new Function<Long,Long>() { 
		public Long apply(final Long a) { 
			return -a; } };

	public static final Function<Long,Function<Long,Long>> add = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() { 
				public Long apply(final Long b) { 
					return a + b; } }; } };

	public static final Function<Long,Function<Long,Long>> subtract = new Function<Long,Function<Long,Long>>() {
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) { 
					return a - b; } }; } };

	public static final Function<Long,Function<Long,Long>> multiply = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() { 
				public Long apply(final Long b) { 
					return a * b; } }; } };

	public static final Function<Long,Function<Long,Long>> divide = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a / b; } }; } };
					
	public static final Function<Long,Function<Long,Long>> modulo = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a % b; } }; } };
					
	// bitwise: & | ^
					
	public static final Function<Long,Long> comp = new Function<Long,Long>() { 
		public Long apply(final Long a) {
			return ~a; } };
					
	public static final Function<Long,Function<Long,Long>> and = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a & b; } }; } };					
					
	public static final Function<Long,Function<Long,Long>> or = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a | b; } }; } };
					
	public static final Function<Long,Function<Long,Long>> xor = new Function<Long,Function<Long,Long>>() {
		public Function<Long, Long> apply(final Long a) {
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a ^ b; } }; } };		
					
	// shift: << >> >>>
					
	public static final Function<Long,Function<Long,Long>> shl = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a << b; } }; } };		
					
	public static final Function<Long,Function<Long,Long>> shr = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a >> b; } }; } };		
					
	public static final Function<Long,Function<Long,Long>> asr = new Function<Long,Function<Long,Long>>() { 
		public Function<Long,Long> apply(final Long a) { 
			return new Function<Long,Long>() {
				public Long apply(final Long b) {
					return a >>> b; } }; } };		
	
	// comparison: < > <= >=
					
	public static final Function<Long,Function<Long,Boolean>> lt = new Function<Long,Function<Long,Boolean>>() { 
		public Function<Long,Boolean> apply(final Long a) { 
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					return a < b; } }; } };		
					
	public static final Function<Long,Function<Long,Boolean>> gt = new Function<Long,Function<Long,Boolean>>() { 
		public Function<Long,Boolean> apply(final Long a) { 
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					return a > b; } }; } };		
					
	public static final Function<Long,Function<Long,Boolean>> le = new Function<Long,Function<Long,Boolean>>() { 
		public Function<Long,Boolean> apply(final Long a) { 
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					return a <= b; } }; } };		
					
	public static final Function<Long,Function<Long,Boolean>> ge = new Function<Long,Function<Long,Boolean>>() { 
		public Function<Long,Boolean> apply(final Long a) { 
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					return a >= b; } }; } };		
					
	// equality: == !=
					
	public static final Function<Long,Function<Long,Boolean>> eq = new Function<Long,Function<Long,Boolean>>() {
		public Function<Long, Boolean> apply(final Long a) {
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					// Long is a reference type!
					return (long)a == (long)b; } }; } };
					
	public static final Function<Long,Function<Long,Boolean>> neq = new Function<Long,Function<Long,Boolean>>() {
		public Function<Long, Boolean> apply(final Long a) {
			return new Function<Long,Boolean>() {
				public Boolean apply(final Long b) {
					// Long is a reference type!
					return (long)a != (long)b; } }; } };
}
	
	