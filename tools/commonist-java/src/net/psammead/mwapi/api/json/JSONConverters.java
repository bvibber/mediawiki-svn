package net.psammead.mwapi.api.json;

import net.psammead.mwapi.api.json.converter.Copy;
import net.psammead.mwapi.api.json.converter.CreateObj;
import net.psammead.mwapi.api.json.converter.ExistsToBool;
import net.psammead.mwapi.api.json.converter.IsoToDate;
import net.psammead.mwapi.api.json.converter.ListIter;
import net.psammead.mwapi.api.json.converter.LongToInt;
import net.psammead.mwapi.api.json.converter.MapEntry;
import net.psammead.mwapi.api.json.converter.MapValuesIter;
import net.psammead.mwapi.api.json.converter.NullSafe;
import net.psammead.mwapi.api.json.converter.TitleToLocation;
import net.psammead.mwapi.api.json.converter.hack.FilteredMapValuesIter;
import net.psammead.mwapi.api.json.converter.hack.StringToInt;
import net.psammead.mwapi.api.json.converter.hack.StringToLong;

public final class JSONConverters {
	private JSONConverters() {}
	
	public static JSONConverter Copy			= new Copy();
	public static JSONConverter ExistsToBool	= new ExistsToBool();
	public static JSONConverter IsoToDate		= new IsoToDate();
	public static JSONConverter LongToInt		= new LongToInt();
	public static JSONConverter TitleToLocation	= new TitleToLocation();
	public static JSONConverter StringToInt		= new StringToInt();
	public static JSONConverter StringToLong	= new StringToLong();
	
	public static JSONConverter CreateObj(Class<?> clazz, JSONConverter... params)			{ return new CreateObj(clazz, params); }
	public static JSONConverter ListIter(JSONConverter sub)									{ return new ListIter(sub); }
	public static JSONConverter MapEntry(String key, JSONConverter sub)						{ return new MapEntry(key, sub); }
	public static JSONConverter MapValuesIter(JSONConverter sub)							{ return new MapValuesIter(sub); }
	public static JSONConverter NullSafe(JSONConverter sub)									{ return new NullSafe(sub); }
	// TODO unused
	public static JSONConverter FilteredMapValuesIter(String keyRegexp, JSONConverter sub)	{ return new FilteredMapValuesIter(keyRegexp, sub); }
}
