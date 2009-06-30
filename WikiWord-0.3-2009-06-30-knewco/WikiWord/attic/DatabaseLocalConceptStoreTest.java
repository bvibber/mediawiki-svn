package de.brightbyte.wikiword.store;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.util.Date;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.ResourceType;
import de.brightbyte.wikiword.TweakSet;
import junit.framework.TestCase;

public class DatabaseLocalConceptStoreTest extends TestCase {

	protected Connection makeTestDatabaseConnection() {
        try {
			Class.forName("org.hsqldb.jdbcDriver"); // load driver
			return DriverManager.getConnection("jdbc:hsqldb:."); // connect to in-memory db
		} catch (ClassNotFoundException e) {
			throw new RuntimeException("HSQLDB driver not found!", e);
		} catch (SQLException e) {
			throw new RuntimeException("Filed to create in-memory HSQLDB database!", e);
		}
	}
	
	protected DatabaseLocalConceptStore makeTestStore(String lang) throws SQLException {
		TweakSet tweaks = new TweakSet();
		tweaks.setTweak("dbstore.table.attributes", "");
		
		Corpus corpus = Corpus.forName(lang+".wikipedia.org");
		
		Connection db = makeTestDatabaseConnection();
		
		DatabaseLocalConceptStore store = new DatabaseLocalConceptStore(corpus, db, tweaks);
		return store;
	}
	
	public void testStoreRecords() throws SQLException, PersistenceException {
		DatabaseLocalConceptStore store = makeTestStore("xx");
		
		store.createTables(false);
		
		Date now = new Date();
		store.storeResource("Foo", ResourceType.ARTICLE, now);
		
		store.close();
	}
	
}
