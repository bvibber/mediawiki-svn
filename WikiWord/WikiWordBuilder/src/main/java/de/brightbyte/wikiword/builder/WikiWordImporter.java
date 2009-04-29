package de.brightbyte.wikiword.builder;

import de.brightbyte.application.Agenda;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.processor.WikiWordProcessor;

public interface WikiWordImporter extends WikiWordProcessor {
		public Agenda getAgenda() throws PersistenceException;
}
