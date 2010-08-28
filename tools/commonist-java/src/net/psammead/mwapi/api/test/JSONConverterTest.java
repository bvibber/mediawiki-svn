package net.psammead.mwapi.api.test;

import java.io.File;
import java.io.IOException;

import net.psammead.mwapi.api.API;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;
import net.psammead.util.IOUtil;
import net.psammead.util.json.JSONDecodeException;
import net.psammead.util.json.JSONDecoder;

public class JSONConverterTest {
	public static void main(String[] args) throws IOException, JSONConverterException, JSONDecodeException {
		testError();
		testProp();
		testList();
//		testAdditional();
	}
	
	private static JSONConverterContext	ctx	= new JSONConverterContext("wikipedia:de");
	
	private static Object loadJSON(String path) throws JSONDecodeException, IOException {
		return JSONDecoder.decode(IOUtil.readStringFromFile(
				new File("/home/daniel/Project/current/mwapi/test/data/" + path),
				"ISO-8859-1"));
	}
	
	//------------------------------------------------------------------------------
	//## additional
	
//	// @see versatz/yurik_json/
//	// normalized/missing	http://de.wikipedia.org/w/api.php?action=query&prop=imageinfo&titles=jjj|kkk&format=jsonfm
//	private static void testAdditional() throws JSONDecodeException, IOException, JSONConverterException {
//		loadJSON("normalized_missing.js");
//	}
	
	//------------------------------------------------------------------------------
	//## error
	
	// @see versatz/yurik_json/
	// http://de.wikipedia.org/w/api.php?action=query&prop=imaasgeinfo&titles=jjj&format=jsonfm
	
	private static void testError() throws JSONDecodeException, IOException, JSONConverterException {
		System.out.println(API.ERROR_CONVERTER.convert(
				ctx, loadJSON("error.js")));
	}
	
	//------------------------------------------------------------------------------
	//## prop
	
	// @see versatz/yurik_json/prop/
	private static void testProp() throws JSONDecodeException, IOException, JSONConverterException {
		// http://de.wikipedia.org/w/api.php?action=query&prop=categories&titles=Albert%20Einstein&clprop=sortkey&format=jsonfm
		System.out.println(API.CATEGORIES_CONVERTER.convert(
				ctx, loadJSON("prop/categories.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=extlinks&titles=Wikipedia:MwJed&format=jsonfm
		System.out.println(API.EXTLINKS_CONVERTER.convert(
				ctx, loadJSON("prop/extlinks.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=imageinfo&titles=Bild:Logo-pu-Aquin.png&iiprop=timestamp|user|comment|url|size&iihistory=yes&format=jsonfm
		System.out.println(API.IMAGEINFO_CONVERTER.convert(
				ctx, loadJSON("prop/imageinfo.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=images&titles=Hauptseite&format=jsonfm
		System.out.println(API.IMAGES_CONVERTER.convert(
				ctx, loadJSON("prop/images.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=info&inprop=protection&titles=Hauptseite&format=jsonfm
		System.out.println(API.INFO_CONVERTER.convert(
				ctx, loadJSON("prop/info.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=langlinks&titles=Hauptseite&format=jsonfm
		System.out.println(API.LANGLINKS_CONVERTER.convert(
				ctx, loadJSON("prop/langlinks.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=links&titles=Hauptseite&format=jsonfm
		System.out.println(API.LINKS_CONVERTER.convert(
				ctx, loadJSON("prop/links.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=revisions&titles=Hauptseite&rvprop=ids|flags|timestamp|user|comment&format=jsonfm
		System.out.println(API.REVISIONS_CONVERTER.convert(
				ctx, loadJSON("prop/revisions.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&prop=templates&titles=Hauptseite&format=jsonfm
		System.out.println(API.TEMPLATES_CONVERTER.convert(
				ctx, loadJSON("prop/templates.js")));
		
		System.out.println(API.IMAGES_CONVERTER.convert(
				ctx, loadJSON("prop/images2.js")));
	}

	//------------------------------------------------------------------------------
	//## list
	
	// @see versatz/yurik_json/list/
	private static void testList() throws JSONConverterException, JSONDecodeException, IOException {
		// http://de.wikipedia.org/w/api.php?action=query&list=allpages&apfrom=B&format=jsonfm
		System.out.println(API.ALLPAGES_CONVERTER.convert(
				ctx, loadJSON("list/allpages.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=alllinks&alunique&alfrom=B&alprop=ids|title&format=jsonfm
		System.out.println(API.ALLLINKS_CONVERTER.convert(
				ctx, loadJSON("list/alllinks.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=allusers&aufrom=Y&auprop=editcount|groups&format=jsonfm
		System.out.println(API.ALLUSERS_CONVERTER.convert(
				ctx, loadJSON("list/allusers.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=backlinks&bltitle=Hauptseite&format=jsonfm
		System.out.println(API.BACKLINKS_CONVERTER.convert(
				ctx, loadJSON("list/backlinks.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=categorymembers&cmcategory=Physik&cmprop=ids|title|sortkey&format=jsonfm
		System.out.println(API.CATEGORYMEMBERS_CONVERTER.convert(
				ctx, loadJSON("list/categorymembers.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=embeddedin&eititle=Template:Stub&format=jsonfm
		System.out.println(API.EMBEDDEDIN_CONVERTER.convert(
				ctx, loadJSON("list/embeddedin.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=exturlusage&euquery=www.mediawiki.org&euprop=ids|title|url&format=jsonfm
		System.out.println(API.EXTURLUSAGE_CONVERTER.convert(
				ctx, loadJSON("list/exturlusage.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=imageusage&iutitle=Image:Albert%20Einstein%20Head.jpg&format=jsonfm
		System.out.println(API.IMAGEUSAGE_CONVERTER.convert(
				ctx, loadJSON("list/imageusage.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=logevents&format=jsonfm
		System.out.println(API.LOGEVENTS_CONVERTER.convert(
				ctx, loadJSON("list/logevents.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=recentchanges&rcprop=user|comment|flags|timestamp|title|ids|sizes&format=jsonfm
		System.out.println(API.RECENTCHANGES_CONVERTER.convert(
				ctx, loadJSON("list/recentchanges.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=usercontribs&ucuser=YurikBot&ucprop=ids|title|timestamp|comment|flags&format=jsonf
		System.out.println(API.USERCONTRIBS_CONVERTER.convert(
				ctx, loadJSON("list/usercontribs.js")));
		
		// http://de.wikipedia.org/w/api.php?action=query&list=watchlist&wlprop=ids|title|flags|user|comment|timestamp|sizes&format=jsonfm
		System.out.println(API.WATCHLIST_CONVERTER.convert(
				ctx, loadJSON("list/watchlist.js")));
	}
}
