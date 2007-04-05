package org.mediawiki.scavenger;

import java.sql.Connection;
import java.sql.DriverManager;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.Properties;

import javax.servlet.ServletContext;

import org.mediawiki.scavenger.mysql.MyWiki;
import org.mediawiki.scavenger.oracle.OraWiki;
import org.mediawiki.scavenger.pg.PgWiki;

public abstract class Wiki {
	public static Wiki getWiki(ServletContext ctx) throws Exception {
		Properties p;
		p = new Properties();
		p.load(ctx.getResourceAsStream("/WEB-INF/config.properties"));
		
		Connection dbc;
		String dbtype = p.getProperty("scavenger.dbtype");
		if (dbtype.equals("mysql")) {
			Class.forName("com.mysql.jdbc.Driver");
			dbc = DriverManager.getConnection(p.getProperty("scavenger.dburl"),
					p.getProperty("scavenger.dbuser"), p.getProperty("scavenger.dbpassword"));
			dbc.setAutoCommit(false);
			return new MyWiki(dbc);
		} else if (dbtype.equals("postgres")) {
			Class.forName("org.postgresql.Driver");
			dbc = DriverManager.getConnection(p.getProperty("scavenger.dburl"),
					p.getProperty("scavenger.dbuser"), p.getProperty("scavenger.dbpassword"));
			dbc.setAutoCommit(false);
			return new PgWiki(dbc, p.getProperty("scavenger.dbschema"));		
		} else if (dbtype.equals("oracle")) {
			Class.forName("oracle.jdbc.OracleDriver");
			dbc = DriverManager.getConnection(p.getProperty("scavenger.dburl"),
					p.getProperty("scavenger.dbuser"), p.getProperty("scavenger.dbpassword"));
			return new OraWiki(dbc);
		} else
			return null;
	}
	
	public abstract Title getTitle(String name);
	public abstract Page getPage(Title t);
	public abstract User getUser(String name, boolean anon) throws SQLException;
	public abstract Revision getRevision(int rev_id) throws SQLException;
	public abstract void commit() throws SQLException;
	public abstract void rollback() throws SQLException;
}
