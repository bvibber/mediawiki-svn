package de.brightbyte.wikiword.integrator.data;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.data.Accumulator;
import de.brightbyte.data.Functor2;
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
		
		DefaultAssociation a = prev.clone(); 
		
		while (true) {
			prev = cursor.next();
			if (prev==null) break;
			
			if (prev.getConceptEntity().getID().equals( a.getConceptEntity().getID())) break;
			if (prev.getForeignEntity().getID().equals( a.getForeignEntity().getID())) break;
			
			aggregate(a, prev);
		}
		
		return a;
	}

	private Association aggregate(Association base, Association add) {
		for (Map.Entry<String, Accumulator<?, ?>> e: accumulators.entrySet()) {
			String field = e.getKey();
			Accumulator<Object, Object> accumulator = (Accumulator<Object, Object>)e.getValue(); //XXX: unsafe case
			
			Object a = base.getPrimitive(field); //TODO: aggregate instead?!
			Object b = add.getPrimitive(field); //TODO: aggregate instead?!
			Object v;
			
			if (a==null) v  = b;
			else if (b==null) v  = a;
			else v = accumulator.apply(a, b); //XXX: unsafe type assumption in apply. not sure accumulator matches object type
			
			base.set(field, v);
		}
		
		return base;
	}

}
