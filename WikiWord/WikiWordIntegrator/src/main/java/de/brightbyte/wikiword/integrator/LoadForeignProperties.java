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
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.integrator.data.AssemblingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSetValueSplitter;
import de.brightbyte.wikiword.integrator.data.ForeignEntity;
import de.brightbyte.wikiword.integrator.data.ForeignEntityCursor;
import de.brightbyte.wikiword.integrator.data.MangelingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.ResultSetFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.TsvFeatureSetCursor;
import de.brightbyte.wikiword.integrator.processor.ForeignPropertyPassThrough;
import de.brightbyte.wikiword.integrator.processor.ForeignPropertyProcessor;
import de.brightbyte.wikiword.integrator.store.DatabaseForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.integrator.store.ForeignPropertyStoreBuilder;
import de.brightbyte.wikiword.store.WikiWordStoreFactory;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public class LoadForeignProperties extends StoreBackedApp<ForeignPropertyStoreBuilder> {

	//protected ForeignPropertyStoreBuilder propertyStore;
	protected ForeignPropertyProcessor propertyProcessor;
	protected InputFileHelper inputHelper;
	private ForeignEntityStoreDescriptor sourceDescriptor;
	
	public LoadForeignProperties() {
		super(true, true);
	}
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	@Override
	protected WikiWordStoreFactory<? extends ForeignPropertyStoreBuilder> createConceptStoreFactory() throws IOException, PersistenceException {
		return new DatabaseForeignPropertyStoreBuilder.Factory(getTargetTableName(), getConfiguredDataset(), getConfiguredDataSource(), tweaks);
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
	
	@Override
	protected void run() throws Exception {
		section("-- fetching properties --------------------------------------------------");
		DataCursor<ForeignEntity> cursor = openPropertySource();
		
		section("-- process properties --------------------------------------------------");
		this.conceptStore.prepareImport();
		
		this.propertyProcessor = new ForeignPropertyPassThrough(conceptStore); //FIXME
		this.propertyProcessor.processProperties(cursor);
		cursor.close();

		this.conceptStore.finalizeImport();
	}	
	
	protected DataCursor<ForeignEntity> openPropertySource() throws IOException, SQLException, PersistenceException {
		ForeignEntityStoreDescriptor sourceDescriptor = getSourceDescriptor();
		
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
			String idField = sourceDescriptor.getConceptIdField();
			fsc = new AssemblingFeatureSetCursor(fsc, idField, propField, valueField);
		}
		
		Map<String, String> splitExp = sourceDescriptor.getSplitExpressions(); 
		if (splitExp!=null) {
			fsc = new MangelingFeatureSetCursor(fsc, FeatureSetValueSplitter.multiFromStringMap(splitExp, 0));
		}
		
		return new ForeignEntityCursor(fsc, sourceDescriptor.getAuthorityName(), sourceDescriptor.getConceptIdField(), sourceDescriptor.getConceptNameField());
	}

	protected ForeignEntityStoreDescriptor getSourceDescriptor() throws IOException {
		if (sourceDescriptor!=null) return sourceDescriptor;
		
		sourceDescriptor = new ForeignEntityStoreDescriptor(tweaks);
		
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

	public static void main(String[] argv) throws Exception {
		LoadForeignProperties app = new LoadForeignProperties();
		app.launch(argv);
	}
}