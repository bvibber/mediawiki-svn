package de.brightbyte.wikiword.biography.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.extractor.CategoryPatternParameterExtractor;
import de.brightbyte.wikiword.analyzer.extractor.PagePropertyValueExtractor;
import de.brightbyte.wikiword.analyzer.extractor.TemplateParameterExtractor;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.matcher.ExactNameMatcher;
import de.brightbyte.wikiword.analyzer.matcher.PatternNameMatcher;
import de.brightbyte.wikiword.analyzer.sensor.HasPropertySensor;
import de.brightbyte.wikiword.analyzer.template.DefaultTemplateParameterPropertySpec;

public class WikiConfiguration_enwiki extends WikiConfiguration {

	public WikiConfiguration_enwiki() {
		super();

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("awd", 1, true), "$1")); //TODO: {{awd|award|year|title|role|name}}
		stripMarkupManglers.add(0, new RegularExpressionMangler( templatePattern("(Birth|Death)(Date(AndAge)?|_date(_and_age)?)", 1, true), " $1") );

		propertyExtractors.add( new CategoryPatternParameterExtractor("^(\\d+s?)_births$", "$1", 0, "person-birth-date") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^(\\d+s?)_deaths$", "$1", 0, "person-death-date") );

		propertyExtractors.add( new CategoryPatternParameterExtractor("^(.+)_(artists|painters|sculptors)$", "$1", 0, "artist-group") );
		propertyExtractors.add( new CategoryPatternParameterExtractor("^.*(^|_)(painter|sculptor|photographer)s$", "$2", Pattern.CASE_INSENSITIVE, "artist-group") );
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Persondata"),
				new DefaultTemplateParameterPropertySpec("NAME", "person-sortname").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("NAME", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("ALTERNATIV NAMENS", "person-name").setStripMarkup(true)
						.setSplitPattern(Pattern.compile("\\s[;]\\s")).addNormalizer(Pattern.compile("\\(.*?\\)"),""),
				new DefaultTemplateParameterPropertySpec("SHORT DESCRIPTION", "person-occupation").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("DATE OF BIRTH", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("PLACE OF BIRTH", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("DATE OF DEATH", "person-death-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("PLACE OF DEATH", "person-death-place").setStripMarkup(true)
			) );
		
		Pattern defaultSplitPattern = Pattern.compile("[,;/]\\s+|<br\\s*/?>");

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_Artist"),
				new DefaultTemplateParameterPropertySpec("name", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthname", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthdate", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birthplace", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("location", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("deathdate", "person-death-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("deathplace", "person-death-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("nationality", "person-nationality").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("field", "artist-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("movement", "artist-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("training", "artist-training").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("award", "artist-award").setStripMarkup(true).setSplitPattern(defaultSplitPattern)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Infobox_(((Medical|Military)_)?[Pp]erson|Actor|Astronaut|Criminal|Engineer|Musical_artist|Philosopher|Pope|ReligiousBio|Scientist)", 0, true),
				new DefaultTemplateParameterPropertySpec("name", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("other_names", "person-name").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birth_date", "person-birth-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("birth_place", "person-birth-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("death_date", "person-death-date").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("death_place", "person-death-place").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("occupation", "person-occupation").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("known_for", "person-known-for").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("nationality", "person-nationality").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("residence", "person-nationality").setStripMarkup(true),
				new DefaultTemplateParameterPropertySpec("citizenship", "person-nationality").setStripMarkup(true)
			) );
		
		/* note: converted to category links by stripClutter!
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Birth_date|BrithDate|Dob", 0, true),
				new DefaultTemplateParameterPropertySpec("1", "person-birth-date").setStripMarkup(true)
				) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Death_date|DeathDate|Dod", 0, true),
				new DefaultTemplateParameterPropertySpec("1", "person-death-date").setStripMarkup(true)
				) );
		
		propertyExtractors.add( new TemplateParameterExtractor(new PatternNameMatcher("Death_date_and_age|DeathDateAndAge|Bda", 0, true),
				new DefaultTemplateParameterPropertySpec("1", "person-birth-date").setStripMarkup(true)
				) );
		*/
		
		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_Medical_Person"),
				new DefaultTemplateParameterPropertySpec("profession", "person-occupation").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("profession", "expert-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("specialism", "expert-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("research_field", "expert-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("work_institutions", "person-affiliation").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("prizes", "expert-prize").setStripMarkup(true).setSplitPattern(defaultSplitPattern)
			) );

		propertyExtractors.add( new TemplateParameterExtractor(new ExactNameMatcher("Infobox_Scientist"),
				new DefaultTemplateParameterPropertySpec("fields", "expert-group").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("alma-mater", "person-education").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("workplaces", "person-affiliation").setStripMarkup(true).setSplitPattern(defaultSplitPattern),
				new DefaultTemplateParameterPropertySpec("awards", "expert-prize").setStripMarkup(true).setSplitPattern(defaultSplitPattern)
			) );

		pageTermExtractors.add( new PagePropertyValueExtractor("person-sortname") ); 
		pageTermExtractors.add( new PagePropertyValueExtractor("person-name") ); 

		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "artist-group"));
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-name"));
		conceptTypeSensors.add( new HasPropertySensor<ConceptType>(ConceptType.PERSON, "person-birth-date"));
	}
	
}
