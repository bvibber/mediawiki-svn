package org.wikimedia.lsearch.storage;

import java.io.BufferedReader;
import java.io.FileReader;
import java.io.IOException;
import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Hashtable;
import java.util.Iterator;
import java.util.List;
import java.util.Map.Entry;

import org.apache.log4j.Logger;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.related.CompactArticleLinks;
import org.wikimedia.lsearch.related.Related;
import org.wikimedia.lsearch.related.RelatedTitle;

/**
 * MySQL storage backend
 * 
 * 
 * @author rainman
 *
 */
@Deprecated
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
			log.error("Cannot load mysql jdbc driver, class not found: "+e.getMessage(),e);
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
		String sql = "SELECT page_key, page_references from "+getTableName("page",dbname)+" WHERE ";
		if(titles == null || titles.size()==0)
			return new ArrayList<CompactArticleLinks>();
		else if(titles.size()==1){
			sql += "page_key="+quote(escape(titles.iterator().next().getKey()));
		} else{			
			StringBuilder sb = new StringBuilder(sql);
			sb.append("page_key IN (");
			Iterator<Title> it = titles.iterator();
			while(it.hasNext()){
				sb.append('\'');
				sb.append(escape(it.next().getKey()));
				sb.append('\'');
				if(it.hasNext())
					sb.append(',');
			}
			sb.append(");");
			sql = sb.toString();
		}
		try {
			Connection conn = getReadConnection(dbname);
			log.info("Fetching references for "+titles.size()+" pages");
			Statement stmt = conn.createStatement();
			ResultSet res = stmt.executeQuery(sql);
			ArrayList<CompactArticleLinks> ret = new ArrayList<CompactArticleLinks>();
			while(res.next()){
				ret.add(new CompactArticleLinks(res.getString("page_key"),res.getInt("page_references")));				
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
		verifyTable("page",dbname,conn);		
		Iterator<CompactArticleLinks> it = refs.iterator();
		// send chunks of maxPerQuery referenace replacements 
		while(it.hasNext()){		
			StringBuilder sb = new StringBuilder("INSERT INTO "+getTableName("page",dbname)+" (page_key,page_references) VALUES ");
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
					sb.append("') ON DUPLICATE KEY UPDATE page_references=VALUES(page_references);");
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

	@Override
	public HashMap<Title, ArrayList<RelatedTitle>> getRelatedPages(Collection<Title> titles, String dbname) throws IOException {
		String page = getTableName("page",dbname);
		String related = getTableName("related",dbname);
		String sql = "SELECT a.page_key as 'r_to', b.page_key as 'r_related', rel_score FROM "+related+", "+page+" a, "+page+" b WHERE rel_to=a.page_id AND rel_related=b.page_id AND ";
		if(titles == null || titles.size()==0)
			return new HashMap<Title, ArrayList<RelatedTitle>>();
		else if(titles.size()==1){
			sql += "a.page_key="+quote(escape(titles.iterator().next().getKey()));
		} else{			
			StringBuilder sb = new StringBuilder(sql);
			sb.append("a.page_key IN (");
			Iterator<Title> it = titles.iterator();
			while(it.hasNext()){
				sb.append('\'');
				sb.append(escape(it.next().getKey()));
				sb.append('\'');
				if(it.hasNext())
					sb.append(',');
			}
			sb.append(");");
			sql = sb.toString();
		}
		try {
			Connection conn = getReadConnection(dbname);
			log.info("Fetching related info for "+titles.size()+" articles");
			Statement stmt = conn.createStatement();
			ResultSet res = stmt.executeQuery(sql);
			HashMap<Title, ArrayList<RelatedTitle>> ret = new HashMap<Title, ArrayList<RelatedTitle>>();
			while(res.next()){
				Title t1 = new Title(res.getString("r_to"));
				Title t2 = new Title(res.getString("r_related"));
				double score = res.getDouble("rel_score");
				ArrayList<RelatedTitle> rel = ret.get(t1);
				if(rel == null){
					rel = new ArrayList<RelatedTitle>();
					ret.put(t1,rel);
				}
				rel.add(new RelatedTitle(t2,score));				
			}
			conn.close();
			return ret;
		} catch (SQLException e) {
			log.error("Cannot execute sql "+sql+" : "+e.getMessage());
			throw new IOException(e.getMessage());
		}
	}
	
	protected HashMap<String,Integer> getPageIDs(Collection<String> keys, String dbname, Connection conn) throws IOException{		
		String sql = "SELECT page_key, page_id from "+getTableName("page",dbname)+" WHERE ";
		if(keys.size()==1){
			sql += "page_key="+quote(escape(keys.iterator().next()));
		} else{			
			StringBuilder sb = new StringBuilder(sql);
			sb.append("page_key IN (");
			Iterator<String> it = keys.iterator();
			while(it.hasNext()){
				sb.append('\'');
				sb.append(escape(it.next()));
				sb.append('\'');
				if(it.hasNext())
					sb.append(',');
			}
			sb.append(");");
			sql = sb.toString();
		}
		try {
			log.info("Fetching page ids for "+keys.size()+" pages");
			Statement stmt = conn.createStatement();
			ResultSet res = stmt.executeQuery(sql);
			HashMap<String,Integer> map = new HashMap<String,Integer>();
			while(res.next()){
				map.put(res.getString("page_key"),res.getInt("page_id"));
			}
			return map;
		} catch (SQLException e) {
			log.error("Cannot execute sql "+sql+" : "+e.getMessage());
			throw new IOException(e.getMessage());
		}
	}

	@Override
	public void storeRelatedPages(Collection<Related> related, String dbname) throws IOException {
		Connection read = getReadConnection(dbname);
		HashSet<String> keys = new HashSet<String>();
		for(Related r : related){
			keys.add(r.getTitle().toString());
			keys.add(r.getRelates().toString());
		}
		HashMap<String,Integer> map = getPageIDs(keys,dbname,read);
		final int maxPerQuery = 20000;		
		Connection write = getWriteConnection(dbname);
		verifyTable("related",dbname,write);		
		Iterator<Related> it = related.iterator();
		// send chunks of maxPerQuery referenace replacements 
		while(it.hasNext()){		
			StringBuilder sb = new StringBuilder("INSERT INTO "+getTableName("related",dbname)+" (rel_to,rel_related,rel_score) VALUES ");
			int count = 0;
			while(it.hasNext() && count < maxPerQuery){
				Related r = it.next();
				sb.append("('");
				sb.append(Integer.toString(map.get(r.getTitle().toString())));
				sb.append("','");
				sb.append(Integer.toString(map.get(r.getRelates().toString())));
				sb.append("','");
				sb.append(Double.toString(r.getScore()));
				count++;
				if(it.hasNext() && count<maxPerQuery)
					sb.append("'), ");
				else
					sb.append("') ON DUPLICATE KEY UPDATE rel_score=VALUES(rel_score);");
			}
			try {
				log.info("Storing "+Math.min(maxPerQuery,count)+" related pages... ");
				Statement stmt = write.createStatement();
				stmt.executeUpdate(sb.toString());			
				
			} catch (SQLException e) {
				log.error("Cannot execute replace query "+sb+" : "+e.getMessage());
				throw new IOException(e.getMessage());
			}		
		}
		try {
			write.close(); // be sure we close the connection
			read.close();
		} catch (SQLException e) {
		}
		
	}	
}
