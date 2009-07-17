package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class FeatureSetCursor<R> implements DataCursor<FeatureSet> {
	protected DataCursor<R> source;
	protected PropertyMapping<R> mapping;
	
	protected FeatureSetCursor(DataCursor<R> source) {
		if (source==null) throw new NullPointerException();
		this.source = source;
	}
	
	public FeatureSetCursor(DataCursor<R> source, PropertyMapping<R> mapping) {
		this(source);
		if (mapping==null) throw new NullPointerException();
		this.mapping = mapping;
	}
	
	public void close() {
			source.close();
	}

	public FeatureSet next() throws PersistenceException {
		R r = source.next();
		if (r==null) return null;
		return record(r);
	}

	protected FeatureSet record(R row) {
		if (mapping==null) throw new IllegalStateException("no peoperty mapping defined yet!");
		
		FeatureSet ft = new DefaultFeatureSet();
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(row, f, null); //XXX: extra type conversion?!
			
			ft.put(f, v);
		}

		return ft;
	}

	protected void finalize() {
		close();
	}

}
