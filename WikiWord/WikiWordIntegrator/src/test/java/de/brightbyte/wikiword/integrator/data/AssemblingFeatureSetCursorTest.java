package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.List;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class AssemblingFeatureSetCursorTest extends TestCase {
	
	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}

	public void testNext() throws PersistenceException {
		FeatureSet a = new DefaultFeatureSet("name");
		a.put("id", 1);
		a.put("property", "name");
		a.put("value", "A");
		a.put("value", "a");
		a.put("xyzzy", "bla");

		FeatureSet b = new DefaultFeatureSet("name");
		b.put("id", 1);
		b.put("property", "foo");
		b.put("value", "X");
		b.put("value", "Y");
		
		FeatureSet x = new DefaultFeatureSet("name");
		x.put("id", 2);
		x.put("property", "name");
		x.put("property", "alias");
		x.put("value", "Foo");

		//--------------------------------------
		
		FeatureSet one = new DefaultFeatureSet();
		one.put("id", 1);
		one.put("name", "A");
		one.put("name", "a");
		one.put("foo", "X");
		one.put("foo", "Y");

		FeatureSet two = new DefaultFeatureSet();
		two.put("id", 2);
		two.put("name", "Foo");
		two.put("alias", "Foo");
		
		List<FeatureSet> exp= Arrays.asList(new FeatureSet[] {one, two});
		List<FeatureSet> source= Arrays.asList(new FeatureSet[] {a, b, x});
		
		DataCursor<FeatureSet> sourceCursor = new IteratorCursor<FeatureSet>(source.iterator());
		DataCursor<FeatureSet> cursor = new FeatureAssemblingCursor(sourceCursor, "id", "property", "value");
		
		assertEquals(exp, slurp(cursor));
	}
	
}
