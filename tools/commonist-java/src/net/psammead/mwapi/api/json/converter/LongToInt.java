package net.psammead.mwapi.api.json.converter;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class LongToInt implements JSONConverter {
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException { 
		if (!(o instanceof Long))	throw new JSONConverterException("expected a Long: " + o);
		final Long	data	= (Long)o;
		
		return data.intValue(); 
	}
}
