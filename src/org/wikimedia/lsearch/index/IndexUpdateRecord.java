/*
 * Created on Feb 11, 2007
 *
 */
package org.wikimedia.lsearch.index;

import java.io.Serializable;

import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.ReportId;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.IndexId;

/**
 * Encapsulates an update to index. Describes the actions that need to
 * be taken to properly apply the update to index, and contains the
 * bare article. 
 * 
 * 
 * @author rainman
 *
 */
public class IndexUpdateRecord implements Serializable {
	protected Article article;	
	public enum Action { ADD, UPDATE, DELETE };
	protected Action action;
	transient protected IndexId iid;
	protected String dbrole; 
	/** Add a record even if it cannot be deleted from the index? (false for split indexes) */
	protected boolean alwaysAdd;
	/** Report back if addition/deletion succeded? */
	protected boolean reportBack;
	/** Host to send report back */
	protected String reportHost;
	/** Used to identify this object in a back report */
	protected ReportId reportId;
	
	public IndexUpdateRecord(String dbrole, long pageId, Title title, String text, boolean redirect, int rank, Action role){
		// FIXME: redirect target might be wrong (but then this is deprecated...)
		article = new Article(pageId,title,text,redirect,rank,0);
		this.action = role;
		iid = IndexId.get(dbrole);
		alwaysAdd = true;
		reportBack = false;
		reportHost = "";
		this.dbrole = dbrole;
		reportId = null;
	}
	public IndexUpdateRecord(IndexId iid, Article article, Action role){
		this.article = article;
		this.action = role;
		this.iid = iid;
		alwaysAdd = true;
		reportBack = false;
		reportHost = "";
		dbrole = iid.toString();
		reportId = null;
	}	
	public IndexUpdateRecord(Article article, Action action, IndexId iid, String dbrole, boolean alwaysAdd, boolean reportBack, String reportHost, ReportId reportId) {
		this.article = article;
		this.action = action;
		this.iid = iid;
		this.dbrole = dbrole;
		this.alwaysAdd = alwaysAdd;
		this.reportBack = reportBack;
		this.reportHost = reportHost;
		this.reportId = reportId;
	}
	
	public void setAction(Action action) {
		this.action = action;
	}
	@Override
	public Object clone() {
		return new IndexUpdateRecord(article,action,iid,dbrole,alwaysAdd,reportBack,reportHost,reportId);
	}
	public ReportId getReportId() {
		return reportId;
	}
	public void setReportId(ReportId reportId) {
		this.reportId = reportId;
	}
	public boolean isAlwaysAdd() {
		return alwaysAdd;
	}
	public void setAlwaysAdd(boolean alwaysAdd) {
		this.alwaysAdd = alwaysAdd;
	}
	public boolean isReportBack() {
		return reportBack;
	}
	public void setReportBack(boolean reportBack) {
		this.reportBack = reportBack;
	}
	public String getReportHost() {
		return reportHost;
	}
	public void setReportHost(String reportHost) {
		this.reportHost = reportHost;
	}
	public IndexId getIndexId(){
		if(iid == null)
			iid = IndexId.get(dbrole);
		return iid;
	}
	
	public void setIndexId(IndexId iid){
		this.iid = iid;
		dbrole = iid.toString();
	}
	
	public String toString(){
		if(iid == null)
			iid = IndexId.get(dbrole);
		return action+" on "+iid+" to "+article;
	}
	
	/**
	 * @return true if update needs to delete the old record
	 */
	public boolean doDelete(){
		return (action == Action.UPDATE || action == Action.DELETE);
	}
	
	/**
	 * @return true if update adds new record 
	 */
	public boolean doAdd(){
		return (action == Action.ADD || action == Action.UPDATE);
	}
	
	public boolean isDelete(){
		return action == Action.DELETE;
	}
	
	/**
	 * @return Returns the article.
	 */
	public Article getArticle() {
		return article;
	}
	
	/**
	 * @return Returns the role.
	 */
	public Action getAction() {
		return action;
	}
	
	/**
	 * @return Returns the page key -- page_id (via article)
	 */
	public String getKey(){
		return article.getKey();
	}
	
	/**
	 * @return Highlight key -- ns:title
	 */
	public String getHighlightKey(){
		return article.getTitleObject().getKey();
	}
	
}
