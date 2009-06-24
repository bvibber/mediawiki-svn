package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class FilteredMappingCandidateCursorTest extends TestCase {
	
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
		x.put("id", 10);
		x.put("value", 3);
		x.put("value", 3);

		FeatureSet y = new DefaultFeatureSet("name");
		y.put("id", 11);
		y.put("value", 4);
		y.put("value", 1);
		
		FeatureSet p = new DefaultFeatureSet("foo");
		p.put("id", 20);
		p.put("concept", "five");
		p.put("value", 3);

		FeatureSet q = new DefaultFeatureSet("foo");
		q.put("id", 21);
		q.put("value", 7);
		
		//--------------------------------------
		ArrayList<MappingCandidates> source = new ArrayList<MappingCandidates>();
		source.add(new MappingCandidates(a, x, y));
		source.add(new MappingCandidates(b, p, q));
		
		ArrayList<MappingCandidates> exp = new ArrayList<MappingCandidates>();
		exp.add(new MappingCandidates(a, x));
		exp.add(new MappingCandidates(b, q));
		
		DataCursor<MappingCandidates> sourceCursor = new IteratorCursor<MappingCandidates>(source.iterator());
		DataCursor<MappingCandidates> cursor = new FilteredMappingCandidateCursor(sourceCursor, "value");
		
		Collection<MappingCandidates> act = slurp(cursor);
		assertEquals(exp, act);
	}
	
}
