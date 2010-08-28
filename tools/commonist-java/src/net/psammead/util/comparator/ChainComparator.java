package net.psammead.util.comparator;

import java.util.Comparator;

import net.psammead.util.ToString;

/** uses a low {@link Comparator} if the high {@link Comparator} indicates equality */
public final class ChainComparator<T> implements Comparator<T> {
	private final Comparator<? super T> high;
	private final Comparator<? super T> low;
	
	public ChainComparator(Comparator<? super T> high, Comparator<? super T> low) {
		this.high	= high;
		this.low	= low;
	}
	
	public int compare(T o1, T o2) {
		int	highVal	= high.compare(o1, o2);
		if (highVal != 0)	return highVal;
		else				return low.compare(o1, o2);
	}

	@Override
	public String toString() {
		return new ToString(this)
				.append("high", high)
				.append("low", low)
				.toString();
	}
	
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + ((high == null) ? 0 : high.hashCode());
		result = prime * result + ((low == null) ? 0 : low.hashCode());
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
		ChainComparator other = (ChainComparator) obj;
		if (high == null) {
			if (other.high != null)
				return false;
		} else if (!high.equals(other.high))
			return false;
		if (low == null) {
			if (other.low != null)
				return false;
		} else if (!low.equals(other.low))
			return false;
		return true;
	}
}