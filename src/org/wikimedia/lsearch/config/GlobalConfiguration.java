/*
 * Created on Feb 9, 2007
 *
 */
package org.wikimedia.lsearch.config;

import java.io.BufferedReader;
import java.io.ByteArrayInputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.StringReader;
import java.net.Inet4Address;
import java.net.InetAddress;
import java.net.MalformedURLException;
import java.net.NetworkInterface;
import java.net.URL;
import java.net.UnknownHostException;
import java.text.MessageFormat;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Enumeration;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Properties;
import java.util.Set;
import java.util.Map.Entry;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.util.PHPParser;

/**
 * Read and parse the global configuration file, is also used
 * to discover where the indexes are. Global configuration manages
 * a pool of global readonly {@link IndexId} instances. 
 * 
 * @author rainman
 *
 */
public class GlobalConfiguration {
	/** The following hashtables are directly read from the config file: */
	/** dbname -> hashtable ( role -> hashtable ( param => value) ) */
	protected Hashtable<String, Hashtable<String, Hashtable<String, String>>> database;
	/** group -> host -> arraylist ( db.role ) */ 
	protected Hashtable<Integer,Hashtable<String,ArrayList<String>>> searchGroup;
	/** host -> arraylist ( db.role ) */
	protected Hashtable<String,ArrayList<String>> search;
	/** dbname@host -> group */
	protected Hashtable<String,Integer> databaseGroup;
	/** host -> arraylist ( db.role) */
	protected Hashtable<String,ArrayList<String>> index;
	/** dbrole -> host */
	protected Hashtable<String,String> indexLocation;
	/** host -> path */
	protected Hashtable<String,String> indexRsyncPath;
	/** path where index are locally stored */
	protected String indexPath;
	/** prefixes, e.g. main, talk, help, and corresponding filters */
	protected Hashtable<String,NamespaceFilter> namespacePrefix;
	/** keyword for all namespaces (i.e. no filtering) */
	protected String namespacePrefixAll; 
	/** suffx -> OAI Repo url pattern */
	protected Hashtable<String,String> oaiRepo;
	/** wgLanguageCode from InitialiseSettings, suffix -> lang code */
	protected Hashtable<String,String> wgLanguageCode = null;
	/** wgServer, suffix -> server (default server is "default")*/
	protected Hashtable<String,String> wgServer = null;
	/** wgNamespacesToBeSearchedDefault from InitialiseSettings, suffix -> lang code */
	protected Hashtable<String,NamespaceFilter> wgDefaultSearch = null;
	/** for each database suffix, it's interwiki (suffix -> iw), e.g. wiki -> w */
	protected Hashtable<String,String> suffixIwMap = new Hashtable<String,String>();
	
	/** info about this host */
	protected static InetAddress myHost;
	protected static String hostAddr, hostName;	
	
	/** Database suffix if dbname, the rest is supposed to be language, e.g srwiki => (suffix wiki) => sr */
	protected String[] databaseSuffixes = null;	
	/** Databases ending in suffix will use additional keyword scores */
	protected String[] keywordScoringSuffixes = null;
	/** Databases ending in suffix will have 2 indexes, one with lowercased words, and one with exact case words */
	protected String[] exactCaseSuffix = null;
	
	protected Properties globalProperties = null;
	
	/** All identifiers of all indexes (dbrole -> IndexId) */
	protected static Hashtable<String,IndexId> indexIdPool = new Hashtable<String,IndexId>();
	
	protected static GlobalConfiguration instance = null;
	
	/** Wether to report warnings and info */
	protected static boolean verbose = true;

	/**
	 * Use this function to override the hosts IP address which 
	 * is determined automatically when first instance is made  
	 * 
	 * @param host  IP adress
	 */
	public static void setHost(InetAddress host){
		myHost = host;
		hostAddr = myHost.getHostAddress();
		hostName = myHost.getHostName();
	}
	
	protected GlobalConfiguration(){
		// try to determin this hosts IP address
		determineInetAddress();
	}	
	
	/**
	 * Get singleton instance of this class
	 * 
	 * @return
	 */
	synchronized public static GlobalConfiguration getInstance() {
		if (instance == null)
			instance = new GlobalConfiguration();
		return instance;
	}
	
	/**
	 * Try to determine the (non-loopback) IP address of this 
	 * computer. Will work only if there is one attached 
	 * network interface, otherwise @link setHost() method
	 * need to be used 
	 */
	protected void determineInetAddress() {
		try {
			setHost(Inet4Address.getLocalHost());
		} catch (UnknownHostException e1) {
			System.out.println("Error resolving local hostname. Make sure that hostname is setup correctly.");
			e1.printStackTrace();
		}		
	}
	
	/** Return true if host is the current host (IP or hostname) */
	public boolean isLocalhost(String host){
		if(host == null)
			return false;
		return host.equalsIgnoreCase(hostAddr) || host.equalsIgnoreCase(hostName);
	}
	
	/** Secure add-to-list, check if the index definition exists, and avoid duplicates in list */
	protected void addToList(ArrayList<String> list, String str){
		if(!list.contains(str)){
			String[] parts = str.split("\\.");
			if(database.containsKey(parts[0]) && 
				(parts.length==1 || (parts.length==2 && database.get(parts[0]).containsKey(parts[1])))){
				list.add(str);
			}
		}
	}
	
	@SuppressWarnings("unchecked")
	protected void checkSubdivisions(String dbname, String type){
		Hashtable<String,Hashtable<String,String>> typeParams = database.get(dbname);
		Hashtable<String,String> params = typeParams.get(type);
		String subdiv = params.get("subdivisions");
		if(subdiv != null){
			int count = Integer.parseInt(subdiv);
			for(int i=1;i<=count;i++)
				typeParams.put(type+".sub"+i,(Hashtable<String, String>) params.clone());
		}
	}
	
	/** 
	 * Check if the setup is correct,i.e. there is indexer and searcher
	 * for each db  ...
	 *   
	 * @return returns true if the setup is OK
	 */
	public boolean checkIntegrity(){
		// check for default path
		if(indexRsyncPath.get("<default>") == null){
			System.out.println("ERROR in GlobalConfiguration: Default path for index absent. Check section [Index-Path].");
			return false;
		}
		// for each DB check if the corresponding parts are defined
		// if not, put them in with default values
		for(String dbname : database.keySet()){
			if(dbname.startsWith("<"))
				continue; // special keywords, like <all>
			Set<String> types = database.get(dbname).keySet();
			if(types.contains("mainsplit")){
				if(!types.contains("mainpart"))
					database.get(dbname).put("mainpart",new Hashtable<String,String>());
				if(!types.contains("restpart"))
					database.get(dbname).put("restpart",new Hashtable<String,String>());
				checkSubdivisions(dbname,"mainpart");
				checkSubdivisions(dbname,"restpart");
			} else if(types.contains("split")){
				int factor = Integer.parseInt(database.get(dbname).get("split").get("number"));
				for(int i=1;i<factor+1;i++){
					String dbpart = "part"+i;
					if(!types.contains(dbpart))
						database.get(dbname).put(dbpart,new Hashtable<String,String>());
					checkSubdivisions(dbname,dbpart);
				}
			} else if(types.contains("nssplit")){
				int factor = Integer.parseInt(database.get(dbname).get("nssplit").get("number"));
				for(int i=1;i<factor+1;i++){
					String dbpart = "nspart"+i;
					if(!types.contains(dbpart))
						database.get(dbname).put(dbpart,new Hashtable<String,String>());
					checkSubdivisions(dbname,dbpart);
				}
			}
			// add the link analysis to indexers
			if(!types.contains("links"))
				database.get(dbname).put("links",new Hashtable<String,String>());
			if(!types.contains("related"))
				database.get(dbname).put("related",new Hashtable<String,String>());
			if(!types.contains("prefix_titles"))
				database.get(dbname).put("prefix_titles",new Hashtable<String,String>());
		}
		// expand logical index names on searchers
		for(String host : search.keySet()){
			ArrayList<String> hostsearch = search.get(host);
			for(String dbname : hostsearch.toArray(new String[]{})){				
				Hashtable<String, Hashtable<String,String>> types = database.get(dbname);
				if(types != null){ // if not null, dbrole is dbname
					if(types.containsKey("mainsplit")){
						addToList(hostsearch,dbname+".mainpart");
						addToList(hostsearch,dbname+".restpart");
					} else if(types.containsKey("split")){
						int factor = Integer.parseInt(database.get(dbname).get("split").get("number"));
						for(int i=1;i<=factor;i++)
							addToList(hostsearch,dbname+".part"+i);
					} else if(types.containsKey("nssplit")){
						int factor = Integer.parseInt(database.get(dbname).get("nssplit").get("number"));
						for(int i=1;i<=factor;i++)
							addToList(hostsearch,dbname+".nspart"+i);
					}
				}
				// spell check indexes are searched by default if they exist
				addToList(hostsearch,dbname+".spell");
			}
		}

		// check if every db.type has an indexer and searcher
		for(String dbname : database.keySet()){
			for(String typeid : database.get(dbname).keySet()){
				String type = "";
				String dbrole = "";
				if(typeid.equals("single") || typeid.equals("mainsplit") || typeid.equals("split") || typeid.equals("nssplit")){
					type = typeid;
					dbrole = dbname;
				} else if(typeid.equals("mainpart") || typeid.equals("restpart")){
					type = "mainsplit";
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches("part[1-9][0-9]*")){
					type = "split";
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches("nspart[1-9][0-9]*")){
					type = "nssplit";
					dbrole = dbname + "." + typeid;
				} else if(typeid.equals("spell") || typeid.equals("links") || typeid.equals("related") || typeid.equals("prefix")  || typeid.equals("prefix_titles")){
					type = typeid;
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches(".*?\\.sub[0-9]+")){
					type = "sub";
					dbrole = dbname + "." + typeid;
				} else
					continue; // uknown type, skip
				
				if(indexLocation.get(dbrole) == null){
					// fill-in with host that indexes dbname
					if(indexLocation.get(dbname) != null ){
						String host = indexLocation.get(dbname);
						indexLocation.put(dbrole,host);
						index.get(host).add(dbrole);
					} /*else{
						System.out.println("ERROR in Global Configuration: index "+dbrole+" does not have an index host.");
						return false;
					} */
					// add same index location for highlight .hl index
					/* String host = indexLocation.get(dbrole); 
					indexLocation.put(dbrole+".hl",host);
					index.get(host).add(dbrole+".hl"); */
					
				}
				boolean searched = (getSearchHosts(dbrole).size() != 0); 
				if(!searched && !(typeid.equals("mainsplit") || typeid.equals("split") 
						|| typeid.equals("nssplit") || typeid.equals("links") || typeid.equals("related") || typeid.equals("prefix_titles"))){
					if(verbose)
						System.out.println("WARNING: in Global Configuration: index "+dbrole+" is not searched by any host.");
				}
			}
		}
		return true;
	}
	
	/** 
	 * Read a config file from a given URL
	 * 
	 * @param url
	 * @throws IOException
	 */
	public void readFromURL(URL url, String indexpath) throws IOException{
		BufferedReader in;
		try {
			in = new BufferedReader(
					new InputStreamReader(
					url.openStream()));
			read(in,indexpath);
		} catch (IOException e) {
			System.out.println("I/O Error in opening or reading global config at url "+url);
			throw e;
		}		
	}

	/**
	 * Prepare hashtables to load data into them
	 * 
	 */
	protected void init(){
		database = new Hashtable<String, Hashtable<String, Hashtable<String, String>>>();		
		searchGroup = new Hashtable<Integer,Hashtable<String, ArrayList<String>>>();
		search = new Hashtable<String, ArrayList<String>>();
		databaseGroup = new Hashtable<String,Integer>();
		index = new Hashtable<String, ArrayList<String>>();
		indexLocation = new Hashtable<String, String>();
		indexRsyncPath = new Hashtable<String, String>();
		namespacePrefix = new Hashtable<String,NamespaceFilter>();
		namespacePrefixAll = "all"; // default
		oaiRepo = new Hashtable<String,String>();
	}
	
	protected String[] getArrayProperty(String name){
		String s = globalProperties.getProperty(name);
		if (s != null)
			return s.split(" ");
		return null;
	}
	
	/** 
	 * Reads a config file from a bufferedreader, will
	 * close the reader when done.
	 *  
	 * @param in   opened reader
	 * @throws IOException
	 */
	protected void read(BufferedReader in, String indexpath) throws IOException{
		String line="";		
		int section = -1; 
		Pattern roleRegexp = Pattern.compile("\\((.*?)\\)");
		int lineNum = 0;
		// sections
		final int DATABASE = 0;
		final int INDEX = 1;
		final int SEARCH = 2;
		final int INDEXPATH = 3;
		final int NAMESPACE_PREFIX = 4;
		final int OAI = 5;
		
		int searchGroupNum = -1;
		
		init();
		this.indexPath = indexpath;
		
		while((line = in.readLine()) != null){
			lineNum ++;		
			if(line.startsWith("#")) // comment, skip
				continue; 
			
			if(line.trim().equals(""))
				continue; 
			
			line = preprocessLine(line);
			
			if(line.startsWith("[") && line.length()>2 && !Character.isDigit(line.charAt(1))){ // section
				int last = line.indexOf("]");
				String s = line.substring(1,last);
				
				if(s.equalsIgnoreCase("properties")){
					globalProperties = new Properties();
					StringBuilder prop = new StringBuilder(line+"\n");
					while((line = in.readLine()) != null){
						if(line.startsWith("[") && line.length()>2 && !Character.isDigit(line.charAt(1)))
							break;
						prop.append(line);
						prop.append("\n");
					}					
					globalProperties.load(new ByteArrayInputStream(prop.toString().getBytes("utf-8")));
					// get some predifined global properties
					this.databaseSuffixes = getArrayProperty("Database.suffix");
					this.keywordScoringSuffixes = getArrayProperty("KeywordScoring.suffix");
					this.exactCaseSuffix = getArrayProperty("ExactCase.suffix");
					// try reading intialisesettings
					String initset = globalProperties.getProperty("WMF.InitialiseSettings");
					if(initset != null)
						initializeWmfSettings(initset);
					if(line == null)
						break;
					// else: line points to beginning of next section
					last = line.indexOf("]");
					s = line.substring(1,last);
				}
				
				if(s.equalsIgnoreCase("database"))
					section = DATABASE;
				else if(s.equalsIgnoreCase("index"))
					section = INDEX;
				else if(s.equalsIgnoreCase("search-group")){
					section = SEARCH;
					searchGroupNum++;
				} else if(s.equalsIgnoreCase("index-path"))
					section = INDEXPATH;
				else if(s.equalsIgnoreCase("namespace-prefix"))
					section = NAMESPACE_PREFIX;
				else if(s.equalsIgnoreCase("oai"))
					section = OAI;
			} else if(section==-1 && !line.trim().equals("")){
				if(verbose)
					System.out.println("Ignoring a line up to first section heading...");
			} else if(section == DATABASE){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;				
				String[] dbs = parts[0].split(",");
				for(int i=0;i<dbs.length;i++) dbs[i]=dbs[i].trim();
				
				// syntax: dbname : (role,params), (role2,params2) 				
				Matcher matcher = roleRegexp.matcher(parts[1]);
				while(matcher.find()){
					processDBRole(dbs,matcher.group(1));					
				}
			} else if(section == SEARCH){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;
				String host = parts[0].trim();
				
				processSearchRoles( host, parts[1], searchGroupNum);
			} else if(section == INDEX){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;
				String host = parts[0].trim();
				
				processIndexRoles(host,parts[1]);
			} else if(section == INDEXPATH){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;
				String host = parts[0].trim();
				String path = parts[1].trim();
				
				if(indexRsyncPath.get(host)!=null && verbose)
					System.out.println("Warning: repeated path definition for host "+host+" on line "+lineNum+", overwriting old.");
				indexRsyncPath.put(host,path);
			} else if(section == NAMESPACE_PREFIX){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;
				String prefix = parts[0].trim();
				String filter = parts[1].trim();				
				
				if(filter.equalsIgnoreCase("<all>"))
					namespacePrefixAll = prefix;
				else
					namespacePrefix.put(prefix,new NamespaceFilter(filter));				
			} else if(section == OAI){
				String[] parts = splitBySemicolon(line,lineNum);
				if(parts == null) continue;
				String suffix = parts[0].trim();
				String url = parts[1].trim();				
				
				oaiRepo.put(suffix,url);
			}
		}
		if( !checkIntegrity() ){
			in.close();
			System.exit(1);
		}
		
		makeIndexIdPool();
		in.close();
	}
	
	/**
	 * A bit hackish: read InitialiseSettings which we know have a certain
	 * format to avoid maintaining two copies for config files (one in php
	 * other for lsearch in global conf)
	 * 
	 * @param initset
	 */
	protected void initializeWmfSettings(String initset) {
		try {
			PHPParser parser = new PHPParser();
			String text = parser.readURL(new URL(initset));
			wgLanguageCode = parser.getLanguages(text);
			wgServer = parser.getServer(text);
			wgDefaultSearch = parser.getDefaultSearch(text);
		} catch (IOException e) {
			System.out.println("Error: Cannot read InitialiseSettings.php from url "+initset+" : "+e.getMessage());
		}	
	}

	/** Get all hosts which search this inxedId (dbrole) */
	protected HashSet<String> getSearchHosts(String dbrole){
		HashSet<String> searchHosts = new HashSet<String>();
		for(String host : search.keySet()){
			if(search.get(host).contains(dbrole))
				searchHosts.add(host);
		}
		return searchHosts;
	}
	
	/** Get all hosts that search this dbname within current hosts search groups */
	protected HashSet<String> getMySearchHosts(String dbname, String dbrole){
		HashSet<String> searchHosts = new HashSet<String>();
		Integer group = databaseGroup.get(dbname+"@"+hostAddr);
		if(group == null)
			group = databaseGroup.get(dbname+"@"+hostName);
		if(group == null)
			return searchHosts;
		
		for(String host : searchGroup.get(group).keySet()){
			if(search.get(host).contains(dbrole))
				searchHosts.add(host);
		}
		return searchHosts;
	}
	
	/**
	 * Call after all data is read from config file, make indexIds for all the 
	 * indexes in the system
	 *
	 */
	protected void makeIndexIdPool(){
		// get local indexes
		/* ArrayList<String> localIndexes = index.get(hostAddr);
		if(localIndexes == null)
			localIndexes = new ArrayList<String>();
		if(index.get(hostName)!=null)
			localIndexes.addAll(index.get(hostName)); */
		
		// dbname -> ts index, e.g. enwiki -> en.tspart1
		HashMap<String,String> dbnameTitlesPart = new HashMap<String,String>();
		// dbname -> matched suffix, e.f. enwiki -> wiki
		HashMap<String,String> dbnameSuffix = new HashMap<String,String>();
		// suffix -> part, e.g. wiki -> tspart1
		HashMap<String,String> suffixPart = new HashMap<String,String>();
		int splitFactor = 0;
		// new db names to be created
		HashSet<String> dbnames = new HashSet<String>();
		// db -> language, e.g. en -> en
		HashMap<String,String> dbLang = new HashMap<String,String>();
		if(database.containsKey("<all>")){
			Hashtable<String,Hashtable<String,String>> allKeyword = database.get("<all>");
			splitFactor = Integer.parseInt(allKeyword.get("titles_by_suffix").get("number"));			
			for(int i=1;i<=splitFactor;i++){
				String part = "tspart"+i;
				suffixIwMap.putAll(allKeyword.get(part));
				for(String suffix : allKeyword.get(part).keySet()){
					suffixPart.put(suffix,part);
				}
			}
			// figure out parts for suffixes
			Set<String> suffixes = suffixPart.keySet();
			for(String dbname : database.keySet()){
				if(dbname.startsWith("<"))
					continue;
				for(String s : suffixes){
					if(dbname.endsWith(s)){
						// e.g. en.tspart1
						String db = dbname.substring(0,dbname.lastIndexOf(s));
						String partName = db+"-titles."+suffixPart.get(s);
						dbnameTitlesPart.put(dbname,partName);
						dbnameSuffix.put(dbname,s);
						dbnames.add(db);
						if(!dbLang.containsKey(db))
							dbLang.put(db,getLanguage(dbname));
					}
				}
			}
			// create the new databases
			Hashtable<String,Hashtable<String,String>> params = database.get("<all>");
			for(String db : dbnames){	
				Hashtable<String,Hashtable<String,String>> p = (Hashtable<String, Hashtable<String, String>>) params.clone();
				if(dbLang.containsKey(db)){
					Hashtable<String,String> code = new Hashtable<String,String>();
					code.put("code",dbLang.get(db));
					p.put("language",code);
				}
				database.put(db+"-titles",p);				
			}
		}
		
		// iterate over all dbs and types
		for(String dbname : database.keySet()){
			if(dbname.startsWith("<"))
				continue; // keyword special case
			for(String typeid : database.get(dbname).keySet()){
				String type = "";
				String dbrole = "";				
				if(typeid.equals("single") || typeid.equals("mainsplit") || typeid.equals("split") || typeid.equals("nssplit")){
					type = typeid;
					dbrole = dbname;
				} else if(typeid.equals("mainpart") || typeid.equals("restpart")){
					type = "mainsplit";
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches("part[1-9][0-9]*")){
					type = "split";
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches("nspart[1-9][0-9]*")){
					type = "nssplit";
					dbrole = dbname + "." + typeid;
				} else if(typeid.equals("spell") || typeid.equals("links") || typeid.equals("related") || typeid.equals("prefix") || typeid.equals("prefix_titles")){
					type = typeid;
					dbrole = dbname + "." + typeid;
				} else if(typeid.matches(".*?\\.sub[0-9]+")){
					type = "subdivided";
					dbrole = dbname + "." + typeid;
				} else if(typeid.equals("titles_by_suffix")){
					type = "titles_by_suffix";
					dbrole = dbname;
				} else if (typeid.matches("tspart[1-9][0-9]*")){
					type = "titles_by_suffix";
					dbrole = dbname + "." + typeid;
				} else
					continue; // uknown type, skip
						
				HashSet<String> searchHosts = getSearchHosts(dbrole);
				HashSet<String> mySearchHosts = getMySearchHosts(dbname,dbrole);
				boolean mySearch = searchHosts.contains(hostAddr) || searchHosts.contains(hostName);
				String indexHost = indexLocation.get(dbrole);
				if(indexHost == null)
					indexHost = indexLocation.get(dbname);
				if(indexHost == null)
					indexHost = indexLocation.get("<default>");
				boolean myIndex = hostAddr.equals(indexHost) || hostName.equals(indexHost);
				Hashtable<String,String> typeidParams = database.get(dbname).get(typeid);
				boolean isSubdivided = false;
				if(typeidParams != null && typeidParams.containsKey("subdivisions")){
					isSubdivided = true;
				}
				
				String rsyncIndexPath = "/";
				// if the index is on the local computer search for it
				// using both ip address and host name
				if(myIndex){
					rsyncIndexPath = indexRsyncPath.get(hostAddr);
					if(rsyncIndexPath == null)
						rsyncIndexPath = indexRsyncPath.get(hostName);
					if(rsyncIndexPath == null)
						rsyncIndexPath = indexRsyncPath.get("<default>");	
				} else{
					rsyncIndexPath = indexRsyncPath.get(indexHost);
					if(rsyncIndexPath == null)
						rsyncIndexPath = indexRsyncPath.get("<default>");
				}
				String oairepo = getOAIRepo(dbname);
				
				IndexId iid = new IndexId(dbrole,
						                    type,
						                    indexHost,
						                    rsyncIndexPath,
						                    database.get(dbname).get(type),
						                    typeidParams,
						                    searchHosts,
						                    mySearchHosts,
						                    indexPath,
						                    myIndex,
						                    mySearch,
						                    oairepo,
						                    isSubdivided,
						                    dbnameTitlesPart.get(dbname),
						                    dbnameSuffix.get(dbname));
				indexIdPool.put(dbrole,iid);
				
				if(type.equals("single") || type.equals("mainsplit") || type.equals("split") 
						||	type.equals("nssplit") || type.equals("subdivided")){
					// add highlight index
					dbrole+=".hl";
					searchHosts = getSearchHosts(dbrole);
					mySearchHosts = getMySearchHosts(dbname,dbrole);
					mySearch = searchHosts.contains(hostAddr) || searchHosts.contains(hostName);
					// other options are identical!
					iid = new IndexId(dbrole,
							            type,
							            indexHost,
							            rsyncIndexPath,
							            database.get(dbname).get(type),
							            typeidParams,
							            searchHosts,
							            mySearchHosts,
							            indexPath,
							            myIndex,
							            mySearch,
							            oairepo,
							            isSubdivided,
							            dbnameTitlesPart.get(dbname),
							            dbnameSuffix.get(dbname));
					indexIdPool.put(dbrole,iid);
				}
				
			}
			if(indexIdPool.get(dbname).isNssplit()){
				indexIdPool.get(dbname).rebuildNsMap(indexIdPool);
				indexIdPool.get(dbname+".hl").rebuildNsMap(indexIdPool);
			}
		}		
	}

	/** 
	 * Return the IndexId object given it's id string representation
	 * @param dbrole
	 * @return
	 */
	static public IndexId getIndexId(String dbrole){
		return indexIdPool.get(dbrole);
	}
	
	/**
	 * Substitute {url} with content of url
	 * where url points to list of db's one in each line
	 * 
	 * @param line
	 * @return processed line
	 */
	protected String preprocessLine(String line) {
		Pattern substRegexp = Pattern.compile("\\{(.*?)\\}");

		StringBuffer replaced = new StringBuffer();

		Matcher matcher = substRegexp.matcher(line);
		while(matcher.find()){
			try {
				URL url = new URL(matcher.group(1));
				BufferedReader in = new BufferedReader(
						new InputStreamReader(
						url.openStream()));
				String l;
				StringBuffer text = new StringBuffer();
				while((l=in.readLine()) != null){
					if(text.length()!=0)
						text.append(",");
					l = l.trim();
					if(!l.equals(""))
						text.append(l);
				}
				in.close();
				
				String dbs = text.toString();
				
				matcher.appendReplacement(replaced,dbs);
			} catch (MalformedURLException e) {
				System.out.println("Error in global config file: URL "+matcher.group(1)+" is a malfomed URL");
			} catch (IOException e) {
				System.out.println("Error: cannot read from URL "+matcher.group(1));
			}
		}
		matcher.appendTail(replaced);
		
		return replaced.toString();

	}

	protected String[] splitBySemicolon(String line, int lineNum){
		String[] parts = line.split(":",2);
		if(parts.length!=2){
			System.out.println("Error at line "+lineNum+": semicolon missing. Ignoring this line.");
			return null;
		}
		return parts;
	}
	
	/**
	 * Process a list: db.role, db1.role2... put this list into
	 * index Hashtable, and make a reverse Hashtable indexLocation
	 * @param host
	 * @param roles
	 */
	protected void processIndexRoles(String host, String roles){
		String[] dbroles = roles.split("[, ]+");
		
		ArrayList<String> hostroles = index.get(host);
		if(hostroles == null){
			hostroles = new ArrayList<String>();
			index.put(host,hostroles);
		}
		for(String dbrole : dbroles){	
			dbrole = dbrole.trim();
			if(dbrole.length()==0)
				continue;
			if(indexLocation.get(dbrole) != null){
				if(verbose)
					System.out.println("Warning: index "+dbrole+" located at multiple host, skipping host "+host);
				continue;
			}
			hostroles.add(dbrole);
			indexLocation.put(dbrole,host);					
		}
	}
	
	/**
	 * Process a list: db.role, db1.role2... and put them into 
	 * search and searchGroup hashtables
	 * @param host
	 * @param roles
	 * @param groupNum
	 */
	protected void processSearchRoles(String host, String roles, int groupNum){
		String[] dbroles = roles.split("[, ]+");
		
		// the cummulative search hastable
		ArrayList<String> hostroles = search.get(host);
		if(hostroles == null){
			hostroles = new ArrayList<String>();
			search.put(host,hostroles);
		}
		
		// process groups
		Integer grp = new Integer(groupNum);
		Hashtable<String,ArrayList<String>> grouphosts = searchGroup.get(grp);
		if(grouphosts == null){
			grouphosts = new Hashtable<String,ArrayList<String>>();
			searchGroup.put(grp,grouphosts);
		}
		
		ArrayList<String> grouproles = grouphosts.get(host);
		if(grouproles == null){
			grouproles = new ArrayList<String>();
			grouphosts.put(host,grouproles);
		}
		
		// add to both lists
		for(String dbrole : dbroles){
			dbrole = dbrole.trim();
			if(dbrole.length()==0)
				continue;
			String name = dbrole.split("\\.")[0]+"@"+host;
			Integer group;
			if((group = databaseGroup.get(name)) != null){
				if(!group.equals(grp) && verbose){
					System.out.println("Warning: Database "+name+" was previously defined in search group "+group+". Overriding with group "+grp);
				}
			}
			databaseGroup.put(name,grp);
			hostroles.add(dbrole);
			grouproles.add(dbrole);
		}
	}
	
	/**
	 * Process roles in format type,param1,param2... and put
	 * them into the database Hashtable
	 * 
	 * @param db   name of the db to which the role belongs
	 * @param role role string in above format
	 */
	protected void processDBRole(String[] dbs, String role){
		String[] tokens = role.split(",");
		String type = tokens[0].trim().toLowerCase();
		
		Hashtable<String,Hashtable<String,String>> dbroles = new Hashtable<String,Hashtable<String,String>>();
		Hashtable<String,String> params = new Hashtable<String,String>();
		
		if(type.equals("single") || type.equals("mainpart") ||
		   type.equals("restpart") || type.matches("part[1-9][0-9]*")){		
			
			// all params are optional, if absent default will be used
			if(tokens.length>1){
				String token = tokens[1].trim().toLowerCase();
				if(token.equals("true") || token.equals("false"))
					params.put("optimize",token);
				else{
					System.err.println("Expecting true/false as second paramter of type "+type+" in database def: "+role);
					System.exit(1);
				}
			}
			if(tokens.length>2)
				params.put("mergeFactor",tokens[2]);
			if(tokens.length>3)
				params.put("maxBufDocs", tokens[3]);
			if(tokens.length>4)
				params.put("subdivisions", tokens[4]);
			
			if(tokens.length>5 && verbose)
				System.out.println("Unrecognized database parameters in ("+role+")");
			
			dbroles.put(type,params);
			
		} else if(type.equals("mainsplit")){
			// currently no params
			dbroles.put(type,params);
		} else if(type.equals("split") || type.equals("nssplit")){
			if(tokens.length>1) // number of segments
				params.put("number",tokens[1]);
			else{
				if(verbose)
					System.out.println("Warning: for dbs "+dbs+" splitFactor is not specified, using default of 2.");
				params.put("number","2");
			}
			dbroles.put(type,params);
		} else if(type.equals("language")){
			// langauge is optional, or maybe it shouldn't be
			if(tokens.length>1)
				params.put("code",tokens[1]);
			
			if(tokens.length>2 && verbose)
				System.out.println("Unrecognized language parameters in ("+role+")");
			
			dbroles.put(type,params);
			
		} else if(type.equals("warmup")){
			// number of warmup queries
			if(tokens.length>1)
				params.put("count",tokens[1]);
			
			if(tokens.length>2 && verbose)
				System.out.println("Unrecognized warmup parameters in ("+role+")");
			
			dbroles.put(type,params);
			
		} else if(type.matches("nspart[1-9][0-9]*")){
			// [0,1,2] syntax gets split up in first split, retokenize
			String ns = role.substring(role.indexOf(",")+1,role.lastIndexOf("]")+1).trim();
			tokens = role.substring(role.lastIndexOf("]")+1).split(",");
			// definition of namespaces, e.g. [0,1,2]			
			if(ns.length() > 2 && ns.startsWith("[") && ns.endsWith("]"))
				ns = ns.substring(1,ns.length()-1);
			else
				ns = "<default>";
			params.put("namespaces",ns);
			
			// all params are optional, if absent default will be used
			if(tokens.length>1){
				String token = tokens[1].trim().toLowerCase();
				if(token.equals("true") || token.equals("false"))
					params.put("optimize",token);
				else{
					System.err.println("Expecting true/false as third paramter of type "+type+" in database def: "+role);
					System.exit(1);
				}
			}
			if(tokens.length>2)
				params.put("mergeFactor",tokens[2]);
			if(tokens.length>3)
				params.put("maxBufDocs", tokens[3]);
			if(tokens.length>4)
				params.put("subdivisions", tokens[4]);
			
			if(tokens.length>5 && verbose)
				System.out.println("Unrecognized database parameters in ("+role+")");
			
			dbroles.put(type,params);
						
		} else if(type.equals("spell")){			
			// all params are optional, if absent default will be used
			if(tokens.length>1)
				params.put("wordsMinFreq",tokens[1]);
			if(tokens.length>2)
				params.put("phrasesMinFreq",tokens[2]);
			
			if(tokens.length>3 && verbose)
				System.out.println("Unrecognized suggest parameters in ("+role+")");
			
			dbroles.put(type,params);			
		} else if(type.equals("prefix")){
			// no params
			if(tokens.length>1 && verbose)
				System.out.println("Unrecognized prefix parameters in ("+role+")");
			
			dbroles.put(type,params);			
		} else if(type.equals("titles_by_suffix")){
			if(tokens.length>1) // number of segments
				params.put("number",tokens[1]);
			else{
				params.put("number","1");
			}
			dbroles.put(type,params);
		} else if(type.matches("tspart[1-9][0-9]*")){
			// [0,1,2] syntax gets split up in first split, retokenize
			String defPart = role.substring(role.indexOf("[")+1,role.lastIndexOf("]")).trim();
			String[] defs = defPart.split(",");
			// entries as expanded as params: wiki -> w
			for(String def : defs){
				String[] parts = def.split(":");
				String suffix, iw;
				suffix = parts[0].trim();
				if(parts.length == 2)
					iw = parts[1].trim();
				else
					iw = suffix;
				params.put(suffix,iw);
			}		
			dbroles.put(type,params);		
		} else if(verbose){
			System.out.println("Warning: Unrecognized role \""+role+"\".Ignoring.");
		}
		
		
		// add dbroles to all given dbs
		for(int i=0;i<dbs.length;i++){
			String db = dbs[i];
			
			Hashtable<String, Hashtable<String, String>> dbr =  database.get(db);
			if(dbr == null){
				dbr = new Hashtable<String, Hashtable<String, String>>();				
				database.put(db,dbr);
			}
			if(type.equals("split") || type.equals("mainsplit") || type.equals("single") || type.equals("nssplit")){
				if(dbr.get("split")!=null || dbr.get("mainsplit")!=null || dbr.get("single")!=null || dbr.get("nssplit")!=null){
					if(verbose)
						System.out.println("WARNING: in Global Configuration: defined new architecture "+type+" for "+db);
					dbr.remove("split"); dbr.remove("mainsplit"); dbr.remove("single"); dbr.remove("nssplit");
				}
			}
			if(dbr.get(type)!=null && verbose)
				System.out.println("WARNING: in Global Configuration: role \""+type+"\" already defined for database \""+db+"\". Overwriting.");
			dbr.putAll(dbroles);
		}
	}

	/**
	 * Returns if host should do some indexing
	 * @return true if this node is indexer
	 */
	public boolean isIndexer(){		
		return index.get(hostAddr)!=null || index.get(hostName)!=null;
	}

	/**
	 * Returns if node is used for searching
	 * @return true if this node is searcher
	 */
	public boolean isSearcher() {
		return search.get(hostAddr)!=null || search.get(hostName)!=null;
	}
	
	/**
	 * Returns parameters of database, i.e. language, warmup ... 
	 * 
	 * @param dbname
	 * @return Hashtable of parameters for dbname.type
	 */
	public Hashtable<String,String> getDBParams(String dbname, String type){
		return database.get(dbname).get(type);
	}
	
	/** 
	 * Get integer parameter for dbname.type 
	 * Returns defaultValue if the param is not defined 
	 */
	public int getIntDBParam(String dbname, String type, String param, int defaultValue){
		Hashtable<String,String> p = database.get(dbname).get(type);
		String val = p.get(param);
		if(val == null)
			return defaultValue;
		else
			return Integer.parseInt(val);
	}
	
	/** 
	 * Get string parameter for dbname.type 
	 * Returns defaultValue if the param is not defined 
	 */
	public String getStringDBParam(String dbname, String type, String param, String defaultValue){
		Hashtable<String,String> p = database.get(dbname).get(type);
		String val = p.get(param);
		if(val == null)
			return defaultValue;
		else
			return val;
	}
	
	/**
	 * Look at the logical DB structure ([Database] in global config)
	 * and figure out the main index type of database (e.g. single, 
	 * mainpart, split).  
	 * @param dbname
	 * @return lowercased db type
	 */
	protected String getMainDBType(String dbname){
		Enumeration e = ((Hashtable)database.get(dbname)).keys();
		while(e.hasMoreElements()){
			String type = (String)e.nextElement();
			if(type.equals("single") || type.equals("mainsplit") || type.equals("split"))
				return type;
		}		
		// global configuration consistency error
		System.out.println("Database "+dbname+" does not have a specified type (eg single, mainsplit, split).");
		return "unknown"; 
	}

	/**
	 * @param db
	 * @return the host which indexes this db
	 */
	public String getIndexHost(String db) {
		return indexLocation.get(db);
	}

	/**
	 * @param host
	 * @return if the given string points to this hostname
	 */
	public boolean isMyHost(String host) {
		return host.equalsIgnoreCase(hostAddr) || host.equalsIgnoreCase(hostName);
	}

	public String getLanguage(IndexId iid){
		return getLanguage(iid.getDBname());
	}
	
	/** Get language for a dbname */
	public String getLanguage(String dbname) {
		// first check explicit language paramter in global settings
		Hashtable<String,Hashtable<String,String>> dbparam = database.get(dbname);
		if(dbparam !=null){
			Hashtable<String,String> lang = dbparam.get("language");
			if(lang!=null)
				return lang.get("code");
		}
		// try to get from initialise settings
		if(wgLanguageCode!=null){
			String key = findSuffix(wgLanguageCode.keySet(),dbname);
			if(key != null)
				return wgLanguageCode.get(key);
		}
		// try to get languages from suffixes
		if(databaseSuffixes != null){
			for (String suffix : databaseSuffixes) {
				if (dbname.endsWith(suffix))
					return dbname.substring(0, dbname.length() - suffix.length());
			}	
		}
		
		return "";
	}
	
	/** All indexes that localhost is indexing */
	public HashSet<IndexId> getMyIndex(){
		HashSet<IndexId> ret = new HashSet<IndexId>();
		
		for(IndexId iid : indexIdPool.values()){
			if(iid.isMyIndex())
				ret.add(iid);
		}
		
		return ret;
	}
	
	/** All indexed that localhost is searching */
	public HashSet<IndexId> getMySearch(){
		HashSet<IndexId> ret = new HashSet<IndexId>();
		
		for(IndexId iid : indexIdPool.values()){
			if(iid.isMySearch())
				ret.add(iid);
		}
		
		return ret;
	}
	
	/** Get the name of the localhost as it appears in global configuration */
	public String getLocalhost(){
		if(index.get(hostAddr) != null || search.get(hostAddr) != null)
			return hostAddr;
		else if(index.get(hostName) != null || search.get(hostName) != null)
			return hostName;
		else
			return null;
	}

	public Hashtable<String, NamespaceFilter> getNamespacePrefixes() {
		return namespacePrefix;
	}

	public String getNamespacePrefixAll() {
		return namespacePrefixAll;
	}
	
	/** Check wether dbname has some of the suffixes */
	protected boolean checkSuffix(String[] suffixes, String dbname){
		if(suffixes == null)
			return false;
		else{
			for (String suffix : suffixes) {
				if (dbname.endsWith(suffix))
					return true;
			}
		}
		return false;
	}
	
	/** Returns if keyword scoring should be used for this db, using
	 *  the suffixes from the global configuration
	 *  
	 * @param dbname
	 * @return
	 */
	public boolean useKeywordScoring(String dbname){
		return checkSuffix(keywordScoringSuffixes,dbname);		
	}
	
	/**
	 * If this dbname is assigned an exact-case additional index. 
	 * 
	 * @param dbname
	 * @return
	 */
	public boolean exactCaseIndex(String dbname){
		return checkSuffix(exactCaseSuffix,dbname);		
	}

	/** Find suffix that matches dbname */
	public String findSuffix(Collection<String> suffixes, String dbname){
		for(String suffix : suffixes){
			if(dbname.endsWith(suffix)){
				return suffix;
			}
		}
		return null;
	}
	
	/** Get OAI-repo url for dbname */
	public String getOAIRepo(String dbname){
		String repo = null;
		// try non-default values from global settings
		repo = findSuffix(oaiRepo.keySet(),dbname);
		if(repo != null)
			repo = oaiRepo.get(repo);
		// try to get from initialise settings
		if(repo == null && wgServer != null){
			String key = findSuffix(wgServer.keySet(),dbname);
			if(key == null)
				key = "default";
			repo = wgServer.get(key);
			if(repo != null){
				if(!repo.endsWith("/"))
					repo += "/";
				repo += "w/index.php"; // FIXME: we take this as generic path to index.php
			}
				
		}
		// get from global config
		if(repo == null){
			repo = findSuffix(oaiRepo.keySet(),dbname);
			if(repo != null)
				repo = oaiRepo.get(repo);
			if(repo == null && oaiRepo.containsKey("<default>"))
				repo = oaiRepo.get("<default>");
		}
		if(repo == null)
			return ""; // failed, no url		
		
		// process $lang
		String lang = getLanguage(dbname);
		repo = repo.replace("$lang",lang);
		repo = repo += "?title=Special:OAIRepository";
		
		return repo;
	}

	public static boolean isVerbose() {
		return verbose;
	}

	public static void setVerbose(boolean verbose) {
		GlobalConfiguration.verbose = verbose;
	}
	
	public NamespaceFilter getDefaultNamespace(IndexId iid){
		return getDefaultNamespace(iid.getDBname());
	}
	public NamespaceFilter getDefaultNamespace(String dbname){
		if(wgDefaultSearch != null){
			if(wgDefaultSearch.containsKey(dbname))
				return wgDefaultSearch.get(dbname);
			else if(wgDefaultSearch.containsKey("default"))
				return wgDefaultSearch.get("default");
		}
		return new NamespaceFilter(0);
	}
	
	

}