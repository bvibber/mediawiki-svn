package org.mediawiki.scavenger;

import java.sql.SQLException;
import java.util.Date;

public interface Revision {
		/**
		 * @return id of this revisio
		 */
		public int getId();
		
		/**
		 * Return the text of this revision.
		 */
		public String getText() throws SQLException;

		/**
		 * Return the time of this edit.
		 */
		public Date getTimestamp() throws SQLException;

		/**
		 * Return the time of this edit in a human-readable format.
		 */
		public String getTimestampString() throws SQLException;

		/**
		 * Return the comment for this edit, or an empty string if none.
		 */
		public String getComment() throws SQLException;
		
		/**
		 * Return the username of the creator of this revision;
		 */
		public String getUsername() throws SQLException;
		
		/**
		 * Return the revision prior to this one.
		 */
		public Revision prevRevision() throws SQLException;

		/**
		 * Return the revision following this one.
		 */
		public Revision nextRevision() throws SQLException;
}
