package de.brightbyte.wikiword.integrator;

import java.io.IOException;
import java.io.InputStream;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Arrays;
import java.util.Collection;
import java.util.Map;
import java.util.regex.Pattern;

import de.brightbyte.data.Functor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.io.IOUtil;
import de.brightbyte.text.Chunker;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.integrator.data.AssemblingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSetValueSplitter;
import de.brightbyte.wikiword.integrator.data.MangelingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.ResultSetFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.TsvFeatureSetCursor;
import de.brightbyte.wikiword.integrator.processor.WikiWordProcessor;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public abstract class AbstractIntegratorApp<S extends WikiWordConceptStoreBase, P extends WikiWordProcessor, E> extends StoreBackedApp<S> {

	//protected ForeignPropertyStoreBuilder propertyStore;
	protected InputFileHelper inputHelper;
	protected P propertyProcessor;
	protected FeatureSetSourceDescriptor sourceDescriptor;
	
	public AbstractIntegratorApp() {
		super(true, true);
	}
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	protected String getTargetTableName() throws IOException {
		if (args.getParameterCount() > 2) return args.getParameter(2);
		
		String authority = getSourceDescriptor().getAuthorityName();
		authority = authority.replaceAll("[^\\w\\d]", "_").toLowerCase();
		
		return authority+"_property";
	}

	protected String getSourceDescriptionFileName() {
		if (args.getParameterCount() < 2) throw new IllegalArgumentException("missing second parameter (descripion file name)");
		return args.getParameter(1);
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<wiki>", null);
		args.declareHelp("<dataset>", "name of the wiki/thesaurus to process");
		args.declare("dataset", null, true, String.class, "sets the wiki name (overrides the <wiki-or-dump> parameter)");
	}
	
	protected DataCursor<FeatureSet> openFeatureSetCursor() throws IOException, SQLException, PersistenceException {
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();

		String enc = sourceDescriptor.getDataEncoding();
		String sql = sourceDescriptor.getSqlQuery();
		InputStream in =  null;
		
		if (sql==null) {
				String n = sourceDescriptor.getSourceFileName();
				String format = getInputHelper().getFormat(n); //FIXME: explicit format!
				in =  getInputHelper().open(sourceDescriptor.getBaseURL(), n);
				
				if (format!=null && format.equals("sql")) {
					sql = IOUtil.slurp(in, enc);
					
					in.close();
					in = null;
				}
		}
		
		DataCursor<FeatureSet> fsc;
		String[] fields = sourceDescriptor.getDataFields();
		
		if (sql!=null) {
			Collection<Functor<String, String>> manglers = Arrays.asList(getSqlScriptManglers()); 
			Connection con = getConfiguredDataSource().getConnection();
			ResultSet rs = SqlScriptRunner.runQuery(con, sql, manglers);
			
			fsc = new ResultSetFeatureSetCursor(rs, fields);
		} else {
			fsc = new TsvFeatureSetCursor(in, enc);
			
			if (fields!=null) ((TsvFeatureSetCursor)fsc).setFields(fields);
			else ((TsvFeatureSetCursor)fsc).readFields();
		}
		
		String propField = sourceDescriptor.getPropertyNameField();
		if (propField!=null) {
			String valueField = sourceDescriptor.getPropertyValueField();
			String subjectField = sourceDescriptor.getPropertySubjectField();
			fsc = new AssemblingFeatureSetCursor(fsc, subjectField, propField, valueField);
		}
		
		Map<String, Chunker> splitters = sourceDescriptor.getDataFieldChunkers();
		if (splitters!=null) {
			fsc = new MangelingFeatureSetCursor(fsc, FeatureSetValueSplitter.multiFromChunkerMap(splitters));
		}
		
		return fsc;
	}
	
	protected FeatureSetSourceDescriptor getSourceDescriptor() throws IOException {
		if (sourceDescriptor!=null) return sourceDescriptor;
		
		sourceDescriptor = new FeatureSetSourceDescriptor("source", tweaks);
		
		String n = getSourceDescriptionFileName();
		InputStream in = getInputHelper().open(n);
		sourceDescriptor.setBaseURL(getInputHelper().getBaseURL(n));
		sourceDescriptor.loadTweaks(in);
		in.close();
		
		sourceDescriptor.setTweaks(System.getProperties(), "wikiword.source."); //XXX: doc
		sourceDescriptor.setTweaks(args, "source."); //XXX: doc
		
		return sourceDescriptor;
	}

	@SuppressWarnings("unchecked")
	protected Functor<String, String>[] getSqlScriptManglers() {
		return new Functor[] {
				new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_prefix* \\*/"), getConfiguredDataset().getDbPrefix()),
				new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_db* \\*/"), getConfiguredDatasetName()),
		};
	}

}