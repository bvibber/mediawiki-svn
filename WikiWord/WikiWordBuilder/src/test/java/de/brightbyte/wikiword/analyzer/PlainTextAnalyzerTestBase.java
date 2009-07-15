package de.brightbyte.wikiword.analyzer;

import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.MissingResourceException;

import junit.framework.TestCase;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.PlainTextAnalyzer;

/**
 * Abstract base class for unit tests for PlainTextAnalyzer.
 */
public abstract class PlainTextAnalyzerTestBase extends TestCase {
	
	protected Corpus corpus;
	protected TweakSet tweaks;
	protected PlainTextAnalyzer analyzer;
	
	public PlainTextAnalyzerTestBase(String wikiName) {
		tweaks = new TweakSet();
		corpus = Corpus.forName("TEST", wikiName, tweaks);
	}

	@Override
	public void setUp() throws Exception {				
		analyzer = PlainTextAnalyzer.getPlainTextAnalyzer(corpus, tweaks);
		analyzer.initialize();
	}

	public void tareDown() {
		//noop
	}
	
	public static String loadTestText(String title, Class ctx, Corpus corpus) throws IOException {
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
	
	public static void run(Class test, String[] args) {
		if (args.length>0 && args[0].equals("-gui")) junit.swingui.TestRunner.run(test); 
		else junit.textui.TestRunner.run(test); 
	}
	
}
