package de.brightbyte.wikiword.integrator;

import java.beans.IntrospectionException;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.io.InputStream;
import java.lang.reflect.InvocationTargetException;
import java.net.URL;
import java.sql.Connection;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collection;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

import javax.sql.DataSource;

import de.brightbyte.data.Functor;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.SqlDialect;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.io.ChunkingCursor;
import de.brightbyte.io.IOUtil;
import de.brightbyte.io.LineCursor;
import de.brightbyte.text.Chunker;
import de.brightbyte.util.BeanUtils;
import de.brightbyte.util.LoggingErrorHandler;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.integrator.data.Association;
import de.brightbyte.wikiword.integrator.data.AssociationCursor;
import de.brightbyte.wikiword.integrator.data.DefaultRecordMangler;
import de.brightbyte.wikiword.integrator.data.MangelingCursor;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.integrator.data.RecordMangler;
import de.brightbyte.wikiword.integrator.data.ResultSetRecordCursor;
import de.brightbyte.wikiword.integrator.data.TsvRecordCursor;
import de.brightbyte.wikiword.integrator.processor.WikiWordProcessor;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;
import de.brightbyte.wikiword.store.builder.WikiWordStoreBuilder;

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
	
	public String getConfiguredCollectionName() {
		if (configuredDataset!=null) return configuredDataset.getCollection();
		else return super.getConfiguredCollectionName();
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
	
	
	@Override
	protected void execute() throws Exception {
		S store = getStoreBuilder();
		store.initialize(false, false);
		
		run();
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

		args.declareHelp("<dataset>", "name of the wiki/thesaurus to process");
	}
	
	protected static final String FOREIGN_AUTHORITY_FIELD_NAME = "foreignAuthorityField";
	protected static final String FOREIGN_ID_FIELD_NAME = "foreignIdField";
	protected static final String FOREIGN_NAME_FIELD_NAME = "foreignNameField";
	protected static final String CONCEPT_ID_FIELD_NAME = "conceptIdField";
	protected static final String CONCEPT_NAME_FIELD_NAME = "conceptNameField";
	
	protected DataCursor<Association> openAssociationCursor() throws IOException, SQLException, PersistenceException {

		DataCursor<Record> fsc = openRecordCursor();
		
		Map<String, String> fields = getDefaultFields();

		DataCursor<Association> cursor = 
			new AssociationCursor(fsc, 
					fields.get(FOREIGN_AUTHORITY_FIELD_NAME), 
					fields.get(FOREIGN_ID_FIELD_NAME), 
					fields.get(FOREIGN_NAME_FIELD_NAME),
					fields.get(CONCEPT_ID_FIELD_NAME),
					fields.get(CONCEPT_NAME_FIELD_NAME));
		
		return cursor;
	}
	
	protected Map<String, String> getDefaultFields() {
		HashMap<String, String> fields = new HashMap<String, String>();
		
		fields.put( FOREIGN_AUTHORITY_FIELD_NAME, (sourceDescriptor.getTweak("foreign-authority-field", null) == null) ? 
				("=" + sourceDescriptor.getAuthorityName()) : 
					sourceDescriptor.getTweak("foreign-authority-field", (String)null) );
					
		fields.put(FOREIGN_ID_FIELD_NAME, sourceDescriptor.getTweak("foreign-id-field", (String)null) );
		fields.put(FOREIGN_NAME_FIELD_NAME, sourceDescriptor.getTweak("foreign-name-field", (String)null) );

		fields.put(CONCEPT_ID_FIELD_NAME, sourceDescriptor.getTweak("concept-id-field", (String)null) );
		fields.put(CONCEPT_NAME_FIELD_NAME, sourceDescriptor.getTweak("concept-name-field", (String)null) );
		
		//FIXME: more fields
		/*
		sourceDescriptor.getTweak("foreign-property-field", (String)null),
		sourceDescriptor.getTweak("concept-property-field", (String)null),
		sourceDescriptor.getTweak("concept-property-source-field", (String)null),
		sourceDescriptor.getTweak("concept-property-freq-field", (String)null),
		sourceDescriptor.getTweak("association-weight-field", (String)null),
		sourceDescriptor.getTweak("association-value-field", (String)null),
		sourceDescriptor.getTweak("association-annotation-field", (String)null),
		sourceDescriptor.getTweak("mapping-filter-field", (String)null) 
		*/
		
		
		return fields;
	}
	
	
	public static URL getBuiltinScriptUrl(String name, String ext) {
		URL u =RunIntegratorSql.class.getResource(name+ext);
		return u;
	}
	
	/*
	protected DataCursor<FeatureSet> openFeatureSetCursor() throws IOException, SQLException, PersistenceException {
		String propField = sourceDescriptor.getPropertyNameField();
		if (propField!=null) {
			String valueField = sourceDescriptor.getPropertyValueField();
			String subjectField = sourceDescriptor.getPropertySubjectField();
			rc = new FeatureAssemblingCursor(rc, subjectField, propField, valueField);
		}
		
		FeatureSetMangler mangler = sourceDescriptor.getRowMangler();
		
		if (mangler==null) {
			Map<String, Chunker> splitters = sourceDescriptor.getDataFieldChunkers();
			if (splitters!=null) mangler = FeatureSetValueSplitter.multiFromChunkerMap(splitters);
		}
		
		if (mangler!=null) {
			rc = new MangelingCursor(rc, mangler);
		}
	} */
	
	protected DataCursor<Record> openRecordCursor() throws IOException, SQLException, PersistenceException {
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
		} else {
			if (sql.matches("[-\\w+\\d]+")) { //plain name
				URL u = getBuiltinScriptUrl(sql, ".sql");
				if (u!=null) sql = IOUtil.slurp(u);
			} 
		}
		
		Connection con = null;
		
		if (sql==null && in==null) {
			if (con==null) con = getConfiguredDataSource().getConnection();
			DatabaseSchema schema = new DatabaseSchema(getConfiguredDataset().getDbPrefix(), con, null);
			
			String t = sourceDescriptor.getSourceTable();
			if (t!=null) sql = getSqlQuery(t, sourceDescriptor, schema.getDialect());
		}
		
		DataCursor<Record> rc;
		String[] fields = sourceDescriptor.getDataFields();
		
		if (sql!=null) {
			Collection<Functor<String, String>> manglers = getSqlScriptManglers(sourceDescriptor);
			if (con==null) con = getConfiguredDataSource().getConnection();
			
			//DatabaseUtil.dumpData(con, sql);
			
			ResultSet rs = SqlScriptRunner.runQuery(con, sql, manglers);
			
			//DatabaseUtil.dumpData(rs, ConsoleIO.output, " | ");
			
			rc = new ResultSetRecordCursor(rs, fields);
		} else {
			LineCursor lines = new LineCursor(in, enc);
			
			Chunker chunker = sourceDescriptor.getCsvLineChunker();
			
			rc = new TsvRecordCursor(lines, chunker);
			
			if (sourceDescriptor.getSkipBadRows()) {
				((TsvRecordCursor)rc).setParseErrorHandler( new LoggingErrorHandler<ChunkingCursor, ParseException, PersistenceException>(out));
			}
			
			if (fields!=null) {
				if (sourceDescriptor.getSkipHeader()) ((TsvRecordCursor)rc).readFields();
				((TsvRecordCursor)rc).setFields(fields);
			} else {
				((TsvRecordCursor)rc).readFields();
				fields = ((TsvRecordCursor)rc).getFields();
			}
		}

		RecordMangler mangler = sourceDescriptor.getRowMangler();
		
		if (mangler==null) {
			Map<String, Chunker> splitters = sourceDescriptor.getDataFieldChunkers();
			if (splitters!=null) {
				mangler = new DefaultRecordMangler();
				
				for (String f: fields) {
					Chunker chunker = splitters.get(f);
					
					if (chunker!=null) ((DefaultRecordMangler)mangler).addConverter(f, f, String.class, chunker, Iterable.class);
					else ((DefaultRecordMangler)mangler).addMapping(f, f, Object.class);
				}
			}
		}
		
		if (mangler!=null) {
			rc = new MangelingCursor<Record>(rc, mangler);
		}

		return rc;
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
			u = getInputHelper().getURL(name);
		}

		if (in!=null) {
			info("loading source descriptor from "+u);
			d.setBaseURL(u);
			d.loadTweaks(in);
			in.close();
		} else {
			throw new FileNotFoundException("source descriptor not found: "+name);
		}
		
		if (u!=null) d.setBaseURL(u);
				
		d = getAugmentedSourceDescriptor(d);
		return d;
	}

	public FeatureSetSourceDescriptor getAugmentedSourceDescriptor(FeatureSetSourceDescriptor d) throws IOException {
		
		String def = d.getTweak("defaults", (String)null);
		if (def!=null) {
			if (!def.endsWith(".properties")) def += ".properties";
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

	protected Collection<Functor<String, String>> getSqlScriptManglers(FeatureSetSourceDescriptor sourceDescriptor) throws IOException {
		ArrayList<Functor<String, String>> list = new ArrayList<Functor<String, String>>();
		
		List<Functor<String, String>> more = sourceDescriptor.getScriptManglers();
		if (more!=null) list.addAll(more);
		
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_prefix", getConfiguredDataset().getDbPrefix()) );
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_db", getConfiguredDatasetName()) );
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_target_table", getTargetTableName()) );
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_mapping_table", getSourceDescriptor().getSourceTable()) );
		
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