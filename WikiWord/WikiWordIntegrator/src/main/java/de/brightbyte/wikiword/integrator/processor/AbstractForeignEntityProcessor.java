package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;

public abstract class AbstractForeignEntityProcessor extends AbstractProcessor<ForeignEntity> implements ForeignEntityProcessor {
	
	public void processEntites(DataCursor<ForeignEntity> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(ForeignEntity e) throws PersistenceException {
		processForeignEntity(e);
	}

	protected abstract  void processForeignEntity(ForeignEntity e) throws PersistenceException;

}
