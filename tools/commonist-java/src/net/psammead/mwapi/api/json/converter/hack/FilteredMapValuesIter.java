package net.psammead.mwapi.api.json.converter.hack;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;
import java.util.Set;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;

public final class FilteredMapValuesIter implements JSONConverter {
	private final JSONConverter	sub;
	private final String		keyRegexp;
	
	public FilteredMapValuesIter(String keyRegexp, JSONConverter sub) {
		this.sub		= sub;
		this.keyRegexp	= keyRegexp;
	}
	
	@SuppressWarnings("unchecked")
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		if (!(o instanceof Map))	throw new JSONConverterException("expected a List: " + o);
		final Map data	= (Map)o;
		
		final List out	= new ArrayList();
		final Set<String>	keys	= data.keySet();
		for (String key : keys) {
			if (!key.matches(keyRegexp))	continue;
			final Object	raw		= data.get(key);
			final Object	mangled	= sub.convert(ctx, raw);
			out.add(mangled);
		}
		return out;
	}
}
