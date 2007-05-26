package org.wikimedia.lsearch.importer;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.HashSet;

import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.beans.Rank;

/**
 * Read a HashSet of titles from dump
 * 
 * @author rainman
 *
 */
public class TitleReader  implements DumpWriter{
	Page page;
	Revision revision;
	HashMap<String,Rank> titles = new HashMap<String,Rank>();

	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		String key = page.Title.Namespace+":"+page.Title.Text.toLowerCase();
		titles.put(key,new Rank(0));		
	}
	public HashMap<String,Rank> getTitles() {
		return titles;
	}
	public void close() throws IOException {
		// nop		
	}
	public void writeEndWiki() throws IOException {
		// nop
	}
	public void writeSiteinfo(Siteinfo info) throws IOException {
		// nop
	}	
	public void writeStartWiki() throws IOException {
		// nop		
	}
}
