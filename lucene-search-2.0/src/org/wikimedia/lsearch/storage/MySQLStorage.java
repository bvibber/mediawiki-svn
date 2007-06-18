package org.wikimedia.lsearch.storage;

import java.io.BufferedReader;
import java.io.FileNotFoundException;
import java.io.FileReader;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.ranks.CompactArticleLinks;

/**
 * MySQL storage backend
 * 
 * 
 * @author rainman
 *
 */
public class MySQLStorage extends Storage {
	static Logger log = Logger.getLogger(MySQLStorage.class);
	protected Configuration config;
	/** master host */
	protected String master;
	/** slave host -> % of load */
	protected Hashtable<String,Double> slaves = null;
	/** mysql username */
	protected String username;
	/** mysql password */
	protected String password;
	/** administrator's username */
	protected String adminUsername;
	/** administrator's password */
	protected String adminPassword;
	/** If we should separate data in many dbs */
	protected boolean separate;
	/** db where to put everything, if we are not using one db per dbname */
	protected String defaultDB;
	/** where sql stuff is, e.g. references_table.sql */
	protected String lib; 
	/** table name -> create table file */ 
	protected Hashtable<String,String> tableDefs = new Hashtable<String,String>();
	
	protected MySQLStorage() {
		config = Configuration.open();
		try {
			Class.forName("com.mysql.jdbc.Driver");
		} catch (ClassNotFoundException e) {
			log.error("Cannot load mysql jdbc driver, class not found: "+e.getMessage());
		}
		
		lib = config.getString("Storage","lib","./sql");
		
		master = config.getString("Storage","master","localhost");
		String[] ss = config.getArray("Storage","slaves");
		if(ss != null){
			Hashtable<String,Double> rawslaves = new Hashtable<String,Double>();
			for(String slave : ss){
				String[] parts = slave.split("->",2);
				if(parts.length==2){
					rawslaves.put(parts[0],Double.parseDouble(parts[1]));
				}
			}
			// normalize to 1
			double sum = 0;
			for(Double d : rawslaves.values())
				sum += d;
			if(sum == 0) // in case no loads are specified
				sum = 1;
			slaves = new Hashtable<String,Double>();
			for(Entry<String,Double> ed : rawslaves.entrySet())
				slaves.put(ed.getKey(),ed.getValue()/sum);
			
		}
		
		username = config.getString("Storage","username","root");
		password = config.getString("Storage","password","");
		
		adminUsername = config.getString("Storage","adminuser",username);
		adminPassword = config.getString("Storage","adminpass",password);
		
		// figure out db configuration
		separate = config.getBoolean("Storage","useSeparateDBs");
		if(!separate){
			defaultDB = config.getString("Storage","defaultDB");
			if(defaultDB == null){
				log.error("Set Storage.defaultDB in local configuration.");
			}
		}
	} 
	
	/** Get connection for writing stuff, i.e. on the master */
	protected Connection getReadConnection(String dbname) throws IOException{
		return openConnection(dbname,false,false);
	}
	
	/** Get connection for reading of (possibly lagged) stuff, i.e. on slaves (or master if there are no slaves) */
	protected Connection getWriteConnection(String dbname) throws IOException{
		return openConnection(dbname,true,false);
	}
	
	/** Get administrators connection for creating tables/db, etc.. (on master) */
	protected Connection getAdminConnection(String dbname) throws IOException {
		return openConnection(dbname,true,true);
	}
	
	/** Open connection on the master, or load-balanced on one of the slaves */
	protected Connection openConnection(String dbname, boolean onMaster, boolean admin) throws IOException {
		String host=null;
		if(onMaster || slaves == null)
			host = master;
		else{
			// load balance slaves
			double r = Math.random();
			for(Entry<String,Double> load : slaves.entrySet()){
				r-=load.getValue();
				if(r < 0){
					host = load.getKey();
					break;
				}
			}
		}
		String dburl = "jdbc:mysql://"+host+":3306/";
		if(!separate && defaultDB!=null)
			dburl += defaultDB;
		dburl += "?useUnicode=yes&characterEncoding=UTF-8";
		try {
			if(admin)
				return DriverManager.getConnection(dburl, adminUsername, adminPassword);
			else
				return DriverManager.getConnection(dburl, username, password);
		} catch (SQLException e) {
			log.error("Cannot establish connection to "+dburl+" - check host, db, username and password : "+e.getMessage());
			throw new IOException("Cannot establish connection to mysql database.");
		}
	}

	public String quote(String str){
		return "'"+str+"'";
	}
	
	public String escape(String str){
		return str.replace("\\","\\\\").replace("'","\\'");
	}
	
	public String getTableName(String name, String dbname){
		if(!separate)
			return dbname+"_"+name;
		else
			return name;
	}
	
	// inherit javadoc 
	public Collection<CompactArticleLinks> getPageReferences(Collection<Title> titles, String dbname) throws IOException {		
		String sql = "SELECT rf_key, rf_references from "+getTableName("references",dbname)+" WHERE ";
		if(titles == null || titles.size()==0)
			return new ArrayList<CompactArticleLinks>();
		else if(titles.size()==1){
			sql += "rf_key="+quote(escape(titles.iterator().next().getKey()));
		} else{			
			StringBuilder sb = new StringBuilder(sql);
			sb.append("rf_key IN (");
			Iterator<Title> it = titles.iterator();
			while(it.hasNext()){
				sb.append('\'');
				sb.append(escape(it.next().getKey()));
				sb.append('\'');
				if(it.hasNext())
					sb.append(',');
			}
			sb.append(")");
			sql = sb.toString();
		}
		try {
			Connection conn = getReadConnection(dbname);
			log.info("Fetching references for "+titles.size()+" pages");
			Statement stmt = conn.createStatement();
			ResultSet res = stmt.executeQuery(sql);
			ArrayList<CompactArticleLinks> ret = new ArrayList<CompactArticleLinks>();
			while(res.next()){
				ret.add(new CompactArticleLinks(res.getString("rf_key"),res.getInt("rf_references")));				
			}
			conn.close();
			return ret;
		} catch (SQLException e) {
			log.error("Cannot execute sql "+sql+" : "+e.getMessage());
			throw new IOException(e.getMessage());
		}
	}

	// inherit javadoc
	public void storePageReferences(Collection<CompactArticleLinks> refs, String dbname) throws IOException {
		final int maxPerQuery = 10000;		
		Connection conn = getWriteConnection(dbname);
		verifyTable("references",dbname,conn);		
		Iterator<CompactArticleLinks> it = refs.iterator();
		// send chunks of maxPerQuery referenace replacements 
		while(it.hasNext()){		
			StringBuilder sb = new StringBuilder("REPLACE INTO "+getTableName("references",dbname)+" (rf_key,rf_references) VALUES ");
			int count = 0;
			while(it.hasNext() && count < maxPerQuery){
				CompactArticleLinks cs = it.next();
				sb.append("('");
				sb.append(escape(cs.getKey()));
				sb.append("','");
				sb.append(cs.links);
				count++;
				if(it.hasNext() && count<maxPerQuery)
					sb.append("'), ");
				else
					sb.append("');");
			}
			try {
				log.info("Storing "+Math.min(maxPerQuery,count)+" page ranks... ");
				Statement stmt = conn.createStatement();
				stmt.executeUpdate(sb.toString());			
				
			} catch (SQLException e) {
				log.error("Cannot execute replace query "+sb+" : "+e.getMessage());
				throw new IOException(e.getMessage());
			}		
		}
		try {
			conn.close(); // be sure we close the connection
		} catch (SQLException e) {
		}
	}

	/** Creates table if it doesn't exist  */
	protected void verifyTable(String name, String dbname, Connection conn) throws IOException {
		// verify if table exists
		String table = getTableName(name,dbname);
		try {
			log.info("Verifying table "+name+" on "+dbname);
			Statement stmt = conn.createStatement();
			ResultSet res = stmt.executeQuery("SHOW TABLES LIKE '"+table+"';");
			if(res.next()) // table exists!
				return;
			
		} catch (SQLException e) {
			log.error("Cannot verify table "+table+" : "+e.getMessage());
			throw new IOException(e.getMessage());
		}
		
		// fetch table definition
		String def = tableDefs.get(name);
		if(def == null){
			if(!lib.endsWith(Configuration.PATH_SEP))
				lib = lib+Configuration.PATH_SEP;
			
			BufferedReader file = new BufferedReader(new FileReader(lib+name+"_table.sql"));		
			StringBuilder sb = new StringBuilder();
			String line;
			while((line = file.readLine()) != null){
				sb.append(line.replaceFirst("--.*",""));
			}
			def = sb.toString();
		}
		// preprocess dbprefix tags
		String tdef;
		if(!separate)
			tdef = def.replace("/*DBprefix*/",dbname+"_");
		else
			tdef = def;		
		// create
		try {
			Connection admin = getAdminConnection(dbname);
			log.info("Creating table "+name+" on "+dbname);
			Statement stmt = admin.createStatement();
			stmt.executeUpdate(tdef);
			admin.close();
		} catch (SQLException e) {
			log.error("Cannot create table "+table+" : "+e.getMessage());
			throw new IOException(e.getMessage());
		}	
	}	
}
