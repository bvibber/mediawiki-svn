package net.psammead.functional;

/** a bidirectional converter between two types of value */
public interface Converter<S,T> {
	T apply(S original);
	S unapply(T derived);
}
