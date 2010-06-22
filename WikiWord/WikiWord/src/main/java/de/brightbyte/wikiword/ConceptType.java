package de.brightbyte.wikiword;

import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.util.Properties;

/**
 * Enumeration of concept types; each concept type represents a very broad category of concepts,
 * which can be used to filter concepts identified in a corpus. The idea is at for some uses, 
 * some kinds of concepts are not usefull, or especially usefull. For example, people and polaces
 * are not suitable for use in a general dictionary, but very useful for topic tracking.  
 * Each type is associated with a code (for internal use) and a URI (for external use).
 * The URI is constructed based on {@link RdfEntities.conceptTypeBase}.
 */
public class ConceptType {

	/** Unknown concept type, indicating that no information is avialable about the concept.
	 * If some information is avialable, but not specific type could be assigned, the type OTHER
	 * MUST be used instead. 
	 **/
	public static final ConceptType UNKNOWN;

	/** A geographic location; This type SHOULD NOT be used for specific buildings or simmilar
	 * sites that merely have a geographic location. 
	 **/
	public static final ConceptType PLACE;

	/** A person (MAY be fictional, SHOULD be human if not fictional). 
	 **/
	public static final ConceptType PERSON;

	/** An organisation, loke a cooperation or NGO, a union, etc. This 
	 * MAY include government organisations, but not states as such. 
	 **/
	public static final ConceptType ORGANISATION;

	/** A name as such, i.e. a first name or last name.
	 **/
	public static final ConceptType NAME;

	/** A point in time, or time period, like "17th century". Recurrent dates (like "March 3rd") 
	 * MAY also have this type. Concepts that go beyond a pure notion of time or date (like "Monday"
	 * or "9/11") SHOULD NOT have this type. 
	 **/
	public static final ConceptType TIME;

	/** A number, like "three". Special constants like Pi or e MAY also have this type.
	 * Concepts that go beyond a pure notion of a number (like "dozen") 
	 * SHOULD NOT have this type. 
	 **/
	public static final ConceptType NUMBER;

	/** Biological taxon describing a life form (i.e. a genus, a species, etc).
	 **/
	public static final ConceptType LIFEFORM;

	/** Specific (historical) events. Recurrent events may also have this type,
	 * however, generic types or classes of events should not.
	 **/
	public static final ConceptType EVENT;

	/** A work of art, like a book, a painting, an opera, a music album, etc.
	 **/
	public static final ConceptType WORK;

	/** Unknown concept type. Generic catch all type, expected to occurr frequently. **/
	public static final ConceptType OTHER;

	/** Not a real concept. MUST be used ONLY for placeholder/proxy entries.
	 * SHOULD only occurr in temporary data. 
	 **/
	//public static final ConceptType NONE;
	
	/** Not a real concept. MUST be used ONLY for placeholder/proxy entries.
	 * SHOULD only occurr in temporary data. Different from NONE only in that it marks
	 * redirects, causing them to be processed differently. 
	 **/
	public static final ConceptType ALIAS;
	
	/**
	 * NamespaceSet for the canonical concept types. Loaded from the ConceptTypes.properties 
	 * file in this package.
	 */
	public static final ConceptTypeSet canonicalConceptTypes; 
	
	static {
		try {
			canonicalConceptTypes = getConceptTypes(null); //FIXME: make unmodifiable!
			
			UNKNOWN = canonicalConceptTypes.getType(0);
			PLACE = canonicalConceptTypes.getType(10);
			PERSON = canonicalConceptTypes.getType(20);
			ORGANISATION = canonicalConceptTypes.getType(30);
			NAME = canonicalConceptTypes.getType(40);
			TIME = canonicalConceptTypes.getType(50);
			NUMBER = canonicalConceptTypes.getType(60);
			LIFEFORM = canonicalConceptTypes.getType(70);
			EVENT = canonicalConceptTypes.getType(80);
			WORK = canonicalConceptTypes.getType(90);
			OTHER = canonicalConceptTypes.getType(1000);
			//NONE = canonicalConceptTypes.getType(100000);
			ALIAS = canonicalConceptTypes.getType(100010);
		}
		catch (NumberFormatException ex) {
			throw new ExceptionInInitializerError(ex);
		}		
	}

	private String name;
	private int code;
//	private String uri;
	
	public ConceptType(int code, String name) {
		this.name = name;
		this.code = code;
		//this.uri = WikiWord.conceptTypeURI(this.getName()); 
	}
	
	public static boolean isWeak(int t) {
		return (t==UNKNOWN.code || t==OTHER.code || t==ALIAS.code);
	}
	
	public String getName() {
		return name;
	}

	public int getCode() {
		return code;
	}

//	public String getURI() {
//		return uri;
//	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + code;
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final ConceptType other = (ConceptType) obj;
		if (code != other.code)
			return false;
		return true;
	}
	
	@Override
	public String toString() {
		return name;
	}

	/**
	 * Returns a ConceptTypeSet representing the ConceptTypes defined for the given Corpus
	 * (i.e. wiki project). The ConceptTypeSet MUST support the canonical ConceptTypes, and 
	 * MAY support additional ConceptTypes defined for that corpus.
	 */
	protected static ConceptTypeSet getConceptTypes(DatasetIdentifier dataset, String... configPackages) {
		ConceptTypeSet ct = new ConceptTypeSet();
		
		//TODO: merge files for language and specific wiki!
		
		ClassLoader loader =  ConceptType.class.getClassLoader();
		
		if (canonicalConceptTypes!=null) ct.addAll(canonicalConceptTypes);
		loadConceptTypes(loader, "de.brightbyte.wikiword", dataset, ct);
		loadConceptTypes(loader, "de.brightbyte.wikiword.wikis", dataset, ct);

		if (configPackages!=null) {
			for (String pkg: configPackages) {
				loadConceptTypes(loader, pkg, dataset, ct);
			}
		}
		
		return ct;
	}
	
	protected static ConceptTypeSet getCanonicalConceptTypes(Class base, String prefix) {
		ConceptTypeSet into = new ConceptTypeSet();
		loadConceptTypes(base.getClassLoader(), prefix, null, into);
		return into;
	}
	
	protected static void loadConceptTypes(ClassLoader loader, String prefix, DatasetIdentifier dataset, ConceptTypeSet into) {
		if (loader==null) loader= ClassLoader.getSystemClassLoader();
		String p = prefix.replace('.', '/');
		
		URL u = loader.getResource(p + "/ConceptTypes.properties");
		if (u!=null) loadConceptTypes(u, into);
		
		if ( dataset != null && dataset instanceof Corpus) { //XXX: per-language types are a BAD IDEA!
			u = loader.getResource(p + "/ConceptTypes_" + ((Corpus)dataset).getFamily() + ".properties");
			if (u!=null) loadConceptTypes(u, into);
	
			u = loader.getResource(p + "/ConceptTypes_" + ((Corpus)dataset).getLanguage() + ".properties");
			if (u!=null) loadConceptTypes(u, into);
	
			u = loader.getResource(p + "/ConceptTypes_" + ((Corpus)dataset).getClassSuffix() + ".properties");
			if (u!=null) loadConceptTypes(u, into);
		}
	}
	
	protected static void loadConceptTypes(URL from, ConceptTypeSet into) {
		try {
			InputStream in = from.openStream();
			Properties p = new Properties();
			p.load(in);
			in.close();
			
			into.addTypes(p);
		} catch (IOException e) {
			throw new RuntimeException("failed to load concept types from "+from);
		}
	}

}
