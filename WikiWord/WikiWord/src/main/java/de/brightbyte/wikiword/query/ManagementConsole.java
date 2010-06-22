package de.brightbyte.wikiword.query;

import java.io.File;
import java.io.IOException;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.List;
import java.util.NoSuchElementException;

import javax.sql.DataSource;

import de.brightbyte.application.Arguments;
import de.brightbyte.data.Pair;
import de.brightbyte.db.DatabaseConnectionInfo;
import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.db.SingletonDataSource;
import de.brightbyte.io.Prompt;
import de.brightbyte.io.WriterOutput;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.DatabaseWikiWordConceptStore;

public class ManagementConsole {
	
	private WikiWordStoreSchema db;
	private boolean force;
	private Prompt prompt;
	
	public ManagementConsole(WikiWordStoreSchema db, boolean force) {
		super();
		this.db = db;
		this.force = force; 
		
		this.prompt = new Prompt();
	}

	public static void main(String[] argv) throws SQLException, IOException, PersistenceException {
		Arguments args = new Arguments();
		args.parse(argv);
		
		File dbfile = CliApp.findConfigFile(args, "db");

		if (dbfile==null) throw new RuntimeException("no database config file found. Splease specify --db option.");

		boolean force = args.isSet("force");

		List<String> params = args.getParameters();
		
		DatabaseConnectionInfo dbinfo = new DatabaseConnectionInfo(dbfile);
		
		TweakSet tweaks = new TweakSet(); //TODO: load?...
		
		DatasetIdentifier dataset = DatasetIdentifier.forName("dummy", "thingy", tweaks);
		
		WikiWordStoreSchema db = new WikiWordStoreSchema(dataset, dbinfo, tweaks, false);
		db.open();
		
		ManagementConsole alter = new ManagementConsole(db, force);
		
		if (params.size()>0) alter.runCommand(params);
		else alter.runConsole();
		
		db.close();
	}
	
	public void runConsole() {
		echo("hello");

		while (true) {
			List<String> params= promptCommand();
			if (params==null) break;
			if (params.size()==0) continue;
			
			String cmd = params.get(0);
			cmd = cmd.trim().toLowerCase();
			
			if (cmd.equals("quit") || cmd.equals("exit")) break;
			
			try {
				runCommand(params);
			} catch (RuntimeException e) {
				e.printStackTrace(prompt.getOut());
			} catch (Exception e) {
				e.printStackTrace(prompt.getOut());
			}
		}
		
		echo("bye");
	}
	
	private List<String> promptCommand() {
		String s = prompt.prompt(">", "");
		if (s==null) return null;
		if (s.length()==0) return Collections.emptyList();
		
		String[] ss = s.split("\\s+");
		return Arrays.asList(ss);
	}

	public void runCommand(List<String> params) throws SQLException, IOException, PersistenceException {
		String cmd = params.get(0);
		cmd = cmd.trim().toLowerCase();
		
		if (cmd.equals("rename") || cmd.equals("move") || cmd.equals("mv")) {
			cmd = "rename";
		}
		else if (cmd.equals("copy") || cmd.equals("cp")) {
			cmd = "copy";
		}
		else if (cmd.equals("remove") || cmd.equals("delete") || cmd.equals("rm") || cmd.equals("del") || cmd.equals("drop")) {
			cmd = "delete";
		}
		else if (cmd.equals("stats") || cmd.equals("check")) {
			//noop
		}
		else if (cmd.equals("processlist") || cmd.equals("processes")) {
			cmd = "processes";
		}
		else if (cmd.equals("collections") || cmd.equals("datasets")) {
			//noop
		}
		else if (cmd.equals("kill")) {
			//noop
		}
		else {
			throw new IllegalArgumentException("unknown command: "+cmd);
		}

		if (cmd.equals("rename") || cmd.equals("copy")) {
			String from = params.get(1);
			String to = params.get(2);

			alterTables(from, to, cmd);
		}
		else if (cmd.equals("delete")) {
			String name = params.get(1);
			deleteTables(name);
		}
		else if (cmd.equals("stats")) {
			String name = params.get(1);
			DatabaseWikiWordConceptStore store = getConceptStore(name);
			store.dumpTableStats(new WriterOutput(prompt.getOut()));
		}
		else if (cmd.equals("check")) {
			String name = params.get(1);
			DatabaseWikiWordConceptStore store = getConceptStore(name);
			store.checkConsistency();
		}
		else if (cmd.equals("processes")) {
			if (params.size()>1 && params.get(1).equalsIgnoreCase("full")) runQuery("SHOW FULL PROCESSLIST"); 
			else runQuery("SHOW PROCESSLIST");
		}
		else if (cmd.equals("kill")) {
			if (params.size()==1) throw new NoSuchElementException("KILL requires at least one argument"); 
			
			for (String k: params) {
				runUpdate("KILL "+Integer.parseInt(k));
			}
		}
		else if (cmd.equals("collections")) {
			String[] tables = db.listPrefixes(null, "concept");
			
			List<String> collections = new ArrayList<String>();
			for (String t: tables) {
				String c = t.replaceAll("^(\\w+)_.*$", "$1");
				if (!collections.contains(c)) collections.add(c);
			}
			
			for (String c: collections) {
				echo("\t"+c);
			}
		}
		else if (cmd.equals("datasets")) {
			if (params.size()==1) throw new NoSuchElementException("DATASETS requires exactly one argument: the collection name");
			
			String coll = params.get(1); 
			String[] tables = db.listPrefixes(coll, "concept");
			
			for (String t: tables) {
				echo("\t"+coll+"_"+t.replace("_concept", ""));
			}
		}
	}

	protected void runQuery(String sql) throws SQLException, IOException {
		ResultSet rs = db.executeQuery("query", sql);
		DatabaseUtil.dumpData(rs, prompt.getOut(), "\t|\t");
	}

	protected void runUpdate(String sql) throws SQLException, IOException {
		int c = db.executeUpdate("query", sql);
		echo("\t"+c+" rows updated.");
	}

	public DatabaseWikiWordConceptStore getConceptStore(String name) throws SQLException, PersistenceException {
		TweakSet tweaks = new TweakSet();
		DatasetIdentifier dataset = DatasetIdentifier.forName("", name, tweaks);
		DataSource ds = new SingletonDataSource(db.getConnection());
		
		DatabaseWikiWordConceptStore store = DatabaseConceptStores.createConceptStore(ds, dataset, tweaks, true, true);
		return store;
	}

	public List<String> getTables(String prefix) throws SQLException {
		String sql = "SHOW TABLES LIKE "+db.quoteString(prefix+"\\_%");
		ResultSet res = db.executeQuery("getTable", sql); 
		List<String> tables = (List<String>)DatabaseSchema.slurpList(res, 1);
		res.close();
		
		return tables;
	}
	
	protected List<Pair<String, String>> prepareTargetList(List<String> tables, String from, String to) throws SQLException {
		List<Pair<String, String>> tt = new ArrayList<Pair<String, String>>(tables.size());
		
		for(String t: tables) {
			String s = to + "_" + t.substring(from.length()+1);
			if (tableExists(s)) {
				if (force) deleteTable(s);
				else throw new IllegalArgumentException("table "+s+" already exists!");
			}
			
			tt.add(new Pair<String, String>(t, s));
		}
		
		return tt;
	}
	
	private boolean tableExists(String s) throws SQLException {
		s = s.replaceAll("_", "\\\\_");
		String sql = "SHOW TABLES LIKE "+db.quoteString(s);
		return db.executeSingleRowQuery("tabeExists", sql) != null;
	}

	public void alterTables(String from, String to, String cmd) throws SQLException{
		List<String> tables = getTables(from);
		echo("processing "+tables.size()+" tables");
		if (tables.size()==0) return;
		
		List<Pair<String, String>>  pairs = prepareTargetList(tables, from, to);
		for (Pair<String, String> p: pairs) {
			if (cmd.equals("rename")) renameTable(p.getA(), p.getB());
			else if (cmd.equals("copy")) copyTable(p.getA(), p.getB());
			else throw new IllegalArgumentException("bad command: "+cmd);
		}
	}

	protected boolean confirm(String msg) {
		String r = prompt.prompt(msg, new String[] {"y", "n"},  "n");
		if (r==null || !r.equals("y")) {
			return false;
		}
		
		return true;
	}
	
	public void deleteTables(String prefix) throws SQLException{
		List<String> tables = getTables(prefix);
		if (tables.size()==0) return;
				
		if (!force) {
			if (!confirm("delete "+tables.size()+" tables with prefix "+prefix+"?")) {
				return;
			}
		}
		else {
			echo("deleting "+tables.size()+" tables with prefix "+prefix);
		}
		
		for (String t: tables) {
			deleteTable(t);
		}
	}

	private void copyTable(String a, String b) throws SQLException {
		echo("copying table "+a+" to "+b);

		String sql = "CREATE TABLE "+db.quoteName(b)+" LIKE "+db.quoteName(a);
		db.executeUpdate("copyTable", sql);

		sql = "INSERT INTO "+db.quoteName(b)+" SELECT * FROM "+db.quoteName(a);
		db.executeUpdate("copyTable", sql);
	}

	private void renameTable(String a, String b) throws SQLException {
		echo("renaming table "+a+" to "+b);

		String sql = "RENAME TABLE "+db.quoteName(a)+" TO "+db.quoteName(b);
		db.executeUpdate("renameTable", sql);
	}

	private void deleteTable(String n) throws SQLException {
		echo("deleting table "+n);

		String sql = "DROP TABLE "+db.quoteName(n);
		db.executeUpdate("deleteTable", sql);
	}

	private void echo(String msg) {
		prompt.println(msg);
	}
}
