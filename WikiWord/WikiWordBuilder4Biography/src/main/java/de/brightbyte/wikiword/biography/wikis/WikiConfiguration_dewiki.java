package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_dewiki extends WikiConfiguration {

	public WikiConfiguration_dewiki() {
		super();

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Personendaten"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-sortname", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-names", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-names", "ALTERNATIVNAMEN").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-occupation", "KURZBESCHREIBUNG").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-born", "GEBURTSDATUM").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-died", "STERBEDATUM").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-born-at", "GEBURTSORT").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-died-at", "STERBEORT").setStripMarkup(true)
			) );

	}
	
}
