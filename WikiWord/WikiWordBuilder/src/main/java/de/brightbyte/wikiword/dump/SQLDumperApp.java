package de.brightbyte.wikiword.dump;

import java.io.IOException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.List;

import de.brightbyte.data.cursor.DataSink;
import de.brightbyte.data.cursor.JoiningSink;
import de.brightbyte.db.QueryDumper;
import de.brightbyte.io.LineSink;
import de.brightbyte.job.ChunkedProgressRateTracker;
import de.brightbyte.text.CsvLineJoiner;
import de.brightbyte.text.Joiner;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.extract.StreamOutputApp;
import de.brightbyte.wikiword.schema.GlobalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.DatabaseWikiWordStore;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class SQLDumperApp extends StreamOutputApp<List<String>, WikiWordConceptStoreBase> {

	protected ChunkedProgressRateTracker dumpTracker;
	
	public SQLDumperApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
		
		dumpTracker = new ChunkedProgressRateTracker("dumping", 10000, 10); //TODO: init later, get values from tweaks
		dumpTracker.setLogOutput(out);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();
		
		args.declare("no-output-header", null, false, Boolean.class, "The first line of the output file will not be a column header");
		args.declare("output-format", null, true, String.class, "Format of the output file. May be csv or tsv, default is csv.");
	}

	protected WikiWordConceptStoreSchema conceptStoreDB;

	@Override
	protected void createStores() throws IOException, PersistenceException {
		conceptStore = DatabaseConceptStores.createConceptStore(getConfiguredDataSource(), getConfiguredDataset(), tweaks, true, true);
		
		registerStore(conceptStore);
		
		if (conceptStore instanceof DatabaseWikiWordStore) {
			conceptStoreDB = (WikiWordConceptStoreSchema)((DatabaseWikiWordStore)conceptStore).getDatabaseAccess();
		} else {
			try {
				if ( isDatasetLocal() ) conceptStoreDB = new LocalConceptStoreSchema(getCorpus(), getConfiguredDataSource(), this.tweaks, false);
				else conceptStoreDB = new GlobalConceptStoreSchema(getConfiguredDataset(), getConfiguredDataSource(), this.tweaks, false);
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}
	}

	@Override
	public void run() throws Exception {
		boolean outputHasHeader = !args.isSet("no-output-header");

		String sql = getQuerySQL();
	
		info("Running query...");
		ResultSet rs = conceptStoreDB.executeBigQuery("dumpList", sql);
	
		QueryDumper dumper = new QueryDumper(sink, (String[])null); 
		dumper.addProgressListener(dumpTracker);
		configureDumper(dumper);

		info("dumping rows...");
		
		if (outputHasHeader) dumper.dumpHeader(rs);
		int c = dumper.dumpRows(rs);
		
		rs.close();

		info("complete, dumped "+c+" rows.");
	}

	protected abstract  String getQuerySQL();

	protected void configureDumper(QueryDumper dumper) {
		// NOOP
	}
		
	private Joiner joiner;
	
	@Override
	protected DataSink<? super List<String>> openSink(int paramOffset) throws PersistenceException {
		if (joiner==null) {
			String format = args.getOption("output-format", "csv").toLowerCase();
			
			if (format.equals("csv")) joiner = new CsvLineJoiner(",", null, '"', false);
			else if (format.equals("tsv")) joiner = new CsvLineJoiner("\t", null, '\0', true);
			else throw new IllegalArgumentException("bad output format: "+format);
		}
		
		try {
			JoiningSink sink = new JoiningSink(new LineSink(getOutputWriter(paramOffset)), joiner);
			return sink;
		} catch (IOException e) {
			throw new PersistenceException();
		} 
	}
	
}
