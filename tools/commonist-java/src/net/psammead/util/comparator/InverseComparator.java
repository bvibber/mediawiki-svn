package net.psammead.util.comparator;

import java.util.Comparator;

import net.psammead.util.ToString;

/** inverts another {@link Comparator} */
public final class InverseComparator<T> implements Comparator<T> {
	private final Comparator<T> delegate;
	
	public InverseComparator(Comparator<T> delegate) {
		this.delegate	= delegate;
	}
	
	public int compare(T o1, T o2) {
		return -delegate.compare(o1, o2);
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("delegate", delegate)
				.toString();
	}

	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result
				+ ((delegate == null) ? 0 : delegate.hashCode());
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
		InverseComparator other = (InverseComparator)obj;
		if (delegate == null) {
			if (other.delegate != null)
				return false;
		} else if (!delegate.equals(other.delegate))
			return false;
		return true;
	}
}
