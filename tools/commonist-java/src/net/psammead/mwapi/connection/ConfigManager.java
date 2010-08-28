package net.psammead.mwapi.connection;

import java.io.FileNotFoundException;
import java.io.IOException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Collections;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import net.psammead.mwapi.config.ConfigDeserializer;
import net.psammead.mwapi.config.ConfigInfo;
import net.psammead.mwapi.config.ConfigSyntaxException;
import net.psammead.mwapi.config.Family;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.ui.UnsupportedWikiException;
import net.psammead.util.IOUtil;

/** manages Families and Sites */
public final class ConfigManager {
	public final List<String>	wikiList;

	private	final Map<String,Family>	familyCache;
	private final Map<String,Site>	siteCache;
	private	final List<String>		supportedWikiNames;
	
	public ConfigManager() throws ConfigException {
		familyCache			= new HashMap<String,Family>();
		siteCache			= new HashMap<String,Site>();
		supportedWikiNames	= new ArrayList<String>();

		try {
			final URL		configuration	= configBuiltin(ConfigInfo.familyListFileName());
			final String	code			= IOUtil.readStringFromURL(configuration, ConfigInfo.FILE_ENCODING);
			wikiList				= ConfigDeserializer.deserializeFamilyList(code);
		}
		catch (Exception e) {
			throw new ConfigException("cannot load families list", e);
		}
		
		
		// init: load preconfigured families
		for (String familyName : wikiList) {
			try {
				final URL	familyDescriptor	= configBuiltin(ConfigInfo.familyFileName(familyName));
				loadFamily(familyDescriptor);
			}
			catch (Exception e) {
				throw new ConfigException("cannot load family description: " + familyName, e);
			}
		}
	}
	
	//------------------------------------------------------------------------------
	//## public API
	
	/** return an immutable List of supported wiki names */
	public List<String> getSupportedWikiNames() {
		return Collections.unmodifiableList(supportedWikiNames);
	}
		
	/** 
	 * get a Family by name or null if it is unknown. 
	 * familyName may be the families shortcut or the family's name. the familyName is lowercased! 
	 */
	public Family getFamily(String familyName) {
		return familyCache.get(familyName.toLowerCase());
	}
		
	/** returns a Site for a supported wiki or throws an UnsupportedWikiException */ 
	public Site getSite(String wiki) throws UnsupportedWikiException {
		final String[]	parts			= wiki.split(":");
		final String	familyName		= parts[0];
		final String	siteLanguage	= parts.length > 1 ? parts[1] : null;
							
		final Family		family	= getFamily(familyName);
		if (family == null)	throw new UnsupportedWikiException("the family: " + familyName + " is not supported");
		
		final Site		site	= getSite(family, siteLanguage);
		if (site == null)	throw new UnsupportedWikiException("the language: " + siteLanguage + " is not supported by family: " + familyName);
		
		return site;
	}
	
	/** 
	 * gets a Site for a Family. 
	 * siteLanguage is null for unilingual Families. 
	 * the siteLanguage is lowercased! 
	 */
	public Site getSite(Family family, String siteLanguage) throws UnsupportedWikiException {
		if (siteLanguage != null) {
			siteLanguage	= siteLanguage.toLowerCase();
			if (!family.multilingual)								return null;
			if (!family.supportedLanguages.contains(siteLanguage))	return null;	//### BÄH
		}
		final String	wiki	= TitleUtil.buildWiki(family.name, siteLanguage);
		final Site	site	= siteCache.get(wiki);
		if (site == null)	throw new UnsupportedWikiException("the wiki: " + wiki + " is not supported");
		return site;
	}
	
	/** (re)loads a Family and returns it */
	public Family loadFamily(URL familyDescriptor) throws ConfigException {
		try {
			final String	code	= IOUtil.readStringFromURL(familyDescriptor, ConfigInfo.FILE_ENCODING);
			final Family	family	= ConfigDeserializer.deserializeFamily(code);

			final String	familyName		= family.name;
			if (family.multilingual) {
				for (String siteName : family.supportedLanguages) {
					final String	siteFileName	= ConfigInfo.siteFileName(familyName, siteName);
					final URL		siteDescriptor	= new URL(Workaround.fixJarURL(familyDescriptor), siteFileName);
					addSite(loadSite(siteDescriptor));
				}
			}
			else {
				final String	siteFileName	= ConfigInfo.siteFileName(familyName, null);
				final URL siteDescriptor		= new URL(Workaround.fixJarURL(familyDescriptor), siteFileName);
				addSite(loadSite(siteDescriptor));
			}
			
			final Family	old	= getFamily(familyName);
			if (old != null)	removeFamily(old);
			
			addFamily(family);
			return family;
		}
		catch (ConfigSyntaxException e) { 
			throw new ConfigException("cannot load family description: " + familyDescriptor, e);
		}
		catch (IOException e) { 
			throw new ConfigException("cannot load family description: " + familyDescriptor, e); 
		}
	}

	/** add a Family */
	private void addFamily(Family family) {
		final String	familyNameLC	= family.name.toLowerCase(); 
		if (familyCache.containsKey(familyNameLC))	return;
		
		familyCache.put(familyNameLC,					family);
		if (family.shortcut != null)
		familyCache.put(family.shortcut.toLowerCase(),	family);
		
		if (family.multilingual) {
			for (String siteName: family.supportedLanguages) {
				supportedWikiNames.add(family.name + ":" + siteName);
			}
		}
		else {
			supportedWikiNames.add(family.name);
		}
		// could use some Comparator
		Collections.sort(supportedWikiNames);
	}
	
	/** loads a Site */
	private Site loadSite(URL siteDescriptor) throws ConfigException {
		try {
			final String	code	= IOUtil.readStringFromURL(siteDescriptor, ConfigInfo.FILE_ENCODING);
			return ConfigDeserializer.deserializeSite(code);
		}
		catch (IOException e) {
			throw new ConfigException("cannot load site description: " + siteDescriptor, e);
		}
		catch (ConfigSyntaxException e) {
			throw new ConfigException("cannot load site description: " + siteDescriptor, e);
		}
	}
	
	private void addSite(Site site) {
		siteCache.put(site.wiki, site);
	}
	
	
	/** remove a Family */
	private void removeFamily(Family family) {
		familyCache.remove(family.name.toLowerCase());
		if (family.shortcut != null)
		familyCache.remove(family.shortcut.toLowerCase());
		
		if (family.multilingual) {
			for (Iterator<String>	it=supportedWikiNames.iterator(); it.hasNext();) {
				final String	wikiName	= it.next();
				if (wikiName.startsWith(family.name + ":"))	it.remove();
			}
		}
		else {
			supportedWikiNames.remove(family.name);
		}
	}
	
	/** where to load a config file from */
	private URL configBuiltin(String name) throws FileNotFoundException {
		final String	path	= "net/psammead/mwapi/config/" + name;
		final URL		url		= getClass().getClassLoader().getResource(path);
		if (url == null)	throw new FileNotFoundException("resource " + path + " could not be found");
		return url;
	}
}
