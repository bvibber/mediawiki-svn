package de.brightbyte.wikiword.extract;

import java.io.File;
import java.net.MalformedURLException;
import java.net.URL;

import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.analyzer.WikiTextAnalyzer;
import de.brightbyte.wikiword.output.DataOutput;
import de.brightbyte.wikiword.processor.DataSourceDriver;
import de.brightbyte.wikiword.processor.WikiWordPageProcessor;
import de.brightbyte.wikiword.processor.XmlDumpDriver;

public abstract class ExtractFromDump<S extends DataOutput> extends ExtractorApp<S> {

	public ExtractFromDump() {
		super();
	}

	protected URL dumpFile;
/*
	protected String getDatasetArgument() {
		return args.getOption("corpus", ":");
	}
*/		
	@Override
	protected boolean applyArguments() {
		String d = getTargetFileName();
		if (d==null) return false;
		
		if (args.isSet("url")) {
			try {
				dumpFile = new URL(d);
			} catch (MalformedURLException e) {
				throw new IllegalArgumentException("bad url: "+d, e);
			}
		}
		else {
			try {
				dumpFile = new File(d).toURI().toURL();
			} catch (MalformedURLException e) {
				throw new RuntimeException("failed to generate local file url for `"+d+"`");
			}
		}
		
		return true;
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<dump-file>", "the dump file to process. If --url is set, this is read as a full URL");
		args.declare("wiki", null, true, String.class, "sets the wiki name (overrides the name given by, or " +
			"guessed from, the <wiki> parameter)");
		args.declare("url", null, false, Boolean.class, "read the <dump-file> parameter as a full URL");
		
		args.declare("namespaces", null, true, String.class, "Only process pages in the given namespace(s).");
	}
	
	@Override
	protected void run() throws Exception {

		WikiTextAnalyzer analyzer = WikiTextAnalyzer.getWikiTextAnalyzer(getCorpus(), tweaks); 
		WikiWordPageProcessor processor = newProcessor(analyzer);
		processor.setLogOutput(getLogOutput());
		processor.configure(args);
		
		DataSourceDriver driver = new XmlDumpDriver(getCorpus(), dumpFile, inputHelper, getLogOutput(), new FatalBackgroundErrorHandler<XmlDumpDriver, Throwable, RuntimeException>(), tweaks);
		
		processor.reset();
		processor.prepare();
		
		driver.run(processor);

		processor.finish();
	}

	protected abstract WikiWordExtractor newProcessor(WikiTextAnalyzer analyzer) throws PersistenceException;

}
