package de.brightbyte.wikiword.builder;

import de.brightbyte.application.Agenda;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.processor.WikiWordPageProcessor;

public interface WikiWordImporter extends WikiWordPageProcessor {
		public Agenda getAgenda() throws PersistenceException;
}
