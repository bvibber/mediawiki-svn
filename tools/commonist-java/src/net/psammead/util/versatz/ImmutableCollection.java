package net.psammead.util.versatz;

import java.util.Iterator;



public interface ImmutableCollection<E> extends Iterable<E> {
    int size();
    boolean isEmpty();
    boolean contains(Object o);
    Iterator<E> iterator();
    Object[] toArray();
    <T> T[] toArray(T[] a);
//    boolean add(E e);
//    boolean remove(Object o);
    boolean containsAll(ImmutableCollection<?> c);
//    boolean addAll(ImmutableCollection<? extends E> c);
//    boolean removeAll(ImmutableCollection<?> c);
//    boolean retainAll(ImmutableCollection<?> c);
//    void clear();
    boolean equals(Object o);
    int hashCode();
}
