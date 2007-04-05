package org.mediawiki.scavenger;

import java.sql.SQLException;

public interface User {
		public void create() throws SQLException;
		public boolean exists();
		public String getName();
		public int getId();
}
