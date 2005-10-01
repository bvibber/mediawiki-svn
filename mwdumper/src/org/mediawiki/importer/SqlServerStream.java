package org.mediawiki.importer;

import java.io.IOException;
import java.sql.Connection;
import java.sql.SQLException;
import java.sql.Statement;

public class SqlServerStream implements SqlStream {
	private Connection connection;
	
	public SqlServerStream(Connection conn) {
		connection = conn; // TODO
	}
	
	public void writeComment(CharSequence sql) {
		// do nothing
	}
	
	public void writeStatement(CharSequence sql) throws IOException {
		Statement statement;
		try {
			statement = connection.createStatement();
			statement.execute(sql.toString());
		} catch (SQLException e) {
			throw new IOException(e.toString());
		}
	}
	
	public void close() throws IOException {
		try {
			connection.close();
		} catch (SQLException e) {
			throw new IOException(e.toString());
		}
	}

}
