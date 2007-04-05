package org.mediawiki.scavenger.pg;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public class PgWiki extends Wiki {
	Connection dbc;
	
	public PgWiki(Connection dbc, String schema) throws SQLException {
		this.dbc = dbc;
		Statement st = dbc.createStatement();
		st.executeUpdate("SET SEARCH_PATH = " + schema + ", public");
		st.close();
	}
	
	public void commit() throws SQLException {
		dbc.commit();
	}

	public Title getTitle(String name) {
		return new Title(dbc, name);
	}

	public User getUser(String name, boolean anon) throws SQLException {
		return new PgUser(dbc, name, anon);
	}

	public void rollback() throws SQLException {
		dbc.rollback();
	}
	
	public PgPage getPage(Title t) {
		return new PgPage(dbc, t);
	}
	
	public PgRevision getRevision(int rev_id) throws SQLException {
		return new PgRevision(dbc, rev_id);
	}
	
	public static int getSerial(Connection dbc, String name) throws SQLException {
		PreparedStatement stmt = dbc.prepareStatement("SELECT NEXTVAL(?)");
		stmt.setString(1, name);
		
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		if (!rs.next())
			throw new SQLException("failed to obtain id");
		int id = rs.getInt(1);
		rs.close();
		stmt.close();
		return id;
	}
}
