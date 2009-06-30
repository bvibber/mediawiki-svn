/**
 * 
 */
package de.brightbyte.wikiword.analyzer.matcher;

import java.util.Collection;
import java.util.Iterator;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.MultiMap;

public abstract class  AbstractAttributeMatcher<T> implements AttributeMatcher<T> {

	public <V, C extends Collection<V>> Iterable<C> matches(MultiMap<? extends T, V, C> m) {
		final Iterator it = m.entrySet().iterator();
		return matches(it);
	}
	
	public <V> Iterable<V> matches(Map<? extends T, V> m) {
		final Iterator it = m.entrySet().iterator();
		return matches(it);
	}
	
	public <V extends T> Iterable<V> matches(Set<V> m) {
		final Iterator it = m.iterator();
		return matches(it);
	}
	
	protected <V> Iterable<V> matches(final Iterator it) {
		return new Iterable<V>() {
			public Iterator<V> iterator() {
				return new Iterator<V>() {
					
					private V next;
					private boolean hasNext;
					
					{ scan(); }
					
					private void scan() {
						hasNext = false;
						while (it.hasNext()) {
							hasNext = true;
							Map.Entry<? extends T, V> e = (Map.Entry<? extends T, V>) it.next();
							next = e.getValue();
							if (matches(e.getKey())) break;
							hasNext = false;
						}
					}
				
					public void remove() {
						throw new UnsupportedOperationException();
					}
				
					public V next() {
						if (!hasNext) return null;
						
						V n = next;
						scan();
						return n;
					}
				
					public boolean hasNext() {
						return hasNext;
					}
				
				};
			}
		};
	}
}