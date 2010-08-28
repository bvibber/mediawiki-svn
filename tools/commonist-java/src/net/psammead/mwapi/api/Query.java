package net.psammead.mwapi.api;

import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import net.psammead.mwapi.NameSpace;
import net.psammead.util.DateUtil;
import net.psammead.util.StringUtil;

import org.apache.commons.httpclient.NameValuePair;
import org.apache.commons.httpclient.util.EncodingUtil;

/** an easy way to put together arguments for a query to api.php */
public final class Query {
	private static final String	FAKE_TRUE	= "yes";
	
	private Map<String,String>	args;
	
	public Query() {
		args	= new HashMap<String, String>();
	}
	
	private  Query(Query base, String key, String value) {
		this();
		args.putAll(base.args);
		args.put(key, value);
	}
	
	private Query extend(String key, String value) {
		return new Query(this, key, value);
	}
	
	//-------------------------------------------------------------------------
	//## input
	
	public Query string(String key, String value) {
		if (value == null)	return this;
		return extend(key, value);
	}
	
	public Query number(String key, Number value) {
		if (value == null)	return this;
		return extend(key, value.toString());
	}
	
	public Query bool(String key, boolean value) {
		if (!value)	return this;
		return extend(key, FAKE_TRUE);
	}
	
	public Query date(String key, Date value) {
		if (value == null)	return this;
		return extend(key, DateUtil.formatIso8601Timestamp(value));
	}
	
	public Query nameSpaces(String key, Iterable<NameSpace> value) {
		if (value == null)	return this;
		final List<String>	indizes	= new ArrayList<String>();
		for (NameSpace nameSpace : value)	indizes.add(""+nameSpace.index);
		final String	str	= StringUtil.join(indizes, "|");
		return extend(key, str);
	}
	
	//-------------------------------------------------------------------------
	//## enums
	
	public Query direction(String key, boolean newerNotOlder) {
		if (newerNotOlder)	return extend(key, "newer");
		else				return extend(key, "older");
	}
	
	public Query protocol(String key, String value) {
		if (value == null)	return this;
		if (!value.matches("http|https|ftp|irc|gopher|telnet|nntp|worldwind|mailto|news"))
			throw new IllegalArgumentException(key + " must be one of http, https, ftp, irc, gopher, telnet, nntp, worldwind, mailto, news: " + value);
		return extend(key, value);
	}

	public Query filterRedir(String key, String value) {
		if (value == null)	return this;
		if (!value.matches("all|redirects|nonredirects"))
			throw new IllegalArgumentException(key + " must be one of all, redirects, nonredirects: " + value);
		return extend(key, value);
	}
	
	public Query group(String key, String value) {
		if (value == null)	return this;
		if (!value.matches("bot|sysop|bureaucrat|checkuser|steward|boardvote|import|developer|oversight"))
			throw new IllegalArgumentException(key + " must be one of bot, sysop, bureaucrat, checkuser, steward, boardvote, import, developer, oversight: " + value);
		return extend(key, value);
	}
	
	public Query limit(String key, int value, int maximum) {
		if (value > maximum)	throw new IllegalArgumentException("limit may not exceed " + maximum);
		return extend(key, ""+value);
	}
	
	//-------------------------------------------------------------------------
	//## output
	
	public String toQueryString(String charSet) {
		return EncodingUtil.formUrlEncode(toNameValuePairs(), charSet);
	}
	
	public NameValuePair[] toNameValuePairs() {
		final List<NameValuePair>	list	= new ArrayList<NameValuePair>();
		for (Map.Entry<String, String> arg : args.entrySet()) {
			list.add(new NameValuePair(
					arg.getKey(), 
					arg.getValue()));
		}
		return list.toArray(new NameValuePair[list.size()]);
	}
}
