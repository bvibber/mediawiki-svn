package org.wikimedia.lsearch.beans;

import org.wikimedia.lsearch.test.WikiTestCase;

public class TitleTest extends WikiTestCase {
	
	public void testStatic(){
		assertEquals(0,Title.namespaceAsInt("0:Title"));
		assertEquals(12,Title.namespaceAsInt("12:Title"));
	}

}
