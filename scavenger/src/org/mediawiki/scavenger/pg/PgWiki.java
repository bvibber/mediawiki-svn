package org.mediawiki.scavenger.pg;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.Properties;

import javax.servlet.http.HttpServletRequest;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.RecentChange;
import org.mediawiki.scavenger.Revision;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public class PgWiki extends Wiki {
	Connection dbc;
	
	public PgWiki(Connection d, String schema, HttpServletRequest req, Properties p) throws SQLException {
		super(req, p);
		dbc = d;
		Statement st = dbc.createStatement();
		st.executeUpdate("SET SEARCH_PATH = " + schema + ", public");
		st.close();
	}
	
	public void commit() throws SQLException {
		dbc.commit();
	}

	public Title getTitle(String name) {
		return new Title(name);
	}

	public User getUser(String name, boolean anon) throws SQLException {
		return new PgUser(dbc, name, anon);
	}

	public void rollback() throws SQLException {
		dbc.rollback();
	}
	
	public PgPage getPage(Title t) throws SQLException {
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
	
	public List<RecentChange> getRecentChanges(int num) throws SQLException {
		List<RecentChange> result = new ArrayList<RecentChange>();
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_id, rev_page, rev_text_id, rev_timestamp, rev_comment, " +
				"rev_user FROM revision ORDER BY rev_timestamp DESC LIMIT ?");
		stmt.setInt(1, num);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		while (rs.next()) {
			Revision r = new PgRevision(rs);
			result.add(new RecentChange(r));
		}
		
		return result;
	}
	
	
	public List<Page> getAllPages() throws SQLException {
		List<Page> result = new ArrayList<Page>();
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT page_id, page_title, page_latest FROM page ORDER BY page_title ASC");
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		while (rs.next()) {
			Page p = new PgPage(dbc, rs);
			result.add(p);
		}
		
		return result;
	}
}
