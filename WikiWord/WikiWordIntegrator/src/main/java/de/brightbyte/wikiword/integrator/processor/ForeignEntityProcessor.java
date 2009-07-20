package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;

public interface ForeignEntityProcessor<T extends ForeignEntity> extends WikiWordProcessor {
		public void processEntites(DataCursor<T> cursor) throws PersistenceException;
}
