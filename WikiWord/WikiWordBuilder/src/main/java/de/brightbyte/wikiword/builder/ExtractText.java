package de.brightbyte.wikiword.builder;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.sql.SQLException;

import javax.sql.DataSource;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.store.builder.DatabaseTextStoreBuilder;
import de.brightbyte.wikiword.store.builder.PlainTextOutput;
import de.brightbyte.wikiword.store.builder.TextStoreBuilder;

public class ExtractText extends ImportDump<TextStoreBuilder> {

	public ExtractText() {
		super("ExtractText");
	}

	@Override
	protected TextImporter newImporter(WikiTextAnalyzer analyzer, TextStoreBuilder store, TweakSet tweaks) {
		return new TextImporter(analyzer, (TextStoreBuilder)store, tweaks);
	}
	
	@Override
	protected void declareOptions() {
		super.declareOptions();

		ConceptImporter.declareOptions(args);
		
		args.declareHelp("<dump-file>", "the dump file to process");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <wiki-or-dump> parameter)");
	}

	@Override
	protected TextStoreBuilder createStore() throws IOException, PersistenceException {
		if (args.isSet("stream")) {
			String n = args.getOption("stream", null);
			OutputStream out;
			String enc = args.getStringOption("encoding", "utf-8");
			
			if (n.equals("-")) {
				out = System.out;
			}
			else {
				File f = new File(n);
				out = new BufferedOutputStream(new FileOutputStream(f, args.isSet("append")));
			}
			
			return new PlainTextOutput(out, enc);
		}
		else {
			return super.createStore();
		}
	}

	@Override
	protected TextStoreBuilder createStore(DataSource db) throws PersistenceException {
		try {
			return new DatabaseTextStoreBuilder(getCorpus(), db, tweaks);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}
	
	public static void main(String[] argv) throws Exception {
		ExtractText app = new ExtractText();
		app.launch(argv);
	}
}
