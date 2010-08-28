package net.psammead.mwapi.api.json.converter;

import net.psammead.mwapi.Location;
import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class TitleToLocation implements JSONConverter {
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof String))	throw new JSONConverterException("expected a String: " + o);
		final String	data	= (String)o;
	
		return new Location(ctx.wiki, data);
	}
}
