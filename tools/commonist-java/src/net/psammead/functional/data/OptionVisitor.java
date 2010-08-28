package net.psammead.functional.data;

public interface OptionVisitor<T> {
	void some(T value);
	void none();
}
