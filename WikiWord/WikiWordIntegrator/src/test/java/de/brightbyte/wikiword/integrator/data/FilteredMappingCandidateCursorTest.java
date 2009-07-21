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
		DefaultForeignEntityRecord a = new DefaultForeignEntityRecord("authority", "name", "name");
		a.add("auhtority", "ACME");
		a.add("name", "A");

		DefaultForeignEntityRecord b = new DefaultForeignEntityRecord("authority", "name", "name");
		b.add("auhtority", "ACME");
		b.add("name", "B");
		
		DefaultConceptEntityRecord x = new DefaultConceptEntityRecord("id", "name");
		x.add("id", 10);
		x.add("value", 3);
		x.add("value", 3);

		DefaultConceptEntityRecord y = new DefaultConceptEntityRecord("id", "name");
		y.add("id", 11);
		y.add("value", 4);
		y.add("value", 1);
		
		DefaultConceptEntityRecord p = new DefaultConceptEntityRecord("id", "name");
		p.add("id", 20);
		p.add("concept", "five");
		p.add("value", 7);

		DefaultConceptEntityRecord q = new DefaultConceptEntityRecord("id", "name");
		q.add("id", 21);
		q.add("value", 3);
		
		//--------------------------------------
		ArrayList<MappingCandidates> source = new ArrayList<MappingCandidates>();
		source.add(new MappingCandidates(a, x, y));
		source.add(new MappingCandidates(b, p, q));
		
		ArrayList<MappingCandidates> exp = new ArrayList<MappingCandidates>();
		exp.add(new MappingCandidates(a, y));
		exp.add(new MappingCandidates(b, p));
		
		DataCursor<MappingCandidates> sourceCursor = new IteratorCursor<MappingCandidates>(source.iterator());
		DataCursor<MappingCandidates> cursor = new FilteredMappingCandidateCursor(sourceCursor, "value");
		
		Collection<MappingCandidates> act = slurp(cursor);
		assertEquals(exp, act);
	}
	
}
