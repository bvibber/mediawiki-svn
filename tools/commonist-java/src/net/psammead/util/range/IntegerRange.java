package net.psammead.util.range;

import java.util.Iterator;

import net.psammead.util.ToString;
import net.psammead.util.annotation.ImmutableValue;

@ImmutableValue 
public final class IntegerRange implements Iterable<Integer> {
	public final int		start;
	public final int		end;
	public final int		size;
	public final boolean	forward;

	public IntegerRange(int start, int end) {
		this.start	= start;
		this.end	= end;
		size	= end-start;
		forward	= end>=start;
	}
	
	public boolean contains(int value) {
		return forward
			? value >= start && value < end
			: value <= start && value > end;
	}
	
	public Iterator<Integer> iterator() {
		return new IntegerRangeIterator();
	}

	private final class IntegerRangeIterator implements Iterator<Integer> {
		private int	value;

		public IntegerRangeIterator() {
			value	= start;
		}
		
		public boolean hasNext() {
			return forward ? value < end : value > end;
		}

		public Integer next() {
			final int	out	= value;
			value	+= forward ? +1 : -1;
			return out;
		}

		public void remove() {
			throw new UnsupportedOperationException();
		}
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("start", start)
				.append("end", end)
				.toString();
	}
	@Override
	public int hashCode() {
		final int prime = 31;
		int result = 1;
		result = prime * result + end;
		result = prime * result + start;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj) return true;
		if (obj == null) return false;
		if (getClass() != obj.getClass()) return false;
		IntegerRange other = (IntegerRange)obj;
		if (end != other.end) return false;
		if (start != other.start) return false;
		return true;
	}
}
