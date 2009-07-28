package de.brightbyte.wikiword.biography;

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
public class BiographyConceptType extends ConceptType {

	/**
	 * NamespaceSet for the canonical concept types. Loaded from the ConceptTypes.properties 
	 * file in this package.
	 */
	public static final ConceptTypeSet biographyConceptTypes; 
	
	static {
		try {
			biographyConceptTypes = getConceptTypes(null, "de.brightbyte.wikiword.biography"); //FIXME: make unmodifiable!
			
		}
		catch (NumberFormatException ex) {
			throw new ExceptionInInitializerError(ex);
		}		
	}

	public BiographyConceptType(int code, String name) {
		super(code, name);
	}
	
}
