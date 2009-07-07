package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.sensor.HasSectionSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.NamespaceSensor;

public class WikiConfiguration_eswiki extends WikiConfiguration {

	public WikiConfiguration_eswiki() {
		//ASK: drini
		
		resourceTypeSensors.add( new NamespaceSensor<ResourceType>(ResourceType.LIST, 104)); // 104 = Anexo
		
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Destruir|Copyvio|Plagio|CdbM?|SRA|Sin_?relevancia|Irrelevante|Autotrad|RobotDestruir|Prob|Publicidad|Infraesbozo)$", 0));
		
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PERSON, "^(Fica_de_(persona|actor|artista|autoridad)|BD|NF|Sucesi\u00f3n)$", 0));
		conceptTypeSensors.add( new HasSectionSensor<ConceptType>(ConceptType.PERSON, "Biograf\u00eda"));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.LIFEFORM, "^(Taxobox|Fica_de_(tax\u00f3n))$", 0));
		conceptTypeSensors.add( new HasTemplateLikeSensor<ConceptType>(ConceptType.PLACE, "^(Ficha_de_localidad.*)$", 0));
		
		//TODO: number, date, event, work...
	}

}
