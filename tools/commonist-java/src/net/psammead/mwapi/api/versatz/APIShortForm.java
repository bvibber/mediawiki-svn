package net.psammead.mwapi.api.versatz;

import net.psammead.mwapi.api.data.list.WatchList;
import net.psammead.mwapi.api.data.list.WatchList_item;
import net.psammead.mwapi.api.data.list.WatchList_watchlist;
import net.psammead.mwapi.api.data.prop.Templates;
import net.psammead.mwapi.api.data.prop.Templates_page;
import net.psammead.mwapi.api.data.prop.Templates_templates;
import net.psammead.mwapi.api.data.prop.Templates_tl;
import net.psammead.mwapi.api.json.JSONConverter;

import static net.psammead.mwapi.api.json.JSONConverters.*;

/** temporary notes */
public class APIShortForm {
	public static final JSONConverter	TEMPLATES_CONVERTER_2	=
		propConv(
			Templates.class,
			Templates_page.class,
			"templates",
			Templates_templates.class,
			CreateObj(Templates_tl.class,
				MapEntry("title",	TitleToLocation)));
	
	public static final JSONConverter	WATCHLIST_CONVERTER_2	= 
		listConv(
			WatchList.class, 
			"watchlist",	
			WatchList_watchlist.class,
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
				MapEntry("comment",		Copy)),
			"wlstart", 
			IsoToDate);
	
	private static final JSONConverter propConv(
			Class<?> resultClass,
			Class<?> pageClass,
			String listName,
			Class<?> listClass,
			JSONConverter itemConv) {
		return 
		CreateObj(resultClass,
			MapEntry("query", 
				MapEntry("pages",
					MapValuesIter(
						CreateObj(pageClass,
							MapEntry("title",		TitleToLocation),
							MapEntry("pageid",		Copy),
							MapEntry(listName, 
								CreateObj(listClass,	ListIter(itemConv))))))));
	}
	
	private static final JSONConverter listConv(
			Class<?> resultClass, 
			String listName, Class<?> listClass, 
			JSONConverter itemConverter, 
			String continueField, JSONConverter continueConverter) {
		return
		CreateObj(resultClass,
			MapEntry("query", 
				CreateObj(listClass,
					MapEntry(listName,
						ListIter(
							itemConverter)))),
			MapEntry("query-continue",
				NullSafe(
					MapEntry(listName,
						MapEntry(continueField,
								continueConverter)))));
	}
}
