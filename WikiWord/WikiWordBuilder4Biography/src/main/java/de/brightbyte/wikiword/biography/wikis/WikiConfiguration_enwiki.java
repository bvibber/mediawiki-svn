package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler(templatePattern("awd", 1, true), "$1")); //TODO: {{awd|award|year|title|role|name}}

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Personendaten"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-sortname", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "NAME").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "ALTERNATIV NAMENS").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-occupation", "SHORT DESCRIPTION").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-date", "DATE OF BIRTH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-place", "PLACE OF BIRTH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-date", "DATE OF DEATH").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-place", "PLACE OF DEATH").setStripMarkup(true)
			) );

		propertyExtractors.add( new WikiTextAnalyzer.TemplateParameterExtractor(new WikiTextAnalyzer.ExactNameMatcher("Infobox_Artist"),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "name").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-name", "birthname").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-date", "birthdate").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-place", "birthplace").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-birth-place", "location").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-date", "deathdate").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-death-place", "deathplace").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("person-nationality", "nationality").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("artist-field", "field").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("artist-movement", "movement").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("artist-training", "training").setStripMarkup(true),
				new WikiTextAnalyzer.DefaultTemplateParameterPropertySpec("artist-award", "award").setStripMarkup(true)
			) );

		conceptTypeSensors.add( new WikiTextAnalyzer.HasTemplateLikeSensor(ConceptType.PERSON, "^(Infobox[ ]Artist)$", 0));
	}
	
}
