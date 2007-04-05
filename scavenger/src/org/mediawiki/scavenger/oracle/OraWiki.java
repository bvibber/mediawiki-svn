package org.mediawiki.scavenger.oracle;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;

import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public class OraWiki extends Wiki {
	Connection dbc;
	
	public OraWiki(Connection dbc) throws SQLException {
		this.dbc = dbc;
	}
	
	public void commit() throws SQLException {
		dbc.commit();
	}

	public Title getTitle(String name) {
		return new Title(dbc, name);
	}

	public User getUser(String name, boolean anon) throws SQLException {
		return new OraUser(dbc, name, anon);
	}

	public void rollback() throws SQLException {
		dbc.rollback();
	}
	
	public OraPage getPage(Title t) {
		return new OraPage(dbc, t);
	}
	
	public OraRevision getRevision(int rev_id) throws SQLException {
		return new OraRevision(dbc, rev_id);
	}
	
	public static int getSerial(Connection dbc, String name) throws SQLException {
		PreparedStatement stmt = dbc.prepareStatement("SELECT " + name + ".nextval FROM dual");
		
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
