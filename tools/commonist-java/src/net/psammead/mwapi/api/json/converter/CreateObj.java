package net.psammead.mwapi.api.json.converter;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.HashSet;
import java.util.List;
import java.util.Map;
import java.util.Set;

import net.psammead.mwapi.api.json.JSONConverter;
import net.psammead.mwapi.api.json.JSONConverterContext;
import net.psammead.mwapi.api.json.JSONConverterException;
import net.psammead.util.DebugUtil;
import net.psammead.util.reflect.ReflectUtil;

public final class CreateObj implements JSONConverter {
	public static boolean debug	= false;
	
	private final Class<?>		clazz;
	private List<JSONConverter>	params;

	public CreateObj(Class<?> clazz, JSONConverter... params) {
		this.clazz	= clazz;
		this.params	= Arrays.asList(params);
	}
	
	public Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException {
		debug("converting: " + clazz);
		printUnusedData(o);
		
		try {
			final List<Object> args	= new ArrayList<Object>();	
			for (JSONConverter param : params) {
				final Object	arg	= param.convert(ctx, o);
				args.add(arg);
				debug("carg: " + DebugUtil.shortClassName(arg) + "\t" + arg);
			}
			return ReflectUtil.object(clazz, args.toArray());
		}
		catch (Exception e) {
			throw new JSONConverterException("cannot convert to " + clazz, e);
		}
	}
	
	@SuppressWarnings("unchecked")
	private void printUnusedData(Object o) {
		if (!debug)					return;
		
		if (!(o instanceof Map))	return;
		final Map	data	= (Map<String,String>)o;
		
		final Set<String>	unused	= new HashSet(data.keySet());
		for (JSONConverter param : params) {
			if (!(param instanceof MapEntry))	continue;
			final MapEntry	entry	= (MapEntry)param;
			unused.remove(entry.key);
		}
		
		if (unused.isEmpty())	return;
		debug("not mapped: " + unused);
	}

	private void debug(String s) {
		if (!debug)	return;
		System.err.println(DebugUtil.shortClassName(this) + "\t" + s);
	}
}
