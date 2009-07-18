package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntityRecord;

public abstract class AbstractForeignEntityProcessor extends AbstractProcessor<ForeignEntityRecord> implements ForeignEntityProcessor {
	
	public void processEntites(DataCursor<ForeignEntityRecord> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(ForeignEntityRecord e) throws PersistenceException {
		processForeignEntity(e);
	}

	protected abstract  void processForeignEntity(ForeignEntityRecord e) throws PersistenceException;

}
