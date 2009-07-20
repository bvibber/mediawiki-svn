package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.abstraction.ConvertingAccessor;
import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;

public class DefaultRecordMangler implements RecordMangler {

	protected PropertyMapping<Record> mapping;
	
	public DefaultRecordMangler() {
		this(new DefaultPropertyMapping<Record>());
	}
	
	public DefaultRecordMangler(PropertyMapping<Record> mapping) {
		if (mapping==null) throw new NullPointerException();
		this.mapping = mapping;
	}
	
	public Record apply(Record obj) {
		if (mapping==null) throw new IllegalStateException("no peoperty mapping defined yet!");
		
		Record rec = new DefaultRecord();
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(obj, f, null);
			
			rec.add(f, v);
		}

		return rec;
	}

	public <V,W> void addConverter(String field, String fromField, Class<V> type, Functor<? extends W, V> converter, Class<W> ctype) {
		Record.Accessor<V> acc = new Record.Accessor<V>(fromField, type);
		ConvertingAccessor<Record, V, W> cacc = new ConvertingAccessor<Record, V, W>(acc, converter, ctype);
		addMapping(field, cacc);
	}

	public <V>void addMapping(String field, String fromField, Class<V> type) {
		addMapping(field, new Record.Accessor<V>(fromField, type));
	}

	public void addMapping(String field, PropertyAccessor<Record, ?> accessor) {
		mapping.addMapping(field, accessor);
	}

	public Iterable<String> fields() {
		return mapping.fields();
	}

}
