package net.psammead.mwapi.config;

import java.util.List;

/** stores FamilyList, Families and Sites into Strings */
public final class ConfigSerializer {
	public static String serializeFamliyList(List<String> families) {
		final StringBuffer out = new StringBuffer();
		for (String s : families) {
			out.append(s).append(ConfigInfo.EOL);
		}
		return out.toString();
	}
	
	public static String serializeFamily(Family family) {
		String	out	= "## configuration for a whole wiki family\n\n";
					
		out	+= "# identification"							+ "\n";
		out	+= "name\t"				+	family.name			+ "\n";
		if (family.shortcut != null)
		out	+= "shortcut\t"			+	family.shortcut		+ "\n";
		out	+= "\n";
					
		out	+= "# languages"								+ "\n";
		out	+= "multilingual\t"		+	family.multilingual	+ "\n";
		
		if (family.multilingual) {
			// the first language is the default language
			out	+= "defaultLanguage\t" + family.defaultLanguage		+ "\n";
			for (String supportedLanguage : family.supportedLanguages) {
				out	+= "supportedLanguage\t" + supportedLanguage	+ "\n";
			}
		}
		
		return out;
	}
	
	public static String serializeSite(Site site) {
		String	out	= "## configuration for a single wiki site\n\n";
		
		out	+= "# identification"	+ "\n";
		out	+= "family\t"		+ site.family		+ "\n";
		if (site.language != null)
		out	+= "language\t"		+ site.language		+ "\n";
		out	+= "\n";

		out	+= "# network"							+ "\n";
		out	+= "protocol\t"		+ site.protocol		+ "\n";
		out	+= "hostName\t"		+ site.hostName		+ "\n";
		out	+= "rawPath\t"		+ site.rawPath		+ "\n";
		out	+= "prettyPath\t"	+ site.prettyPath	+ "\n";
		out	+= "apiPath\t"		+ site.apiPath		+ "\n";
		out	+= "\n";
		
		out	+= "# config"							+ "\n";
		out	+= "charSet\t"		+ site.charSet		+ "\n";
		out	+= "titleCase\t"	+ site.titleCase	+ "\n";
		out	+= "uselang\t"		+ site.uselang		+ "\n";
		out	+= "\n";
		
		out	+= "# nameSpaces\n";
		for (Integer key : site.nameSpaceNames.keySet()) {
			final String	value	= site.nameSpaceNames.get(key);
			out	+= "nameSpace\t" + key + "\t" + quoteMessage(value) + "\n";
		}
		out	+= "\n";
		
		out	+= "# specialPages\n";
		for (String key : site.specialPageNames.keySet()) {
			final String	value	= site.specialPageNames.get(key);
			out	+= "specialPage\t" + quoteMessage(key) + "\t" + quoteMessage(value) + "\n";
		}
		out	+= "\n";
		
		out	+= "# messages\n";
		for (String key : site.messages.keySet()) {
			final String	value	= site.messages.get(key);
			out	+= "message\t" + key + "\t" + quoteMessage(value) + "\n";
		}

		return out;
	}
	
	public static String quoteMessage(String message) {
		return "'"
			+ message.replaceAll("\\\\", "\\\\\\\\")
					.replaceAll("'", "\\\\'")
					.replaceAll("\n", "\\\\n")
			+ "'";
	}
}
