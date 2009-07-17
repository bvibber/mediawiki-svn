package de.brightbyte.wikiword.integrator.processor;

import java.util.HashMap;
import java.util.Map;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.FeatureMapping;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;
import de.brightbyte.wikiword.integrator.store.ForeignRecordStoreBuilder;

public class ForeignRecordPassThrough extends AbstractForeignEntityProcessor {
	protected ForeignRecordStoreBuilder store;
	protected FeatureMapping mapping;
	
	public ForeignRecordPassThrough(ForeignRecordStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	@Override
	protected  void processForeignEntity(ForeignEntity e) throws PersistenceException {
		Map<String, Object> rec = new HashMap<String, Object>(); //TODO: recycle!
		
		for (String f : mapping.fields()) {
			Object v = mapping.getValue(e.getProperties(), f, null); //XXX: extra type conversion?!
			//TODO: if v is complex, coolaps it! or just throw, because accessorws should do that?
			rec.put(f, v); 
		}
		
		store.storeRecord(rec);
	}

}
