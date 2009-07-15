package de.brightbyte.wikiword.analyzer;

import java.io.IOException;

import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiTextAnalyzerBenchmark {
	protected boolean titleCase;
	protected NamespaceSet namespaces;
	protected Corpus corpus;
	protected WikiTextAnalyzer analyzer;
	protected TweakSet tweaks;
	
	public WikiTextAnalyzerBenchmark(String wikiName) throws InstantiationException {
		tweaks = new TweakSet();
		corpus = Corpus.forName("TEST", wikiName, tweaks);

		//site.Base = "http://"+corpus.getDomain()+"/wiki/";
		//site.Sitename = corpus.getFamily();
		
		titleCase = true;
		namespaces = corpus.getNamespaces();

		analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(corpus, tweaks);
		analyzer.initialize(namespaces, titleCase);
	}

	public long benchmark(String file, int count, boolean disambig) throws IOException {
		String text = WikiTextAnalyzerTestBase.loadTestPage(file, getClass(), corpus);

		long t = System.currentTimeMillis();
		for (int i = 0; i<count; i++) {
			WikiPage page = analyzer.makePage(0, file, text, false);
			page.getResourceType();
			page.getConceptType();
			page.getCleanedText(true);
			page.getCategories();
			page.getLinks();
			page.getTemplates();
			page.getSections();
			page.getFirstSentence();
			page.getTitleTerms();
			page.getTitleBaseName();
			page.getDefaultSortKey();
			if (disambig) page.getDisambigLinks();
		}
		
		long d = System.currentTimeMillis() - t;
		return d;
	}
	
	public static void main(String[] args) throws InstantiationException, IOException {
		String wiki = args[0];
		String file = args[1];
		int count = 1000;
		boolean disambig = false;
		
		WikiTextAnalyzerBenchmark benchmark = new WikiTextAnalyzerBenchmark(wiki);
		long d = benchmark.benchmark(file, count, disambig);
		
		System.out.println(String.format("Benchmark: took %s analyzer %04.2f seconds to process %s %d times (%04.2f pages/sec)", wiki, d/1000.0, file, count, (count*1000.0)/d));
	}
}
