package org.mediawiki.scavenger.pg;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.text.DateFormat;
import java.util.Date;

import org.mediawiki.scavenger.Revision;

import com.petebevin.markdown.MarkdownProcessor;

/**
 * Represents one revision of a page.
 */
public class PgRevision implements Revision {
	int rev_id;
	int rev_page;
	int rev_text_id;
	Date rev_timestamp;
	String rev_comment;
	String rev_user_text;
	
	String text;
	Connection dbc;
	
	/**
	 * Construct a new revision from its rev_id.
	 */
	public PgRevision(Connection dbc, int id) {
		this.dbc = dbc;
		rev_id = id;
		text = null;
		rev_page = -1;
		rev_text_id = -1;
		rev_comment = null;
		rev_user_text = null;
	}
	
	/**
	 * Construct a new revision from a ResultSet.
	 */
	public PgRevision(ResultSet rs) throws SQLException {
		rev_id = rs.getInt("rev_id");
		rev_page = rs.getInt("rev_page");
		rev_text_id = rs.getInt("rev_text_id");
		rev_timestamp = rs.getTimestamp("rev_timestamp");
		rev_comment = rs.getString("rev_comment");
		rev_user_text = rs.getString("user_name");
		
		dbc = rs.getStatement().getConnection();
	}
	
	/**
	 * @return id of this revisio
	 */
	public int getId() {
		return rev_id;
	}
	
	/**
	 * Initialise ourselves from the database.
	 */
	private void loadFromDB() throws SQLException {
		if (rev_page != -1)
			return;
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_page, rev_text_id, rev_timestamp, rev_comment, user_name " +
				"FROM revision, users WHERE rev_id = ? AND rev_user = user_id");
		stmt.setInt(1, rev_id);
		stmt.execute();

		ResultSet rs = stmt.getResultSet();
		if (!rs.next()) {
			/*
			 * Shouldn't happen?
			 */
			stmt.close();
			return;
		}
		
		rev_page = rs.getInt(1);
		rev_text_id = rs.getInt(2);
		rev_timestamp = rs.getTimestamp(3);
		rev_comment = rs.getString(4);
		rev_user_text = rs.getString(5);
		stmt.close();
	}
	
	/**
	 * Return the text of this revision.
	 */
	public String getText() throws SQLException {
		if (text != null)
			return text;

		loadFromDB();
		
		if (rev_id == -1)
			return null;
		
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT text_content FROM text WHERE text_id = ?");
		stmt.setInt(1, rev_text_id);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		if (rs.next())
			text = rs.getString(1);
		stmt.close();
		return text;
	}

	/**
	 * Return the time of this edit.
	 */
	public Date getTimestamp() throws SQLException {
		loadFromDB();
		return rev_timestamp;
	}

	/**
	 * Return the time of this edit in a human-readable format.
	 */
	public String getTimestampString() throws SQLException {
		loadFromDB();
		return DateFormat.getDateTimeInstance(DateFormat.MEDIUM, DateFormat.MEDIUM)
					.format(rev_timestamp);
	}

	/**
	 * Return the comment for this edit, or an empty string if none.
	 */
	public String getComment() throws SQLException {
		loadFromDB();
		return rev_comment;
	}
	
	/**
	 * Return the username of the creator of this revision;
	 */
	public String getUsername() throws SQLException {
		loadFromDB();
		return rev_user_text;
	}
	
	/**
	 * Return the revision prior to this one.
	 */
	public PgRevision prevRevision() throws SQLException {
		loadFromDB();
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_id FROM revision WHERE rev_id < ? AND rev_page = ? " +
				"ORDER BY rev_id DESC LIMIT 1");
		stmt.setInt(1, rev_id);
		stmt.setInt(2, rev_page);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		PgRevision r = null;
		if (rs.next())
			r = new PgRevision(dbc, rs.getInt(1));
		rs.close();
		stmt.close();
		return r;
	}

	/**
	 * Return the revision following this one.
	 */
	public PgRevision nextRevision() throws SQLException {
		loadFromDB();
		PreparedStatement stmt = dbc.prepareStatement(
				"SELECT rev_id FROM revision WHERE rev_id > ? AND rev_page = ? " +
				"ORDER BY rev_id ASC LIMIT 1"); 
		stmt.setFetchSize(Integer.MIN_VALUE);
		stmt.setInt(1, rev_id);
		stmt.setInt(2, rev_page);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		PgRevision r = null;
		if (rs.next())
			r = new PgRevision(dbc, rs.getInt(1));
		rs.close();
		stmt.close();
		return r;
	}
}
