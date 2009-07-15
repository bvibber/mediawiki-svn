package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.mangler.RegularExpressionMangler;
import de.brightbyte.wikiword.analyzer.sensor.HasSectionSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.NamespaceSensor;

public class WikiConfiguration_eswiki extends WikiConfiguration {

	public WikiConfiguration_eswiki() {
		//ASK: drini

		//strip
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("clic"
		, 0, true), ""));
		
		//subst
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("Okina", 0, true), "\u02bb"));
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern(",", 0, true), "\u00b7"));
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("C", 0, true), "\u00a9"));
		stripClutterManglers.add( new RegularExpressionMangler( templatePattern("E", 1, true), "\u00d710^$2"));
		
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commons", 1, true), "[[commons:$2]]"));
		stripClutterManglers.add( new RegularExpressionMangler(templatePattern("commonscat", 1, true), "[[commons:Category:$2]]"));
		
		//reduce to third param
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("fontcolor" 
				, 3, true), "$4"));
		
		//reduce to second param
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("fontcolor|lang" 
				, 2, true), "$3"));

		//reduce to first param
		stripClutterManglers.add( new RegularExpressionMangler(
				templatePattern("IPA|AFI|Unicode|aut|cita|AC"
						+"|M\u00fasica|music|polytonic"
						//+"|en|pt|kl|ca"
						+"|IdV\u00edaEsp|Identificador_carretera_espa\u00f1ola" 
				, 1, true), "$2"));
		
		
		// resource types (categorie, redirects and to (to some extent) disambiguations are detected automatically)
		resourceTypeSensors.add( new NamespaceSensor<ResourceType>(ResourceType.LIST, 104)); // 104 = Anexo		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Destruir|Copyvio|Plagio|CdbM?|SRA|Sin_?relevancia|Irrelevante|Autotrad|RobotDestruir|Prob|Infraesbozo)$", 0));
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(BD|NF|Sucesi\u00f3n)$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Fica_de_.+)$", 0, "fechanac"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Fica_de_.+)$", 0, "fechamuerte"));
		
		conceptTypeSensors.add( new HasSectionSensor<ConceptType>(ConceptType.PERSON, "Biograf\u00eda"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.LIFEFORM, "^(Taxobox|Fica_de_(tax\u00f3n))$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Ficha_de_localidad.*)$", 0));
		conceptTypeSensors.add( new HasTemplateSensor<ConceptType>(ConceptType.PLACE, "coord"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Fica_de_.+)$", 0, "coor"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Fica_de_.+)$", 0, "mapa"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Fica_de_.+)$", 0, "poblaci\u00f3n"));
		
		//TODO: number, date, event, work...
	}

}
