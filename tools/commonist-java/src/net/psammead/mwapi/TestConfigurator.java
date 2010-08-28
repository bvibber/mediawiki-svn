package net.psammead.mwapi;

import java.io.File;
import java.io.IOException;

//testCode, see scrap.bsh
public final class TestConfigurator {
	public static void main(String[] args) throws IOException {
		final File		outDir		= new File("/tmp/config");
		final String[]	minLangs	= new String[] { "en", "de" };
		final String[]	defLangs	= new String[] { "en", "de", "fr", "pl", "ja", "nl", "it", "sv", "pt", "es" };
		final String[]	wpLangs		= new String[] { "en", "ar", "ca", "da", "de", "eo", "es", "et", "fi", "fr", "he", "hr",
													 "ia", "it", "ja", "nl", "no", "pl", "pt", "ro", "sl", "sv", "zh" };

		final Configurator cfg	= new Configurator();

		// wikimedia sites		name			shortcut	languages	protocol	domain						rawPath			prettyPath		apiPath			charSet		uselang
		cfg.generate(outDir,	"meta",			"m",		null,		"http://",	"meta.wikimedia.org",		"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"commons",		"c",		null,		"http://",	"commons.wikimedia.org",	"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"mediawiki",	"mw",		null,		"http://",	"www.mediawiki.org",		"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikipedia",	"w",		wpLangs,	"http://",	"*.wikipedia.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikibooks",	"b",		defLangs,	"http://",	"*.wikibooks.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikinews",		"n",		defLangs,	"http://",	"*.wikinews.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikiquote",	"q",		defLangs,	"http://",	"*.wikiquote.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikisource",	"s",		defLangs,	"http://",	"*.wikisource.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wiktionary",	"wikt",		defLangs,	"http://",	"*.wiktionary.org",			"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");
		cfg.generate(outDir,	"wikiversity",	"v",		minLangs,	"http://",	"*.wikiversity.org",		"/w/index.php",	"/wiki/",		"/w/api.php",	"UTF-8",	"nds");

		// other sites			name			shortcut	languages	protocol	domain						rawPath			prettyPath		apiPath			charSet		uselang
		cfg.generate(outDir,	"uncyclopedia",	"unc",		null,		"http://",	"uncyclopedia.org",			"/index.php",	"/wiki/",		null, 			"UTF-8",	"nds");
		cfg.generate(outDir,	"kamelopedia",	"kamelo",	null,		"http://",	"kamelopedia.mormo.org",	"/index.php",	"/index.php/",	null, 			"UTF-8",	"nds");
	}
}
