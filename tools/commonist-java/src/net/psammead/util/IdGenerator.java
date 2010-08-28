package net.psammead.util;

import java.util.IdentityHashMap;
import java.util.Map;

/** assigns long ids to arbitrary Objects */
public final class IdGenerator {
	private final	Map<Object,Long> map;
	private long	nextId;
	
	public IdGenerator() {
		map		= new IdentityHashMap<Object,Long>();
		nextId	= 0;
	}
	
	public synchronized long id(Object object) {
		final Long	id1	= map.get(object);
		if (id1 != null)	return id1;
		
		final Long	id	= nextId;
		nextId++;
		map.put(object, id);
		return id;
	}
}

/*
class Sequencer {
	private final AtomicLong sequenceNumber = new AtomicLong(0);
	public long next() { return sequenceNumber.getAndIncrement(); }
}
*/