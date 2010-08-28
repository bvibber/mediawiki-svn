package net.psammead.util.cache;

import net.psammead.functional.Function;
import net.psammead.util.ref.WeakAssociation;

/** keys and values are weakly referenced by using a {@link WeakAssociation} */
public final class WeakAssociationCache<S,T> extends BaseCache<S,T> {
	private final WeakAssociation<S,T>	data;
	
	public WeakAssociationCache(Function<? super S,? extends T> loader) {
		super(loader);
		data	= new WeakAssociation<S,T>();
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
