package de.brightbyte.wikiword.integrator.processor;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;

public interface ForeignPropertyProcessor {
		public void processProperties(DataCursor<ForeignEntity> cursor) throws PersistenceException;
}
