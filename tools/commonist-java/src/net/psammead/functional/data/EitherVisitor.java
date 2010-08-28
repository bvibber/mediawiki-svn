package net.psammead.functional.data;

public interface EitherVisitor<L,R> {
	void left(L value);
	void right(R value);
}
