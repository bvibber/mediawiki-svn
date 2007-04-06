package org.mediawiki.scavenger.mysql;

import java.io.StringReader;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;

/**
 * Represents one page in the database.
 */
public class MyPage implements Page {
	Connection dbc;
	Title title;
	
	/* From our database row... */
	int page_id;
	int page_latest;
	String page_title;
	
	public MyPage(Connection dbc, Title t) throws SQLException {
		PreparedStatement stmt = dbc.prepareStatement(
			"SELECT page_id, page_title, page_latest FROM page WHERE page_title = ?");
		stmt.setString(1, title.getKey());
		stmt.execute();

		ResultSet rs = stmt.getResultSet();
		if (!rs.next()) {
			stmt.close();
			return;	
		}

		page_id = rs.getInt(1);
		page_latest = rs.getInt(2);
		stmt.close();
	}
	
	public MyPage(Connection dbc, int id) throws SQLException {
		page_id = id;
		PreparedStatement stmt = dbc.prepareStatement(
			"SELECT page_title, page_latest FROM page WHERE page_id = ?");
		stmt.setInt(1, page_id);
		stmt.execute();

		ResultSet rs = stmt.getResultSet();
		if (!rs.next()) {
			page_id = -1;
			stmt.close();
			return;	
		}

		title = new Title(rs.getString(1));
		page_latest = rs.getInt(2);
		stmt.close();
	}

	public MyPage(Connection dbc, ResultSet rs) throws SQLException {
		page_id = rs.getInt("page_id");
		title = new Title(rs.getString("page_title"));
		page_latest = rs.getInt("page_latest");
	}
	
	public Title getTitle() {
		return title;
	}
	
	/**
	 * @return The latest version of this page.
	 */
	public MyRevision getLatestRevision() throws SQLException {
		if (page_id == -1)
			/* no such page... */
			return null;
		
		return new MyRevision(dbc, page_latest);
	}
	
	/**
	 * @return Whether this page exists
	 */
	public boolean exists() throws SQLException {
		return (page_id != -1);
	}
	
	/**
	 * Create this page.  Does not create any text or revisions.
	 * @return true if the page was created, otherwise false
	 */
	public boolean create() throws SQLException {
		PreparedStatement stmt = dbc.prepareStatement(
				"INSERT INTO page(page_id, page_title, page_latest) VALUES(NULL, ?, NULL)",
				Statement.RETURN_GENERATED_KEYS);
		stmt.setString(1, title.getKey());
		stmt.executeUpdate();

		ResultSet rs = stmt.getGeneratedKeys();
		if (rs.next()) {
			page_id = rs.getInt(1);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Add a new revision to this page.  Handles updating history, etc.
	 * @param text Text of the new revision
	 */
	public MyRevision edit(User u, String text, String comment) throws SQLException {
		if (page_id == -1)
			create();
		
		/*
		 * Insert the text row first.
		 */
		PreparedStatement stmt = dbc.prepareStatement(
				"INSERT INTO text(text_id, text_content) VALUES(NULL, ?)",
				Statement.RETURN_GENERATED_KEYS);
		stmt.setString(1, text);
		stmt.executeUpdate();
		
		ResultSet rs = stmt.getGeneratedKeys();
		if (!rs.next()) {
			/*
			 * Something went wrong.
			 */
			stmt.close();
			return null;
		}
		
		int text_id = rs.getInt(1);
		stmt.close();
		
		/*
		 * Now insert the revision.
		 */
		PreparedStatement revstmt = dbc.prepareStatement(
				"INSERT INTO revision(rev_id, rev_page, rev_text_id, rev_timestamp, rev_comment, rev_user) " +
				"VALUES(NULL, ?, ?, UTC_TIMESTAMP(), ?, ?)",
				Statement.RETURN_GENERATED_KEYS);
		revstmt.setInt(1, page_id);
		revstmt.setInt(2, text_id);
		revstmt.setString(3, comment);
		revstmt.setInt(4, u.getId());
		
		revstmt.executeUpdate();
		
		rs = revstmt.getGeneratedKeys();
		if (!rs.next()) {
			revstmt.close();
			return null;
		}
		
		int rev_id = rs.getInt(1);
		revstmt.close();
		
		/*
		 * Now update page_latest.
		 */
		PreparedStatement pstmt = dbc.prepareStatement(
				"UPDATE page SET page_latest = ? WHERE page_id = ?");
		pstmt.setInt(1, rev_id);
		pstmt.setInt(2, page_id);
		pstmt.executeUpdate();
		
		return new MyRevision(dbc, rev_id);
	}
	
	/**
	 * Return the edit history for this page.
	 */
	public List<Revision> getHistory(int num) throws Exception {
		List<Revision> revs = new ArrayList<Revision>();
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_id, rev_page, rev_text_id, rev_timestamp, rev_comment, user_name " +
				"FROM revision, user " +
				"WHERE rev_page = ? AND user_id = rev_user ORDER BY rev_timestamp DESC LIMIT ?");
		stmt.setInt(1, page_id);
		stmt.setInt(2, num);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		while (rs.next()) {
			MyRevision r = new MyRevision(rs);
			revs.add(r);
		}
		
		stmt.close();
		return revs;
	}
}
