package net.psammead.mwapi.config;

import java.util.ArrayList;
import java.util.List;
import java.util.SortedMap;
import java.util.SortedSet;
import java.util.TreeMap;
import java.util.TreeSet;
import java.util.regex.Matcher;

import net.psammead.util.TextUtil;

/** loads FamilyList, Families and Sites from Strings */
public final class ConfigDeserializer {
	public final static List<String> deserializeFamilyList(String code) throws ConfigSyntaxException {
		List<String>	out		= new ArrayList<String>();
		final String[]		lines	= splitLines(code);
		for (int i=0; i<lines.length; i++) {
			final String	line	= normalizeLine(lines[i]);	
			if (line.length() == 0)		continue;
			out.add(line);
		}
		if (out.size() == 0)	throw new ConfigSyntaxException("empty family list");
		return out;
	}

	public static Family deserializeFamily(String code) throws ConfigSyntaxException {
		String	name			= null;
		String	shortcut		= null;
		boolean	multilingual	= false;
		String	defaultLanguage	= null;
		final SortedSet<String>	supportedLanguages	= new TreeSet<String>();

		final String[]	lines		= splitLines(code);
		for (int i=0; i<lines.length; i++) {
			final String	line	= normalizeLine(lines[i]);
			if (line.length() == 0)		continue;
			final Matcher	matcher	= ConfigInfo.LINE_RE.matcher(line);
			if (!matcher.matches())	throw new ConfigSyntaxException("not a command line", i, line);
			final String	cmd	= matcher.group(1);
			final String	arg	= matcher.group(2);
				if (cmd.equals("name"))					name			= arg;
			else if (cmd.equals("shortcut"))			shortcut		= arg;
			else if (cmd.equals("multilingual"))		multilingual	= arg.equals("true");
			else if (cmd.equals("defaultLanguage"))		defaultLanguage	= arg;
			else if (cmd.equals("supportedLanguage"))	supportedLanguages.add(arg);
			else throw new ConfigSyntaxException("command unknown: " + cmd, i, line);
		}
		if (name == null)	throw new ConfigSyntaxException("family name missing");
		// shortcut is optional
		if (multilingual) {
			if (defaultLanguage == null)						throw new ConfigSyntaxException("defaultLanguage missing");
			if (supportedLanguages.isEmpty())					throw new ConfigSyntaxException("supportedLanguage missing");
			if (!supportedLanguages.contains(defaultLanguage))	throw new ConfigSyntaxException("supportedLanguage must contain defaultLanguage: " + defaultLanguage);
		}
		return new Family(name, shortcut, multilingual, defaultLanguage, supportedLanguages);
	}
	
	public static Site deserializeSite(String code) throws ConfigSyntaxException {
		String	family		= null;
		String	language	= null;
		String	protocol	= null;
		String	hostName	= null;
		String	rawPath		= null;
		String	prettyPath	= null;
		String	apiPath		= null;
		String	charSet		= null;
		String	titleCase	= null;
		String	uselang		= null;
		final SortedMap<Integer,String>	nameSpaces		= new TreeMap<Integer,String>();
		final SortedMap<String,String>	specialPages	= new TreeMap<String,String>();
		final SortedMap<String,String>	messages		= new TreeMap<String,String>();
		
		final String[]	lines		= splitLines(code);
		for (int i=0; i<lines.length; i++) {
			final String	line	= normalizeLine(lines[i]);
			if (line.length() == 0)		continue;
		
			final Matcher	matcher	= ConfigInfo.LINE_RE.matcher(line);
			if (!matcher.matches())	throw new ConfigSyntaxException("not a command line", i, line);
			final String	cmd	= matcher.group(1);
			final String	arg	= matcher.group(2);
				 if (cmd.equals("family"))		family		= arg;
			else if (cmd.equals("language"))	language	= arg;
			else if (cmd.equals("protocol"))	protocol	= arg;
			else if (cmd.equals("hostName"))	hostName	= arg;
			else if (cmd.equals("rawPath"))		rawPath		= arg;
			else if (cmd.equals("prettyPath"))	prettyPath	= arg;
			else if (cmd.equals("apiPath"))		apiPath		= arg;
			else if (cmd.equals("charSet"))		charSet		= arg;
			else if (cmd.equals("titleCase"))	titleCase	= arg;
			else if (cmd.equals("uselang"))		uselang		= arg;
			else if (cmd.equals("nameSpace")) {
				if (arg.equals("0")) {
					nameSpaces.put(
							new Integer(0),
							"");
				}
				else {
					final Matcher	nsMatcher	= ConfigInfo.NAMESPACE_RE.matcher(arg);
					if (!nsMatcher.matches())	throw new ConfigSyntaxException("wrong nameSpace syntax", i, line);
					nameSpaces.put(
							new Integer(Integer.parseInt(nsMatcher.group(1))),
							unquoteMessage(nsMatcher.group(2)));
				}
			}
			else if (cmd.equals("message")) {
				final Matcher	msgMatcher	= ConfigInfo.MESSAGE_RE.matcher(arg);
				if (!msgMatcher.matches())	throw new ConfigSyntaxException("wrong message syntax", i, line);
				messages.put(
						msgMatcher.group(1),
						unquoteMessage(msgMatcher.group(2)));
				
			}
			else if (cmd.equals("specialPage")) {
				Matcher	msgMatcher	= ConfigInfo.SPECIALPAGE_RE.matcher(arg);
				if (!msgMatcher.matches())	throw new ConfigSyntaxException("wrong specialPage syntax", i, line);
				specialPages.put(
						unquoteMessage(msgMatcher.group(1)),
						unquoteMessage(msgMatcher.group(2)));
			}
			else if (cmd.equals("queryPath")) {
				//### log.warn("queryPath is no longer supported");
			}
			else throw new ConfigSyntaxException("command unknown: " + cmd, i, line);
		}
		if (family == null)			throw new ConfigSyntaxException("wiki missing");
		// language is optional
		if (protocol == null)		throw new ConfigSyntaxException("protocol missing");
		if (hostName == null)		throw new ConfigSyntaxException("host missing");
		// at least one of these must exist
		if (rawPath == null && prettyPath == null)		
									throw new ConfigSyntaxException("rawPath and prettyPath are both missing");
		// apiPath is optional
		if (charSet == null)		throw new ConfigSyntaxException("charSet missing");
		if (titleCase == null)		throw new ConfigSyntaxException("titleCase missing");
		if (uselang == null)		throw new ConfigSyntaxException("uselang missing");
		if (nameSpaces.isEmpty())	throw new ConfigSyntaxException("nameSpaces missing");
		if (specialPages.isEmpty())	throw new ConfigSyntaxException("specialPages missing");
		if (messages.isEmpty())		throw new ConfigSyntaxException("messages missing");
		
		return new Site(family, language, 
				protocol, hostName, rawPath, prettyPath, apiPath,
				charSet, titleCase, uselang, nameSpaces, specialPages, messages);
	}
	
	public static String unquoteMessage(String quoted) {
		if (!(quoted.startsWith("'") && quoted.endsWith("'")))	return quoted;
		return quoted.substring(1, quoted.length()-1)
					.replaceAll("\\\\n", "\n")
					.replaceAll("\\\\'", "'")
					.replaceAll("\\\\\\\\", "\\\\");
	}
	
	public static String[] splitLines(String code) {
		return TextUtil.unixLF(code).split(ConfigInfo.EOL_RE);
	}
	
	private static String normalizeLine(String line) {
		return line.replaceAll(ConfigInfo.LINE_COMMENT_RE, "").trim();
	}
}
