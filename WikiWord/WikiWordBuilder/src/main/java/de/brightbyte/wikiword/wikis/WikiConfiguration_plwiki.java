package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasSectionSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;

public class WikiConfiguration_plwiki extends WikiConfiguration {

	public WikiConfiguration_plwiki() {
		//subst
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("centuryBack", 0, true), "Sto lat wstecz"));
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("PrevYear", 0, true), "Poprzedni rok"));
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("NextYear", 0, true), "Nast\u0119pny rok"));
		
		//reduce to first param
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("IPA[123]?|Unicode|Nowrap"
						+"|grc"
				, 1, true), "$2"));
		
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^(Tablice|Listy)(_|$)", 0));
		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Ek|EK|Delete|Ekspresowe_kasowanko|SDU|SdUplus|NPA|Copyvio|DNU|PoczSDU|PoczSdU|Poczekalnia|NPAfrgm)$", 0));
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^Do_(Wikibooks|Wikicytat\u00f3w|Wikinews|Wikis\u0142ownika|Wiki\u017ar\u00f3de\u0142|przet\u0142umaczenia)$", 0));
		
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "^Urodzeni_w_.*$", 0));
		conceptTypeSensors.add( new HasCategoryLikeSensor<ConceptType>(ConceptType.PERSON, "^Zmarli_w_.*$", 0));
	  
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^.*infobox$", 0, "powierzchnia"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^.*infobox$", 0, "ludno\u015b\u0107"));
		
		conceptTypeSensors.add( new HasSectionSensor<ConceptType>(ConceptType.LIFEFORM, "Taksonomia"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Ro\u015blina_infobox"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Zwierz\u0119_infobox"));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.LIFEFORM, "Cechy_taksonu"));
	}

}
