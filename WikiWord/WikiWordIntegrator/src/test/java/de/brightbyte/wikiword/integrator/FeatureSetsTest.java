package de.brightbyte.wikiword.integrator;

import java.util.ArrayList;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.MapLabeledVector;

import junit.framework.TestCase;

public class FeatureSetsTest extends TestCase {

	public void testMerge() {
		FeatureSet a = new DefaultFeatureSet("name");
		a.put("name", "A");

		FeatureSet b = new DefaultFeatureSet("name");
		b.put("name", "B");
		b.put("x", 23);
		
		FeatureSet c = new DefaultFeatureSet("name");
		c.put("name", "C");
		
		//---------------------------------------------------
		
		FeatureSet ab = new DefaultFeatureSet("name");
		ab.put("name", "A");
		ab.put("name", "B");
		ab.put("x", 23);
		
		assertEquals(ab, FeatureSets.merge(a, b));

		//---------------------------------------------------

		FeatureSet abc = new DefaultFeatureSet("name");
		abc.put("name", "A");
		abc.put("name", "B");
		abc.put("x", 23);
		abc.put("name", "C");
		
		assertEquals(abc, FeatureSets.merge(a, b, c));
	}

	public void testHistogram() {
		ArrayList<String> list = new ArrayList<String>();
		list.add("x");
		list.add("y");
		list.add("z");
		list.add("x");
		list.add("z");
		list.add("z");
		
		LabeledVector<String> v = FeatureSets.histogram(list);
		LabeledVector<String> w = new MapLabeledVector<String>();
		w.set("x", 2);
		w.set("y", 1);
		w.set("z", 3);
		
		assertEquals(w, v);
	}

	public void testCount() {
		ArrayList<String> list = new ArrayList<String>();
		list.add("x");
		list.add("y");
		list.add("z");
		list.add("x");
		list.add("z");
		list.add("z");
		
		assertEquals(2, FeatureSets.count(list, "x"));
		assertEquals(1, FeatureSets.count(list, "y"));
		assertEquals(3, FeatureSets.count(list, "z"));
	}

}
