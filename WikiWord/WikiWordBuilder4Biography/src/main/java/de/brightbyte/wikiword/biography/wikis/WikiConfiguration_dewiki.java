package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_dewiki extends WikiConfiguration {

	public WikiConfiguration_dewiki() {
		super();

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Personendaten"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-sortname", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "ALTERNATIVNAMEN").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-occupation", "KURZBESCHREIBUNG").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-date", "GEBURTSDATUM").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-date", "STERBEDATUM").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-place", "GEBURTSORT").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-place", "STERBEORT").setStripMarkup(true)
			) );

	}
	
}
