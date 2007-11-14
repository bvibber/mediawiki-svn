package org.wikimedia.lsearch.config;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.HashSet;
import java.util.Hashtable;

import org.apache.log4j.Logger;

/**
 * Encapsulated an index ID in form db.part, e.g. entest.mainpart.
 * It identifies each index part in the system. If entest is the name
 * of the database, and it uses mainsplit architecture, there will be
 * three IndexIds: <br/>
 *   entest (main index, logical),<br/> 
 *   entest.mainpart (index where the documents from mainspace are),<br/> 
 *   entest.restpart (index where all other documents are) .<br/>
 *   <br/>
 * The object is global, and provides all possible information about
 * the particular index. Abreviated: iid.
 * 
 *  
 * @author rainman
 *
 */
public class IndexId {
	static org.apache.log4j.Logger log = Logger.getLogger(IndexId.class);
	/** Where the index is */
	protected String indexHost;
	/** Path to index on remote machine */
	protected String indexRsyncPath; 
	
	/** Type (single, mainsplit, split) in string repesentation */
	protected String typeString; 
	/** Parameters associated with type, e.g. number of split parts */
	protected Hashtable<String,String> typeParams;
	
	/** All hosts that search this index */
	protected HashSet<String> searchHosts;
	/** All hosts within my group (of current machine) that search the index */ 
	protected HashSet<String> mySearchHosts;
	/** If this hosts searches the index */
	protected boolean mySearch;
	
	/** Database name, e.g. for entest.mainpart, it's entest */
	protected String dbname;
	/** The part after the dot in the name, e.g. for entest.mainpart, it's mainpart */
	protected String part;
	/** The string representation of IndexId, e.g. entest.mainpart */
	protected String dbrole;
	/** The sub-division part, e.g. sub1 in enwiki.mainpart.sub1 */
	protected String subpart;
	
	/** Names of split indexes, e.g. entest.part1, entest.part2 ... */
	protected String[] splitParts;
	/** The number of subindexes in split architecture */
	protected int splitFactor;
	
	/** If true, this machine is an indexer for this index */
	protected boolean myIndex;
	
	protected enum IndexType { SINGLE, MAINSPLIT, SPLIT, NSSPLIT, SPELL, LINKS, RELATED, PREFIX, PREFIX_TITLES, SUBDIVIDED };
	
	/** the index of this subdivided part */
	protected int subpartNum;
	/** total number of subdivided parts */
	protected int subFactor;
	/** if this index (e.g. enwiki.mainpart) is further subdivided */
	protected boolean furtherSubdivided;
	/** full dbroles of every subpart, e.g. enwiki.nspart1.sub1, enwiki.nspart1.sub2 ..  */
	protected String[] subParts = null;
	
	/** If this is the highlight index, containing the fulltext version of articles */
	protected boolean highlight = false; 
	
	/** Type of index, enumeration */
	protected IndexType type;
	/** Part number in split repestnation, e.g. 1..N */
	protected int partNum;
	
	/** Namespace -> part (for nssplit indexes) */
	protected Hashtable<String,String> nssplitMap;
	
	/** Set of namespaces for this nssplit part */
	protected HashSet<String> namespaceSet;
	
	/** All parameters as they appear in the global conf, e.g. merge factor, optimize, etc.. */
	protected Hashtable<String,String> params;
	
	/** Where the indexer writes the current version of index */
	protected String indexPath;
	/** Where the searcher stores the latest snapshot of index */
	protected String searchPath = null;
	/** Where the indexer places the snapshots */
	protected String snapshotPath;
	/** Path of index updates on searcher */
	protected String updatePath = null;
	/** Build path for indexes from xml import */
	protected String importPath;
	/** Path where status files are for incremental updater */
	protected String statusPath;
	/** Path where transaction data is stored */
	protected String transactionPath;
	/** Path to temporary index (e.g. for rebuilding spell-check words) */
	protected String tempPath;
	
	/** url of OAI repository */
	protected String OAIRepository;
	
	protected String rsyncSnapshotPath = null;
	
	/** language code, e.g. "en" */
	protected String langCode = null;

	/**
	 * Get index Id object given it's string representation, the actual object
	 * is pulled out of the GlobalConfigurations prepopulated pool of all possible
	 * index Ids
	 * 
	 * @param dbrole
	 * @return
	 */
	static public IndexId get(String dbrole){
		IndexId ret = GlobalConfiguration.getIndexId(dbrole);
		if(ret == null)
			throw new RuntimeException("Index "+dbrole+" doesn't exist");
		return ret;
	}
		
	/**
	 * Construct new IndexId given all the information that is to be 
	 * known about this index location
	 * 
	 * @param dbrole   string repesentation of IndexId, e.g. entest.mainpart
	 * @param type     type, e.g. mainsplit
	 * @param indexHost    where the indexer is
	 * @param indexRsyncPath    the rsync base of path
	 * @param typeParams    parameters of type, e.g. split factor
	 * @param params    parameters of this index, e.g. merge factor, optimize, .. 
	 * @param searchHosts    all hosts that search this index
	 * @param mySearchHosts   searcher hosts within this machines group
	 * @param localIndexPath   the path of local indexes  
	 * @param myIndex    if this machines is an indexer for this index
	 * @param mySearch   if this machine is a searcher for this index
	 */
	public IndexId(String dbrole, String type, String indexHost, String indexRsyncPath, 
			Hashtable<String, String> typeParams, Hashtable<String, String> params, 
			HashSet<String> searchHosts, HashSet<String> mySearchHosts, String localIndexPath, 
			boolean myIndex, boolean mySearch, String OAIRepository, boolean isSubdivided) {
		final String sep = Configuration.PATH_SEP;
		this.indexHost = indexHost;
		if(!indexRsyncPath.endsWith("/"))
			indexRsyncPath += "/";
		if(!indexRsyncPath.startsWith("/"))
			indexRsyncPath = "/" + indexRsyncPath;
		if(localIndexPath!= null && !localIndexPath.endsWith(sep))
			localIndexPath += sep;
		this.indexRsyncPath = indexRsyncPath;
		this.typeString = type;
		this.typeParams = typeParams;
		this.searchHosts = searchHosts;
		this.mySearchHosts = mySearchHosts;
		this.myIndex = myIndex;
		this.mySearch = mySearch;
		this.dbrole = dbrole;
		this.params = params;
		this.OAIRepository = OAIRepository;
		this.furtherSubdivided = isSubdivided;
		
		// types
		if(type.equals("single"))
			this.type = IndexType.SINGLE;
		else if(type.equals("mainsplit"))
			this.type = IndexType.MAINSPLIT;
		else if(type.equals("split"))
			this.type = IndexType.SPLIT;
		else if(type.equals("nssplit"))
			this.type = IndexType.NSSPLIT;
		else if(type.equals("spell"))
			this.type = IndexType.SPELL;
		else if(type.equals("links"))
			this.type = IndexType.LINKS;
		else if(type.equals("related"))
			this.type = IndexType.RELATED;
		else if(type.equals("prefix"))
			this.type = IndexType.PREFIX;
		else if(type.equals("prefix_titles"))
			this.type = IndexType.PREFIX_TITLES;
		else if(type.equals("subdivided"))
			this.type = IndexType.SUBDIVIDED;
		
		// highlight index
		String suffix = "";
		if(dbrole.endsWith(".hl")){
			highlight = true;
			dbrole = dbrole.substring(0,dbrole.lastIndexOf('.'));
			suffix = ".hl";
		}
		
		// parts
		part = null;
		subpart = null;
		subpartNum = -1;
		String[] parts = dbrole.split("\\.");
		dbname = parts[0];		
		if(parts.length==2)
			part = parts[1];
		else if(parts.length==3 && this.type == IndexType.SUBDIVIDED){
			part = parts[1];
			subpart = parts[2];
			subpartNum = Integer.parseInt(subpart.substring(3));
		}
		
		partNum = -1;
		splitParts = null;
		// split part names
		if(this.type == IndexType.MAINSPLIT){
			splitParts = new String[2];
			splitParts[0] = dbname+".mainpart"+suffix;
			splitParts[1] = dbname+".restpart"+suffix;
			
			if(part == null);
			else if(part.equalsIgnoreCase("mainpart"))
				partNum = 0;
			else if(part.equalsIgnoreCase("restpart"))
				partNum = 1;
		} else if(this.type == IndexType.SPLIT){
			splitFactor = Integer.parseInt(typeParams.get("number"));
			splitParts = new String[splitFactor];
			for(int i=0;i<splitFactor;i++)
				splitParts[i] = dbname+".part"+(i+1)+suffix;
			if(part != null)
				partNum = Integer.parseInt(part.substring(4));
			else
				partNum = 0;
		} else if(this.type == IndexType.NSSPLIT){
			splitFactor = Integer.parseInt(typeParams.get("number"));
			splitParts = new String[splitFactor];
			for(int i=0;i<splitFactor;i++)
				splitParts[i] = dbname+".nspart"+(i+1)+suffix;			
			if(part!=null){
				partNum = Integer.parseInt(part.substring(6));
				namespaceSet = new HashSet<String>();
				String[] nss = params.get("namespaces").split(",");
				for(String ns : nss)
					namespaceSet.add(ns.trim());	 
			} else
				partNum = 0;
		} 
		
		// handle subdivided indexes
		if(isSubdivided){
			subFactor = Integer.parseInt(params.get("subdivisions"));
			subParts = new String[subFactor];
			for(int i=1;i<=subFactor;i++)
				subParts[i-1] = dbname+"."+part+".sub"+i+suffix;
			if(this.type == IndexType.SUBDIVIDED)
				this.furtherSubdivided = false; // cannot futher subdivide
			else
				this.furtherSubdivided = true;
		}
		
		// for split/mainsplit the main iid is logical, it doesn't have local path
		if(myIndex 
				&& !(part == null && (this.type==IndexType.SPLIT || this.type==IndexType.MAINSPLIT || this.type==IndexType.NSSPLIT))
				&& !furtherSubdivided){
			indexPath = localIndexPath + "index" + sep + this.dbrole;
			importPath = localIndexPath + "import" + sep + this.dbrole;
			snapshotPath = localIndexPath + "snapshot" + sep + this.dbrole;
		} else{
			indexPath = null;
			importPath = null;
			snapshotPath = null;
		}
		
		rsyncSnapshotPath = indexRsyncPath+"snapshot/" + this.dbrole;
		statusPath = localIndexPath + "status" + sep + this.dbrole;
		transactionPath = localIndexPath + "transaction" + sep + this.dbrole;
		tempPath = localIndexPath + "temp" + sep + this.dbrole;

		if(mySearch){
			searchPath = localIndexPath + "search" + sep + this.dbrole;
			updatePath = localIndexPath + "update" + sep + this.dbrole;
		} else{
			searchPath = null;
			updatePath = null;
		}
		
	}
	
	/** If this is logical name referring to a set of split indexes */
	public boolean isLogical(){
		return (part == null && type != IndexType.SINGLE) || (subpart==null && furtherSubdivided);
	}	
	/** If type of this index is single */
	public boolean isSingle(){
		return type == IndexType.SINGLE;
	}
	/** If type of this index is mainsplit */
	public boolean isMainsplit(){
		return type == IndexType.MAINSPLIT;
	}
	/** If type of this index is split */
	public boolean isSplit(){
		return type == IndexType.SPLIT;
	}
	/** If type of this index is mainsplit */
	public boolean isNssplit(){
		return type == IndexType.NSSPLIT;
	}
	/** If this is the spell-check index */
	public boolean isSpell(){
		return type == IndexType.SPELL;
	}
	/** If this is the index storing pagelinks */
	public boolean isLinks(){
		return type == IndexType.LINKS;
	}
	/** If this is the index storing info about related articles */
	public boolean isRelated(){
		return type == IndexType.RELATED;
	}
	/** If this is the index storing article list for specific prefixes */
	public boolean isPrefix(){
		return type == IndexType.PREFIX;
	}
	/** If this is the index storing titles for the prefix index */
	public boolean isPrefixTitles(){
		return type == IndexType.PREFIX_TITLES;
	}	
	/** If this is the index storing titles for the prefix index */
	public boolean isSubdivided(){
		return type == IndexType.SUBDIVIDED;
	}
	
	/** If this is a split index, returns the current part number, e.g. for entest.part4 will return 4 */
	public int getPartNum() {
		if(type == IndexType.SPLIT || type == IndexType.NSSPLIT || type == IndexType.MAINSPLIT)
			return partNum;
		else{
			log.error("Called getPartNum() on non-split object! Probably a bug in the code.");
			return 0;
		}
	}
	/** Get parameters of index, e.g. "mergeFactor", "maxBufDocs", "optimize" */
	public String getParam(String id){
		return params.get(id);
	}
	/** Get an int parameters, similar to getParam, but assumes int return value */
	public int getIntParam(String id, int defaultValue){
		String num = params.get(id);
		if(num == null)
			return defaultValue;
		try{
			int n = Integer.parseInt(num);
			return n;
		} catch(Exception e){
			return defaultValue;
		}
	}
	/** Get an int parameters, similar to getParam, but assumes boolean return value */
	public boolean getBooleanParam(String id, boolean defaultValue){
		String num = params.get(id);
		if(num == null)
			return defaultValue;
		try{
			boolean b = Boolean.parseBoolean(num);
			return b;
		} catch(Exception e){
			return defaultValue;
		}
	}
	
	/** Get if this is mainpart of mainsplit index */
	public boolean isMainPart(){
		return type == IndexType.MAINSPLIT && partNum == 0;
	}
	
	/** Get if this is restpart of mainsplit index */
	public boolean isRestPart(){
		return type == IndexType.MAINSPLIT && partNum == 1;
	}

	
	@Override
	public String toString() {
		return dbrole;
	}

	/** If this is the index containing higlighing info */
	public boolean isHighlight(){
		return highlight;
	}
	/** If this index (e.g. mainpart) has been subdivided into smaller indexes */
	public boolean isFurtherSubdivided(){
		return furtherSubdivided;
	}	
	/** Get textual repesentation of subdivided part, e.g. sub1 */
	public String getSubpartString(){
		return subpart;
	}
	/** Get number of this index in array of subivisions, 1..subFactor */
	public int getSubpartNum(){
		return subpartNum;
	}
	/** Return the number of subdivided parts */
	public int getSubdivisionFactor(){
		return subFactor;
	}
	/** Get string representations of all subparts */
	public String[] getSubparts(){
		return subParts;
	}
	/** Get iid of subpart with index i */
	public IndexId getSubpart(int i){
		return get(subParts[i-1]);
	}
	
	/** Get IndexId for the mainpart of mainsplit index */
	public IndexId getMainPart(){
		if(type == IndexType.MAINSPLIT)
			return get(splitParts[0]);
		else{
			log.error("Called getMainPart() on non-mainsplit object! Probably a bug in the code.");
			return null;
		}
	}
	/** Get IndexId for the restpart of mainsplit index */
	public IndexId getRestPart(){
		if(type == IndexType.MAINSPLIT)
			return get(splitParts[1]);
		else{
			log.error("Called getRestPart() on non-mainsplit object! Probably a bug in the code.");
			return null;
		}
	}
	/** Get snapshot path in default rsync module "mwsearch" */
	public String getRsyncSnapshotPath() {
		return rsyncSnapshotPath;
	}

	/** Where the indexer writes the current version of index */
	public String getIndexPath() {
		return indexPath;
	}
	/** Where the searcher stores the latest snapshot of index */
	public String getSearchPath() {
		return searchPath;
	}
	/** Where the indexer places the snapshots */
	public String getSnapshotPath() {
		return snapshotPath;
	}
	/** Where the searcher fetches the snapshot via rsync */
	public String getUpdatePath() {
		return updatePath;
	}
	/** Where indexes are made when built from XML importing */
	public String getImportPath() {
		return importPath;
	}
	/** Where transaction data is stored */
	public String getTransactionPath() {
		return transactionPath;
	}
	/** Status file for incremental updater */ 
	public String getStatusPath() {
		return statusPath;
	}
	/** Path for temporary indexes */ 
	public String getTempPath() {
		return tempPath;
	}

	/** Get search path with resolved symlinks */
	public String getCanonicalSearchPath(){
		try {
			return new File(searchPath).getCanonicalPath();
		} catch (IOException e) {
			return searchPath;
		}
	}
	
	/** Get IndexId for part of the split index. Parts are indexed 1...N */
	public IndexId getPart(int index){
		return get(splitParts[index-1]);
	}
	
	/** Get database name, e.g. for entest.mainpart, it's entest */
	public String getDBname() {
		return dbname;
	}

	/** Where the indexer is */
	public String getIndexHost() {
		return indexHost;
	}

	/** If current host is an indexer for this index */
	public boolean isMyIndex() {
		return myIndex;
	}

	/** all hosts that search this index */
	public HashSet<String> getSearchHosts() {
		return searchHosts;
	}
	
	protected void addToHosts(HashSet<String> hosts, String[] splitParts){
		for(String splitpart : splitParts){
			if(get(splitpart)!=null)
				hosts.addAll(get(splitpart).getSearchHosts());
		}
	}
	
	/** get all hosts that search db this iid belongs to */
	public HashSet<String> getDBSearchHosts(){
		if(isSingle() || isSpell() || isLinks() || isRelated() || isPrefix() || isPrefixTitles())
			return searchHosts;
		else{
			// add all hosts that search: dbname and all parts
			HashSet<String> hosts = new HashSet<String>();
			if(get(dbname)!=null)
				hosts.addAll(get(dbname).getSearchHosts());
			if(splitParts != null)
				addToHosts(hosts,splitParts);
			if(subParts != null)
				addToHosts(hosts,subParts);
			
			return hosts;
		}
	}
	
	/** searcher hosts within this machines group */
	public HashSet<String> getMySearchHosts() {
		return mySearchHosts;
	}
	/** Number of subindexes in split architecture */
	public int getSplitFactor() {
		return splitFactor;
	}
	/** Get names of split parts, e.g. entest.part1, entest.part2 .. */
	public String[] getSplitParts() {
		return splitParts;
	}
	/** Get string representation of type, e.g. "single" */
	public String getType() {
		return typeString;
	}
	/** Get parameters associated with type, e.g. number of subindexes for split indexes */
	public Hashtable<String, String> getTypeParams() {
		return typeParams;
	}
	/** If this host is searching this index */
	public boolean isMySearch() {
		return mySearch;
	}
	/** Get base URL of OAI repository */
	public String getOAIRepository() {
		return OAIRepository;
	}
	
	/** 
	 * Get all indexes parts for this iid except for logical names.
	 * I.e. for db of kind mainsplit, it will return db.mainpart, db.restpart
	 * @return
	 */
	public HashSet<String> getPhysicalIndexes() {
		HashSet<String> ret = new HashSet<String>();
		if(isSingle() || isSpell() || isLinks() || isRelated() || isPrefix() || isPrefixTitles())
			ret.add(dbrole);
		else if(isMainsplit() || isSplit() || isNssplit()){
			String[] parts = furtherSubdivided? subParts : splitParts;
			for(String p : parts){
				IndexId ip = IndexId.get(p);
				if(ip.isFurtherSubdivided())
					ret.addAll(ip.getPhysicalIndexes());
				else
					ret.add(p);
			}
		}
		
		return ret;
	}
	
	/**
	 * Wrapper for getPhysicalIndexes to get iid objects
	 * 
	 * @return
	 */
	public ArrayList<IndexId> getPhysicalIndexIds(){
		HashSet<String> physical = getPhysicalIndexes();
		ArrayList<IndexId> parts = new ArrayList<IndexId>();
		for(String p : physical)
			parts.add(get(p));
		return parts;		
	}

	/** Rebuild namespace map from information, call only when sure that iid's for all parts are constructed.
	 *  Note: always call on main iid, not parts */
	public void rebuildNsMap(Hashtable<String,IndexId> pool) {
		if(isNssplit() && part==null){
			// rebuild
			nssplitMap = new Hashtable<String,String>();
			for(String part : splitParts){
				for(String ns : pool.get(part).namespaceSet){
					nssplitMap.put(ns,part);
				}
			}
			// set on all parts as well
			for(String part : splitParts){
				pool.get(part).nssplitMap = nssplitMap;
			}
		}		
	}	
	
	public IndexId getPartByNamespace(int ns){
		return getPartByNamespace(Integer.toString(ns));
	}
	
	/** If this is nssplit/mainsplit index, get part with certain namespace */
	public IndexId getPartByNamespace(String ns){
		if(isNssplit()){
			String dbrole = nssplitMap.get(ns);
			if(dbrole == null)
				dbrole = nssplitMap.get("<default>");
			return get(dbrole);
		} else if(isMainsplit()){
			if(ns.equals("0"))
				return getMainPart();
			else
				return getRestPart();
		} else
			return this;
	}

	/** Return the set of namespaces which are searched by this nssplit part */
	public HashSet<String> getNamespaceSet() {
		return namespaceSet;
	}
	
	/** Get iid for dbname of this index, i.e. enwiki.mainpart -> enwiki */
	public IndexId getDB(){
		return get(dbname);
	}

	/** Get the coresponding spell words iid */
	public IndexId getSpell() {
		return get(dbname+".spell");
	}
	
	/** Get the pagelinks iid */
	public IndexId getLinks() {
		return get(dbname+".links");
	}
	
	/** Get the related-articles index iid */
	public IndexId getRelated() {
		return get(dbname+".related");
	}
	
	/** Get the prefix index iid */
	public IndexId getPrefix() {
		return get(dbname+".prefix");
	}
	
	/** Get the prefix titles index iid */
	public IndexId getPrefixTitles() {
		return get(dbname+".prefix_titles");
	}
	
	/** Get the higlight index for this iid */
	public IndexId getHighlight() {
		if(highlight)
			return get(dbrole);
		else
			return get(dbrole+".hl");
	}
	
	/** Get language code for this db, e.g. "en" */
	public String getLangCode(){
		if(langCode == null)
			langCode = GlobalConfiguration.getInstance().getLanguage(dbname);
		return langCode;
	}
	
		
}
