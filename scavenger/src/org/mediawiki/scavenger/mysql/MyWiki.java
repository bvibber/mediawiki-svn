package org.mediawiki.scavenger.mysql;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
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

public class MyWiki extends Wiki {
	Connection dbc;
	
	public MyWiki(Connection d, HttpServletRequest req, Properties p) {
		super(req, p);
		dbc = d;
	}
	
	public void commit() throws SQLException {
		dbc.commit();
	}

	public Title getTitle(String name) {
		return new Title(name);
	}

	public User getUser(String name, boolean anon) throws SQLException {
		return new MyUser(dbc, name, anon);
	}

	public void close() throws SQLException {
		dbc.close();
	}
	
	public void rollback() throws SQLException {
		dbc.rollback();
	}
	
	public MyPage getPage(Title t) throws SQLException {
		return new MyPage(dbc, t);
	}
	
	public MyRevision getRevision(int rev_id) throws SQLException {
		return new MyRevision(dbc, rev_id);
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
			Revision r = new MyRevision(rs);
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
			Page p = new MyPage(dbc, rs);
			result.add(p);
		}
		
		return result;
	}
	
	public List<String> getPrefixMatches(String pfx, int num) throws SQLException {
		List<String> result = new ArrayList<String>();
		pfx = pfx.replaceAll("\\$", "$$")
				.replaceAll("[%_]", "$\\1") + "%";
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT page_title FROM page WHERE page_key LIKE ? {escape '$'}");
		stmt.setString(1, pfx);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		while (rs.next() && (num-- > 0))
			result.add(rs.getString(1));
		rs.close();
		stmt.close();
		return result;
	}
}
