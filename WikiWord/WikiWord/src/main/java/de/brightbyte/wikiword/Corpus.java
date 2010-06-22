package de.brightbyte.wikiword;

import java.io.File;
import java.io.UnsupportedEncodingException;
import java.net.MalformedURLException;
import java.net.URL;
import java.net.URLEncoder;
import java.util.Collections;
import java.util.List;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.wikiword.rdf.WikiWordIdentifiers;

/**
 * This class represents a corpus, i.e., a collection of texts that is to be used as a source for 
 * analysis. In practice, a Corpus represents a specific wiki project.
 */
public class Corpus extends DatasetIdentifier {
	protected String classSuffix;
	protected URL url;
	protected String domain;
	protected String wikiName;
	protected String family;
	protected String language;
	
	protected NamespaceSet namespaces;
	protected Map<String, Interwiki> interwikiMap;
	
	public Corpus(String collection, String name, String dbPrefix, String domain, String classSuffix, String language, String family, String[] configPackages) {
		super(collection, name, dbPrefix, configPackages);
		this.domain = domain;
		this.dbPrefix = dbPrefix;
		this.classSuffix = classSuffix;
		this.language = language;
		this.family = family;
		this.wikiName = language + "wiki";
		
		url = WikiWordIdentifiers.corpusURL(domain);

		this.namespaces = Namespace.getNamespaces(this);
		this.interwikiMap = Interwiki.getInterwikiMap(this);
	}
	
	/** Suffix to use when loading specialized classes for this corpus. 
	 * Especially used to load the correct impolementation of {@link WikiTextAnalyzer}.  
	 **/
	public String getClassSuffix() {
		return classSuffix;
	}
	
	/**
	 * Retruns the wiki's "family" - i.e. "wikipedia" for wikipedia. 
	 */
	public String getFamily() {
		return family;
	}

	/**
	 * Retruns the language code for this wiki.  
	 */
	public String getLanguage() {
		return language;
	}

	/**
	 * Retruns the domain name for this wiki. MUST be unique.
	 */
	public String getDomain() {
		return domain;
	}
	
	public String[] getConfigPackages() {
		return configPackages;
	}
	
	/** 
	 * returns a new Corpus instance corresponding to the wiki project
	 * guessed from the file name. Equivalent to <tt>Corpus.forName(Corpus.guessCorpusName(f))</tt>.
	 * If the file is named according to the conventions used for XML dumps from Wikimedia projects,
	 * as supplied at http://download.wikimedia.org, this method should return a Corpus instance
	 * appropriate for the wiki the dump was generated for.
	 **/
	public static Corpus forFile(String collection, File f, TweakSet tweaks) {
		return forName(collection, guessCorpusName(f), tweaks);
	}

	/**
	 * returns a new Corpus instance corresponding to the wiki project
	 * with the given name. The name may be given as a domain name following
	 * the convention used for wikimedia projects, or as a project name as 
	 * used for wikimedia dumps, as provided on http://download.wikimedia.org.
	 * For example, <tt>de.wikipedia.org</tt> and <tt>dewiki</tt> are both
	 * interpreted as meaning the german language Wikipedia project, having
	 * the language code "de", the family "wikipedia", the domain "de.wikipedia.org"
	 * and the URI "http://de.wikipedia.org".
	 */
	public static Corpus forName(String collection, String name, TweakSet tweaks) {
		String[] configPackages = getConfigPackages(tweaks);

		String domain = name;
		if (domain.indexOf('.')<0) domain = guessCorpusDomain(domain, tweaks);
		
		String[] ss = domain.split("\\.");
		if (ss.length<2) throw new IllegalArgumentException("bad domain: "+domain);
		
		String language = guessCorpusLanguage(ss[0], tweaks);
		String family = guessCorpusFamily(ss[1], tweaks);
		
		String classSuffix = family.equals("wikipedia") || family.equals("wikimedia") ? ss[0] + "wiki" : ss[0] + family;
		
		String dbPrefix = dbPrefix(collection, ss[0]);
		
		//TODO: cache!
		//NOTE: force domain as name
		return new Corpus(collection, name, dbPrefix, domain, classSuffix, language, family, configPackages);
	}
	
	public static Corpus forDataset(DatasetIdentifier dataset, TweakSet tweaks) {
		return forName(dataset.getCollection(), dataset.getName(), tweaks);
	}

	protected static String[] getConfigPackages(TweakSet tweaks) {
		List<String> pkg = tweaks.getTweak("wikiword.ConfigPackages", Collections.<String>emptyList()); 
		return pkg.toArray(new String[pkg.size()]);
	}
	
	/** guesses the wiki family from a name as used for dump files **/
	protected static String guessCorpusFamily(String n, TweakSet tweaks) {
		if (n.matches(".*commonswiki$")) return "commons";
		else if (n.matches(".*meta(wiki)?$")) return "meta";
		else if (n.matches(".*wiki$")) return "wikipedia";
		else if (n.matches(".*wiktionary$")) return "wiktionary";
		else if (n.matches("\\w{1,3}$")) return "wikipedia";
		else if (n.matches(".*wiki.*$")) return n.replaceAll("^.*(wiki.*)$", "$1");
		else return n;
	}
	
	/** guesses the wiki language from a name as used for dump files **/
	protected static String guessCorpusLanguage(String n, TweakSet tweaks) {
		String lang = n.replaceAll("^(.*?)(wiki|wikt).*$", "$1");
		
		if (lang.equals("commons")) return tweaks.getTweak("languages.commonsAsLanguage", false) ? lang : "en";
		else if (lang.equals("meta")) return tweaks.getTweak("languages.metaAsLanguage", false) ? lang : "en";
		else if (lang.equals("simple")) return tweaks.getTweak("languages.simpleAsLanguage", true) ? lang : "en";
		else return lang;
	}
	
	/** guesses the wiki subdomain from a name as used for dump files **/
	protected static String guessCorpusSubdomain(String n, TweakSet tweaks) {
		String sd = n.replaceAll("^(.*?)(wiki|wikt).*$", "$1");
		
		return sd;
	}
	
	/** guesses the wiki domain from a name as used for dump files **/
	protected static String guessCorpusDomain(String n, TweakSet tweaks) {
		String sub =  guessCorpusSubdomain(n, tweaks);
		String fam =  guessCorpusFamily(n, tweaks);
		
		if (!fam.matches("^(wiki.*|wiktionary)$")) {
			sub = fam;
			fam = "wikimedia";
		}
		
		return sub+"."+fam+".org";
	}
	
	/** guesses the wiki name from full dump file name, as used on http://download.wikimedia.org **/
	public static String guessCorpusName(File f) {
		return guessCorpusName(f.getName());
	}
	
	/** guesses the wiki name from full URL. Two cases are recognized: 
	 * a) a remote dump file, like http://download.wikimedia.org/dewiki/latest/dewiki-latest-pages-articles.xml.bz2,
	 * and b) a mediawiki export page, like http://de.wikipedia.org/wiki/Special:Export/Foo 
	 **/
	public static String guessCorpusName(URL url) {
		String u = url.toExternalForm();
		
		if (u.matches("https?://\\w+\\.wik(i|ti)\\w+\\.\\w+/.*/Special:Export/.*")) {
			return guessCorpusFromDomain(url.getHost());
		}
		else {
			return guessCorpusName(url.getPath());
		}
	}
	
	public static String guessCorpusName(String n) {
		n = n.replaceAll("^(.*/)?([^-/.]+).*", "$2");
		return n;
	}
	
	protected static final Pattern domainPattern = Pattern.compile("(\\w+)\\.(wik(i|ti)\\w+)\\.\\w+");
	
	public static String guessCorpusFromDomain(String n) {
		Matcher m = domainPattern.matcher(n);
		if (!m.matches()) return null;
		
		String lang = m.group(1);
		String fam = m.group(2);
		
		if (fam.equals("wikipedia")) fam = "wiki"; 
		
		return lang + fam;
	}
	
	public NamespaceSet getNamespaces() {
		return namespaces;
	}
	
	public Map<String, Interwiki> getInterwikiMap() {
		return interwikiMap;
	}
	
	@Override
	public String toString() {
		return getDomain();
	}

	public URL getResourceURL(String resource) {
		try {
			return new URL(url, URLEncoder.encode(resource, "UTF-8"));
		} catch (MalformedURLException e) {
			throw new IllegalArgumentException("bad resource identifier: "+resource);
		} catch (UnsupportedEncodingException e) {
			throw new Error("UTF-8 not supported");
		}
	}
	
	public URL getURL() {
		return url;
	}

	public String getWikiName() {
		return wikiName;
	}
	
}
