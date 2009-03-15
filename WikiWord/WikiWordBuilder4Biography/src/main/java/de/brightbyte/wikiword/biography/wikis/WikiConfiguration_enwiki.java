package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("awd", 1, true), "$1")); //TODO: {{awd|award|year|title|role|name}}

		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^(\\d+s?)_births$", "$1", 0, "person-birth-date") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^(\\d+s?)_deaths$", "$1", 0, "person-death-date") );

		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^(.+)_(artists|painters|sculptors)$", "$1", 0, "artist-group") );
		propertyExtractors.add( new WikiTextAnalyzer.CategoryPatternParameterExtractor("^.*(^|_)(painter|sculptor|photographer)s$", "$2", Pattern.CASE_INSENSITIVE, "artist-group") );
		
		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Persondata"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("NAME", "person-sortname").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("NAME", "person-name").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "ALTERNATIV NAMENS").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-occupation", "SHORT DESCRIPTION").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-date", "DATE OF BIRTH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-place", "PLACE OF BIRTH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-date", "DATE OF DEATH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-place", "PLACE OF DEATH").setStripMarkup(true)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Infobox_Artist"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("name", "person-name").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("birthname", "person-name").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("birthdate", "person-birth-date").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("birthplace", "person-birth-place").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("location", "person-birth-place").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("deathdate", "person-death-date").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("deathplace", "person-death-place").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("nationality", "person-nationality").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("field", "artist-group").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("movement", "artist-group").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("training", "artist-training").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("award", "artist-award").setStripMarkup(true)
			) );

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PERSON, "^(Infobox[ ]Artist)$", 0));
	}
	
}
