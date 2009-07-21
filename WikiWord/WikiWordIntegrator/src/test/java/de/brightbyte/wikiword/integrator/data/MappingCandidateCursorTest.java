package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class MappingCandidateCursorTest extends TestCase {
	
	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}

	public void testNext() throws PersistenceException {
		DefaultForeignEntityRecord a = new DefaultForeignEntityRecord("authority", "name", "name");
		a.add("authority", "ACME");
		a.add("name", "A");

		DefaultForeignEntityRecord b = new DefaultForeignEntityRecord("authority", "name", "name");
		b.add("authority", "ACME");
		b.add("name", "B");
		
		DefaultConceptEntityRecord x = new DefaultConceptEntityRecord("id", "name");
		x.add("id", "11");
		x.add("authority", "ACME");
		x.add("name", "X");

		DefaultConceptEntityRecord y = new DefaultConceptEntityRecord("id", "name");
		y.add("id", "12");
		y.add("authority", "ACME");
		y.add("name", "Y");
		
		DefaultRecord p = new DefaultRecord();
		p.add("foo", "P");

		DefaultRecord q = new DefaultRecord();
		q.add("foo", "Q");
		
		//--------------------------------------
		ArrayList<Association> source = new ArrayList<Association>();
		source.add(new GenericAssociation(a, x, p));
		source.add(new GenericAssociation(a, y, p));
		source.add(new GenericAssociation(a, y, q));
		source.add(new GenericAssociation(b, y, q));
		source.add(new GenericAssociation(a, y, q));
		
		ArrayList<MappingCandidates> exp = new ArrayList<MappingCandidates>();
		exp.add(new MappingCandidates(a, new DefaultConceptEntityRecord(new DefaultRecord(x, p), "id", "name"), 
																		new DefaultConceptEntityRecord(new DefaultRecord(y, p), "id", "name"),
																		new DefaultConceptEntityRecord(new DefaultRecord(y, q), "id", "name")));
		exp.add(new MappingCandidates(b, new DefaultConceptEntityRecord(new DefaultRecord(y, q), "id", "name")));
		exp.add(new MappingCandidates(a, new DefaultConceptEntityRecord(new DefaultRecord(y, q), "id", "name")));

		DataCursor<Association> sourceCursor = new IteratorCursor<Association>(source.iterator());
		DataCursor<MappingCandidates> cursor = new MappingCandidateCursor(sourceCursor);
		
		Collection<MappingCandidates> act = slurp(cursor);
		assertEquals(exp, act);
	}
	
}
