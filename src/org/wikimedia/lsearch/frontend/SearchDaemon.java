/*
 * Created on Jan 23, 2007
 *
 */
package org.wikimedia.lsearch.frontend;

import java.io.IOException;
import java.io.UnsupportedEncodingException;
import java.net.Socket;
import java.net.URLEncoder;
import java.text.MessageFormat;
import java.util.HashMap;

import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.SearchEngine;
import org.wikimedia.lsearch.util.QueryStringMap;

/**
 * HTTP frontend for searcher.  
 * 
 * @author rainman
 *
 */
public class SearchDaemon extends HttpHandler {
	/** The search term after urlencoding */
	String searchterm;
	/** What operation we're performing */
	String what;
	/** Client-supplied database we should operate on */
	String dbname;

	public SearchDaemon(Socket sock) {
		super(sock);
	}
	
	
	protected void processRequest() {
		String path = uri.getPath();
		if (path.equals("/robots.txt")) {
			robotsTxt();
			return;
		}
		if (path.equals("/stats")) {
			showStats();
			return;
		}
		
		String[] paths = path.split( "/", 4);
		if (paths.length != 4) {
			sendError(404, "Not found", "Not a recognized search path.");
			log.warn("Unknown request " + path);
			return;
		}
		what = paths[1];
		dbname = paths[2];
		searchterm = paths[3];
		
		log.info(MessageFormat.format("query:{0} what:{1} dbname:{2} term:{3}",
			new Object[] {rawUri, what, dbname, searchterm}));	
		try{
			SearchEngine engine = new SearchEngine();
			HashMap query = new QueryStringMap(uri);
			SearchResults res = engine.search(IndexId.get(dbname),what,searchterm,query);
			contentType = "text/plain";
			// format: 
			// <namespace> <title> (resNum-times)
			if(what.equals("prefix")){
				sendHeaders(200, "OK");
				for(ResultSet rs : res.getResults()){
					sendResultLine(rs.namespace, rs.title);
				}
			}
			// format:
			// <num of hits>
			// #suggest <query> or #no suggestion
			// <score> <ns> <title> (resNum-times)
			else if(res!=null && res.isSuccess()){
				sendHeaders(200, "OK");
				sendOutputLine(Integer.toString(res.getNumHits()));
				if(res.getSuggest() != null)
					sendOutputLine("#suggest "+res.getSuggest());
				else 
					sendOutputLine("#no suggestion");
				for(ResultSet rs : res.getResults()){
					sendResultLine(rs.score, rs.namespace, rs.title);
					if(rs.getContext() != null){
						for(String c : rs.getContext())
							sendOutputLine("#context "+c);
					}
					if(rs.getExplanation() != null)
						sendOutputLine(rs.getExplanation().toString());
				}
			} else{
				sendError(500, "Server error", res.getErrorMsg());
			}	
		} catch(Exception e){
			e.printStackTrace();
			sendError(500,"Server error","Error opening index.");
		}
		
	}
	
	private void robotsTxt() {
		contentType = "text/plain";
		sendHeaders(200, "OK");
		sendOutputLine("# This is a search daemon. Don't spider it.");
		sendOutputLine("User-Agent: *");
		sendOutputLine("Disallow: /");
	}
	
	private void showStats() {
		contentType = "text/plain";
		sendHeaders(200, "OK");
		sendOutputLine(SearchServer.stats.summarize());
	}
	
	// never use keepalive
	public boolean isKeepAlive(){
		return false;
	}
	
	/**
	 * @param score
	 * @param namespace
	 * @param title
	 * @throws IOException
	 * @throws UnsupportedEncodingException
	 */
	private void sendResultLine(double score, String namespace, String title) {
		try{
		sendOutputLine(score + " " + namespace + " " +
			URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8"));
		} catch(Exception e){
			log.error("Error sending result line ("+score + " " + namespace + " " + title +"): "+e.getMessage());
		}
	}
	
	private void sendResultLine(String namespace, String title) {
		try{
			sendOutputLine(namespace + " " +	URLEncoder.encode(title.replaceAll(" ", "_"), "UTF-8"));
		} catch(Exception e){
			log.error("Error sending prefix result line (" + namespace + " " + title +"): "+e.getMessage());
		}
	}
	
}
