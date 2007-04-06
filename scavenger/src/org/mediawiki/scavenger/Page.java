package org.mediawiki.scavenger;

import java.sql.SQLException;
import java.util.List;

public interface Page {
	/**
	 * @return The title of this page.
	 */
	public Title getTitle() throws SQLException;
	
		/**
		 * @return The latest version of this page.
		 */
		public Revision getLatestRevision() throws SQLException;
		
		/**
		 * @return Whether this page exists
		 */
		public boolean exists() throws SQLException;
		
		/**
		 * Create this page.  Does not create any text or revisions.
		 * @return true if the page was created, otherwise false
		 */
		public boolean create() throws SQLException;
		
		/**
		 * Add a new revision to this page.  Handles updating history, etc.
		 * @param text Text of the new revision
		 */
		public Revision edit(User u, String text, String comment) throws SQLException;
		
		/**
		 * Return the edit history for this page.
		 */
		public List<Revision> getHistory(int num) throws Exception;
}
