package de.brightbyte.wikiword.integrator;

import java.io.BufferedWriter;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.net.URL;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Map;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.data.Functor;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.SqlResultDumper;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.db.file.TsvWriter;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;

public class RunIntegratorSql extends CliApp {
	
	public class Dumper extends SqlResultDumper {
		protected Writer writer;
		
		public Dumper(Writer wr) {
			if (wr==null) throw new NullPointerException();
			this.writer = wr;
		}
		
		@Override
		public void handleQueryResult(String context, ResultSet rs) throws PersistenceException {
			try {
				String[] fields = DatabaseUtil.getFieldNames(rs);
				Object[] row = new Object[fields.length];
				
				TsvWriter.Codec[] codecs = new TsvWriter.Codec[fields.length];
				for (int i=0; i<codecs.length; i++) codecs[i] = TsvWriter.objectCodec;
				
				TsvWriter tsv = new TsvWriter(codecs, writer);				

				tsv.writeRow(fields);
				
				while (rs.next()) {
					int i=0;
					for (String f: fields) {
						row[i++] = rs.getObject(f);
					}

					tsv.writeRow(row);
				}
				
				tsv.flush();
			} catch (SQLException e) {
				throw new PersistenceException(e);
			}
		}

	}

	protected InputFileHelper inputHelper;
	
	protected InputFileHelper getInputHelper() {
		if (inputHelper==null)  {
			inputHelper = new InputFileHelper(tweaks);
		} 
		return inputHelper;
	}

	protected String getSourceFileName() {
		if (args.getParameterCount() < 2) return null;
		return args.getParameter(1);
	}
	
	protected void declareOptions() {
		super.declareOptions();

		args.declareHelp("<dataset>", "name of the wiki/thesaurus to process");
		args.declare("outfile", "f", true, String.class, "file to write results to");
		args.declare("substfile", "u", true, String.class, "file to load subsitutions from");
	}
	
	protected String sqlScriptName;
	protected String mappingTableName;
	protected String foreignTableName;
	protected String outputFileName;
	protected String substitutionFileName;
	private DataSource configuredDataSource;
	private DatasetIdentifier configuredDataset;
	private ArrayList<Functor<String, String>> subsitutions;
	
	public void slaveInit(DataSource dataSource, DatasetIdentifier dataset, TweakSet tweaks, 
			String sqlScriptName, String mappingTableName, String foreignTableName, 
			String outputFileName, String substitutionFileName) {
		
		this.sqlScriptName = sqlScriptName;
		this.mappingTableName = mappingTableName;
		this.foreignTableName = foreignTableName;
		this.outputFileName = outputFileName;
		this.substitutionFileName = substitutionFileName;
		
		this.configuredDataSource = dataSource;
		this.configuredDataset = dataset;
		this.tweaks = tweaks;
	}

	public void slaveInit(DataSource dataSource, DatasetIdentifier dataset, TweakSet tweaks, 
			String sqlScriptName,  
			String outputFileName, Map<Object, String>  subst) {
		
		if (subst!=null) {
				this.subsitutions = new ArrayList<Functor<String, String>>(subst.size());
				
				for (Map.Entry<Object, String> e: subst.entrySet()) {
					Object o = e.getKey();
					Functor<String, String> f;
					if (o instanceof Pattern)  {
						f = new SqlScriptRunner.RegularExpressionMangler((Pattern)o, Matcher.quoteReplacement(e.getValue()));
					}
					else {
						f = SqlScriptRunner.makeCommentSubstitutionMangler(e.getKey().toString(), e.getValue());
					}
					
					this.subsitutions.add(f);
				}
		}
		
		this.sqlScriptName = sqlScriptName;
		this.outputFileName = outputFileName;
		
		this.configuredDataSource = dataSource;
		this.configuredDataset = dataset;
		this.tweaks = tweaks;
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

	public void slaveLaunch() throws Exception {
		setKeepAlive(true);
		launchExecute();
	}
	
	
	protected String getSqlScriptName() throws IOException {
		if (sqlScriptName==null) sqlScriptName = args.getParameter(1);
		return sqlScriptName;
	}

	protected String getMappingTableName() throws IOException {
		if (mappingTableName==null) mappingTableName = args.getParameterCount() > 2 ? args.getParameter(2) : null;
		return mappingTableName;
	}

	protected String getForeignTableName() throws IOException {
		if (foreignTableName==null) foreignTableName = args.getParameterCount() > 3 ? args.getParameter(3) : null;
		return foreignTableName;
	}

	protected String getOutputFileName() {
		if (outputFileName==null) outputFileName = args.getOption("outfile", null);
		return outputFileName;
	}

	protected String getSubstitutionFileName() {
		if (substitutionFileName==null) substitutionFileName = args.getOption("substfile", null);
		return substitutionFileName;
	}

	@Override
	protected void run() throws Exception {
		String n = getSqlScriptName();
		URL u = null;
		
		if (n.matches("[-\\w+\\d]+")) { //plain name
			u = AbstractIntegratorApp.getBuiltinScriptUrl(n, ".sql");
		} 
		
		if (u==null) {
			u = getInputHelper().getURL(n);
		}
		
		String enc = tweaks.getTweak("console-encoding", "UTF-8"); //XXX...
		
		Writer out;
		String o = getOutputFileName();
		boolean doClose;
		
		if (o==null || o.equals("-")) {
			out = ConsoleIO.writer;
			doClose = false;
		} else {
			FileOutputStream fout = new FileOutputStream(new File(o));
			out = new OutputStreamWriter(fout, enc);
			out = new BufferedWriter(out);
			doClose = true;
		}
		
		SqlResultDumper dumper = new Dumper(out);
		SqlScriptRunner runner = new SqlScriptRunner(getConfiguredDataSource(), dumper);
		runner.addManglers(getSqlScriptManglers());
		
		String subst = getSubstitutionFileName();
		if (subst!=null) runner.loadCommentSubstitutions(new File(subst));
		
		runner.runScript(u, null);
		
		out.flush();
		if (doClose) out.close();
	}	

	public String getQualifiedTableName(String table) {
		if (table==null || table.length()==0) return "";
		return getConfiguredDataset().getDbPrefix()+table;
	}
	
	protected Collection<Functor<String, String>> getSqlScriptManglers() throws IOException {
		ArrayList<Functor<String, String>> list = new ArrayList<Functor<String, String>>();
		
		if (this.subsitutions!=null) list.addAll(this.subsitutions);
		
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_prefix", getConfiguredDataset().getDbPrefix()) );
		list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_db", getConfiguredDatasetName()) );
		
		if (getMappingTableName()!=null) list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_mapping_table", getMappingTableName()) );
		if (getForeignTableName()!=null) list.add( SqlScriptRunner.makeCommentSubstitutionMangler("wikiword_foreign_table", getForeignTableName()) );
		
		return list;
	}
	
	public static void main(String[] argv) throws Exception {
		RunIntegratorSql app = new RunIntegratorSql();
		app.launch(argv);
	}
}