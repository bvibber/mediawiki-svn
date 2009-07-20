package de.brightbyte.wikiword.integrator.data;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.data.Accumulator;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;

public class AggregatingAssociationCursor implements DataCursor<Association> {

	protected DataCursor<Association> cursor;
	protected Association prev;
	
	protected Map<String, Accumulator<?, ?>> accumulators = new HashMap<String, Accumulator<?, ?>>();
	
	public AggregatingAssociationCursor(DataCursor<Association> cursor) {
		if (cursor==null) throw new NullPointerException();
		
		this.cursor = cursor;
	}
	
	public <T>void addAccumulator(String field, Accumulator<T, ? extends T> acc) {
		accumulators.put(field, acc);
	}

	public void close() {
		cursor.close();
	}

	public Association next() throws PersistenceException {
		if (prev==null) prev = cursor.next();
		if (prev==null) return null;
		
		Association a = prev.clone(); 
		
		while (true) {
			prev = cursor.next();
			if (prev==null) break;
			
			if (prev.getConceptEntity().getID() != a.getConceptEntity().getID()) break;
			if (!prev.getForeignEntity().getAuthority().equals( a.getForeignEntity().getAuthority())) break;
			if (!prev.getForeignEntity().getID().equals( a.getForeignEntity().getID())) break;
			
			((DefaultAssociation)a).aggregate((DefaultAssociation)prev, accumulators);
		}
		
		return a;
	}

}
