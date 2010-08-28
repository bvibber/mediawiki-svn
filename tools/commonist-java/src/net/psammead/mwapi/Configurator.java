package net.psammead.mwapi;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map;
import java.util.Set;

import net.psammead.mwapi.config.ConfigInfo;
import net.psammead.mwapi.config.ConfigSerializer;
import net.psammead.mwapi.config.Family;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.scrapper.Scrapper;
import net.psammead.mwapi.scrapper.SiteInfo;
import net.psammead.util.IOUtil;
import net.psammead.util.Logger;

/** gets data from a MediaWiki instance necessary to communicate with it */
public final class Configurator {
	private static final Logger logger	= new Logger(Configurator.class);
	
	private final Scrapper	scrapper;

	public Configurator() throws MalformedURLException {
		scrapper	= new Scrapper();
	}
	
	// IDEE
	/*
	String	longName	= "wikipedia";
	String	shortName	= "w";
	String	languages	= "de,en";
	String	uselang		= "nds";
	String	charSet		= "utf-8";
	String	rawURL		= "http://${lang}.wikipedia.org/w/index.php";
	String	prettyURL	= "http://${lang}.wikipedia.org/wiki/${title}";
	String	apiURL		= "http://${lang}.wikipedia.org/w/api.php";
	*/
	
//	public void generate(File outputDir,
//			String	familyName, String String familyShortcut, 
//			String[] languages, String protocol, String host, 
//			String rawPath, String prettyPath, String apiPath,
//			String charSet, String uselang) throws Exception {
//	}

	/** generate a .family and one or more .family files */
	public void generate(File outputDir, 
			String familyName, String familyShortcut, 
			String[] languages, String protocol, String host, 
			String rawPath, String prettyPath, String apiPath,
			String charSet, String uselang) 
			throws IOException {
		
		outputDir.mkdirs();
		
		final File	familyFile	= new File(outputDir, ConfigInfo.familyFileName(familyName));
		final Family	family		= family(familyName, familyShortcut, languages);
		final String	familyCode	= ConfigSerializer.serializeFamily(family);
		logger.info("writing " + familyFile);
		try {
			IOUtil.writeStringToFile(familyFile, familyCode, ConfigInfo.FILE_ENCODING);
		}
		catch (IOException e) {
			logger.error("could not create " + familyFile, e);
			throw e;
		}
		
		if (languages != null) {
			for (int i=0; i<languages.length; i++) {
				final String	language 	= languages[i];
				final File	siteFile	= new File(outputDir, ConfigInfo.siteFileName(familyName, language));
				try {
					Site	site	= site(familyName, language, 
											protocol, host.replaceAll("\\*", language),
											rawPath, prettyPath, apiPath,
											charSet, uselang);
					String	siteCode	= ConfigSerializer.serializeSite(site);
					logger.info("writing " + siteFile);
					IOUtil.writeStringToFile(siteFile, siteCode, ConfigInfo.FILE_ENCODING);
				}
				catch (IOException e) {
					logger.error("could not create " + siteFile, e);
					throw e;
				}
			}
		}
		else {
			final File	siteFile	= new File(outputDir, ConfigInfo.siteFileName(familyName, null));
			try {
				final Site	site	= site(familyName, null, 
										protocol, host, 
										rawPath, prettyPath, apiPath,
										charSet, uselang);
				final String	siteCode	= ConfigSerializer.serializeSite(site);
				logger.info("writing " + siteFile);
				IOUtil.writeStringToFile(siteFile, siteCode, ConfigInfo.FILE_ENCODING);
			}
			catch (IOException e) {
				logger.error("could not create " + siteFile, e);
				throw e;
			}
		}
	}
	
	/** create a String representation of a .family file */
	private Family family(String name, String shortcut, String[] supportedLanguages) {
		final boolean 		multilingual	= supportedLanguages != null;
		final String		defaultLanguage;
		final Set<String>	supportedLanguagesSet;
		
		if (multilingual) {
			if (supportedLanguages == null)	throw new RuntimeException("supportedLanguages may not be null for multilingual wikis");
			defaultLanguage	= supportedLanguages[0]; 	// may not be null, then multilingual would not be true!
			supportedLanguagesSet	= new HashSet<String>();
			for (int i=0; i<supportedLanguages.length; i++) {
				supportedLanguagesSet.add(supportedLanguages[i]);
			}
		}
		else {
			defaultLanguage			= null;
			supportedLanguagesSet	= null;
		}
		
		return new Family(name, shortcut, multilingual, defaultLanguage, supportedLanguagesSet);
	}

	/** create a String representation of a .site file */
	private Site site(String family, String language, String protocol, String host, 
			String rawPath, String prettyPath, String apiPath, String charSet, String uselang) 
			throws IOException {
		final SiteInfo	siteInfo	= scrapper.fetchSiteInfo(protocol, host, prettyPath);

		final Map<String,String>	messages_en		= scrapper.fetchMessages(protocol, host, rawPath, "en");
		final Map<String,String>	messages_use	= scrapper.fetchMessages(protocol, host, rawPath, uselang);
		final Map<String,String>	messages	= new HashMap<String,String>();
		for (int i=0; i<ConfigInfo.MESSAGE_NAMES.length; i++) {
			final String	key		= ConfigInfo.MESSAGE_NAMES[i];
			final String	val1	= messages_en.get(key);
			if (val1 != null)	messages.put(key, val1);
			final String	val2	= messages_use.get(key);
			if (val2 != null)	messages.put(key, val2);
		}
		
		// TODO: SiteInfo properties base, sitename and generator are missing
		return new Site(family, language, 
				protocol, host, rawPath, prettyPath, apiPath,
				charSet, siteInfo.titleCase, uselang, 
				siteInfo.nameSpaces, siteInfo.specialPages, messages);
	}
}
