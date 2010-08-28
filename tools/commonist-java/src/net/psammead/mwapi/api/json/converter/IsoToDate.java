package net.psammead.mwapi.api.json.converter;

import java.text.ParseException;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;
import net.psammead.util.DateUtil;

public final class IsoToDate implements JSONConverter {
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof String))	throw new JSONConverterException("expected a String: " + o);
		final String	data	= (String)o;
		
		try {
			return DateUtil.parseIso8601Timestamp(data);
		}
		catch (ParseException e) {
			throw new RuntimeException("### cannot parse", e);
		}
	}

}
