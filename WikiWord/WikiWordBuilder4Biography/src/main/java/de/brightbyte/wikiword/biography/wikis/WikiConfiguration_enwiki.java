package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.extractor.CategoryPatternParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.PagePropertyValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterExtractor;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.template.DefaultTemplateParameterPropertySpec;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("awd", 1, true), "$1")); //TODO: {{awd|award|year|title|role|name}}

		propertyExtractors.add( new CategoryPatternParameterExtractor("^(\\d+s?)_births$", "$1", 0, "person-birth-date") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^(\\d+s?)_deaths$", "$1", 0, "person-death-date") );

		propertyExtractors.add( new CategoryPatternParameterExtractor("^(.+)_(artists|painters|sculptors)$", "$1", 0, "artist-group") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^.*(^|_)(painter|sculptor|photographer)s$", "$2", Pattern.CASE_INSENSITIVE, "artist-group") );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Persondata"),
				new DefaultTemplateParameterPropertySpec("NAME", "person-sortname").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("NAME", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("person-name", "ALTERNATIV NAMENS").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new DefaultTemplateParameterPropertySpec("person-occupation", "SHORT DESCRIPTION").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("person-birth-date", "DATE OF BIRTH").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("person-birth-place", "PLACE OF BIRTH").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("person-death-date", "DATE OF DEATH").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("person-death-place", "PLACE OF DEATH").setStripMarkup(true)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_Artist"),
				new DefaultTemplateParameterPropertySpec("name", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthname", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthdate", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthplace", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("location", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("deathdate", "person-death-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("deathplace", "person-death-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("nationality", "person-nationality").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("field", "artist-group").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("movement", "artist-group").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("training", "artist-training").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("award", "artist-award").setStripMarkup(true)
			) );

		pageTermExtractors.add( new PagePropertyValueExtractor("person-sortname") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("person-name") ); 

		conceptTypeSensors.add( new HasTemplateLikeSensor(ConceptType.PERSON, "^(Infobox[ ]Artist)$", 0));
	}
	
}
