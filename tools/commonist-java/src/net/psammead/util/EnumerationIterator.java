package net.psammead.util;

import java.util.Enumeration;
import java.util.Iterator;

/** wraps an Enumeration in an Iterator */
public final class EnumerationIterator<E> implements Iterator<E> {
	private final Enumeration<? extends E>	delegate;

	public EnumerationIterator(Enumeration<? extends E> delegate) {
		this.delegate = delegate;
	}
	
    public boolean hasNext() { 
    	return delegate.hasMoreElements(); 
    }

    public E next() {
    	return delegate.nextElement();
    }

    public void remove() {
    	 throw new UnsupportedOperationException();
     }
}
