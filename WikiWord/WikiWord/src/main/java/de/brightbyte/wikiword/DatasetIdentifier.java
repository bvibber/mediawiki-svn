package de.brightbyte.wikiword;

import java.util.Collections;
import java.util.List;


public class DatasetIdentifier {

	protected String collection;
	protected String name;
	protected String dbPrefix;
	
	protected String[] configPackages;	
	protected ConceptTypeSet conceptTypes;
	
	public DatasetIdentifier(String collection, String name, String dbPrefix, String[] configPackages) {
		super();
		this.collection = collection;
		this.name = name;
		this.dbPrefix = dbPrefix;
		
		this.configPackages = configPackages==null ? new String[] {} : configPackages;
		this.conceptTypes = ConceptType.getConceptTypes(this, configPackages);
	}

	public String getName() {
		return name;
	}

	public String getQName() {
		if (collection==null) return ":"+name;
		else return collection+":"+name;
	}

	/** The prefix to use for naming database tables . **/
	public String getDbPrefix() {
		return dbPrefix;
	}

	@Override
	public String toString() {
		return getCollection()+":"+getName();
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((collection == null) ? 0 : collection.hashCode());
		result = PRIME * result + ((name == null) ? 0 : name.hashCode());
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
		final DatasetIdentifier other = (DatasetIdentifier) obj;
		if (collection == null) {
			if (other.collection != null)
				return false;
		} else if (!collection.equals(other.collection))
			return false;
		if (name == null) {
			if (other.name != null)
				return false;
		} else if (!name.equals(other.name))
			return false;
		return true;
	}

	protected static String dbPrefix(String collection, String name) {
		String db = name.replaceAll("[-. ]", "_")+"_";
		if (collection!=null && collection.length()>0) {
			db = collection.replaceAll("[-. ]", "_")+"_"+db;
		}
		
		return db;
	}
	
	public static DatasetIdentifier forName(String collection, String name, TweakSet tweaks) {
		String[] configPackages = getConfigPackages(tweaks);

		return new DatasetIdentifier(collection, name, dbPrefix(collection, name), configPackages);
	}

	protected static String[] getConfigPackages(TweakSet tweaks) {
		List<String> pkg = tweaks.getTweak("wikiword.ConfigPackages", Collections.<String>emptyList()); 
		return pkg.toArray(new String[pkg.size()]);
	}

	public String getCollection() {
		return collection;
	}
	
	public ConceptTypeSet getConceptTypes() {
		return conceptTypes;
	}
	
}
