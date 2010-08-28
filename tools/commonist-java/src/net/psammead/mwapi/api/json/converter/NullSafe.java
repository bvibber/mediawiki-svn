package net.psammead.mwapi.api.json.converter;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class NullSafe implements JSONConverter {
	private final JSONConverter	sub;

	public NullSafe(JSONConverter sub) {
		this.sub	= sub;
	}

	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (o == null)	return o;
		return sub.convert(ctx, o);
	}
}
