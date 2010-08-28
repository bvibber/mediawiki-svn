package net.psammead.functional.data;

import net.psammead.functional.Function;
import net.psammead.util.ToString;
import net.psammead.util.annotation.ImmutableValue;

@ImmutableValue 
public final class Pair<A,B> {
	public final A first;
	public final B second;

	public Pair(A first, B second) {
		this.first	= first;
		this.second	= second;
	}
	
	public static <A,B> Pair<A,B> create(A first, B second) {
		return new Pair<A,B>(first,second);
	}
	
	public <X> Pair<X,B> changeFirst(X first) {
		return new Pair<X,B>(first, second);
	}

	public <X> Pair<A,X> changeSecond(X second) {
		return new Pair<A,X>(first, second);
	}
	
	public Pair<B,A> swap() {
		return new Pair<B,A>(second,first);
	}
	
	public <X> Pair<X,B> mapFirst(Function<? super A, X> function) {
		return changeFirst(function.apply(first));
	}
	
	public <X> Pair<A,X> mapSecond(Function<? super B, X> function) {
		return changeSecond(function.apply(second));
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("first",  first)
				.append("second", second)
				.toString();
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((first == null) ? 0 : first.hashCode());
		result = prime * result + ((second == null) ? 0 : second.hashCode());
		return result;
	}

	@SuppressWarnings("unchecked")
	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		Pair<?,?> other = (Pair) obj;
		if (first == null) {
			if (other.first != null)
				return false;
		} else if (!first.equals(other.first))
			return false;
		if (second == null) {
			if (other.second != null)
				return false;
		} else if (!second.equals(other.second))
			return false;
		return true;
	}
}
