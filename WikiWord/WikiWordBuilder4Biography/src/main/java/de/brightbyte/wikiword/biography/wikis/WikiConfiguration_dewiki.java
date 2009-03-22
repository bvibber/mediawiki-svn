package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_dewiki extends WikiConfiguration {

	public WikiConfiguration_dewiki() {
		super();

		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^Geboren_(\\d+(_v\\._Chr\\.)?)$", "$1", 0, "person-birth-date") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^Gestorben_(\\d+(_v\\._Chr\\.)?)$", "$1", 0, "person-death-date") );
 
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^Maler_(der|des)_(.+)$", "$2", 0, "artist-group") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^(Maler|Bildhauer|Fotograf)(_|$)", "$2", 0, "artist-group") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^.*[^_](maler|bildhauer|fotograf)$", "$2", 0, "artist-group") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^.*([-_\\wäöü]+)(maler|bildhauer|fotograf)$", "$1", 0, "artist-group") );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Personendaten"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("NAME", "person-sortname").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("NAME", "person-name").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("ALTERNATIVNAMEN", "person-name").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("KURZBESCHREIBUNG", "person-occupation").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("GEBURTSDATUM", "person-birth-date").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("STERBEDATUM", "person-death-date").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("GEBURTSORT", "person-birth-place").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("STERBEORT", "person-death-place").setStripMarkup(true)
			) );

		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("person-sortname") ); 
		pageTermExtractors.add( new WikiTextAnalyzer.PagePropertyValueExtractor("person-name") ); 
	}
	
}
