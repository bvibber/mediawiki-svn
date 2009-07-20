package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.List;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class FeatureAssemblingCursorTest extends TestCase {
	
	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}

	public void testNext() throws PersistenceException {
		DefaultRecord a = new DefaultRecord();
		a.add("id", 1);
		a.add("property", "name");
		a.add("value", "A");
		a.add("value", "a");
		a.add("xyzzy", "bla");

		DefaultRecord b = new DefaultRecord();
		b.add("id", 1);
		b.add("property", "foo");
		b.add("value", "X");
		b.add("value", "Y");
		
		DefaultRecord x = new DefaultRecord();
		x.add("id", 2);
		x.add("property", "name");
		x.add("value", "Foo");

		DefaultRecord y = new DefaultRecord();
		y.add("id", 2);
		y.add("property", "alias");
		y.add("value", "Foo");

		//--------------------------------------
		
		DefaultFeatureSet one = new DefaultFeatureSet();
		one.addFeature("id", 1, null);
		one.addFeature("name", "A", null);
		one.addFeature("name", "a", null);
		one.addFeature("foo", "X", null);
		one.addFeature("foo", "Y", null);

		DefaultFeatureSet two = new DefaultFeatureSet();
		two.addFeature("id", 2, null);
		two.addFeature("name", "Foo", null);
		two.addFeature("alias", "Foo", null);
		
		List<FeatureSet> exp= Arrays.asList(new FeatureSet[] {one, two});
		List<Record> source= Arrays.asList(new Record[] {a, b, x, y});
		
		DataCursor<Record> sourceCursor = new IteratorCursor<Record>(source.iterator());
		DataCursor<FeatureSet> cursor = FeatureAssemblingCursor.newForRecordCursor(sourceCursor, "id", "property", "value");
		
		Collection<FeatureSet> act = slurp(cursor);
		assertEquals(exp, act);
	}
	
}
