package de.brightbyte.wikiword.dump;

import java.io.File;
import java.io.IOException;
import java.io.InterruptedIOException;
import java.io.OutputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Date;
import java.util.List;
import java.util.regex.Pattern;

import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.io.IOUtil;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StringUtils;
import de.brightbyte.util.SystemUtils;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.DatasetIdentifier;

public class DumpThesaurus extends CliApp {

	@Override
	protected void declareOptions() {
		super.declareOptions();
		
		declareOption("outdir", "O", true, String.class, "the directory to write the output to. Defaults to the current dir");
	}

	private List<String> getThesaurusFields(String t, boolean local) {
		return Arrays.asList( new String[] { "*" } );
	}

	private List<String> getThesaurusTables(boolean local) {
		ArrayList<String> tables = new ArrayList<String>();
		
		tables.add( "about" );
		tables.add( "concept" );
		tables.add( "broader" );
		tables.add( "relation" );
		tables.add( "meaning" );
		
		tables.add( "property" );
		tables.add( "feature" );
		tables.add( "degree" );
		
		if ( local ) {
			tables.add( "resource" );
		} else {
			tables.add( "origin" );
		}
		
		return tables;
	}

	@Override
	protected void run() throws Exception {
		DatabaseConnectionInfo dbSpec = (DatabaseConnectionInfo)getConfiguredDataSource() ;		
		DatasetIdentifier dataset = getConfiguredDataset();
		
		File dir = new File( args.getStringOption("outdir", ".") );
		
		if ( !dir.exists() ) throw new IOException("not found: "+dir);
		if ( !dir.isDirectory() ) throw new IOException("not a directory: "+dir);
		
		List<String> tables;
		if ( args.getParameterCount() > 1 ) {
			tables = args.getParameters().subList(1, args.getParameters().size());
		} else {
			tables = getThesaurusTables( isDatasetLocal() );
		}
		
		for (String table: tables) {
				List<String> fields = getThesaurusFields( table, isDatasetLocal() );
				
				table = dataset.getDbPrefix() + table;
				dumpTable( dbSpec, table, fields, dir);
		}

	}

	protected File dumpTable(DatabaseConnectionInfo dbCredentials, String table, List<String> fields, File outFile) throws IOException, PersistenceException {
		String dbName = dbCredentials.getDatabaseName();
		if ( dbName == null ) throw new PersistenceException( "no database name configured" );
		
		if (outFile==null)  outFile = File.createTempFile(dbName+"-"+table, ".csv");
		else if (outFile.isDirectory()) outFile = new File(outFile, dbName+"-"+table+"-"+SystemUtils.timestamp()+".csv");
		
		info("Dumping table "+table+" to "+outFile+"...");

		List<String> cmd = getDumpCommand( dbCredentials, table, fields, outFile );
		String input = getDumpCommandInput( dbCredentials, table, fields, outFile );
		
		runCommand(cmd, input);
		
		//info("Table "+table+" dumped to "+outFile);
		return outFile;
	}
	
	protected void runCommand(List<String> cmd, String input) throws IOException, PersistenceException {
		String enc = tweaks.getTweak("console.encoding", "UTF-8");
		
		ProcessBuilder pb = new ProcessBuilder(cmd);
		
		debug("Running Command: "+pb.command().toString());
		Process p = pb.start();
		
		if (input!=null) {
			OutputStream toProcess = p.getOutputStream();
			toProcess.write( input.getBytes(enc) );
			toProcess.flush();
		}
		
		IOUtil.pump(p.getInputStream(), System.out);
		String error = IOUtil.slurp(p.getErrorStream(), enc); //just hope the buffer is big enough...		
		
		try {
			p.waitFor();
		} catch (InterruptedException e) {
			throw (InterruptedIOException)new InterruptedIOException(e.getMessage()).initCause(e);
		}
		
		if ( p.exitValue() > 0 ) {
			throw new IOException( "dump command returned error code #"+ p.exitValue() + ". Output: " + error );
		}
	}

	private String getDumpCommandInput(DatabaseConnectionInfo dbCredentials, String table, List<String> fields, File tmp) {
		return dbCredentials.getPassword() + "\n";
	}

	private List<String> getDumpCommand(DatabaseConnectionInfo dbCredentials, String table, List<String> fields, File tmp) throws PersistenceException {
		String dbName = dbCredentials.getDatabaseName();
		if ( dbName == null ) throw new PersistenceException( "no database name configured" );
		
		List<String> shell = getShellCommand();
		String sql = getDumpSQL( dbName,  table, fields );
		
		StringBuilder command = new StringBuilder( tweaks.getTweak("mysql.command", "mysql") );
		
		command.append( " " ).append( shellQuote( dbName ) );
		command.append( " -u " ).append( shellQuote( dbCredentials.getUser() ) );
		command.append( " -p " );
		command.append( " -e " ).append( shellQuote( sql )  );
		command.append( " > " ).append( shellQuote( tmp.getAbsolutePath() )  );
		
		List<String> cmd = new ArrayList<String>( shell ); 
		cmd.add( command.toString() );
		return cmd;
	}

	private List<String> getShellCommand() {
		String s = tweaks.getTweak("console.shell", null);
		if ( s==null ) {
			if ( SystemUtils.isWindows() ) s = "cmd.exe /c";
			else s = "/bin/sh -c";
		}
		
		String[] ss = s.split(" ");
		List<String> shell = Arrays.asList( ss );
		return shell;
	}

	private String getDumpSQL(String dbName, String table, List<String> fields) {
		String f = StringUtils.join(", ", fields);
		String sql = "SELECT " + f + " FROM " + table;
		return sql;
	}

	private static Pattern shellQuotePattern = Pattern.compile("([\"!\\\\])");
	
	protected static String shellQuote(String s) {
		s = shellQuotePattern.matcher(s).replaceAll("\\\\\\1");
		return "\"" + s + "\"";
	}

	public static void main(String[] argv) throws Exception {
		DumpThesaurus app = new DumpThesaurus();
		app.launch(argv);
	}

}
