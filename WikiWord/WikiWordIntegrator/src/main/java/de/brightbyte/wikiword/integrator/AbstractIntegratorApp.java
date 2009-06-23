package de.brightbyte.wikiword.integrator;

import java.beans.IntrospectionException;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.InvocationTargetException;
import java.net.URL;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.List;
import java.util.Map;
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.data.Functor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.io.IOUtil;
import de.brightbyte.io.LineCursor;
import de.brightbyte.text.Chunker;
import de.brightbyte.util.BeanUtils;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.integrator.data.AssemblingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.AssociationCursor;
import de.brightbyte.wikiword.integrator.data.FeatureSet;
import de.brightbyte.wikiword.integrator.data.FeatureSetValueSplitter;
import de.brightbyte.wikiword.integrator.data.MangelingFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.ResultSetFeatureSetCursor;
import de.brightbyte.wikiword.integrator.data.TsvFeatureSetCursor;
import de.brightbyte.wikiword.integrator.processor.WikiWordProcessor;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

/**
 * This is the primary entry point to the first phase of a WikiWord analysis.
 * ImportDump can be invoked as a standalone program, use --help as a
 * command line parameter for usage information.
 */
public abstract class AbstractIntegratorApp<S extends WikiWordStoreBuilder, P extends WikiWordProcessor, E> extends StoreBackedApp<WikiWordConceptStoreBase> {

	//protected ForeignPropertyStoreBuilder propertyStore;
	protected InputFileHelper inputHelper;
	protected P propertyProcessor;
	protected FeatureSetSourceDescriptor sourceDescriptor;
	private DataSource configuredDataSource;
	private DatasetIdentifier configuredDataset;
	private String targetTableName;
	
	public AbstractIntegratorApp() {
		super(true, true);
	}
	
	public S getStoreBuilder() throws IOException, PersistenceException {
		if (conceptStoreFactory==null) conceptStoreFactory= createConceptStoreFactory();
		if (conceptStore==null) createStores(conceptStoreFactory);

		return (S)conceptStore; //XXX: nasty cast!
	}
	
	@Override
	protected DataSource getConfiguredDataSource() throws IOException, PersistenceException {
		if (configuredDataSource!=null) return configuredDataSource; 
		configuredDataSource = super.getConfiguredDataSource();
		return configuredDataSource;
	}
	
	@Override
	public DatasetIdentifier getConfiguredDataset() {
		if (configuredDataset!=null) return configuredDataset; 
		configuredDataset = super.getConfiguredDataset();
		return configuredDataset;
	}
	
	public String getConfiguredDatasetName() {
		if (configuredDataset!=null) return configuredDataset.getName();
		else return super.getConfiguredDatasetName();
	}
	
	public void slaveInit(DataSource dataSource, DatasetIdentifier dataset, TweakSet tweaks, FeatureSetSourceDescriptor sourceDescriptor, String targetTableName) {
		if (this.sourceDescriptor!=null) throw new IllegalStateException("application already initialized");
		
		this.configuredDataSource = dataSource;
		this.configuredDataset = dataset;
		this.tweaks = tweaks;
		this.sourceDescriptor = sourceDescriptor;
		this.targetTableName = targetTableName;
	}
	
	public void slaveLaunch() throws Exception {
		setKeepAlive(true);
		launchExecute();
	}
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	protected String getTargetTableName() throws IOException {
		if (targetTableName!=null) return targetTableName;
		
		if (args.getParameterCount() > 2) targetTableName = args.getParameter(2);
		else {
			String authority = getSourceDescriptor().getAuthorityName();
			authority = authority.replaceAll("[^\\w\\d]", "_").toLowerCase();
			
			targetTableName= authority+"_property";
		}
		
		return targetTableName;
	}

	protected String getSourceFileName() {
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
	
	protected DataCursor<Association> openAssociationCursor() throws IOException, SQLException, PersistenceException {
		Iterable<String> foreignFields = sourceDescriptor.getTweak("foreign-fields", (Iterable<String>)null);
		Iterable<String> conceptFields = sourceDescriptor.getTweak("concept-fields", (Iterable<String>)null);
		Iterable<String> propertyFields = sourceDescriptor.getTweak("property-fields", (Iterable<String>)null);
		
		if (foreignFields==null) {
			foreignFields = getDefaultForeignFields();
		}

		if (conceptFields==null) {
			conceptFields = getDefaultConceptFields();
		}
		
		if (propertyFields==null) {
			propertyFields = getDefaultPropertyFields();
		}
		
		DataCursor<FeatureSet> fsc = openFeatureSetCursor();

		DataCursor<Association> cursor = 
			new AssociationCursor(fsc, 
					foreignFields, 
					conceptFields, 
					propertyFields );
		
		return cursor;
	}
	
	protected List<String> getDefaultFields(SqlDialect dialect) {
		ArrayList<String> fields = new ArrayList<String>();
		
		for (String f: getDefaultForeignFields()) if (f!=null) fields.add(dialect.quoteName(f));
		for (String f: getDefaultConceptFields()) if (f!=null) fields.add(dialect.quoteName(f));
		for (String f: getDefaultPropertyFields()) if (f!=null) fields.add(dialect.quoteName(f));
		
		return fields;
	}
	protected List<String> getDefaultForeignFields() {
		return Arrays.asList(new String[] {
				(sourceDescriptor.getTweak("foreign-authority-field", null) == null) ? 
						("=" + sourceDescriptor.getAuthorityName()) : 
							sourceDescriptor.getTweak("foreign-authority-field", (String)null),
							
				sourceDescriptor.getTweak("foreign-id-field", (String)null),
				sourceDescriptor.getTweak("foreign-name-field", (String)null)
		});
	}
	
	protected List<String> getDefaultConceptFields() {
		return Arrays.asList(new String[] {
				sourceDescriptor.getTweak("concept-id-field", (String)null),
				sourceDescriptor.getTweak("concept-name-field", (String)null)
		});
	}

	protected List<String> getDefaultPropertyFields() {
		return Arrays.asList(new String[] {
				sourceDescriptor.getTweak("foreign-property-field", (String)null),
				sourceDescriptor.getTweak("concept-property-field", (String)null),
				sourceDescriptor.getTweak("concept-property-source-field", (String)null),
				sourceDescriptor.getTweak("concept-property-freq-field", (String)null),
				sourceDescriptor.getTweak("association-weight-field", (String)null),
				sourceDescriptor.getTweak("association-value-field", (String)null),
				sourceDescriptor.getTweak("association-annotation-field", (String)null),
				sourceDescriptor.getTweak("optimization-field", (String)null)
		});
	}
	
	protected DataCursor<FeatureSet> openFeatureSetCursor() throws IOException, SQLException, PersistenceException {
		FeatureSetSourceDescriptor sourceDescriptor = getSourceDescriptor();

		String enc = sourceDescriptor.getDataEncoding();
		String sql = sourceDescriptor.getSqlQuery();
		InputStream in =  null;
		
		if (sql==null) {
				String n = sourceDescriptor.getSourceFileName();
				
				if (n!=null) {
						String format = sourceDescriptor.getSourceFileFormat() ;
						in =  getInputHelper().open(sourceDescriptor.getBaseURL(), n);
						
						if (format!=null && format.equals("sql")) {
							sql = IOUtil.slurp(in, enc);
							
							in.close();
							in = null;
						}
				}
		}
		
		Connection con = null;
		
		if (sql==null && in==null) {
			if (con==null) con = getConfiguredDataSource().getConnection();
			DatabaseSchema schema = new DatabaseSchema(getConfiguredDataset().getDbPrefix(), con, null);
			
			String t = sourceDescriptor.getSourceTable();
			if (t!=null) sql = getSqlQuery(t, sourceDescriptor, schema.getDialect());
		}
		
		DataCursor<FeatureSet> fsc;
		String[] fields = sourceDescriptor.getDataFields();
		
		if (sql!=null) {
			Collection<Functor<String, String>> manglers = getSqlScriptManglers(sourceDescriptor);
			if (con==null) con = getConfiguredDataSource().getConnection();
			
			//DatabaseUtil.dumpData(con, sql);
			
			ResultSet rs = SqlScriptRunner.runQuery(con, sql, manglers);
			
			//DatabaseUtil.dumpData(rs, ConsoleIO.output, " | ");
			
			fsc = new ResultSetFeatureSetCursor(rs, fields);
		} else {
			LineCursor lines = new LineCursor(in, enc);
			
			Chunker chunker = sourceDescriptor.getCsvLineChunker();
			
			fsc = new TsvFeatureSetCursor(lines, chunker);
			
			if (fields!=null) {
				if (sourceDescriptor.getSkipHeader()) ((TsvFeatureSetCursor)fsc).readFields();
				((TsvFeatureSetCursor)fsc).setFields(fields);
			} else {
				((TsvFeatureSetCursor)fsc).readFields();
				fields = ((TsvFeatureSetCursor)fsc).getFields();
			}
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
	
	protected abstract String getSqlQuery(String table, FeatureSetSourceDescriptor sourceDescriptor, SqlDialect dialct);

	public String getQualifiedTableName(String table) {
		return getConfiguredDataset().getDbPrefix()+table;
	}
	
	public FeatureSetSourceDescriptor loadSourceDescriptor(String name) throws IOException {
		return loadSourceDescriptor(name, true, false, null);
	}
	
	private FeatureSetSourceDescriptor loadSourceDescriptor(String name, boolean file, boolean classpath, URL base) throws IOException {
		FeatureSetSourceDescriptor d = new FeatureSetSourceDescriptor("", null);
		
		InputStream in = null;
		URL u = null;
		
		if (in==null && classpath) {
			u = AbstractIntegratorApp.class.getResource(name);
			if (u!=null) in = u.openStream();
		} 
		
		if (in==null && base!=null) {
			u = new URL(base, name);
			in = u.openStream();
		} 
		
		if (in==null && file) {
			in = getInputHelper().open(name);
			u = getInputHelper().getBaseURL(name);
		}

		d.setBaseURL(getInputHelper().getBaseURL(name));
		d.loadTweaks(in);
		in.close();
		
		if (u!=null) d.setBaseURL(u);
				
		d = getAugmentedSourceDescriptor(d);
		return d;
	}

	public FeatureSetSourceDescriptor getAugmentedSourceDescriptor(FeatureSetSourceDescriptor d) throws IOException {
		
		String def = d.getTweak("defaults", (String)null);
		if (def!=null) {
			FeatureSetSourceDescriptor dd = loadSourceDescriptor(def, false, true, null);
			dd.setTweaks(d);
			d = dd;
		}

		String par = d.getTweak("parent", (String)null);
		if (par!=null) {
			FeatureSetSourceDescriptor pd = loadSourceDescriptor(par, false, false, d.getBaseURL());
			pd.setTweaks(d);
			d = pd;
		}
		return d;
		
	}
	
	protected FeatureSetSourceDescriptor getSourceDescriptor() throws IOException {
		if (sourceDescriptor!=null) return sourceDescriptor;
		
		sourceDescriptor = new FeatureSetSourceDescriptor("source", tweaks);
		
		String n = getSourceFileName();
		FeatureSetSourceDescriptor d = loadSourceDescriptor(n, true, false, null);
		sourceDescriptor.setTweaks(d);
		
		sourceDescriptor.setTweaks(System.getProperties(), "wikiword.source."); //XXX: doc
		sourceDescriptor.setTweaks(args, "source."); //XXX: doc
		
		return sourceDescriptor;
	}

	@SuppressWarnings("unchecked")
	protected Collection<Functor<String, String>> getSqlScriptManglers(FeatureSetSourceDescriptor sourceDescriptor) {
		ArrayList<Functor<String, String>> list = new ArrayList<Functor<String, String>>();
		
		List more = sourceDescriptor.getScriptManglers();
		if (more!=null) list.addAll(more);
		
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_prefix* \\*/"), getConfiguredDataset().getDbPrefix()) );
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_db* \\*/"), getConfiguredDatasetName()) );
		
		return list;
	}
	
	protected <T> T instantiate(TweakSet config, String optionName, Class<? extends T> def, Object... params) throws InstantiationException {
		if (config==null) config = tweaks;
		Class<? extends T> cls = null;
		
		if (optionName!=null) cls = tweaks.getTweak(optionName, def);
		if (cls==null) return null;
		
		try {
			return BeanUtils.createBean(cls, Arrays.asList(params), null);
		}  catch (IntrospectionException e) {
			throw (InstantiationException)new InstantiationException("failed to instantiate "+cls+" because of "+e).initCause(e);
		} catch (IllegalAccessException e) {
			throw (InstantiationException)new InstantiationException("failed to instantiate "+cls+" because of "+e).initCause(e);
		} catch (NoSuchMethodException e) {
			throw (InstantiationException)new InstantiationException("failed to instantiate "+cls+" because of "+e).initCause(e);
		} catch (InvocationTargetException e) {
			throw (InstantiationException)new InstantiationException("failed to instantiate "+cls+" because of "+e).initCause(e);
		}
	}
	
	protected abstract P createProcessor(S conceptStore) throws InstantiationException;


}