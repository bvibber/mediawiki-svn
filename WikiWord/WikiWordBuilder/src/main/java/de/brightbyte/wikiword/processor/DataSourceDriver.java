package de.brightbyte.wikiword.processor;

import java.io.IOException;
import java.sql.SQLException;

import de.brightbyte.util.PersistenceException;

/**
 * An import driver pushes wiki pages from some source into a
 * WikiImporter for processing. It's an abstraction of a source of
 * wiki pages (such as a dump or database), with the ability to send 
 * these entries to the importer, one after the other. 
 */
public interface DataSourceDriver {
	public void run(WikiWordPageProcessor importer) throws IOException, SQLException, InterruptedException, PersistenceException;
}
