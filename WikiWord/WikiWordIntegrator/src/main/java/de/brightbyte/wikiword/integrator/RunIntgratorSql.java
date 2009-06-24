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
import java.util.regex.Pattern;

import javax.sql.DataSource;

import de.brightbyte.data.Functor;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.SqlResultDumper;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.db.file.TsvWriter;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;

public class RunIntgratorSql extends CliApp {
	
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
				
				tsv.close();
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
		if (outputFileName!=null) outputFileName = args.getOption("outfile", null);
		return outputFileName;
	}

	protected String getSubstitutionFileName() {
		if (substitutionFileName!=null) substitutionFileName = args.getOption("substfile", null);
		return substitutionFileName;
	}

	protected URL getDefaultSqlScriptUrl(String name) {
		URL u =RunIntgratorSql.class.getResource(name+".sql");
		return u;
	}
	
	@Override
	protected void run() throws Exception {
		String n = getSqlScriptName();
		URL u = null;
		
		if (n.matches("[-\\w+\\d]+")) { //plain name
			u = getDefaultSqlScriptUrl(n);
		} 
		
		if (u==null) {
			u = getInputHelper().getURL(n);
		}
		
		String enc = tweaks.getTweak("console-encoding", "UTF-8"); //XXX...
		
		Writer out;
		String o = getOutputFileName();
		if (o==null || o.equals("-")) {
			out = new OutputStreamWriter(System.out, enc);
		} else {
			FileOutputStream fout = new FileOutputStream(new File(o));
			out = new OutputStreamWriter(fout, enc);
		}
		
		out = new BufferedWriter(out);

		SqlResultDumper dumper = new Dumper(out);
		SqlScriptRunner runner = new SqlScriptRunner(getConfiguredDataSource(), dumper);
		runner.addManglers(getSqlScriptManglers());
		
		String subst = getSubstitutionFileName();
		if (subst!=null) runner.loadCommentSubstitutions(new File(subst));
		
		runner.runScript(u, null);
		out.close();
	}	

	public String getQualifiedTableName(String table) {
		if (table==null || table.length()==0) return "";
		return getConfiguredDataset().getDbPrefix()+table;
	}
	
	protected Collection<Functor<String, String>> getSqlScriptManglers() throws IOException {
		ArrayList<Functor<String, String>> list = new ArrayList<Functor<String, String>>();
		
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_prefix* \\*/"), getConfiguredDataset().getDbPrefix()) );
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_db* \\*/"), getConfiguredDatasetName()) );
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_mapping_table* \\*/"), getQualifiedTableName(getMappingTableName())) );
		list.add( new SqlScriptRunner.RegularExpressionMangler(Pattern.compile("/\\* *wikiword_foreign_table* \\*/"), getQualifiedTableName(getForeignTableName())) );
		
		return list;
	}
	
	public static void main(String[] argv) throws Exception {
		RunIntgratorSql app = new RunIntgratorSql();
		app.launch(argv);
	}
}