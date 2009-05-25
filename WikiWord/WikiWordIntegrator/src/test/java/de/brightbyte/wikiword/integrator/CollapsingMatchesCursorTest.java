package de.brightbyte.wikiword.integrator;

import java.util.ArrayList;
import java.util.Collection;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.cursor.IteratorCursor;
import de.brightbyte.util.PersistenceException;
import junit.framework.TestCase;

public class CollapsingMatchesCursorTest extends TestCase {
	
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
		
		FeatureSet p = new DefaultFeatureSet("foo");
		p.put("foo", "P");

		FeatureSet q = new DefaultFeatureSet("foo");
		q.put("foo", "Q");
		
		//--------------------------------------
		ArrayList<Association> source = new ArrayList<Association>();
		source.add(new Association(a, x, p));
		source.add(new Association(a, y, p));
		source.add(new Association(a, y, q));
		source.add(new Association(b, y, q));
		source.add(new Association(a, y, q));
		
		ArrayList<MappingCandidates> exp = new ArrayList<MappingCandidates>();
		exp.add(new MappingCandidates(FeatureSets.merge(a, a), FeatureSets.merge(x, p), FeatureSets.merge(y, y, p, q)));
		exp.add(new MappingCandidates(b, FeatureSets.merge(y, q)));
		exp.add(new MappingCandidates(a, FeatureSets.merge(y, q)));

		DataCursor<Association> sourceCursor = new IteratorCursor<Association>(source.iterator());
		DataCursor<MappingCandidates> cursor = new CollapsingMatchesCursor(sourceCursor, "name", "name");
		
		assertEquals(exp, slurp(cursor));
	}
	
}
