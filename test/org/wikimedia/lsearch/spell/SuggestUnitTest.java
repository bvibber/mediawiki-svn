package org.wikimedia.lsearch.spell;

import java.io.IOException;

import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.test.WikiTestCase;

public class SuggestUnitTest extends WikiTestCase {
	
	public void testMakeNamespaces() throws IOException {
		IndexId iid = IndexId.get("entest");
		Suggest sug = new Suggest(iid);
		assertNull(sug.makeNamespaces(new NamespaceFilter("0")));
		assertNull(sug.makeNamespaces(new NamespaceFilter("0,2,4,14")));
		assertNotNull(sug.makeNamespaces(new NamespaceFilter("0,2,4,100")));
		assertEquals("[0, 100, 2, 4]",sug.makeNamespaces(new NamespaceFilter("0,2,4,100")).namespaces.toString());
	}

}
