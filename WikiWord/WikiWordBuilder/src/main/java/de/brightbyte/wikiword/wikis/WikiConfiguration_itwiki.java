package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;

public class WikiConfiguration_itwiki extends WikiConfiguration {

	public WikiConfiguration_itwiki() {
		//resourceTypeSensors.add( new TitleSensor<ResourceType>(ResourceType.LIST, "^Elenco_", 0));
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^(Liste|Cronologie|Cronologia)(_|$)", 0));
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Cancellazione|Cancella_subito|Cancelcopy|ViolazioneCopyright)$", 0));
	}

}
