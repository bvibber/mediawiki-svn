package net.psammead.util.cache;

import java.lang.ref.SoftReference;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Map.Entry;

import net.psammead.functional.Function;

/** keys are strongly referenced, values are softly referenced */
public final class SoftCache<S,T> extends BaseCache<S,T> {
	private static final int CLEANUP_CYCLE = 1000;
	
	private final Map<S,SoftReference<T>>	references;
	private int accessCount;
	
	public SoftCache(Function<? super S,? extends T> loader) {
		super(loader);
		references	= new HashMap<S, SoftReference<T>>();
		accessCount	= 0;
	}
	
	public void clear() {
		references.clear();
	}
	
	public T apply(S key) {
		maybeCleanup();
		final SoftReference<T>	reference	= references.get(key);
		if (reference == null)	return load(key);
		final T					value		= reference.get();
		if (value == null)		return load(key);
		return value;
	}

	private T load(S key) {
		final T	value	= fetch(key);
		references.put(key, new SoftReference<T>(value));
		return value;
	}

	private void maybeCleanup() {
		accessCount++;
		if (accessCount < CLEANUP_CYCLE)	return;
		accessCount	= 0;
		
		for (Iterator<Map.Entry<S, SoftReference<T>>> it=references.entrySet().iterator(); it.hasNext();) {
			final Entry<S, SoftReference<T>> entry = it.next();
			if (entry.getValue().get() == null)	it.remove();
		}
	}
}
