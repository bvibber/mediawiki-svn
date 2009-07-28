package de.brightbyte.wikiword.lifescience;

import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.ConceptTypeSet;

/**
 * Enumeration of concept types; each concept type represents a very broad category of concepts,
 * which can be used to filter concepts identified in a corpus. The idea is at for some uses, 
 * some kinds of concepts are not usefull, or especially usefull. For example, people and polaces
 * are not suitable for use in a general dictionary, but very useful for topic tracking.  
 * Each type is associated with a code (for internal use) and a URI (for external use).
 * The URI is constructed based on {@link RdfEntities.conceptTypeBase}.
 */
public class LifeScienceConceptType extends ConceptType {

	public static final ConceptType DISEASE;
	public static final ConceptType DRUG;
	public static final ConceptType CHEMICAL;
	public static final ConceptType PROTEIN;
	public static final ConceptType ORGAN;
	public static final ConceptType FOOD;

	/**
	 * NamespaceSet for the canonical concept types. Loaded from the ConceptTypes.properties 
	 * file in this package.
	 */
	public static final ConceptTypeSet lifeScienceConceptTypes; 
	
	static {
		try {
			lifeScienceConceptTypes = getConceptTypes(null, "de.brightbyte.wikiword.lifescience"); //FIXME: make unmodifiable!
			
			DISEASE =   lifeScienceConceptTypes.getType(1001);
			//SYMPTOM =   wikiProConceptTypes.getType(1002);
			DRUG =      lifeScienceConceptTypes.getType(1003);
			//TREATMENT = wikiProConceptTypes.getType(1004);
			CHEMICAL =  lifeScienceConceptTypes.getType(1005);
			PROTEIN =  lifeScienceConceptTypes.getType(1006);
			//GENE =      wikiProConceptTypes.getType(1007);
			ORGAN =     lifeScienceConceptTypes.getType(1008);
			FOOD =     lifeScienceConceptTypes.getType(1009);
		}
		catch (NumberFormatException ex) {
			throw new ExceptionInInitializerError(ex);
		}		
	}

	public LifeScienceConceptType(int code, String name) {
		super(code, name);
	}
	
}
