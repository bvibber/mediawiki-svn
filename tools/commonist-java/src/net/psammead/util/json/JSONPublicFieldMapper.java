package net.psammead.util.json;

import java.lang.reflect.*;
import java.util.*;

/** converts all public, non-transient and non-static fields of an Object into a Map suitable for JSON */
public final class JSONPublicFieldMapper implements JSONMapper {
	public Object map(Object o) {
		final Map<String, Object>	out		= new LinkedHashMap<String, Object>();
		final Field[]				fields	= o.getClass().getFields();
		for (Field field : fields) {
			final int	modifiers	= field.getModifiers();
			if (!Modifier.isPublic(modifiers))		continue;
			if (Modifier.isTransient(modifiers))	continue;
			if (Modifier.isStatic(modifiers))		continue;
			
			try {
				final String	key		= field.getName();
				final Object	value	= field.get(o);
				out.put(key, value);
			}
			catch (Exception e) {
				throw new JSONMappingException("mapping field failed: " + field.getName(), e);
			}
		}
		return out;
	}
}
