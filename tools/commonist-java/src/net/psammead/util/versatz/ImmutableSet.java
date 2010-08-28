package net.psammead.util.versatz;

import java.util.Collection;
import java.util.Iterator;

public interface ImmutableSet<E> extends ImmutableCollection<E> {
    int size();
    boolean isEmpty();
    boolean contains(Object o);
    Iterator<E> iterator();
    Object[] toArray();
    <T> T[] toArray(T[] a);
//    boolean add(E e);
//    boolean remove(Object o);
    boolean containsAll(Collection<?> c);
//    boolean addAll(Collection<? extends E> c);
//    boolean retainAll(Collection<?> c);
//    boolean removeAll(Collection<?> c);
//    void clear();
    boolean equals(Object o);
    int hashCode();
}
