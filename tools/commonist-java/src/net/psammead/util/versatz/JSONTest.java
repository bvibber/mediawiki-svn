package net.psammead.util.versatz;

import java.io.File;
import java.io.IOException;

import net.psammead.util.IOUtil;
import net.psammead.util.json.JSONDecodeException;
import net.psammead.util.json.JSONDecoder;

public class JSONTest {
//	http://de.wikipedia.org/w/api.php?action=query&list=recentchanges&rcprop=user|comment|flags|timestamp|title|ids|sizes&format=json
		
	public static void main(String[] args) throws IOException {
		testStrings();
		
		final String text = IOUtil.readStringFromFile(new File("/home/daniel/Project/current/util/versatz/rc_api.php"), "ISO-8859-1");
		testString(text);
	}

	private static void testStrings() {
		testString("1");
		testString("-1");
		testString("1.0");
		testString("-1.0");
		testString("1.0e2");
		testString("1.0e+2");
		testString("1.0e-2");
		testString("1.0E2");
		testString("\"hallo\"");
		testString("\"h\\u00ffllo\"");
		testString("\"h\\/\"");
		testString("\"ha\\nlo\"");
		testString("[]");
		testString("[1]");
		testString("[1,2]");
		testString("[1,2,3]");
		testString("{}");
		testString("{\"1\":2}");
		testString("{\"1\":2,\"2\":3}");
		testString("[{\"1\":1},{\"2\":2},{\"3\":3}]");
		testString("{[1]:[2],[3]:[4],[5]:[6]}");
		testString("\"h\\u00fgllo\"");
		testString("/*xxx*/1");
		testString("1/*xxx*/");
		testString("/*xxx*/1/*xxx*/");
		testString("//test\n1");
		testString("1\n//test");
		testString("//test");
		testString("{1}");
	}
	
	private static void testString(String text) {
		try {
			System.err.println(text);
			System.err.println("\t" + JSONDecoder.decode(text));
		}
		catch (JSONDecodeException e) {
			System.err.println("\texpected:\t" + e.expectation);
			System.err.println("\tat offset:\t" + e.offset);
			System.err.println("\tlooking at:\t" + e.lookingAt());
		}
		System.err.println("");
	}
	
//	private static class Deco {
//		public Deco() {}
//		
//		public Deco rename(String jsonName, String javaName) {
//			return this;
//		}
//		public Deco mapping(String jsonName, Deco subMapper) {
//			return this;
//		}
//		
//		public Object map(Object json) {
//			return null;
//		}
//	}
//	
//	private RecentChangesResult readQuery(Object jsonQuery) {
//		Deco root = new Deco(RecentChangesResult.class)
//		.rename("query-continue", "queryContinue")
//		.mapping("query-continue",
//			new Deco()
//			.rename("recentchanges", "recentChanges")
//			.mapping("recentchanges",
//				new Deco()
//				.rename("rccontinue", "continueKey")
//			)
//		);
//		return root.map(jsonQuery);
//	}
}
