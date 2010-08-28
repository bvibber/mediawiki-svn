package net.psammead.util.ref;

import java.lang.ref.WeakReference;
import java.util.Iterator;
import java.util.Map;
import java.util.WeakHashMap;
import java.util.Map.Entry;

/** a Map holding both key and value with a {@link WeakReference} */
public final class WeakAssociation<K,V> {
	private final Map<K,WeakReference<V>>	references;
	
	public WeakAssociation() {
		references		= new WeakHashMap<K,WeakReference<V>>();
	}
	
	public V get(K key) {
		expungeStale();
		final WeakReference<V> reference = references.get(key);
		if (reference == null)	return null;
		return reference.get();
	}
	
	public void put(K key, V value) {
		expungeStale();
		references.put(key, new WeakReference<V>(value));
	}
	
	public void clear() {
		for (WeakReference<V> reference : references.values()) {
			reference.clear();
		}
		references.clear();
	}
	
	private void expungeStale() {
		for (Iterator<Map.Entry<K, WeakReference<V>>> it=references.entrySet().iterator(); it.hasNext();) {
			final Entry<K, WeakReference<V>> entry = it.next();
			if (entry.getValue().get() == null)	it.remove();
		}
	}
}
