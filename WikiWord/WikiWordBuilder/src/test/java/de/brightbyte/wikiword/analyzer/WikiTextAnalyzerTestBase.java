package de.brightbyte.wikiword.analyzer;

import java.beans.BeanInfo;
import java.beans.IntrospectionException;
import java.beans.Introspector;
import java.beans.PropertyDescriptor;
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.HashMap;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.MissingResourceException;
import java.util.Set;

import junit.framework.TestCase;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

/**
 * Abstract base class for unit tests for WikiTextAnalyzer.
 */
public abstract class WikiTextAnalyzerTestBase extends TestCase {
	
	protected boolean titleCase;
	protected NamespaceSet namespaces;
	protected Corpus corpus;
	protected WikiTextAnalyzer analyzer;
	protected TweakSet tweaks;
	
	public WikiTextAnalyzerTestBase(String wikiName) {
		tweaks = new TweakSet();
		corpus = Corpus.forName("TEST", wikiName, tweaks);

		//site.Base = "http://"+corpus.getDomain()+"/wiki/";
		//site.Sitename = corpus.getFamily();
		
		titleCase = true;
		namespaces = corpus.getNamespaces(); 
	}

	@Override
	public void setUp() throws Exception {				
		analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(corpus, tweaks);
		analyzer.initialize(namespaces, titleCase);
	}

	public void tareDown() {
		//noop
	}

	protected void assertTestCase(String title, String property, Object value) throws Throwable {
		WikiPage page = makeTestPage(title);
		assertTestCase(page, property, value);
	}
	
	protected void assertTestCase(String title, Map<String, Object> values) throws Throwable {
		WikiPage page = makeTestPage(title);
		assertTestCase(page, values);
	}
	
	public static String loadTestPage(String title, Class ctx, Corpus corpus) throws IOException {
		String u = "de/brightbyte/wikiword/wikis/test_"+corpus.getClassSuffix()+"_"+title+".text";
		ClassLoader cl = ctx.getClassLoader();
		InputStream in = cl.getResourceAsStream(u);
		if (in == null) throw new MissingResourceException(u, ctx.getName(), title);
		
		InputStreamReader rd = new  InputStreamReader(in, "UTF-8");
		StringBuilder text = new StringBuilder();
		char[] buff = new char[1024];
		while (true) {
			int c = rd.read(buff);
			if (c<0) break;
			if (c==0) continue;
			
			text.append(buff, 0, c);
		}
		
		rd.close();
		in.close();
		
		return text.toString();
	}
	
	protected WikiPage makeTestPage(String title) throws IOException {		
		String text = loadTestPage(title, getClass(), corpus);
		return makeTestPage(0, title, text);
	}
	
	protected WikiPage makeTestPage(int namespace, String title, String text) {
		WikiPage page = analyzer.makePage(namespace, title, text, false);
		return page;
	}
	
	protected void assertTestCase(int namespace, String title, String text, Map<String, Object> values) throws Throwable {
		WikiPage page = makeTestPage(namespace, title, text);
		assertTestCase(page, values);
	}
	
	protected void assertTestCase(WikiPage page, String property, Object expected) throws Throwable {
		BeanInfo info = Introspector.getBeanInfo(page.getClass());
		PropertyDescriptor p = null;
		for (PropertyDescriptor prop: info.getPropertyDescriptors()) {
			if (prop.getName().equals(property)) {
				p = prop;
				break;
			}
		}
		
		if (p==null) throw new IntrospectionException("unknown property: "+property);
		assertTestCase(page, p, expected);
	}
	
	protected void assertTestCase(WikiPage page, PropertyDescriptor p, Object expected) throws Throwable {
		Object actual = getPropertyValue(page, p);
		
		if (expected instanceof String && !(actual instanceof String) && actual instanceof CharSequence) actual = actual.toString();
		
		if (expected instanceof String && actual instanceof String) assertEquals(p.getName(), (String)expected, (String)actual);
		else if (expected instanceof List && actual instanceof List) assertEquals(p.getName(), (List)expected, (List)actual);
		else if (expected instanceof Map && actual instanceof Map) assertEquals(p.getName(), (Map)expected, (Map)actual);
		else assertEquals(p.getName(), expected, actual);
	}
	
	protected void assertTestCase(WikiPage page, Map<String, Object> values) throws Throwable {
		BeanInfo info = Introspector.getBeanInfo(page.getClass());
		Map<String, PropertyDescriptor> props = new HashMap<String, PropertyDescriptor>();
		for (PropertyDescriptor p: info.getPropertyDescriptors()) {
			props.put(p.getName(), p);
		}
		
		for (Map.Entry<String, Object> e : values.entrySet()) {
			PropertyDescriptor p = props.get(e.getKey());
			if (p==null) throw new IntrospectionException("unknown property: "+e.getKey());
				
			Object expected = e.getValue();
			assertTestCase(page, p, expected);
		}
	}

	protected Object getPropertyValue(Object obj, PropertyDescriptor prop) throws Throwable {
		Method getter = prop.getReadMethod();
		if (getter == null) throw new IntrospectionException("not readable: "+prop.getName());
		
		try {
			return getter.invoke(obj, (Object[])null);
		} catch (IllegalArgumentException e) {
			throw (IntrospectionException)new IntrospectionException("failed to invoke "+getter.getName()).initCause(e);
		} catch (IllegalAccessException e) {
			throw (IntrospectionException)new IntrospectionException("failed to invoke "+getter.getName()).initCause(e);
		} catch (InvocationTargetException e) {
			throw e.getTargetException();
		}
	}

	protected void assertEquals(List expected, List actual) {
		assertEquals("mismatch", expected, actual);
	}
	
	protected void assertEquals(String message, List expected, List actual) {
		if (expected.size()!=actual.size()) fail(message+" lists have different size, expected "+expected.size()+", actual "+actual.size()+";\nexpected: "+expected+",\nactual: "+actual);
		
		int c = expected.size();
		for (int i = 0; i<c; i++) {
			Object e = expected.get(i);
			Object a = actual.get(i);
			
			if (e instanceof Map && a instanceof Map) assertEquals(message+"; position "+i, (Map)e, (Map)a);
			else if (e instanceof List && a instanceof List) assertEquals(message+"; position "+i, (Map)e, (Map)a);
			else assertEquals(message+"; position "+i, e, a);
		}
	}

	protected void assertEquals(Map expected, Map actual) {
		assertEquals("mismatch", expected, actual);
	}
	
	@SuppressWarnings("unchecked")
	protected void assertEquals(String message, Map expected, Map actual) {
		Set exp = expected.keySet();
		Set act = actual.keySet();
		
		if (!exp.equals(act)) {
			Set extra = new HashSet();
			extra.addAll(act);
			extra.removeAll(exp);

			Set missing = new HashSet();
			missing.addAll(exp);
			missing.removeAll(act);
			
			fail(message+" map keys mismatch;\n\nmissing: "+missing+",\nextra: "+extra+";\nexpected: "+expected+",\nactual: "+actual);
		}
		
		for (Object k : exp) {
			Object e = expected.get(k);
			Object a = actual.get(k);
			
			if (e instanceof Map && a instanceof Map) assertEquals(message+"; key "+k, (Map)e, (Map)a);
			else if (e instanceof List && a instanceof List) assertEquals(message+"; key "+k, (Map)e, (Map)a);
			else assertEquals(message+"; key "+k, e, a);
		}
	}

	public static void run(Class test, String[] args) {
		if (args.length>0 && args[0].equals("-gui")) junit.swingui.TestRunner.run(test); 
		else junit.textui.TestRunner.run(test); 
	}
	
}
