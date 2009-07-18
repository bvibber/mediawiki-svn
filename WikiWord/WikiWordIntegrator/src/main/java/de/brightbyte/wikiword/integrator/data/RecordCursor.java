package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class RecordCursor<R> implements DataCursor<Record> {
	protected DataCursor<R> source;
	protected PropertyMapping<R> mapping;
	
	protected RecordCursor(DataCursor<R> source) {
		if (source==null) throw new NullPointerException();
		this.source = source;
	}
	
	public RecordCursor(DataCursor<R> source, PropertyMapping<R> mapping) {
		this(source);
		if (mapping==null) throw new NullPointerException();
		this.mapping = mapping;
	}
	
	public void close() {
			source.close();
	}

	public Record next() throws PersistenceException {
		R r = source.next();
		if (r==null) return null;
		return record(r);
	}

	protected Record record(R row) {
		if (mapping==null) throw new IllegalStateException("no peoperty mapping defined yet!");
		
		Record rec = new DefaultRecord();
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(row, f, null); //XXX: extra type conversion?!
			
			rec.add(f, v);
		}

		return rec;
	}

	protected void finalize() {
		close();
	}

}
