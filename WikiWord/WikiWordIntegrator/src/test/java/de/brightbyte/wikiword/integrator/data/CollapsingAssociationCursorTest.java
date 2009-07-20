package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class CollapsingAssociationCursorTest extends TestCase {

	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}
	
	public void testNext() throws PersistenceException {
		FeatureSet a = new DefaultFeatureSet("name");
		a.put("name", "A");

		FeatureSet b = new DefaultFeatureSet("name");
		b.put("name", "B");
		
		FeatureSet x = new DefaultFeatureSet("name");
		x.put("name", "X");

		FeatureSet y = new DefaultFeatureSet("name");
		y.put("name", "Y");
		
		FeatureSet p = new DefaultFeatureSet("name");
		p.put("name", "P");

		FeatureSet q = new DefaultFeatureSet("name");
		q.put("name", "Q");
		
		//--------------------------------------
		ArrayList<Association> source = new ArrayList<Association>();
		source.add(new Association(a, x, p));
		source.add(new Association(a, y, p));
		source.add(new Association(a, y, q));
		source.add(new Association(b, y, q));
		source.add(new Association(a, y, q));
		
		ArrayList<Association> exp = new ArrayList<Association>();
		exp.add(new Association(a, x, p));
		exp.add(Association.merge(new Association(a, y, p), new Association(a, y, q)));
		exp.add(new Association(b, y, q));
		exp.add(new Association(a, y, q));

		DataCursor<Association> sourceCursor = new IteratorCursor<Association>(source.iterator());
		DataCursor<Association> cursor = new AggregatingAssociationCursor(sourceCursor, "name", "name");
		
		assertEquals(exp, slurp(cursor));
	}

}
