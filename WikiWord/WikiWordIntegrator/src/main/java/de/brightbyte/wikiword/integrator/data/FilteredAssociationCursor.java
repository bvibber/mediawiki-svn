package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.filter.Filter;
import de.brightbyte.util.PersistenceException;

public class FilteredAssociationCursor implements DataCursor<Association> {

	protected DataCursor<Association> cursor;
	protected Filter<Association> filter;
	
	public FilteredAssociationCursor(DataCursor<Association> cursor, Filter<Association> filter) {
		if (filter==null) throw new NullPointerException();
		if (cursor==null) throw new NullPointerException();
		
		this.filter = filter;
		this.cursor = cursor;
	}

	public void close() {
		cursor.close();
	}

	public Association next() throws PersistenceException {
		Association a = null;
		while (true) {
			a = cursor.next();
			if (a==null) return null;
			if (!filter.matches(a)) continue;
			
			break;
		}
		
		return a;
	}

}
