package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasCategorySensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;

public class WikiConfiguration_dewiki extends WikiConfiguration {

	public WikiConfiguration_dewiki() {
		super();
		
		/*
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{[Oo]kina\\}\\}", "\u02BB", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*(IPA(?:-Text)|IAST|Unicode|Musik)\\s*\\|\\s*([^|}]+)\\s*(\\|.*?)?\\s*(\\|.*?)?\\}\\}", "$2", Pattern.CASE_INSENSITIVE));
		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("^\\{\\{(" +
				"SWD|Commons|Wiktionary" +
				")\\s*(\\|[^\\{\\}\\r\\n]*)?\\}\\}\\s*$", "", Pattern.MULTILINE | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*(?:" +
				"en|it|fr|ar|Polytonisch" +
				")\\s*\\|\\s*(.*?)\\s*\\}\\}", "$1", Pattern.DOTALL | Pattern.CASE_INSENSITIVE));

		stripClutterManglers.add( new WikiTextAnalyzer.RegularExpressionMangler("\\{\\{\\s*(?:" +
				"lang" +
				")\\s*\\|(?:\\s*rtl\\s*\\|)?.*?\\|\\s*(.*?)\\s*\\}\\}", "$1", Pattern.DOTALL | Pattern.CASE_INSENSITIVE));
		*/
		
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commons", 1, true), "[[commons:$2]]"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commonscat", 1, true), "[[commons:Category:$2]]"));
		stripClutterManglers.add( new RegularExpressionMangler("\\[\\[:commons:", "[[commons:", Pattern.CASE_INSENSITIVE));
		
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("Okina", 0, false), "\u02BB"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("IPA(?:-Text)|IAST|Unicode|Musik", 1, true), "$2"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("SWD|Wiktionary", 0, true), ""));

		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("en|it|fr|ar|Polytonisch", 1, true), "$2"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("lang", 2, true), "$3"));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PLACE, "^(Kreis_in|Ort(steil)?|Gemeinde|Stadt(bezirk|teil)?|Staat|Bundestaat|Provinz|Territorium|Bundesland|Insel(gruppe)?|Atoll)(_|$)|(^|_)(Provinz)$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^Infobox_", 0, new String[] { "Einwohner" }));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^Infobox_", 0, new String[] { "EINWOHNER" }));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^Infobox_", 0, new String[] { "GEO-LAGE" }));
		
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PERSON, "Mann"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.PERSON, "Frau"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.PERSON, "Personendaten"));

		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "M\u00e4nnlicher_Vorname"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Weiblicher_Vorname"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.NAME, "Familienname"));

		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.TIME, "^Jahr_\\(.+\\)$", 0));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Tag"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Jahrzehnt"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Jahrhundert"));
		conceptTypeSensors.add( new HasCategorySensor<ConceptType>(ConceptType.TIME, "Jahrtausend"));

		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Taxobox"));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.NUMBER, "[Zz]ahl$", 0));

		//XXX: find only *instances*, not *classes* of organizations! (tricky...)
		//conceptTypeSensors.add( new WikiTextAnalyzer.HasCategoryLikeSensor(ConceptType.ORGANISATION, "(Agentur|Amt|Beh\u00f6rde|Ministerium|Unternehmen|Organisation|Partei|Dienst)([_)/]|$)|(Hersteller|Institut|Universität|Schule|Verein|Verband|Klub|Team)(_\\(|[)/]|$)", Pattern.CASE_INSENSITIVE));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.ORGANISATION, "^Infobox_.*([Vv]erein|[Uu]nternehmen|[Kk]lub)$|^Navi.*([Vv]erein|[Uu]nternehmen|[Oo]rganisation|[Mm]inisterien|[Kk]lubs?|[Tt]eams?)$", 0));
		
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "L\u00f6schen"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "L\u00f6schantragstext"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "URV"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Urheberrecht_ungekl\u00e4rt"));
		resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "Falschschreibung"));
		
		//resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.DISAMBIG, "Begriffskl\u00e4rung", null) );
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^(Teill|L)iste$|^(Teill|L)iste_\\(.+\\)$", 0));

		disambigStripSectionPattern = sectionPattern("Siehe auch", 0);  

		//redirectPattern = Pattern.compile("^#(?:REDIRECT(?:ION)?|WEITERLEITUNG)"+REDIRECT_LINK, Pattern.CASE_INSENSITIVE);
		//displayTitlePattern = Pattern.compile("DISPLAYTITLE|SEITENTITEL", Pattern.CASE_INSENSITIVE);
		//defaultSortKeyPattern = Pattern.compile("DEFAULT(SORT(KEY)?|CATEGORYSORT)|SORTIERUNG", Pattern.CASE_INSENSITIVE);
	}

}
