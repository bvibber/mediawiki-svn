package net.psammead.mwapi.api.json;

public interface JSONConverter {
	Object convert(JSONConverterContext ctx, Object o) throws JSONConverterException;
}
