package org.apache.lucene.search;

import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.index.TermPositions;
import org.apache.lucene.util.PriorityQueue;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.List;
/**
 * Similar to MultipleTermPositions, but allows custom boost
 * factors for each term
 * 
 * @author rainman
 * @author Anders Nielsen
 *
 */
public class MultiBoostTermPositions implements TermPositions {

	private static final class TermPositionsQueue extends PriorityQueue {
		TermPositionsQueue(List<TermPositionsBoost> termPositions) throws IOException {
			initialize(termPositions.size());

			for(TermPositionsBoost tp : termPositions){
				if (tp.next())
					put(tp);
			}
		}

		final TermPositionsBoost peek() {
			return (TermPositionsBoost) top();
		}

		public final boolean lessThan(Object a, Object b) {
			return ((TermPositions) a).doc() < ((TermPositions) b).doc();
		}
	}

	private static final class IntQueue {
		private int _arraySize = 128;
		private int _index = 0;
		private int _lastIndex = 0;
		private int[] _array = new int[_arraySize];
		private float[] _boost = new float[_arraySize];

		final void add(int i, float b) {
			if (_lastIndex == _arraySize)
				growArray();

			_array[_lastIndex] = i;
			_boost[_lastIndex++] = b;
		}

		final int next() {
			return _array[_index++];
		}
		/** valid only after calling next() */
		final float boost(){
			return _boost[_index-1];
		}

		final void sort() {
			joinsort(_array, _boost, _index, _lastIndex);
		}

		final void clear() {
			_index = 0;
			_lastIndex = 0;
		}

		final int size() {
			return (_lastIndex - _index);
		}

		private void growArray() {
			int[] newArray = new int[_arraySize * 2];
			System.arraycopy(_array, 0, newArray, 0, _arraySize);
			float[] newBoost = new float[_arraySize * 2];
			System.arraycopy(_boost,0,newBoost,0,_arraySize);
			_array = newArray;
			_boost = newBoost;
			_arraySize *= 2;
		}

		/**
		 * Sorts the specified sub-array of integers into ascending order.
		 */
		private final void joinsort(int x[], float[] y, int off, int len) {
			// Insertion sort on smallest arrays
			if (len < 7) {
				for (int i=off; i<len+off; i++)
					for (int j=i; j>off && x[j-1]>x[j]; j--)
						swap(x, y, j, j-1);
				return;
			}

			// Choose a partition element, v
			int m = off + (len >> 1);       // Small arrays, middle element
			if (len > 7) {
				int l = off;
				int n = off + len - 1;
				if (len > 40) {        // Big arrays, pseudomedian of 9
					int s = len/8;
					l = med3(x, l,     l+s, l+2*s);
					m = med3(x, m-s,   m,   m+s);
					n = med3(x, n-2*s, n-s, n);
				}
				m = med3(x, l, m, n); // Mid-size, med of 3
			}
			int v = x[m];

			// Establish Invariant: v* (<v)* (>v)* v*
			int a = off, b = a, c = off + len - 1, d = c;
			while(true) {
				while (b <= c && x[b] <= v) {
					if (x[b] == v)
						swap(x, y, a++, b);
					b++;
				}
				while (c >= b && x[c] >= v) {
					if (x[c] == v)
						swap(x, y, c, d--);
					c--;
				}
				if (b > c)
					break;
				swap(x, y, b++, c--);
			}

			// Swap partition elements back to middle
			int s, n = off + len;
			s = Math.min(a-off, b-a  );  vecswap(x, y, off, b-s, s);
			s = Math.min(d-c,   n-d-1);  vecswap(x, y, b,   n-s, s);

			// Recursively sort non-partition-elements
			if ((s = b-a) > 1)
				joinsort(x, y, off, s);
			if ((s = d-c) > 1)
				joinsort(x, y, n-s, s);
		}

		/**
		 * Swaps x[a] with x[b].
		 */
		private final void swap(int x[], float y[], int a, int b) {
			int t = x[a];
			x[a] = x[b];
			x[b] = t;
			float tt = y[a];
			y[a] = y[b];
			y[b] = tt;
		}

		/**
		 * Swaps x[a .. (a+n-1)] with x[b .. (b+n-1)].
		 */
		private final void vecswap(int x[], float y[], int a, int b, int n) {
			for (int i=0; i<n; i++, a++, b++)
				swap(x, y, a, b);
		}

		/**
		 * Returns the index of the median of the three indexed integers.
		 */
		private final int med3(int x[], int a, int b, int c) {
			return (x[a] < x[b] ?
					(x[b] < x[c] ? b : x[a] < x[c] ? c : a) :
						(x[b] > x[c] ? b : x[a] > x[c] ? c : a));
		}

	}


	private int _doc;
	private int _freq;
	private TermPositionsQueue _termPositionsQueue;
	private IntQueue _posList;

	/**
	 * Creates a new <code>MultipleTermPositions</code> instance.
	 * 
	 * @exception IOException
	 */
	public MultiBoostTermPositions(IndexReader indexReader, Term[] terms, float[] boost) throws IOException {
		List<TermPositionsBoost> termPositions = new ArrayList<TermPositionsBoost>();

		for (int i = 0; i < terms.length; i++)
			termPositions.add(new TermPositionsBoost(indexReader.termPositions(terms[i]),boost[i]));

		_termPositionsQueue = new TermPositionsQueue(termPositions);
		_posList = new IntQueue();
	}

	public final boolean next() throws IOException {
		if (_termPositionsQueue.size() == 0)
			return false;

		_posList.clear();
		_doc = _termPositionsQueue.peek().doc();

		TermPositionsBoost tp;
		do {
			tp = _termPositionsQueue.peek();

			for (int i = 0; i < tp.freq(); i++)
				_posList.add(tp.nextPosition(),tp.boost);

			if (tp.next())
				_termPositionsQueue.adjustTop();
			else {
				_termPositionsQueue.pop();
				tp.close();
			}
		} while (_termPositionsQueue.size() > 0 && _termPositionsQueue.peek().doc() == _doc);

		_posList.sort();
		_freq = _posList.size();

		return true;
	}

	public final int nextPosition() {
		return _posList.next();
	}
	
	public final float boost() {
		return _posList.boost();
	}

	public final boolean skipTo(int target) throws IOException {
		while (_termPositionsQueue.peek() != null && target > _termPositionsQueue.peek().doc()) {
			TermPositions tp = (TermPositions) _termPositionsQueue.pop();
			if (tp.skipTo(target))
				_termPositionsQueue.put(tp);
			else
				tp.close();
		}
		return next();
	}

	public final int doc() {
		return _doc;
	}

	public final int freq() {
		return _freq;
	}

	public final void close() throws IOException {
		while (_termPositionsQueue.size() > 0)
			((TermPositions) _termPositionsQueue.pop()).close();
	}

	/**
	 * Not implemented.
	 * @throws UnsupportedOperationException
	 */
	public void seek(Term arg0) throws IOException {
		throw new UnsupportedOperationException();
	}

	/**
	 * Not implemented.
	 * @throws UnsupportedOperationException
	 */
	public void seek(TermEnum termEnum) throws IOException {
		throw new UnsupportedOperationException();
	}

	/**
	 * Not implemented.
	 * @throws UnsupportedOperationException
	 */
	public int read(int[] arg0, int[] arg1) throws IOException {
		throw new UnsupportedOperationException();
	}


	/**
	 * Not implemented.
	 * @throws UnsupportedOperationException
	 */
	public int getPayloadLength() {
		throw new UnsupportedOperationException();
	}

	/**
	 * Not implemented.
	 * @throws UnsupportedOperationException
	 */
	public byte[] getPayload(byte[] data, int offset) throws IOException {
		throw new UnsupportedOperationException();
	}

	/**
	 *
	 * @return false
	 */
	// TODO: Remove warning after API has been finalized
	public boolean isPayloadAvailable() {
		return false;
	}

	/** Delegate class with a custom boost */
	private static final class TermPositionsBoost implements TermPositions {
		TermPositions pos;
		float boost;

		public TermPositionsBoost(TermPositions pos, float boost) {
			this.pos = pos;
			this.boost = boost;
		}
		
		public void close() throws IOException {
			pos.close();
		}

		public int doc() {
			return pos.doc();
		}

		public int freq() {
			return pos.freq();
		}

		public byte[] getPayload(byte[] data, int offset) throws IOException {
			return pos.getPayload(data, offset);
		}

		public int getPayloadLength() {
			return pos.getPayloadLength();
		}

		public boolean isPayloadAvailable() {
			return pos.isPayloadAvailable();
		}

		public boolean next() throws IOException {
			return pos.next();
		}

		public int nextPosition() throws IOException {
			return pos.nextPosition();
		}

		public int read(int[] docs, int[] freqs) throws IOException {
			return pos.read(docs, freqs);
		}

		public void seek(Term term) throws IOException {
			pos.seek(term);
		}

		public void seek(TermEnum termEnum) throws IOException {
			pos.seek(termEnum);
		}

		public boolean skipTo(int target) throws IOException {
			return pos.skipTo(target);
		}



	}

}

