package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Collection;

import junit.framework.TestCase;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;

public class AggregatingAssociationCursorTest extends TestCase {

	private static <T> Collection<T> slurp(DataCursor<T> cursor) throws PersistenceException {
		ArrayList<T> list = new ArrayList<T>();
		T obj;
		while ((obj = cursor.next()) != null) list.add(obj);
		return list;
	}
	
	public void testNext() throws PersistenceException {
		Record ar = new DefaultRecord();
		ar.add("authority", "TEST");
		ar.add("id", 11);
		ar.add("name", "A");
		ForeignEntityRecord a = new DefaultForeignEntityRecord(ar, "authority", "id", "name");

		Record br = new DefaultRecord();
		br.add("authority", "TEST");
		br.add("id", 12);
		br.add("name", "B");
		ForeignEntityRecord b = new DefaultForeignEntityRecord(br, "authority", "id", "name");
		
		Record xr = new DefaultRecord();
		xr.add("concept", 22);
		xr.add("label", "X");
		ConceptEntityRecord x = new DefaultConceptEntityRecord(xr, "concept", "label");

		Record yr = new DefaultRecord();
		yr.add("concept", 23);
		yr.add("label", "Y");
		ConceptEntityRecord y = new DefaultConceptEntityRecord(yr, "concept", "label");
		
		Record p = new DefaultRecord();
		p.add("foo", "P");

		Record q = new DefaultRecord();
		q.add("foo", "Q");

		Record pq = new DefaultRecord();
		pq.addAll(p);
		pq.addAll(q);
		
		//--------------------------------------
		ArrayList<Association> source = new ArrayList<Association>();
		source.add(new GenericAssociation(a, x, p));
		source.add(new GenericAssociation(a, y, p));
		source.add(new GenericAssociation(a, y, q));
		source.add(new GenericAssociation(b, y, q));
		source.add(new GenericAssociation(a, y, q));
		
		ArrayList<Association> exp = new ArrayList<Association>();
		exp.add(new GenericAssociation(a, x, p));
		exp.add(new GenericAssociation(a, y, pq));
		exp.add(new GenericAssociation(b, y, q));
		exp.add(new GenericAssociation(a, y, q));

		DataCursor<Association> sourceCursor = new IteratorCursor<Association>(source.iterator());
		DataCursor<Association> cursor = new AggregatingAssociationCursor(sourceCursor);
		
		assertEquals(exp, slurp(cursor));
	}

}
