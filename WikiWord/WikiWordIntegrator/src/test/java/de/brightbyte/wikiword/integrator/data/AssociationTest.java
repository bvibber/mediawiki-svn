package de.brightbyte.wikiword.integrator.data;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.data.Accumulator;
import de.brightbyte.data.Functors;
import junit.framework.TestCase;

public class AssociationTest extends TestCase {

	public void testAggregate() {
		Map<String, Accumulator<?, ?>> accumulators = new HashMap<String, Accumulator<?, ?>>();
		accumulators.put("terms", Functors.concat2("|"));
		accumulators.put("weight", Functors.Integer.sum2);
		accumulators.put("score", Functors.Double.max2);

		DefaultRecord ar = new DefaultRecord();
		
		ar.add("id", "X5");
		ar.add("name", "FIVE");
		ar.add("concept", 5);
		ar.add("label", "five");
		
		ar.add("terms", "A");
		ar.add("terms", "A", true);
		ar.add("terms", "a");

		ar.add("weight", 2);
		ar.add("score", 2.3);
		
		DefaultAssociation a = new DefaultAssociation(ar, "authority", "id", "name", "concept", "label");

		DefaultRecord br = new DefaultRecord();
		br.add("terms", "B");

		br.add("weight", 3);
		br.add("score", 1.7);
		
		DefaultAssociation b = new DefaultAssociation(br, "authority", "id", "name", "concept", "label");
		
		DefaultRecord cr = new DefaultRecord();
		
		cr.add("terms", "C");

		cr.add("weight", 1);
		cr.add("score", 0.9);
		
		DefaultAssociation c = new DefaultAssociation(cr, "authority", "id", "name", "concept", "label");

		//---------------------------------------------------
		
		DefaultRecord abr = new DefaultRecord();
		
		abr.add("id", "X5");
		abr.add("name", "FIVE");
		abr.add("concept", 5);
		abr.add("label", "five");

		abr.add("terms", "A|A|a|B");

		abr.add("weight", 5);
		abr.add("score", 2.3);
		
		DefaultAssociation ab = new DefaultAssociation(abr, "authority", "id", "name", "concept", "label");
		
		a.aggregate(b, accumulators);
		assertEquals(ab, a);

		//---------------------------------------------------

		DefaultRecord abcr = new DefaultRecord();
		
		abcr.add("id", "X5");
		abcr.add("name", "FIVE");
		abcr.add("concept", 5);
		abcr.add("label", "five");

		abcr.add("terms", "A|A|a|B|C");

		abcr.add("weight", 6);
		abcr.add("score", 2.3);
		
		DefaultAssociation abc = new DefaultAssociation(abcr, "authority", "id", "name", "concept", "label");
		
		a.aggregate(c, accumulators);
		assertEquals(abc, a);
	}

}
