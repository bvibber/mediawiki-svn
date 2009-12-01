package de.brightbyte.wikiword;

import java.io.IOException;
import java.io.InputStream;
import java.util.ArrayList;
import java.util.Enumeration;
import java.util.List;
import java.util.MissingResourceException;
import java.util.Properties;

/**
 * Static helper class for dealing with MediaWiki namespaces. Provides constants for 
 * the standard namespaces as well as a way to load the local NamespaceSet for a given
 * Corpus (i.e. wiki project).
 */
public class Namespace {
    public static final int NONE = -999; 
    public static final int MEDIA = -2; 
    public static final int SPECIAL = -1; 
    public static final int MAIN = 0; 
    public static final int TALK = 1; 
    public static final int USER = 2; 
    public static final int USER_TALK = 3; 
    public static final int PROJECT = 4; 
    public static final int PROJECT_TALK = 5; 
    public static final int IMAGE = 6; 
    public static final int IMAGE_TALK = 7; 
    public static final int FILE= 6; 
    public static final int FILE_TALK = 7; 
    public static final int MEDIAWIKI = 8; 
    public static final int MEDIAWIKI_TALK = 9; 
    public static final int TEMPLATE = 10; 
    public static final int TEMPLATE_TALK = 11; 
    public static final int HELP = 12; 
    public static final int HELP_TALK = 13; 
    public static final int CATEGORY = 14; 
    public static final int CATEGORY_TALK = 15; 
	
	
	/**
	 * NamespaceSet for the canonical namespaces. Loaded from the Namespaces.properties 
	 * file in this package.
	 */
	public static final NamespaceSet canonicalNamespaces; 
	
	static {
		try {
			canonicalNamespaces = getNamespaces(null); //FIXME: make unmodifiable!
		}
		catch (NumberFormatException ex) {
			throw new ExceptionInInitializerError(ex);
		}		
	}
	
    private int number;
    private List<String> names = new ArrayList<String>(4);
    
	public Namespace(int number, String name) {
		this.number = number;
		this.names.add(name);
	}

	public List<String> getNames() {
		return names;
	}

	public String getCanonicalName() {
		return names.get(0);
	}
	
	public String getLocalName() {
		return names.size()>1 ? names.get(1) : names.get(0);
	}

	public int getNumber() {
		return number;
	}	

	/*package*/ void addName(String name) {
		if (!names.contains(name)) names.add(name);
	}
	

	/**
	 * Returns a NamespaceSet representing the namespaces defined for the given Corpus
	 * (i.e. wiki project). The NamespaceSet MUST support the canonical namespaces, and 
	 * SHOULD support all localized names for namespaces in the given Corpus, as well as 
	 * any custom namespaces and aliases. 
	 */
	public static NamespaceSet getNamespaces(Corpus corpus) {
		String n;
		
		//TODO: merge files for language and specific wiki!
		if (corpus!=null) n = "wikis/Namespaces_"+corpus.getClassSuffix()+".properties";
		else n = "Namespaces.properties";
		
		InputStream in = Namespace.class.getResourceAsStream(n);
		if (in == null) return corpus==null ? null : canonicalNamespaces;
		
		NamespaceSet ns;
		try {
			Properties p = new Properties();
			p.load(in);
			in.close();
			
			ns = new NamespaceSet();
			if (canonicalNamespaces!=null) ns.addAll(canonicalNamespaces);
			
			Enumeration en = p.propertyNames();
			while (en.hasMoreElements()) {
				String k = (String)en.nextElement();
				
				int nsp;
				try {
					nsp = Integer.parseInt(k);
				}
				catch (NumberFormatException ex) {
					nsp = Namespace.valueOf(k);
				}
				
				if (nsp==NONE) throw new MissingResourceException("invalid namespace key: "+k, Namespace.class.getName(), n);
				String v = p.getProperty(k);
					
				String[] vv = v.split("\\s*\\|\\s*");
				for (String vn: vv) {
					ns.addNamespace(nsp, vn);
				}
			}
			
			return ns;
		} catch (IOException e) {
			throw new RuntimeException("failed to load namespaces from "+n);
		}
		
	}

	@Override
	public String toString() {
		return number+"#"+names;
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + number;
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
		final Namespace other = (Namespace) obj;
		if (number != other.number)
			return false;
		return true;
	}

	/**
	 * Given the canonical namespace name, returns the ID for that statndard namespace.
	 * This method id case-insensitive. If the provided name is not a canonical namespace name,
	 * this method throws an IllegalArgumentException. See NamespaceSet for deailing with 
	 * localized namespace names and custom namespaces and aliases.
	 */
	private static int valueOf(String k) {
		k = k.toUpperCase().trim().replace(' ', '_');
		
		if (k.equals("NONE")) return NONE;
		if (k.equals("MEDIA")) return MEDIA;
		if (k.equals("SPECIAL")) return SPECIAL;
		if (k.equals("MAIN")) return MAIN;
		if (k.equals("*")) return MAIN;
		if (k.equals("")) return MAIN;
		if (k.equals("TALK")) return TALK;
		if (k.equals("USER")) return USER;
		if (k.equals("USER_TALK")) return USER_TALK;
		if (k.equals("PROJECT")) return PROJECT;
		if (k.equals("PROJECT_TALK")) return PROJECT_TALK;
		if (k.equals("IMAGE")) return IMAGE;
		if (k.equals("IMAGE_TALK")) return IMAGE_TALK;
		if (k.equals("FILE")) return FILE;
		if (k.equals("FILE_TALK")) return FILE_TALK;
		if (k.equals("MEDIAWIKI")) return MEDIAWIKI;
		if (k.equals("MEDIAWIKI_TALK")) return MEDIAWIKI_TALK;
		if (k.equals("TEMPLATE")) return TEMPLATE;
		if (k.equals("TEMPLATE_TALK")) return TEMPLATE_TALK;
		if (k.equals("HELP")) return HELP;
		if (k.equals("HELP_TALK")) return HELP_TALK;
		if (k.equals("CATEGORY")) return CATEGORY;
		if (k.equals("CATEGORY_TALK")) return CATEGORY_TALK;
		else throw new IllegalArgumentException("bad namespace constant "+k);
	}

	public static NamespaceSet newEmptySet() {
		return new NamespaceSet();
	}

}
