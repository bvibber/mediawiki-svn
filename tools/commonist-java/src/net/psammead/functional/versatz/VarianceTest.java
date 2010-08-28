package net.psammead.functional.versatz;

@SuppressWarnings("all")
public class VarianceTest {
	interface A {}
	interface B extends A {}
	interface C extends B {}
	
	static interface Co<T> {}
	static interface Contra<T> {}
	
	static <T> Co<? extends T> vary(Co<T> foobar) { return foobar; } 
	static <T> Contra<? super T> vary(Contra<T> foobar) { return foobar; }
	
	/*
	<Apocalisp> datura: It's not that somebody would expect an F<? super A, ? extends B>, 
			but might expect a List<? extends B> and you have a List<A>.
	<Apocalisp> rather, a List<C> where C extends B, and you have a List<A>
	*/
	
	private static void xxx() {
		Co<B>	bb	= null;
		expects(bb);
		Co<? extends A> varied = VarianceTest.vary(bb);
		expects(varied);
	}
	
	static void expects(Co<? extends A> foobar) {}
}
