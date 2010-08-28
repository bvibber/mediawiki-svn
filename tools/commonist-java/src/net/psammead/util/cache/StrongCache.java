package net.psammead.util.cache;

import java.util.HashMap;
import java.util.Map;

import net.psammead.functional.Function;

/** keys and values are strongly referenced */
public final class StrongCache<S,T> extends BaseCache<S,T> {
	private final Map<S,T>	data;
	
	public StrongCache(Function<? super S,? extends T> loader) {
		super(loader);
		data	= new HashMap<S, T>();
	}
	
	public void clear() {
		data.clear();
	}

	public T apply(S key) {
		final T	value1	= data.get(key);
		if (value1 != null)	return value1;
		final T value	= fetch(key);
		data.put(key, value);
		return value;
	}
}
