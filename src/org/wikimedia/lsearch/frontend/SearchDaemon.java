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
import java.util.ArrayList;
import java.util.HashMap;

import org.wikimedia.lsearch.beans.ResultSet;
import org.wikimedia.lsearch.beans.SearchResults;
import org.wikimedia.lsearch.beans.SearchResults.Format;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.highlight.HighlightResult;
import org.wikimedia.lsearch.highlight.Snippet;
import org.wikimedia.lsearch.search.SearchEngine;
import org.wikimedia.lsearch.spell.SuggestQuery;
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
			// <num of hits>
			// #suggest <query> or #no suggestion
			// <score> <ns> <title> (resNum-times)
			if(res!=null && res.isSuccess()){				
				if(res.getFormat() == Format.STANDARD){
					sendHeaders(200, "OK");
					// format: 
					// <namespace> <title> (resNum-times)
					if(what.equals("prefix")){
						for(ResultSet rs : res.getResults()){
							sendResultLine(rs.namespace, rs.title);
						}
					} else{
						sendOutputLine(Integer.toString(res.getNumHits()));
						SuggestQuery sq = res.getSuggest();
						if(sq != null && sq.hasSuggestion()){
							sendOutputLine("#suggest ["+sq.getRangesSerialized()+"] "+encode(sq.getSearchterm()));
						} else 
							sendOutputLine("#no suggestion");
						if(res.getTitles() != null){
							sendOutputLine("#interwiki "+res.getTitles().size());
							for(ResultSet rs : res.getTitles()){
								sendOutputLine(rs.getScore()+" "+encode(rs.getInterwiki())+" "+rs.getNamespace()+" "+encodeTitle(rs.getTitle()));
								if(rs.getExplanation() != null)
									sendOutputLine(rs.getExplanation().toString());
								if(rs.getHighlight() != null){
									HighlightResult hr = rs.getHighlight();
									sendHighlight("title",hr.getTitle());								
									sendHighlightWithTitle("redirect",hr.getRedirect());
								}
							}
						} else
							sendOutputLine("#interwiki 0");
						sendOutputLine("#results");
						for(ResultSet rs : res.getResults()){
							sendResultLine(rs.score, rs.namespace, rs.title);
							if(rs.getContext() != null){
								for(String c : rs.getContext())
									sendOutputLine("#context "+c);
							}
							if(rs.getExplanation() != null)
								sendOutputLine(rs.getExplanation().toString());
							if(rs.getHighlight() != null){
								HighlightResult hr = rs.getHighlight();
								sendHighlight("title",hr.getTitle());
								for(Snippet sn : hr.getText())
									sendHighlight("text",sn);
								sendHighlightWithTitle("redirect",hr.getRedirect());
								sendHighlightWithFragment("section",hr.getSection());
							}
						}
					}
				} else if(res.getFormat() == Format.JSON){
					contentType = "application/json";
					sendHeaders(200, "OK");
					// json format, currently only support limited syntax for prefix queries
					// but could in principle be used for everything
					sendOutputLine("{ \"results\":[");
					ArrayList<ResultSet> results = res.getResults();
					for(int i=0;i<results.size();i++){
						ResultSet rs = results.get(i);
						String title = encode(rs.getNamespaceTextual());
						if(!title.equals(""))
							title += ":";
						title += encode(rs.getTitle());
						String comma = (i != results.size()-1)? "," : "";
						sendOutputLine("\""+title+"\""+comma);
					}
					sendOutputLine("]}");
				} else if(res.getFormat() == Format.OPENSEARCH){
					contentType = "application/json";
					charset = "utf-8";
					sendHeaders(200, "OK");
					// opensearch for firefox suggestion engine ... 
					sendOutputLine("[\""+searchterm+"\",[");
					ArrayList<ResultSet> results = res.getResults();
					for(int i=0;i<results.size();i++){
						ResultSet rs = results.get(i);
						String title = rs.getNamespaceTextual();
						if(!title.equals(""))
							title += ":";
						title += rs.getTitle();
						String comma = (i != results.size()-1)? "," : "";
						sendOutputLine("\""+title+"\""+comma);
					}
					sendOutputLine("]]");
				}
			} else{
				sendError(500, "Server error", res.getErrorMsg());
			}	
		} catch(Exception e){
			e.printStackTrace();
			sendError(500,"Server error","Error opening index.");
		}
	}
	
	
	private String makeHighlight(String type, Snippet snippet){
		if(snippet == null)
			return null;
		String s = snippet.getSplitPointsSerialized();
		String r = snippet.getRangesSerialized();
		String sx = snippet.getSuffixSerialized();
		String t = snippet.getText();
		if(s!=null && r!=null && t!=null && t.length()>0 && sx!=null)
			return "#h."+type+" ["+s+"] ["+r+"] ["+encodePlus(sx)+"] "+encodePlus(t);
		return null;
	}
	
	private void sendHighlight(String type, Snippet snippet){
		String s = makeHighlight(type,snippet);
		if(s != null)
			sendOutputLine(s);
	}
	
	private void sendHighlightWithTitle(String type, Snippet snippet){
		String s = makeHighlight(type,snippet);
		if(s != null)
			sendOutputLine(s+" "+encodeTitle(snippet.getOriginalText()));
	}
	
	private void sendHighlightWithFragment(String type, Snippet snippet){
		String s = makeHighlight(type,snippet);
		if(s != null)
			sendOutputLine(s+" "+encodeFragment(snippet.getOriginalText()));
	}
	
	/** URL-encoding */
	private String encode(String text){
		try {
			String s = URLEncoder.encode(text, "UTF-8");
			return s.replaceAll("\\+","%20");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
			return "";
		}
	}
	/** form encoding, space -> plus sign */
	private String encodePlus(String text){
		try {
			return URLEncoder.encode(text, "UTF-8");
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
			return "";
		}
	}
	/** encode titles, convert spaces into underscores */
	private String encodeTitle(String title){
		if(title.equals(""))
			return "";
		return encode(title.replaceAll(" ", "_"));
	}
	
	/** encode url fragments, i.e. stuff after # */
	private String encodeFragment(String fragment){
		String enc = encodeTitle(fragment);
		return enc.replaceAll("%3A",":").replaceAll("%",".");
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
		sendOutputLine(score + " " + namespace + " " + encodeTitle(title));
		} catch(Exception e){
			log.error("Error sending result line ("+score + " " + namespace + " " + title +"): "+e.getMessage());
		}
	}
	
	private void sendResultLine(String namespace, String title) {
		try{
			sendOutputLine(namespace + " " +	encodeTitle(title));
		} catch(Exception e){
			log.error("Error sending prefix result line (" + namespace + " " + title +"): "+e.getMessage());
		}
	}
	
}
