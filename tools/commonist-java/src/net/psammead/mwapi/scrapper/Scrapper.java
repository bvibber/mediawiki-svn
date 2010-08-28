package net.psammead.mwapi.scrapper;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.HashMap;
import java.util.List;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import net.htmlparser.jericho.Attribute;
import net.htmlparser.jericho.Element;
import net.htmlparser.jericho.Source;
import net.psammead.mwapi.config.ConfigInfo;
import net.psammead.mwapi.connection.TitleUtil;
import net.psammead.mwapi.net.IllegalFormException;
import net.psammead.mwapi.net.JerichoUtil;
import net.psammead.mwapi.ui.UnsupportedURLException;
import net.psammead.util.IOUtil;
import net.psammead.util.Logger;
import net.psammead.util.XMLCodec;

public final class Scrapper {
	private static final Logger log	= new Logger(Scrapper.class);
	
	private HttpUtil	http;
	
	public Scrapper() throws MalformedURLException {
		http	= new HttpUtilCommons();
		http.useSystemProxy();
	}
	
	//=========================================================================
	//## basic info
	
	/** download the main page and return an Object containing its charset and the special namespace */
	public BasicInfo fetchBasicInfo(String protocol, String host, String prettyPath) throws IOException, IllegalFormException {
		// download main page
		final URL			url				= new URL(protocol + host + prettyPath);
		final HttpResult	content			= http.download(url);
		final Source		source 			= JerichoUtil.createSource(content.body, log);
		final Element		form			= JerichoUtil.fetchForm(source, "searchform", "searchform", -1);
		final String		searchAction	= JerichoUtil.fetchAttributeValue(form.getStartTag(), "action");
		final String		specialNs		= searchAction.replaceAll(".*/(.*):.*", "$1");
		return new BasicInfo(content.charset, specialNs);
	}
	
	//=========================================================================
	//## site info
	
	/** fetch the localized name of all namespaces using the siteinfo in Special:Export */
	public SiteInfo fetchSiteInfo(String protocol, String host, String prettyPath) throws IOException {
		// uses a (hopefully) not existing title because we only want the siteinfo
		final URL			url			= new URL(protocol + host + prettyPath + "Special:Export?action=submit&pages=23kl5jskdjfhskdfhslkfjsdkqweuh23&curonly=checked");
		final HttpResult	content		= http.download(url);
		final Source		source		= JerichoUtil.createSource(content.body, log);
		final Element		siteinfo	= JerichoUtil.firstElement(source, "siteinfo");

		// TODO: should generate NameSpace objects!
		final Map<Integer,String>	nameSpaces	= new HashMap<Integer,String>();
		final List<Element>		elements	= siteinfo.getAllElements("namespace");
		for (Element element : elements) {
			final Attribute	key		= element.getAttributes().get("key");
			if (key == null)	throw new RuntimeException("namespace.key not found");
			final int		index	= Integer.parseInt(key.getValue());
			// element.EmptyElementTag
			final String	name	= element.getContent().toString();	// toString() was getSourceText()
			nameSpaces.put(new Integer(index), name);
		}
		
		final String sitename		= JerichoUtil.firstElementText(siteinfo, "sitename");
		final String base			= JerichoUtil.firstElementText(siteinfo, "base");
		final String generator	= JerichoUtil.firstElementText(siteinfo, "generator");
		final String titleCase	= JerichoUtil.firstElementText(siteinfo, "case");
		
		final String				specialNS		= nameSpaces.get(-1);
		final Map<String,String>	specialPages	= fetchSpecialPages(protocol, host, prettyPath, content.charset, specialNS);
		
		return new SiteInfo(sitename, base, generator, titleCase, specialPages, nameSpaces);
	}
	
	//=========================================================================
	//## special pages

	public Map<String,String> fetchSpecialPages(String protocol, String host, 
			String prettyPath, String charset, String specialNS) throws IOException {
		final Map<String,String> out = new HashMap<String,String>();
		for (int i = 0; i < ConfigInfo.SPECIAL_PAGES.length; i++) {
			final String canonical = ConfigInfo.SPECIAL_PAGES[i];
			final String localized = fetchSpecialPage(protocol, host, prettyPath, charset, specialNS, canonical);
			out.put(canonical, localized);
		}
		return out;
	}
	
	// attention, specialNs already is URL-encoded
	public String fetchSpecialPage(String protocol, String host, String prettyPath, 
			String charset, String specialNS, String canonical) throws IOException {
		try {
			final URL	url	= new URL(protocol + host + prettyPath + TitleUtil.encodeTitle(specialNS + ":" + canonical, charset));
			final String	location	= http.redirectsTo(url);
			if (location == null)	return canonical;
			
//			// with both specialNS and specialPage canonical, we need to follow 2 redirects :/
//			URL	url2	= new URL(location);
//			String	location2	= redirectsTo(url2);
//			if (location2 != null)	location	= location2;
		
			final String	raw		= location.replaceAll(".*/", "");
			final String	title	= TitleUtil.spaces(TitleUtil.decodeTitle(raw, charset));
			return title.replaceAll(".*:", "");
		}
		catch (UnsupportedURLException e) {
			IOException ee = new IOException("cannot decode specialPage title: " + canonical);
			ee.initCause(e);
			throw ee;
		}
	}
	
	//=========================================================================
	//## messages
	
	// PHP parsing
	private final String	Q_VALUE		= "'((?:[^'\\\\]*+|\\\\.)*)'";
//	private final String	DQ_VALUE	= "\"((?:[^\"\\\\]*+|\\\\.)*)\"";
	private final String	q_decode(String s)	{ return s.replaceAll("\\\\'", "'");	}
//	private final String	dq_decode(String s)	{ return s.replaceAll("\\\\\"", "\"");	}
	
	private final String	BASE		= Q_VALUE + " => " + Q_VALUE + ",";
	private final String	PLAIN		= BASE + "\n";
	private final String	HASHED		= "#" + BASE + "\n";
	private final String	SLASHED		= "/\\* " + BASE + " \\*/\n";
	private final String	COMBINED	= SLASHED + "|" + HASHED + "|" + PLAIN;
	
	/** download the messages page and return an Object containing its charset and a Map of messages */
	private Map<String,String> fetchMessagesPHP(String protocol, String host, String rawPath, String uselang) throws IOException {
		// download allmessages page
		// TODO: would be faster using the specialNS
		// TODO: ot=php doesn't exist any more
		final URL			url		= new URL(protocol + host + rawPath + "?title=Special:Allmessages&ot=php&useskin=monobook&uselang=" + uselang);
		final HttpResult	content	= http.download(url);
		
		// test
		if (!content.body.matches("(?s).*<!-- start content -->.*")) {
			IOUtil.writeStringToFile(new File("/tmp/scrapped.html"), content.body, "UTF-8");
			throw new RuntimeException("### start content not found");	
		}
		if (!content.body.matches("(?s).*<!-- end content -->.*")) {
			IOUtil.writeStringToFile(new File("/tmp/scrapped.html"), content.body, "UTF-8");
			throw new RuntimeException("### end content not found");	
		}
		if (!content.body.matches("(?s).*\n\\$(wgAllMessages|messages).*? = array\\(\n(.*,\n)\\);.*")) {
			IOUtil.writeStringToFile(new File("/tmp/scrapped.html"), content.body, "UTF-8");
			throw new RuntimeException("### content not found");	
		}
		// find messages php code
		// since 03aug06 on wikimedia sites it's messages instead of wgAllMessages
		final Pattern	pattern1	= Pattern.compile(".*?<!-- start content -->.*?\n\\$(?:wgAllMessages|messages).*? = array\\(\n(.*,\n)\\);.*?<!-- end content -->.*?", Pattern.DOTALL);
		final Matcher	matcher1	= pattern1.matcher(content.body);
		if (!matcher1.matches())	{
//			net.psammead.util.IOUtil.writeFile(new File("/tmp/scrapped.html"), content.body, "UTF-8");
			throw new RuntimeException("### no content matches found: " + url);
		}
		final String	decoded		= XMLCodec.decode(matcher1.group(1), true, false);
		//net.psammead.util.IOUtil.writeFile(new File("/tmp/content.php"), decoded, "UTF-8");
		//System.exit(1);
		
//		System.err.println("### combined=" + COMBINED);
		
		final Map<String,String>	out	= new HashMap<String,String>();
		final Pattern pattern2 = Pattern.compile(COMBINED, Pattern.DOTALL);
		final Matcher matcher2 = pattern2.matcher(decoded);
//		System.err.println("### finding");
		while (matcher2.find()) {	//### StackOverflowError ???
//			System.err.println("### found " + matcher.start() + ".." + matcher.end() + ": " + decoded.substring(matcher.start(), matcher.end()));
			
			final String	key;
			String	value;
			if (matcher2.group(1) != null) {		// slashed
				key		= matcher2.group(1);
				value	= matcher2.group(2);
			}
			else if (matcher2.group(3) != null) {	// hashed
				key		= matcher2.group(3);
				value	= matcher2.group(4);
			}
			else if (matcher2.group(5) != null) {	// plain
				key		= matcher2.group(5);
				value	= matcher2.group(6);
			}
			else {
				continue;
			}
			value	= q_decode(value);
			out.put(key, value);
		}
		
		return out;
	}
	
	//=========================================================================
	//## messages
	
	/** download the messages page and return an Object containing its charset and a Map of messages */
	private Map<String,String> fetchMessagesXML(String protocol, String host, String rawPath, String uselang) throws IOException {
		// download allmessages page
		// TODO: would be faster using the specialNS
		final URL			url		= new URL(protocol + host + rawPath + "?title=Special:Allmessages&ot=xml&uselang=" + uselang);
		final HttpResult	content	= http.download(url);
		
		final Map<String,String>	out	= new HashMap<String,String>();
		final Source source = JerichoUtil.createSource(content.body, log);
		final List<Element> elements = source.getAllElements("message");
		for (Element element : elements) {
			final String	key		= element.getAttributeValue("name");
//			final String	value	= element.getContent().getTextExtractor().toString();	// getTextExtractor löscht linefeeds
//			final String	value	= element.getContent().toString();						// ohne dekodiert er das XML nicht
			final String	value	= JerichoUtil.decodedTextOnly(source, element.getContent());
			// TODO getTextExtractor löscht linefeeds, ohne dekodiert er das XML nicht
			out.put(key, value);
		}
		return out;
	}
	
	public Map<String,String> fetchMessages(String protocol, String host, String rawPath, String uselang) throws IOException {
		final Map<String, String> messagesXML = fetchMessagesXML(protocol, host, rawPath, uselang);
		if (!messagesXML.isEmpty())	return messagesXML;
		log.info(host + ": could not get XML messages, trying PHP");
		final Map<String, String> 	messagesPHP	= fetchMessagesPHP(protocol, host, rawPath, uselang);
		if (!messagesPHP.isEmpty())	return messagesPHP;
		throw new IOException("could not fetch messages");
	}
}
