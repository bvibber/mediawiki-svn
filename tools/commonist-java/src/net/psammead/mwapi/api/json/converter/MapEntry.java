package net.psammead.mwapi.api.json.converter;

import java.util.Map;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class MapEntry implements JSONConverter {
	final String key;
	private final JSONConverter	sub;

	public MapEntry(String key, JSONConverter sub) {
		this.key	= key;
		this.sub	= sub;
	}

	@SuppressWarnings("unchecked")
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof Map))	throw new JSONConverterException("expected a Map: " + o);
		final Map		data	= (Map)o;

		final Object	value	= data.get(key);
		return sub.convert(ctx, value);
	}
}
