package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;

public abstract class AbstractForeignPropertyProcessor extends AbstractProcessor<ForeignEntity> implements ForeignPropertyProcessor {
	
	public void processProperties(DataCursor<ForeignEntity> cursor) throws PersistenceException {
		process(cursor);
	}
	
	protected void processEntry(ForeignEntity e) throws PersistenceException {
		processForeignEntity(e);
	}

	protected abstract  void processForeignEntity(ForeignEntity e) throws PersistenceException;

}
