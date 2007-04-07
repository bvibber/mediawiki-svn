package org.mediawiki.scavenger.mysql;

import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;

import org.mediawiki.scavenger.User;

/**
 * Represents a user, either an account or anonymous;
 */
public class MyUser implements User {
	Connection dbc;
	String user_name;
	int user_id;
	boolean user_anon;
	
	public MyUser(Connection d, int id, boolean anon) throws SQLException {
		dbc = d;
		user_id = id;
		user_anon = anon;
		PreparedStatement stmt = dbc.prepareStatement(
			"SELECT user_name, user_anon FROM user WHERE user_id = ?");
		stmt.setInt(1, user_id);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		if (rs.next()) {
			user_name = rs.getString(1);
			user_anon = rs.getInt(1) == 1;
		} else
			user_id = -1;
		
		stmt.close();
	}
	
	public MyUser(Connection d, String name, boolean anon) throws SQLException {
		dbc = d;
		user_name = name;
		user_id = -1;
		user_anon = anon;
		PreparedStatement stmt = dbc.prepareStatement(
			"SELECT user_id, user_name, user_anon FROM user WHERE user_name = ?");
		stmt.setString(1, name);
		stmt.execute();
		ResultSet rs = stmt.getResultSet();
		if (rs.next()) {
			user_id = rs.getInt(1);
			user_name = rs.getString(2);
			user_anon = rs.getInt(3) == 1;
		}
		stmt.close();
	}

	public void create() throws SQLException {
		if (exists())
			return;
		
		PreparedStatement stmt = dbc.prepareStatement(
				"INSERT INTO user(user_id, user_name, user_anon) VALUES(NULL, ?, ?)");
		stmt.setString(1, user_name);
		stmt.setInt(2, user_anon ? 1 : 0);
		stmt.executeUpdate();
		ResultSet rs = stmt.getGeneratedKeys();
		if (rs.next())
			user_id = rs.getInt(1);
		
		stmt.close();
	}
	
	public boolean exists() {
		return user_id != -1;
	}
	
	public String getName() {
		return user_name;
	}
	
	public int getId() {
		return user_id;
	}
}
