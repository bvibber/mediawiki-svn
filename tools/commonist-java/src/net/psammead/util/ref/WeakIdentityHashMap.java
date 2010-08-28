package net.psammead.util.ref;

import java.util.*;

/** generic wrapper */
@SuppressWarnings("unchecked")
public class WeakIdentityHashMap<K,V> implements Map<K,V> {
	private UncheckedWeakIdentityHashMap delegate;
	
	public WeakIdentityHashMap() {
		delegate = new UncheckedWeakIdentityHashMap();
	}
	
	public WeakIdentityHashMap(Map<? extends K, ? extends V> m) {
		this();
		putAll(m);
	}

	public void clear() {
		delegate.clear();
	}

	public boolean containsKey(Object key) {
		return delegate.containsKey(key);
	}

	public boolean containsValue(Object value) {
		return delegate.containsValue(value);
	}

	
	public Set<Map.Entry<K, V>> entrySet() {
		return delegate.entrySet();
	}

	@Override
	public boolean equals(Object obj) {
		return delegate.equals(obj);
	}

	public V get(Object key) {
		return (V)delegate.get(key);
	}

	@Override
	public int hashCode() {
		return delegate.hashCode();
	}

	public boolean isEmpty() {
		return delegate.isEmpty();
	}

	public Set<K> keySet() {
		return delegate.keySet();
	}

	public V put(Object key, Object value) {
		return (V)delegate.put(key, value);
	}

	public void putAll(Map<? extends K, ? extends V> t) {
		delegate.putAll(t);
	}

	public V remove(Object key) {
		return (V)delegate.remove(key);
	}

	public int size() {
		return delegate.size();
	}

	@Override
	public String toString() {
		return delegate.toString();
	}

	public Collection<V> values() {
		return delegate.values();
	}
}
