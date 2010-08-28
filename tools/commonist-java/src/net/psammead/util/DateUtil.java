package net.psammead.util;

import java.text.DateFormat;
import java.text.ParseException;
import java.text.SimpleDateFormat;
import java.util.Date;
import java.util.TimeZone;

import net.psammead.util.annotation.FullyStatic;

@FullyStatic
public final class DateUtil {
	// BETTER ?? use javax.xml.datatype.XMLGregorianCalendar
	public static final TimeZone	GMT_ZONE	= TimeZone.getTimeZone("GMT");
	public static final DateFormat	ISO_8601	= new SimpleDateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'");
	static { ISO_8601.setTimeZone(GMT_ZONE); }
	
	private DateUtil() {}

	/** parse a timestamp in ISO 8601 format */
	public static Date parseIso8601Timestamp(String s) throws ParseException {
		return ISO_8601.parse(s);
	}
	
	public static String formatIso8601Timestamp(Date d) {
		return ISO_8601.format(d);
	}
}
