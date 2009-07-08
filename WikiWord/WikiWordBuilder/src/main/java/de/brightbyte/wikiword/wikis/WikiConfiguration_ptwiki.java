package de.brightbyte.wikiword.wikis;

import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.analyzer.WikiConfiguration;
import de.brightbyte.wikiword.analyzer.sensor.HasCategoryLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateLikeSensor;
import de.brightbyte.wikiword.analyzer.sensor.HasTemplateSensor;
import de.brightbyte.wikiword.analyzer.sensor.NamespaceSensor;

public class WikiConfiguration_ptwiki extends WikiConfiguration {

	public WikiConfiguration_ptwiki() {
		resourceTypeSensors.add( new NamespaceSensor<ResourceType>(ResourceType.LIST, 102)); // 102 = Anexo
		resourceTypeSensors.add( new HasCategoryLikeSensor<ResourceType>(ResourceType.LIST, "^(Listas|\u00cdndice)(_|$)", 0));
		resourceTypeSensors.add( new HasTemplateLikeSensor<ResourceType>(ResourceType.BAD, "^(Elimina\u00e7\u00e3o_r\u00e1pida|Er1?|ER1?|Delete|D|ESR2|Esr2|Apagar2|Vda2|VDA2|Copyright2)$", 0));
		
	   resourceTypeSensors.add( new HasTemplateSensor<ResourceType>(ResourceType.BAD, "ambox", "tipo", "elimina\u00e7\u00e3o"));
	}

}
