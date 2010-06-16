package de.brightbyte.wikiword.processor;

import java.io.IOException;
import java.util.Date;

import de.brightbyte.application.Arguments;
import de.brightbyte.io.Output;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.RevisionInfo;

/**
 * A WikiWordImporter receives data from a ImportDriver; it will generally analyze the data, 
 * and then store the results. The general life-cycle of a  WikiWordImporter is:
 * <ol>
 * <li>&lt;init&gt;()<li>
 * <li>configure(Arguments) - apply application wide configuration options, generally from command line switches<li>
 * <li>reset() - drop all state</li>
 * <li>prepare() - prepare for the next import run</li>
 * <li>initialize(...) - apply initial option from imported data (such as defined namespaces)</li>
 * <li>handlePage(...) - called with the data for each page</li>
 * <li>afterPages() - terminate page import, especially, close all "open" state from page import</li>
 * <li>finish() - finish present import run; usually calculates statistics, etc</li>
 * <li><i>possibly back to step 3</i></li>
 * </ol>
 * A WikiWordImporter is generally stateful, and not thread-safe.
 * 
 * @author daniel
 */
public interface WikiWordPageProcessor {

	/** called before a new inport run. Must reset all internal state, including trackers. **/
	public void reset();

	/** called just before the first call to handlePage, used to provide meta-info about the 
	 * data-set that is being imported.
	 * 
	 * @param namespaces the namespaces defined for the data-set that is being imported
	 * @param titleCase true iff title-case rules should be applied to page names
	 **/
	public void initialize(NamespaceSet namespaces, boolean titleCase);

	/**
	 * called for each page being imported - this is the core method of this interface. It is expected to
	 * do somethign meaningful with the data it is given, usually to analyze it and then store the result
	 * somewhere.
	 * 
	 * @param revision revision info
	 * @param text the page's full wiki-text
	 * @throws IOException 
	 * @throws PersistenceException
	 */
	public void handlePage(RevisionInfo revision, String text) throws IOException, PersistenceException;

	/**
	 * called to prepare for a new improt run, after reset, but before initialize.
	 * @throws PersistenceException
	 */
	public void prepare() throws PersistenceException;

	//public boolean shouldRunAnalyze() throws PersistenceException;

	/**
	 * called before the first call to handlePage, but after prepare; should create/initialize any
	 * resources needed for importing individual pages. 
	 * @throws PersistenceException 
	 */
	public void beforePages() throws PersistenceException;

	/**
	 * called after the last call to handlePage, but before finish; should flush/close/finish all
	 * resources needed for importing individual pages. 
	 * @throws PersistenceException 
	 */
	public void afterPages() throws PersistenceException;

	/**
	 * called to finalize the current import run. can be used to calculate additional statistics and 
	 * further analyze the imported data.
	 * @throws PersistenceException
	 */
	public void finish() throws PersistenceException;

	/**
	 * called once after the WikiWordImporter has been created, should initialize permanent options
	 * from command line switches.
	 * @throws Exception 
	 */
	public void configure(Arguments args) throws Exception;

	/**
	 * Tells the WikiWordImporter to skip all pages up to the given title.
	 * @param title
	 */
	public void setSkipTo(String title);

	/**
	 * Tells the WikiWordImporter to skip all pages up to the given id (hte id being the external page id, not the internal resource id).
	 * @param title
	 */
	public void setSkipToId(int id);

	/**
	 * Tells the WikiWordImporter to skip the given number of pages
	 * @param count
	 */
	public void setSkip(int count);


	/**
	 * sets the log output to be used by the inporter  
	 * @param logOutput
	 */
	public void setLogOutput(Output logOutput);
	
	/**
	 * set to true if the importer should apply its own case normalization rules to the title supplied.
	 * should be set to true when importing from a "dirty" source, and set to false if inporting from a "clean"
	 * source such as a MediaWiki dump. If set to true when importing from a dump, over-normaization
	 * may occur, resulting in name clashes. 
	 */
	public void setForceTitleCase(boolean forceTitleCase);

}
