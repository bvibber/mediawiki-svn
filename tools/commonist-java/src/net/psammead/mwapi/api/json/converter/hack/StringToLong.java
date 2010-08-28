package net.psammead.mwapi.api.json.converter.hack;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

// TODO temporary, delete later

public final class StringToLong implements JSONConverter {
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException { 
		if (!(o instanceof String))	throw new JSONConverterException("expected a String: " + o);
		final String	data	= (String)o;
		
		try {
			return Long.parseLong(data);
		}
		catch (NumberFormatException e) {
			throw new JSONConverterException("invalid number: " + data, e);
		}
	}
}
