package de.brightbyte.wikiword.builder;
import java.io.File;
import java.io.IOException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.MultiMap;
import de.brightbyte.io.IOUtil;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiLink;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer.WikiPage;


public class PropertyDump {
	public static void main(String[] args) throws InstantiationException, IOException {
		String lang = args[0];
		String n = args[1];
		
		Corpus corpus = Corpus.forName("TEST", lang, new String[] {"de.brightbyte.wikiword.wikipro", "de.brightbyte.wikiword.wikipro.wikis"});
		
		URL u;
		
		if ( args.length>2 ) {
			u = new File(args[2]).toURI().toURL();
		}
		else {
			u =  new URL("http://"+lang+".wikipedia.org/w/index.php?action=raw&title=" + URLEncoder.encode(n, "UTF-8"));
		}
		
		String p = n;

		TweakSet tweaks = new TweakSet();
		WikiTextAnalyzer analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(corpus, tweaks);
		
		System.out.println("loading "+u+"...");
		String text = IOUtil.slurp(u, "UTF-8");
		System.out.println("loaded.");
		
		NamespaceSet namespaces = Namespace.getNamespaces(null);
		analyzer.initialize(namespaces, true);
		
		WikiLink t = analyzer.makeLink(p, p, null, null);
		
		WikiPage page = analyzer.makePage(t.getNamespace(), t.getTarget().toString(), text, true);

		System.out.println("Resource: " + page.getResourceName());
		System.out.println("Concept: " + page.getConceptName());
		
		System.out.println("ResourceType: " + page.getResourceType());
		System.out.println("ConceptType: " + page.getConceptType());

		System.out.println("Definition: " + page.getFirstSentence());
		
		System.out.println("Properties:");
		MultiMap<String, CharSequence, Set<CharSequence>> properties = page.getProperties();
		for (Map.Entry<String, Set<CharSequence>> e : properties.entrySet()) {
			System.out.print("\t");
			System.out.print(e.getKey());
			System.out.print(": ");
			
			boolean first = true;
			for (CharSequence v : e.getValue()) {
				if (first) first = false;
				else System.out.print(", ");
				
				System.out.print(v);
			}
			System.out.println();
		}

		System.out.println("Supplements:");
		Set<CharSequence> supplements = page.getSupplementLinks();
		for (CharSequence s : supplements) {
			System.out.println("\t"+s);
		}

		CharSequence supplementedConcept = page.getSupplementedConcept();
		if (supplementedConcept!=null) {
			System.out.println("Supplemented: ");
			System.out.println("\t"+supplementedConcept);
		}

		System.out.println("TitleTerms:");
		Set<CharSequence> titleTerms = page.getTitleTerms();
		for (CharSequence s : titleTerms) {
			System.out.println("\t"+s);
		}

		System.out.println("PageTerms:");
		Set<CharSequence> titlePage = page.getPageTerms();
		for (CharSequence s : titlePage) {
			System.out.println("\t"+s);
		}
	}

}
