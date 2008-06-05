package org.wikimedia.lsearch.spell;

import java.io.IOException;
import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map.Entry;
import java.util.concurrent.ThreadPoolExecutor.AbortPolicy;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.ArticleLinks;
import org.wikimedia.lsearch.beans.Redirect;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.related.CompactArticleLinks;
import org.wikimedia.lsearch.related.CompactLinks;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.storage.ArticleAnalytics;
import org.wikimedia.lsearch.storage.LinkAnalysisStorage;
import org.wikimedia.lsearch.util.Localization;

/**
 * Make a  
 * 
 * @author rainman
 *
 */
public class CleanIndexImporter implements DumpWriter {
	static Logger log = Logger.getLogger(CleanIndexImporter.class);
	Page page;
	Revision revision;
	CleanIndexWriter writer;
	String langCode;
	Links links;

	public CleanIndexImporter(IndexId iid, String langCode) throws IOException{
		Configuration.open(); // make sure configuration is loaded
		this.writer = CleanIndexWriter.newForWrite(iid);
		this.langCode = langCode;
		this.links = Links.openStandalone(iid);
		
		//log.info("Rebuilding for namespaces: "+nsf);
	}
	public void writeRevision(Revision revision) throws IOException {
		this.revision = revision;		
	}
	public void writeStartPage(Page page) throws IOException {
		this.page = page;
	}
	public void writeEndPage() throws IOException {
		String key = page.Title.Namespace+":"+page.Title.Text;
		int references = links.getNumInLinks(key);			
		String redirectTo = links.getRedirectTarget(key);
		int redirectTargetNamespace = links.getRedirectTargetNamespace(key);
		if(redirectTo != null){
			ArrayList<String> redirectsHere = links.getRedirectsTo(key);
			references -= redirectsHere.size(); // we want raw rank, without redirects

			if(redirectTargetNamespace<0 || redirectTargetNamespace != page.Title.Namespace)
				redirectTo = null; // redirect to different namespace
		}			
		Date date = new Date(revision.Timestamp.getTimeInMillis());

		// make list of redirects
		ArrayList<Redirect> redirects = new ArrayList<Redirect>();
		ArrayList<String> anchors = new ArrayList<String>();
		for(String rk : links.getRedirectsTo(key)){
			String[] parts = rk.toString().split(":",2);
			redirects.add(new Redirect(Integer.parseInt(parts[0]),parts[1],links.getNumInLinks(rk)));
		}
		// make article
		Article article = new Article(page.Id,page.Title.Namespace,page.Title.Text,revision.Text,redirectTo,
				references,redirectTargetNamespace,0,redirects,new ArrayList<RelatedTitle>(),anchors,date);

		writer.addArticleInfo(article);
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
	
	public void closeIndex() throws IOException {
		writer.closeAndOptimize();
		links.close();
	}
	

}
