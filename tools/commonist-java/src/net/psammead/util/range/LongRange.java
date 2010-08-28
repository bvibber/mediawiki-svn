package net.psammead.util.range;

import java.util.Iterator;

import net.psammead.util.ToString;
import net.psammead.util.annotation.ImmutableValue;

@ImmutableValue 
public final class LongRange implements Iterable<Long> {
	public final long		start;
	public final long		end;
	public final long		size;
	public final boolean	forward;

	public LongRange(long start, long end) {
		this.start	= start;
		this.end	= end;
		forward	= end>=start;
		size	= end-start;
	}
	
	public boolean contains(long value) {
		return forward
			? value >= start && value < end
			: value <= start && value > end;
	}
	
	public Iterator<Long> iterator() {
		return new RangeIterator();
	}

	private final class RangeIterator implements Iterator<Long> {
		private long	value;

		public RangeIterator() {
			value	= start;
		}
		
		public boolean hasNext() {
			return forward ? value < end : value > end;
		}

		public Long next() {
			final long	out	= value;
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
		result = prime * result + (int)(end ^ (end >>> 32));
		result = prime * result + (forward ? 1231 : 1237);
		result = prime * result + (int)(size ^ (size >>> 32));
		result = prime * result + (int)(start ^ (start >>> 32));
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj) return true;
		if (obj == null) return false;
		if (getClass() != obj.getClass()) return false;
		LongRange other = (LongRange)obj;
		if (end != other.end) return false;
		if (forward != other.forward) return false;
		if (size != other.size) return false;
		if (start != other.start) return false;
		return true;
	}
}
