package net.psammead.mwapi.api.json.converter;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class MapValuesIter implements JSONConverter {
	private final JSONConverter	sub;
	
	public MapValuesIter(JSONConverter sub) {
		this.sub = sub;
	}
	
	@SuppressWarnings("unchecked")
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof Map))	throw new JSONConverterException("expected a List: " + o);
		final Map data	= (Map)o;
		
		final List out	= new ArrayList();
		for (Object i : data.values()) {
			out.add(sub.convert(ctx, i));
		}
		return out;
	}
}
