package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.DefaultRecord;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;
import de.brightbyte.wikiword.integrator.data.PropertyMapping;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.integrator.store.ForeignRecordStoreBuilder;

public class ForeignRecordPassThrough extends AbstractProcessor<ForeignEntityRecord> implements ForeignEntityProcessor<ForeignEntityRecord> {
	protected ForeignRecordStoreBuilder store;
	protected PropertyMapping<Record> mapping;
	
	public ForeignRecordPassThrough(ForeignRecordStoreBuilder store) {
		if (store==null) throw new NullPointerException();
		this.store = store;
	}
	
	public void setMapping(PropertyMapping<Record> mapping) {
		this.mapping = mapping;
	}
	
	public void processEntites(DataCursor<ForeignEntityRecord> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(ForeignEntityRecord e) throws PersistenceException {
		processForeignEntity(e);
	}
	
	protected  void processForeignEntity(ForeignEntityRecord e) throws PersistenceException {
		Record rec;
		
		if (mapping==null) rec = e;
		else {
			rec = new DefaultRecord(); 
			
			for (String f : mapping.fields()) {
				Object v = mapping.getValue(e, f, null); 
				//TODO: if v is complex, coolaps it! or just throw, because accessorws should do that?
				rec.set(f, v); 
			}
		}
		
		store.storeRecord(rec);
	}

}
