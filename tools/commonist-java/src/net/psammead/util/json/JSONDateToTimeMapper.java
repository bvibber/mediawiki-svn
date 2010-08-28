package net.psammead.util.json;

import java.util.*;

import net.psammead.util.annotation.ImmutableValue;

/** converts Date objects into their Time property value */
@ImmutableValue 
public final class JSONDateToTimeMapper implements JSONMapper {
	public Object map(Object o) {
		return ((Date)o).getTime();
	}
}
