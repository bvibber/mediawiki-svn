/*
 * Created on Jan 16, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import java.util.HashMap;

import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;

/**
 * RPC frontend to {@link IndexDaemon} using Apache XMLRPC package.
 * 
 * @author Brion Vibber
 *
 */
public class RPCIndexDaemon {
	private static IndexDaemon ud = null;

	public RPCIndexDaemon() {
		if(ud == null)
			ud = new IndexDaemon();
	}

	public boolean updatePage(String databaseName, HashMap titleMap, String text, String isRedirect) {
		Title title = new Title(((Integer)titleMap.get("namespace")).intValue(),(String) titleMap.get("title"));
		ud.updatePage(databaseName,title,isRedirect,text);
		return true;
	}


	public boolean deletePage(String databaseName, HashMap titleMap) {
		Title title = new Title(((Integer)titleMap.get("namespace")).intValue(),(String) titleMap.get("title"));
		ud.deletePage(databaseName,title);
		return true;
	}

	public String getStatus() {
		return ud.getStatus();
	}

	public boolean stop() {
		ud.stop();
		return true;
	}

	public boolean start() {
		ud.start();
		return true;
	}

	public boolean flushAll() {
		ud.flushAll();
		return true;
	}

	public boolean quit() {
		ud.quit();
		return true;
	}

}
