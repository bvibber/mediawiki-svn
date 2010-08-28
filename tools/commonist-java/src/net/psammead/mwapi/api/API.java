package net.psammead.mwapi.api;

import java.io.IOException;
import java.util.Date;
import java.util.List;
import java.util.Map;

import net.psammead.mwapi.MediaWiki;
import net.psammead.mwapi.MediaWikiException;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.api.data.*;
import net.psammead.mwapi.api.data.list.*;
import net.psammead.mwapi.api.data.prop.*;
import net.psammead.mwapi.api.json.*;
import net.psammead.mwapi.ui.MethodException;
import net.psammead.mwapi.ui.UnexpectedAnswerException;
import net.psammead.util.Logger;
import net.psammead.util.Throttle;
import net.psammead.util.json.JSONDecodeException;
import net.psammead.util.json.JSONDecoder;

import org.apache.commons.httpclient.HttpClient;
import org.apache.commons.httpclient.HttpException;
import org.apache.commons.httpclient.HttpMethod;
import org.apache.commons.httpclient.StatusLine;
import org.apache.commons.httpclient.URIException;
import org.apache.commons.httpclient.methods.GetMethod;

import static net.psammead.mwapi.api.json.JSONConverters.*;

/** interface to api.php */
public final class API {
	private final Logger		logger;
	private final HttpClient	client;
	private final Throttle		throttle;
	
//	private final String		wiki;
	private final String		apiURL;
	private final String		charSet;
	private final String		userAgent;
	
	private final JSONConverterContext	ctx;

	public API(
			Logger		logger,
			HttpClient	client,
			Throttle	throttle,
			String		apiURL,
			String		userAgent,
			String		wiki,
			String		charSet
	) {
		this.logger		= logger;
		this.client		= client;
		this.throttle	= throttle;
		this.apiURL		= apiURL;
		this.userAgent	= userAgent;
//		this.wiki		= wiki;
		this.charSet	= charSet;
		
		ctx	= new JSONConverterContext(wiki);
	}

	//==============================================================================
	//## prop
	
/* prop=categories (cl) *
  List all categories the page(s) belong to
Parameters:
  clprop         - Which additional properties to get for each category.
                   Values (separate with '|'): sortkey
*/
	public Categories categories(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"categories")
				.string("titles",	title)
				.string("clprop",	"sortkey");
		
		return (Categories)apiJSON(query, CATEGORIES_CONVERTER);
	}
	
	public static final JSONConverter	CATEGORIES_CONVERTER	= 
		CreateObj(Categories.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Categories_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("categories",
								CreateObj(Categories_categories.class,
									ListIter(
										CreateObj(Categories_cl.class,
											MapEntry("title",	TitleToLocation),
											MapEntry("sortkey",	Copy))))))))));
	
/* prop=extlinks (el) *
  Returns all external urls (not interwikies) from the given page(s)
*/
	public ExtLinks extLinks(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",	"extlinks")
				.string("titles",	title);
		
		return (ExtLinks)apiJSON(query, EXTLINKS_CONVERTER);
	}
	
	public static final JSONConverter	EXTLINKS_CONVERTER	= 
		CreateObj(ExtLinks.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(ExtLinks_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("extlinks",
								CreateObj(ExtLinks_extlinks.class,
									ListIter(
										CreateObj(ExtLinks_el.class,
											MapEntry("*",	Copy))))))))));

/* prop=imageinfo (ii) *
  Returns image information and upload history
Parameters:
  iiprop         - What image information to get.
                   Values (separate with '|'): timestamp, user, comment, url, size
                   Default: timestamp|user
  iihistory      - Include upload history
*/
	public ImageInfo imageInfo(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"imageinfo")
				.string("titles",	title)
				.string("iiprop",	"timestamp|user|comment|url|size")
				.bool("iihistory",	true);
		
		return (ImageInfo)apiJSON(query, IMAGEINFO_CONVERTER);
	}
	
	public static final JSONConverter	IMAGEINFO_CONVERTER	= 
		CreateObj(ImageInfo.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(ImageInfo_page.class,
							MapEntry("title",			TitleToLocation),
							MapEntry("pageid",			Copy),
							MapEntry("imagerepository",	Copy),
							MapEntry("imageinfo",
								CreateObj(ImageInfo_imageinfo.class,
									ListIter(
										CreateObj(ImageInfo_ii.class,
											MapEntry("timestamp",	IsoToDate),
											MapEntry("user",		Copy),
											MapEntry("size",		Copy),
											MapEntry("width",		LongToInt),
											MapEntry("height",		LongToInt),
											MapEntry("url",			Copy),
											MapEntry("comment",		Copy),
											MapEntry("content",		Copy))))))))));
	
/* prop=images (im) *
  Returns all images contained on the given page(s)
*/
	public Images images(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"images")
				.string("titles",	title);
		
		return (Images)apiJSON(query, IMAGES_CONVERTER);
	}
	
	public static final JSONConverter	IMAGES_CONVERTER	= 
		CreateObj(Images.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Images_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("images",
								CreateObj(Images_images.class,
									NullSafe(	// BÄH
										ListIter(
											CreateObj(Images_im.class,
												MapEntry("title",	TitleToLocation)))))))))));

/* prop=info (in) *
  Get basic page information such as namespace, title, last touched date, ...
Parameters:
  inprop         - Which additional properties to get:
                    "protection"   - List the protection level of each page
                   Values (separate with '|'): protection
*/
	public Info info(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"info")
				.string("titles",	title)
				.string("inprop",	"protection");
		
		return (Info)apiJSON(query, INFO_CONVERTER);
	}
	
	public static final JSONConverter	INFO_CONVERTER	= 
		CreateObj(Info.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Info_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("lastrevid",	Copy),
							MapEntry("touched",		IsoToDate),
							MapEntry("counter",		LongToInt),
							MapEntry("length",		LongToInt),
							MapEntry("redirect",	ExistsToBool))))));

		/*
		TODO
		MapToObj	converting: class net.psammead.mwapi.api.data.prop.Info_page
		MapToObj	not mapped: [protection, ns]
		 
		"protection": [
		]
		
		new MapParam("protection",
			ListIter( ???
		*/
	
/* prop=langlinks (ll) *
  Returns all interlanguage links from the given page(s)
*/		
	public LangLinks langLinks(String title) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"langlinks")
				.string("titles",	title);
		
		return (LangLinks)apiJSON(query, LANGLINKS_CONVERTER);
	}
	
	public static final JSONConverter	LANGLINKS_CONVERTER	= 
		CreateObj(LangLinks.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(LangLinks_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("langlinks",
								CreateObj(LangLinks_langlinks.class,
									ListIter(
										CreateObj(LangLinks_li.class,
											MapEntry("lang",	Copy))))))))));
											//new MapParam("*",	Copy)

/* prop=links (pl) *
  Returns all links from the given page(s)
Parameters:
  plnamespace    - Show links in this namespace(s) only
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
*/
	public Links links(String title, List<NameSpace> nameSpaces) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"links")
				.string("titles",	title)
				.nameSpaces("plnamespace",	nameSpaces);
		
		return (Links)apiJSON(query, LINKS_CONVERTER);
	}
	
	public static final JSONConverter	LINKS_CONVERTER	= 
		CreateObj(Links.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Links_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("links",
								CreateObj(Links_links.class,
									ListIter(
										CreateObj(Links_pl.class,
											MapEntry("title",	TitleToLocation))))))))));
	
/* prop=revisions (rv) *
Get revision information.
This module may be used in several ways:
 1) Get data about a set of pages (last revision), by setting titles or pageids parameter.
 2) Get revisions for one given page, by using titles/pageids with start/end/limit params.
 3) Get data about a set of revisions by setting their IDs with revids parameter.
All parameters marked as (enum) may only be used with a single page (#2).
Parameters:
rvprop         - Which properties to get for each revision.
                 Values (separate with '|'): ids, flags, timestamp, user, comment, content
                 Default: ids|timestamp|flags|comment|user
rvlimit        - limit how many revisions will be returned (enum)
                 No more than 50 (500 for bots) allowed.
rvstartid      - from which revision id to start enumeration (enum)
rvendid        - stop revision enumeration on this revid (enum)
rvstart        - from which revision timestamp to start enumeration (enum)
rvend          - enumerate up to this timestamp (enum)
rvdir          - direction of enumeration - towards "newer" or "older" revisions (enum)
                 One value: newer, older
                 Default: older
rvuser         - only include revisions made by user
rvexcludeuser  - exclude revisions made by user
*/
	public static final int REVISIONS_MAX_LIMIT	= 50;
	
	public Revisions revisions(
			String title,
			Long startId, Long endId,
			Date start, Date end, boolean newer,
			String user, String excludeUser,
			int limit) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"revisions")
				.string("titles",	title)
				.string("rvprop",			"ids|flags|timestamp|user|comment")	// TODO add |content
				.number("rvstartid",		startId)
				.number("rvendid",			endId)
				.date("rvstart",			start)
				.date("rvend",				end)
				.direction("rvdir",			newer)
				.string("rvuser",			user)
				.string("rvexcludeuser",	excludeUser)
				.limit("rvlimit",			limit, REVISIONS_MAX_LIMIT);
		
		return (Revisions)apiJSON(query, REVISIONS_CONVERTER);
	}
	
	public static final JSONConverter	REVISIONS_CONVERTER	= 
		CreateObj(Revisions.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Revisions_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("revisions",
								CreateObj(Revisions_revisions.class,
									ListIter(
										CreateObj(Revisions_rev.class,
											MapEntry("revid",		Copy),
											MapEntry("user",		Copy),
											MapEntry("timestamp",	IsoToDate),
											MapEntry("comment",		Copy),
											MapEntry("minor",		ExistsToBool),
											MapEntry("anon",		ExistsToBool),
											MapEntry("content",		Copy))))))))));
	
/* prop=templates (tl) *
  Returns all templates from the given page(s)
Parameters:
  tlnamespace    - Show templates in this namespace(s) only
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
*/
	public Templates templates(String title, List<NameSpace> nameSpaces) throws MediaWikiException {
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("prop",		"templates")
				.string("titles",	title)
				.nameSpaces("tlnamespace",	nameSpaces);
		
		return (Templates)apiJSON(query, TEMPLATES_CONVERTER);
	}
	
	public static final JSONConverter	TEMPLATES_CONVERTER	= 
		CreateObj(Templates.class,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(Templates_page.class,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry("templates",
								CreateObj(Templates_templates.class,
									ListIter(
										CreateObj(Templates_tl.class,
											MapEntry("title",	TitleToLocation))))))))));
		
	//==============================================================================
	//## list
	
/* list=allpages (ap) *
  Enumerate all pages sequentially in a given namespace
Parameters:
  apfrom         - The page title to start enumerating from.
  apprefix       - Search for all page titles that begin with this value.
  apnamespace    - The namespace to enumerate.
				   One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
				   Default: 0
  apfilterredir  - Which pages to list.
				   One value: all, redirects, nonredirects
				   Default: all
  aplimit        - How many total pages to return.
				   No more than 500 (5000 for bots) allowed.
				   Default: 10
*/
	public static final int	ALLPAGES_MAX_LIMIT	= 500;
	
	public AllPages allPages(
			String from, String prefix, List<NameSpace> nameSpaces, String filterRedir,
			int limit) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"allpages")
				.string("apfrom",				from)
				.string("apprefix",				prefix)
				.nameSpaces("apnamespace",		nameSpaces)
				.filterRedir("apfilterredir",	filterRedir)
				.limit("aplimit", limit, ALLPAGES_MAX_LIMIT);
		
		return (AllPages)apiJSON(query, ALLPAGES_CONVERTER);
	}
	
	public static final JSONConverter	ALLPAGES_CONVERTER	= 
		CreateObj(AllPages.class,
			MapEntry("query", 
				CreateObj(AllPages_allpages.class,
					MapEntry("allpages", 
						ListIter(
							CreateObj(AllPages_p.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("allpages",
						MapEntry("apfrom",	Copy)))));

/* list=alllinks (al) *
  Enumerate all links that point to a given namespace
Parameters:
  alfrom         - The page title to start enumerating from.
  alprefix       - Search for all page titles that begin with this value.
  alunique       - Only show unique links. Cannot be used with generator or prop=ids
  alprop         - What pieces of information to include
                   Values (separate with '|'): ids, title
                   Default: title
  alnamespace    - The namespace to enumerate.
                   One value: 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
                   Default: 0
  allimit        - How many total links to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	ALLLINKS_MAX_LIMIT	= 500;
	
	public AllLinks allLinks(
			String from, String prefix, List<NameSpace> nameSpaces, /*boolean unique,*/
			int limit) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"alllinks")
				.string("alprop",	"ids|title")
				.string("alfrom",			from)
				.string("alprefix",			prefix)
				.nameSpaces("alnamespace",	nameSpaces)
				.bool("alunique",			false)	// unique and  alprop=id do not work together
				.limit("allimit", limit, ALLLINKS_MAX_LIMIT);
		
		return (AllLinks)apiJSON(query, ALLLINKS_CONVERTER);
	}
	
	public static final JSONConverter	ALLLINKS_CONVERTER	= 
		CreateObj(AllLinks.class,
			MapEntry("query", 
				CreateObj(AllLinks_alllinks.class,
					MapEntry("alllinks", 
						ListIter(
							CreateObj(AllLinks_l.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("fromid",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("alllinks",
						MapEntry("alfrom",	Copy)))));
	
/* list=allusers (au) *
  Enumerate all registered users
Parameters:
  aufrom         - The user name to start enumerating from.
  auprefix       - Search for all page titles that begin with this value.
  augroup        - Limit users to a given group name
                   One value: bot, sysop, bureaucrat, checkuser, steward, boardvote, import, developer, oversight
  auprop         - What pieces of information to include.
                   `groups` property uses more server resources and may return fewer results than the limit.
                   Values (separate with '|'): editcount, groups
  aulimit        - How many total user names to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	ALLUSERS_MAX_LIMIT	= 500;
	
	public AllUsers allUsers(
			String from, String prefix, List<NameSpace> nameSpaces, String group,
			int limit) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"allusers")
				.string("auprop",			"editcount|groups")
				.string("aufrom",			from)
				.string("auprefix",			prefix)
				.nameSpaces("alnamespace",	nameSpaces)
				.group("augroup",			group)
				.limit("aulimit", limit, ALLUSERS_MAX_LIMIT);
		
		return (AllUsers)apiJSON(query, ALLUSERS_CONVERTER);
	}
	
	public static final JSONConverter	ALLUSERS_CONVERTER	= 
		CreateObj(AllUsers.class,
			MapEntry("query", 
				CreateObj(AllUsers_allusers.class,
					MapEntry("allusers", 
						ListIter(
							CreateObj(AllUsers_u.class,
								MapEntry("name",		Copy),
								MapEntry("editcount",	LongToInt)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("allusers",
						MapEntry("aufrom",	Copy)))));
	
/* list=backlinks (bl) *
  Find all pages that link to the given page
Parameters:
  bltitle        - Title to search. If null, titles= parameter will be used instead, but will be obsolete soon.
  blcontinue     - When more results are available, use this to continue.
  blnamespace    - The namespace to enumerate.
				   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  blredirect     - If linking page is a redirect, find all pages that link to that redirect (not implemented)
  bllimit        - How many total pages to return.
				   No more than 500 (5000 for bots) allowed.
				   Default: 10
*/
	public static final int	BACKLINKS_MAX_LIMIT	= 500;
	
	public BackLinks backLinks(
			String title, List<NameSpace> nameSpaces, boolean redirect,
			int limit, String continueKey) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"backlinks")
				.string("bltitle",			title)
				.nameSpaces("blnamespace",	nameSpaces)
				.bool("blredirect",			redirect)
				.string("blcontinue",		continueKey)
				.limit("bllimit", limit, BACKLINKS_MAX_LIMIT);
		
		return (BackLinks)apiJSON(query, BACKLINKS_CONVERTER);
	}
	
	public static final JSONConverter	BACKLINKS_CONVERTER	= 
		CreateObj(BackLinks.class,
			MapEntry("query", 
				CreateObj(BackLinks_backlinks.class,
					MapEntry("backlinks", 
						ListIter(
							CreateObj(BackLinks_bl.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("backlinks",
						MapEntry("blcontinue",	Copy)))));
	
/* list=categorymembers (cm) *
  List all pages in a given category
Parameters:
  cmcategory     - Which category to enumerate (required)
  cmprop         - What pieces of information to include
                   Values (separate with '|'): ids, title, sortkey
                   Default: ids|title
  cmnamespace    - Only include pages in these namespaces
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  cmcontinue     - For large categories, give the value retured from previous query
  cmlimit        - The maximum number of pages to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	CATEGORYMEMBERS_MAX_LIMIT	= 500;
	
	public CategoryMembers categoryMembers(
			String category, List<NameSpace> nameSpaces, 
			int limit, String continueKey) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"categorymembers")
				.string("cmcategory",		category)
				.string("cmprop",			"ids|title|sortkey")
				.nameSpaces("cmnamespace",	nameSpaces)
				.string("cmcontinue",		continueKey)
				.limit("cmlimit", limit, CATEGORYMEMBERS_MAX_LIMIT);
		
		return (CategoryMembers)apiJSON(query, CATEGORYMEMBERS_CONVERTER);
	}
	
	public static final JSONConverter	CATEGORYMEMBERS_CONVERTER	= 
		CreateObj(CategoryMembers.class,
			MapEntry("query", 
				CreateObj(CategoryMembers_categorymembers.class,
					MapEntry("categorymembers",
						ListIter(
							CreateObj(CategoryMembers_cm.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("sortkey",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("categorymembers",
						MapEntry("cmcontinue",	Copy)))));
	
/* list=embeddedin (ei) *
  Find all pages that embed (transclude) the given title
Parameters:
  eititle        - Title to search. If null, titles= parameter will be used instead, but will be obsolete soon.
  eicontinue     - When more results are available, use this to continue.
  einamespace    - The namespace to enumerate.
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  eiredirect     - If linking page is a redirect, find all pages that link to that redirect (not implemented)
  eilimit        - How many total pages to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/		
	public static final int	EMBEDDEDIN_MAX_LIMIT	= 500;
	
	public EmbeddedIn embeddedIn(
			String title, List<NameSpace> nameSpaces, boolean redirect,
			int limit, String continueKey) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"embeddedin")
				.string("eititle",			title)
				.nameSpaces("einamespace",	nameSpaces)
				.bool("eiredirect",			redirect)
				.string("eicontinue",		continueKey)
				.limit("eilimit", limit, EMBEDDEDIN_MAX_LIMIT);
				
		return (EmbeddedIn)apiJSON(query, EMBEDDEDIN_CONVERTER);
	}
	
	public static final JSONConverter	EMBEDDEDIN_CONVERTER	= 
		CreateObj(EmbeddedIn.class,
			MapEntry("query",
				CreateObj(EmbeddedIn_embeddedin.class,
					MapEntry("embeddedin",
						ListIter(
							CreateObj(EmbeddedIn_ei.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("embeddedin",
						MapEntry("eicontinue",	Copy)))));
	
/* list=exturlusage (eu) *
  Enumerate pages that contain a given URL
Parameters:
  euprop         - What pieces of information to include
                   Values (separate with '|'): ids, title, url
                   Default: ids|title|url
  euoffset       - Used for paging. Use the value returned for "continue"
  euprotocol     - Protocol of the url
                   One value: http, https, ftp, irc, gopher, telnet, nntp, worldwind, mailto, news
                   Default: http
  euquery        - Search string without protocol. See [[Special:LinkSearch]]
  eunamespace    - The page namespace(s) to enumerate.
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  eulimit        - How many entries to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	EXTURLUSAGE_MAX_LIMIT	= 500;
	
	public ExtUrlUsage extUrlUsage(
			String protocol, String search, List<NameSpace> nameSpaces,
			int limit, String continueKey) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"exturlusage")
				.string("euquery",			search)
				.protocol("euprotocol",		protocol)
				.string("euprop",			"ids|title|url")
				.nameSpaces("eunamespace",	nameSpaces)
				.string("euoffset",			continueKey)	// NOTE: not "continue" this time // TODO not a number??
				.limit("eulimit", limit, EXTURLUSAGE_MAX_LIMIT);
		
		return (ExtUrlUsage)apiJSON(query, EXTURLUSAGE_CONVERTER);
	}
	
	public static final JSONConverter	EXTURLUSAGE_CONVERTER	= 
		CreateObj(ExtUrlUsage.class,
			MapEntry("query", 
				CreateObj(ExtUrlUsage_exturlusage.class,
					MapEntry("exturlusage",
						ListIter(
							CreateObj(ExtUrlUsage_eu.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("url",			Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("exturlusage",
						MapEntry("euoffset",	LongToInt)))));
	
/* list=imageusage (iu) *
  Find all pages that use the given image title.
Parameters:
  iutitle        - Title to search. If null, titles= parameter will be used instead, but will be obsolete soon.
  iucontinue     - When more results are available, use this to continue.
  iunamespace    - The namespace to enumerate.
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  iuredirect     - If linking page is a redirect, find all pages that link to that redirect (not implemented)
  iulimit        - How many total pages to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	IMAGEUSAGE_MAX_LIMIT	= 500;
	
	public ImageUsage imageUsage(
			String title, List<NameSpace> nameSpaces, boolean redirect,
			int limit, String continueKey) throws MediaWikiException {
		
		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"imageusage")
				.string("iutitle",			title)
				.nameSpaces("iunamespace",	nameSpaces)
				.bool("iuredirect",			redirect)
				.string("iucontinue",		continueKey)
				.limit("iulimit", limit, IMAGEUSAGE_MAX_LIMIT);
		
		return (ImageUsage)apiJSON(query, IMAGEUSAGE_CONVERTER);
	}
	
	public static final JSONConverter	IMAGEUSAGE_CONVERTER	= 
		CreateObj(ImageUsage.class,
			MapEntry("query", 
				CreateObj(ImageUsage_imageusage.class,
					MapEntry("imageusage",
						ListIter(
							CreateObj(ImageUsage_iu.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("imageusage",
						MapEntry("iucontinue",	Copy)))));
	
/* list=logevents (le) *
  Get events from logs.
Parameters:
  leprop         - 
                   Values (separate with '|'): ids, title, type, user, timestamp, comment, details
                   Default: ids|title|type|user|timestamp|comment|details
  letype         - Filter log entries to only this type(s)
                   Can be empty, or Values (separate with '|'): block, protect, rights, delete, upload, move, import, patrol, renameuser, newusers, makebot
  lestart        - The timestamp to start enumerating from.
  leend          - The timestamp to end enumerating.
  ledir          - In which direction to enumerate.
                   One value: newer, older
                   Default: older
  leuser         - Filter entries to those made by the given user.
  letitle        - Filter entries to those related to a page.
  lelimit        - How many total event entries to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	LOGEVENTS_MAX_LIMIT	= 500;
	
	public LogEvents logEvents(
			Date start, Date end, boolean newer, 
			String user, String title,
			int limit) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"logevents")
				.string("leprop",		"ids|title|type|user|timestamp|comment|details")
				.string("letype",		"block|protect|rights|delete|upload|move|import|patrol|renameuser|newusers|makebot")
				.date("lestart",		start)
				.date("leend",			end)
				.direction("ledir",		newer)
				.string("leuser",		user)
				.string("letitle",		title)
				.limit("lelimit", limit, LOGEVENTS_MAX_LIMIT);
		
		return (LogEvents)apiJSON(query, LOGEVENTS_CONVERTER);
	}
	
	public static final JSONConverter	LOGEVENTS_CONVERTER	= 
		CreateObj(LogEvents.class,
			MapEntry("query", 
				CreateObj(LogEvents_logevents.class,
					MapEntry("logevents",
						ListIter(
							CreateObj(LogEvents_item.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("logid",		LongToInt),
								MapEntry("timestamp",	IsoToDate),
								MapEntry("type",		Copy),
								MapEntry("action",		Copy),
								MapEntry("user",		Copy),
								MapEntry("comment",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("logevents",
						MapEntry("lestart",	IsoToDate)))));
		/*
		TODO
		MapToObj	converting: class net.psammead.mwapi.api.data.list.LogEvents_item
		MapToObj	not mapped: [ns, move]
		"move": {
			"new_ns": 0,
			"new_title": "Johannes Garcaeus der \u00c4ltere"
		},
		*/
	
	
/* list=recentchanges (rc) *
  Enumerate recent changes
Parameters:
  rcstart        - The timestamp to start enumerating from.
  rcend          - The timestamp to end enumerating.
  rcdir          - In which direction to enumerate.
                   One value: newer, older
                   Default: older
  rcnamespace    - Filter log entries to only this namespace(s)
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  rcprop         - Include additional pieces of information
                   Values (separate with '|'): user, comment, flags, timestamp, title, ids, sizes
                   Default: title|timestamp|ids
  rcshow         - Show only items that meet this criteria.
                   For example, to see only minor edits done by logged-in users, set show=minor|!anon
                   Values (separate with '|'): minor, !minor, bot, !bot, anon, !anon
  rclimit        - How many total pages to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
*/
	public static final int	RECENTCHANGES_MAX_LIMIT	= 500;
	
	public RecentChanges recentChanges(
			List<NameSpace> nameSpaces, Date start, Date end, boolean newer, 
			int limit) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"recentchanges")
				.string("rcprop",			"user|comment|flags|timestamp|title|ids|sizes")
				.nameSpaces("rcnamespace",	nameSpaces)
				.date("rcstart",			start)
				.date("rcend",				end)
				.direction("rcdir",			newer)
				/*
				rcshow         - Show only items that meet this criteria.
		                   For example, to see only minor edits done by logged-in users, set show=minor|!anon
		                   Values (separate with '|'): minor, !minor, bot, !bot, anon, !anon
				*/
				.limit("rclimit", limit, RECENTCHANGES_MAX_LIMIT);
		
		return (RecentChanges)apiJSON(query, RECENTCHANGES_CONVERTER);
	}
	
	public static final JSONConverter	RECENTCHANGES_CONVERTER	= 
		CreateObj(RecentChanges.class,
			MapEntry("query", 
				CreateObj(RecentChanges_recentchanges.class,
					MapEntry("recentchanges",
						ListIter(
							CreateObj(RecentChanges_rc.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("revid",		Copy),
								MapEntry("old_revid",	Copy),
								MapEntry("rcid",		Copy),
								MapEntry("timestamp",	IsoToDate),
								MapEntry("new",			ExistsToBool),
								MapEntry("minor",		ExistsToBool),
								MapEntry("anon",		ExistsToBool),
								MapEntry("type",		Copy),
								MapEntry("user",		Copy),
								MapEntry("oldlen",		LongToInt),
								MapEntry("newlen",		LongToInt),
								MapEntry("comment",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("recentchanges",
						MapEntry("rcstart",	IsoToDate)))));
	
/* list=usercontribs (uc) *
  Get all edits by a user
Parameters:
  uclimit        - The maximum number of contributions to return.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
  ucstart        - The start timestamp to return from.
  ucend          - The end timestamp to return to.
  ucuser         - The user to retrieve contributions for.
  ucdir          - The direction to search (older or newer).
                   One value: newer, older
                   Default: older
  ucnamespace    - Only list contributions in these namespaces
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  ucprop         - Include additional pieces of information
                   Values (separate with '|'): ids, title, timestamp, comment, flags
                   Default: ids|title|timestamp|flags|comment
  ucshow         - Show only items that meet this criteria, e.g. non minor edits only: show=!minor
                   Values (separate with '|'): minor, !minor
*/
	public static final int	USERCONTRIBS_MAX_LIMIT	= 500;
	
	public UserContribs userContribs(
			String user, List<NameSpace> nameSpaces, Date start, Date end, boolean newer, 
			int limit) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"usercontribs")
				.string("ucuser",			user)
				.string("ucprop",			"ids|title|timestamp|comment|flags")
				.nameSpaces("ucnamespace",	nameSpaces)
				.date("ucstart",			start)
				.date("ucend",				end)
				.direction("ucdir",			newer)
				/*
				show         - Show only items that meet this criteria, e.g. non minor edits only: show=!minor
		                 		Values (separate with '|'): minor, !minor
				*/
				.limit("uclimit", limit, USERCONTRIBS_MAX_LIMIT);
		
		return (UserContribs)apiJSON(query, USERCONTRIBS_CONVERTER);
	}
	
	public static final JSONConverter	USERCONTRIBS_CONVERTER	= 
		CreateObj(UserContribs.class,
			MapEntry("query", 
				CreateObj(UserContribs_usercontribs.class,
					MapEntry("usercontribs",
						ListIter(
							CreateObj(UserContribs_item.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("revid",		Copy),
								MapEntry("timestamp",	IsoToDate),
								MapEntry("new",			ExistsToBool),
								MapEntry("minor",		ExistsToBool),
								MapEntry("comment",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("usercontribs",
						MapEntry("ucstart",	IsoToDate)))));
	
/* list=watchlist (wl) *

Parameters:
  wlallrev       - Include multiple revisions of the same page within given timeframe.
  wlstart        - The timestamp to start enumerating from.
  wlend          - The timestamp to end enumerating.
  wlnamespace    - Filter changes to only the given namespace(s).
                   Values (separate with '|'): 0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 100, 101
  wldir          - In which direction to enumerate pages.
                   One value: newer, older
                   Default: older
  wllimit        - How many total pages to return per request.
                   No more than 500 (5000 for bots) allowed.
                   Default: 10
  wlprop         - Which additional items to get (non-generator mode only).
                   Values (separate with '|'): ids, title, flags, user, comment, timestamp, patrol, sizes
                   Default: ids|title|flags
*/
	public static final int	WATCHLIST_MAX_LIMIT	= 500;
	
	public WatchList watchList(
			List<NameSpace> nameSpaces, Date start, Date end, boolean newer, 
			int limit) throws MediaWikiException {

		final Query	query	= new Query()
				.string("action",	"query")
				.string("format",	"json")
				.string("list",		"watchlist")
				.string("wlprop",			"ids|title|flags|user|comment|timestamp|sizes")	// TODO patrol (not available sometimes)
				.bool("wlallrev",			true)
				.nameSpaces("wlnamespace",	nameSpaces)
				.date("wlstart",			start)
				.date("wlend",				end)
				.direction("wldir",			newer)
				.limit("wllimit", limit, WATCHLIST_MAX_LIMIT);
		
		return (WatchList)apiJSON(query, WATCHLIST_CONVERTER);
	}
	
	public static final JSONConverter	WATCHLIST_CONVERTER	= 
		CreateObj(WatchList.class,
			MapEntry("query", 
				CreateObj(WatchList_watchlist.class,
					MapEntry("watchlist",
						ListIter(
							CreateObj(WatchList_item.class,
								MapEntry("title",		TitleToLocation),
								MapEntry("pageid",		Copy),
								MapEntry("revid",		Copy),
								MapEntry("timestamp",	IsoToDate),
								MapEntry("new",			ExistsToBool),
								MapEntry("minor",		ExistsToBool),
								MapEntry("anon",		ExistsToBool),
								MapEntry("type",		Copy),
								MapEntry("user",		Copy),
								MapEntry("oldlen",		LongToInt),
								MapEntry("newlen",		LongToInt),
								MapEntry("comment",		Copy)))))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry("watchlist",
						MapEntry("wlstart",	IsoToDate)))));
	
	//==============================================================================
	//## private helper
	
	public static final JSONConverter	ERROR_CONVERTER	=
		MapEntry("error",
			CreateObj(Error_.class,
				MapEntry("code",	Copy),
				MapEntry("info",	Copy),
				MapEntry("*",		Copy)));
	
	/** fetch api.php, convert to JSON and handle error tags */
	private Object apiJSON(Query query, JSONConverter resultConverter) throws MediaWikiException {
		final String	raw	= apiGET(query.toQueryString(charSet));
		final Object	json;
		try { 
			json = JSONDecoder.decode(raw);
		}
		catch (JSONDecodeException e) { 
			throw new APIJSONException("json decoding failed", e)
					.addFactoid("content",	raw); 
		}
		
		if (!(json instanceof Map<?,?>)) {
			throw new APIJSONException("expected a Map at top level")
					.addFactoid("content",	raw)
					.addFactoid("json",		json);
		}
		final Map<?,?>	data	= (Map<?,?>)json;

		try { 
			if (data.get("error") != null) {
				throw new APIErrorException(
						(Error_)ERROR_CONVERTER.convert(ctx, json)); 
			}
			return resultConverter.convert(ctx, json); 
		}
		catch (JSONConverterException e) { 
			throw new APIJSONException("json conversion failed", e)
					.addFactoid("content",	raw)
					.addFactoid("json",		json);
		}
	}

	// TODO use POST ?
	private String apiGET(String query) throws MediaWikiException {
		HttpMethod	method	= null;
		try {
			throttle.gate();
			
			// execute method
			method	= new GetMethod(apiURL);
			method.getParams().setCookiePolicy(MediaWiki.COOKIE_POLICY);
			method.setFollowRedirects(false);
			method.addRequestHeader("User-Agent", userAgent);
			method.setQueryString(query);
			
			int			responseCode	= client.executeMethod(method);
			String		responseBody	= method.getResponseBodyAsString();
			StatusLine	statusLine		= method.getStatusLine();
			debug(method);
			
			// handle response
			if (responseCode != 200) {
				throw new UnexpectedAnswerException("unexpected response code from api.php")
						.addFactoid("status",	statusLine)
						.addFactoid("content",	responseBody);
			}
			
			return responseBody;
		}
		catch (HttpException		e) { throw new MethodException("method failed",  e); }
		catch (IOException			e) { throw new MethodException("method failed",  e); }
		catch (InterruptedException	e) { throw new MethodException("method aborted", e); }
		finally { if (method != null) method.releaseConnection(); }
	}
	
	/** print debug info for a HTTP-request */
	private void debug(HttpMethod method) throws URIException {
		logger.debug(
				"HTTP " + method.getName() + 
				" " + method.getURI().toString() + 
				" " + method.getStatusLine());
	}
}
