package net.psammead.util.cache;

import net.psammead.functional.Cache;
import net.psammead.functional.Function;

public abstract class BaseCache<S,T> implements Cache<S, T> {
	private final Function<? super S,? extends T>	loader;
	
	public BaseCache(Function<? super S,? extends T> loader) {
		this.loader	= loader;
	}
	
	protected T fetch(S key) {
		return loader.apply(key);
	}
}
