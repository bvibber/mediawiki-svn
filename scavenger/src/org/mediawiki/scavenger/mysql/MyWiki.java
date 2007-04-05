package org.mediawiki.scavenger.mysql;

import java.sql.Connection;
import java.sql.SQLException;

import org.mediawiki.scavenger.Title;
import org.mediawiki.scavenger.User;
import org.mediawiki.scavenger.Wiki;

public class MyWiki extends Wiki {
	Connection dbc;
	
	public MyWiki(Connection dbc) {
		this.dbc = dbc;
	}
	
	public void commit() throws SQLException {
		dbc.commit();
	}

	public Title getTitle(String name) {
		return new Title(dbc, name);
	}

	public User getUser(String name, boolean anon) throws SQLException {
		return new MyUser(dbc, name, anon);
	}

	public void rollback() throws SQLException {
		dbc.rollback();
	}
	
	public MyPage getPage(Title t) {
		return new MyPage(dbc, t);
	}
	
	public MyRevision getRevision(int rev_id) throws SQLException {
		return new MyRevision(dbc, rev_id);
	}
}
