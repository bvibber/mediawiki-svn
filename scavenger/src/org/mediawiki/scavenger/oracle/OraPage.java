package org.mediawiki.scavenger.oracle;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import org.mediawiki.scavenger.Page;
import org.mediawiki.scavenger.Revision;
import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;

/**
 * Represents one page in the database.
 */
public class OraPage implements Page {
	Connection dbc;
	Title title;
	
	/* From our database row... */
	int page_id;
	int page_latest;
	
	public OraPage(Connection dbc, Title t) throws SQLException {
		this.dbc = dbc;
		page_id = -1;
		PreparedStatement stmt = dbc.prepareStatement(
			"SELECT page_id, page_latest, page_title FROM page WHERE page_key = ?");
		stmt.setString(1, t.getKey());
		stmt.execute();
		
		ResultSet rs = stmt.getResultSet();
		if (!rs.next()) {
			title = t;
			stmt.close();
			return;
		}

		title = new Title(rs.getString(3));
		page_id = rs.getInt(1);
		page_latest = rs.getInt(2);
		stmt.close();
	}

	public OraPage(Connection dbc, int id) throws SQLException {
		this.dbc = dbc;
		title = null;
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

	public OraPage(Connection dbc, ResultSet rs) throws SQLException {
		this.dbc = dbc;
		title = new Title(rs.getString("page_title"));
		page_id = rs.getInt("page_id");
		page_latest = rs.getInt("page_latest");
	}
	
	public Title getTitle() {
		return title;
	}
	
	/**
	 * @return The latest version of this page.
	 */
	public OraRevision getLatestRevision() throws SQLException {
		if (page_id == -1)
			/* no such page... */
			return null;
		
		return new OraRevision(dbc, page_latest);
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
		page_id = OraWiki.getSerial(dbc, "page_page_id_seq");
		PreparedStatement stmt;
		
		stmt = dbc.prepareStatement(
				"INSERT INTO page(page_id, page_title, page_key, page_latest) VALUES(?, ?, ?, NULL)");
		stmt.setInt(1, page_id);
		stmt.setString(2, title.getText());
		stmt.setString(3, title.getKey());
		stmt.executeUpdate();
		return true;
	}
	
	/**
	 * Add a new revision to this page.  Handles updating history, etc.
	 * @param text Text of the new revision
	 */
	public OraRevision edit(User u, String text, String comment) throws SQLException {
		if (page_id == -1)
			create();
		
		/*
		 * Insert the text row first.
		 */
		int text_id = OraWiki.getSerial(dbc, "text_text_id_seq");
		PreparedStatement stmt = dbc.prepareStatement(
				"INSERT INTO text(text_id, text_content) VALUES(?, ?)");
		stmt.setInt(1, text_id);
		stmt.setString(2, text);
		stmt.executeUpdate();
		
		stmt.close();
		
		/*
		 * Now insert the revision.
		 */
		int rev_id = OraWiki.getSerial(dbc, "revision_rev_id_seq");
		PreparedStatement revstmt = dbc.prepareStatement(
				"INSERT INTO revision(rev_id, rev_page, rev_text_id, rev_timestamp, rev_comment, rev_user) " +
				"VALUES(?, ?, ?, CURRENT_TIMESTAMP AT TIME ZONE 'UTC', ?, ?)");
		revstmt.setInt(1, rev_id);
		revstmt.setInt(2, page_id);
		revstmt.setInt(3, text_id);
		revstmt.setString(4, comment);
		revstmt.setInt(5, u.getId());
		
		revstmt.executeUpdate();
	
		revstmt.close();
		
		/*
		 * Now update page_latest.
		 */
		PreparedStatement pstmt = dbc.prepareStatement(
				"UPDATE page SET page_latest = ? WHERE page_id = ?");
		pstmt.setInt(1, rev_id);
		pstmt.setInt(2, page_id);
		pstmt.executeUpdate();
		
		return new OraRevision(dbc, rev_id);
	}
	
	/**
	 * Return the edit history for this page.
	 */
	public List<Revision> getHistory(int num) throws Exception {
		List<Revision> revs = new ArrayList<Revision>();
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_id, rev_page, rev_text_id, rev_timestamp, rev_comment, user_name " +
				"FROM revision, users " +
				"WHERE rev_page = ? AND user_id = rev_user ORDER BY rev_timestamp DESC");
		stmt.setInt(1, page_id);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		while (rs.next() && (num-- > 0)) {
			OraRevision r = new OraRevision(rs);
			revs.add(r);
		}
		
		stmt.close();
		return revs;
	}
}
