package de.brightbyte.wikiword.integrator.data;

import junit.framework.TestCase;

public class FeatureSetValueSplitterTest extends TestCase {

	public void testApply() {
		FeatureSet in = new DefaultFeatureSet("name");
		in.put("name", "A");
		in.put("name", "");
		in.put("name", "B,C,");
		in.put("name", "D; E ,F");
		in.put("name", ";G");
		in.put("foo", "A,B,C");

		FeatureSet exp = new DefaultFeatureSet("name");
		exp.put("name", "A");
		exp.put("name", "");
		exp.put("name", "B");
		exp.put("name", "C");
		exp.put("name", "D");
		exp.put("name", "E");
		exp.put("name", "F");
		exp.put("name", "");
		exp.put("name", "G");
		exp.put("foo", "A,B,C");
		
		FeatureSetValueSplitter splitter = new FeatureSetValueSplitter("name", "\\s*[,;]\\s*", 0); 
		assertEquals(exp, splitter.apply(in));

		in = new DefaultFeatureSet("name");
		in.put("name", "A");
		in.put("name", "");
		in.put("foo", "A,B,C");

		exp = new DefaultFeatureSet("name");
		exp.put("name", "A");
		exp.put("name", "");
		exp.put("foo", "A,B,C");
		
		assertEquals(exp, splitter.apply(in));
	}

}
