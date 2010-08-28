package net.psammead.util.json;

/** 
 * converts Objects into JSONifyable values.
 * implementations can be registered with a JSON object
 * to support JSONification of arbitrarily typed objects. 
 */
public interface JSONMapper {
	/** convert an object into a JSONifyable value */
	Object map(Object o);
}
