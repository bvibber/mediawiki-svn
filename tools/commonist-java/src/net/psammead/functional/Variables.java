package net.psammead.functional;


public final class Variables {
	private Variables() {}

	public static <T> Variable<T> variable(final Donor<T> donor, final Acceptor<T> acceptor) {
		return new Variable<T>() {
			public T get() {
				return donor.get();
			}
			public void set(T value) {
				acceptor.set(value);
			}
		};
	}
}
