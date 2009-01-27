package de.brightbyte.wikiword.wikipro;

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
public class WikiProConceptType extends ConceptType {

	public static final ConceptType DISEASE;
	public static final ConceptType DRUG;
	public static final ConceptType CHEMICAL;
	public static final ConceptType PROTEIN;
	public static final ConceptType ORGAN;

	/**
	 * NamespaceSet for the canonical concept types. Loaded from the ConceptTypes.properties 
	 * file in this package.
	 */
	public static final ConceptTypeSet wikiProConceptTypes; 
	
	static {
		try {
			wikiProConceptTypes = getConceptTypes(null, "de.brightbyte.wikiword.wikipro"); //FIXME: make unmodifiable!
			
			DISEASE =   wikiProConceptTypes.getType(1001);
			//SYMPTOM =   wikiProConceptTypes.getType(1002);
			DRUG =      wikiProConceptTypes.getType(1003);
			//TREATMENT = wikiProConceptTypes.getType(1004);
			CHEMICAL =  wikiProConceptTypes.getType(1005);
			PROTEIN =  wikiProConceptTypes.getType(1006);
			//GENE =      wikiProConceptTypes.getType(1007);
			ORGAN =     wikiProConceptTypes.getType(1008);
		}
		catch (NumberFormatException ex) {
			throw new ExceptionInInitializerError(ex);
		}		
	}

	public WikiProConceptType(int code, String name) {
		super(code, name);
	}
	
}
