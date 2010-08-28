package net.psammead.mwapi.api.json.converter;

import java.util.ArrayList;
import java.util.List;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class ListIter implements JSONConverter {
	private final JSONConverter	sub;
	
	public ListIter(JSONConverter sub) {
		this.sub = sub;
	}
	
	@SuppressWarnings("unchecked")
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof List))	throw new JSONConverterException("expected a List: " + o);
		final List data	= (List)o;
		
		final List out	= new ArrayList();
		for (Object i : data) {
			out.add(sub.convert(ctx, i));
		}
		return out;
	}
}
