package net.psammead.functional;



public interface Cache<S,T> extends Function<S,T> {
	void clear();
}
