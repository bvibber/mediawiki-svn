package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingFeatureSetCursorTest extends TestCase {

	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}
	
	public void testNext() throws PersistenceException {
		FeatureSet a = new DefaultFeatureSet("id");
		a.put("id", 1);
		a.put("foo", "A");

		FeatureSet b = new DefaultFeatureSet("id");
		b.put("id", 1);
		b.put("foo", "B");
		
		FeatureSet x = new DefaultFeatureSet("id");
		x.put("id", 2);
		x.put("foo", "X");

		FeatureSet y = new DefaultFeatureSet("id");
		y.put("id", 2);
		y.put("foo", "Y");
		
		FeatureSet p = new DefaultFeatureSet("id");
		p.put("id", 3);
		p.put("foo", "P");

		FeatureSet q = new DefaultFeatureSet("id");
		q.put("id", 3);
		q.put("foo", "Q");
		
		//--------------------------------------
		FeatureSet ab = new DefaultFeatureSet("id");
		ab.put("id", 1);
		ab.put("id", 1);
		ab.put("foo", "A");
		ab.put("foo", "B");
		
		FeatureSet xy = new DefaultFeatureSet("id");
		xy.put("id", 2);
		xy.put("id", 2);
		xy.put("foo", "X");
		xy.put("foo", "Y");
		
		FeatureSet pq = new DefaultFeatureSet("id");
		pq.put("id", 3);
		pq.put("id", 3);
		pq.put("foo", "P");
		pq.put("foo", "Q");
		
		//--------------------------------------
		ArrayList<FeatureSet> source = new ArrayList<FeatureSet>();
		source.add(a);
		source.add(b);
		source.add(x);
		source.add(y);
		source.add(p);
		source.add(q);
		
		ArrayList<FeatureSet> exp = new ArrayList<FeatureSet>();
		exp.add(ab);
		exp.add(xy);
		exp.add(pq);

		DataCursor<FeatureSet> sourceCursor = new IteratorCursor<FeatureSet>(source.iterator());
		DataCursor<FeatureSet> cursor = new FeatureBuilderCursor(sourceCursor, "id");
		
		assertEquals(exp, slurp(cursor));
	}

}
