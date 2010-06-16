package de.brightbyte.wikiword.processor;

import java.io.IOException;
import java.io.InputStream;
import java.io.InterruptedIOException;
import java.net.URL;
import java.sql.SQLException;
import java.util.Iterator;
import java.util.Map;
import java.util.concurrent.TimeUnit;

import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.mediawiki.importer.XmlDumpReader;

import de.brightbyte.io.LeveledOutput;
import de.brightbyte.job.BlockingJobQueue;
import de.brightbyte.util.ErrorHandler;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.RevisionInfo;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;

/**
 * DumpImportDriver implements ImportDriver for reading content from 
 * MediaWiki XML dumps.
 */
public class XmlDumpDriver implements DataSourceDriver {
		
	protected URL dump;
	protected Corpus corpus;
	protected InputStream in;
	protected TweakSet tweaks;
	protected LeveledOutput log;
	protected ErrorHandler<XmlDumpDriver, Throwable, RuntimeException> errorHandler;

	protected class Sink implements DumpWriter {
		
		protected WikiWordPageProcessor importer;
		protected BlockingJobQueue executor = null;
		
		public Sink(WikiWordPageProcessor importer, int queueCapacity) {
			this.importer = importer;

			if (queueCapacity > 0) {
				executor = new BlockingJobQueue(queueCapacity, false);
			}
		}
		
		protected Page page;
		protected Revision revision;
		protected Throwable error;

		public void close() throws IOException {
			this.page = null;
			this.revision = null;
			if (executor!=null) {
				executor.shutdownNow();
			}
		}
		
		protected void checkError() throws IOException {
			if (error==null) return;
			
			//NOTE: Different thread, don't chain exceptions. 
			//      The error should have already been logged, 
			//      we just need to stop reading the dump now. 
			throw new IOException("aborting import, error signaled from importer thread: "+error);
		}

		public void writeStartWiki() throws IOException {
			this.page = null;
			this.revision = null;
		}
		
		public void writeEndWiki() throws IOException {
			checkError();
			
			this.page = null;
			this.revision = null;
			
			if (executor!=null) {
				try {
					executor.shutdown();
					executor.awaitTermination(Long.MAX_VALUE, TimeUnit.SECONDS);
				} catch (InterruptedException e) {
					throw (IOException)new InterruptedIOException("interrupted while waiting for importer thread.").initCause(e);
				}
			}

			checkError();
		}

		public void writeSiteinfo(final Siteinfo info) throws IOException {
			checkError();
			
			if (executor==null)	{
				prepareImport(info);
			}
			else {
				executeLater(new Runnable() {
					public void run() {
						try {
							prepareImport(info);
						} catch (Throwable e) {
							log.error("exception while processing page `"+page.Title.toString()+"` (id="+page.Id+", rev="+revision.Id+", date="+revision.Timestamp.getTime()+")", e);
							error = e;
							executor.shutdownNow();
							
							if (e instanceof Error) throw (Error)e;
						} 
					}
				});
			}
		}

		public void writeStartPage(Page page) throws IOException {
			checkError();
			
			this.page = page;
		}
		
		public void writeEndPage() throws IOException {
			checkError();
			
			if ( this.revision!=null ) {
				if (executor==null)	{
					try {
						importPage(page, revision);
					} catch (PersistenceException e) {
						throw (IOException)new IOException("exception while processing page `"+page.Title.toString()+"` (id="+page.Id+", rev="+revision.Id+", date="+revision.Timestamp.getTime()+")").initCause(e);
					} catch (IOException e) {
						throw (IOException)new IOException("exception while processing page `"+page.Title.toString()+"` (id="+page.Id+", rev="+revision.Id+", date="+revision.Timestamp.getTime()+")").initCause(e);
					} catch (RuntimeException e) {
						throw (RuntimeException)new RuntimeException("exception while processing page `"+page.Title.toString()+"` (id="+page.Id+", rev="+revision.Id+", date="+revision.Timestamp.getTime()+")").initCause(e);
					}
				}
				else {
					final Page p= page;
					final Revision r = revision;
					
					//XXX: would be nice if we could avoid this overhead while skipping pages
					executeLater(new Runnable() {
						public void run() {
							try {
								importPage(p, r);
							} catch (Throwable e) {
								executor.shutdownNow();
								handleImportError("exception while processing page `"+p.Title.toString()+"` (id="+p.Id+", rev="+r.Id+", date="+r.Timestamp.getTime()+")", e);
								
								if (e instanceof Error) throw (Error)e;
							} 
						}
					});
				}
			}
			
			this.revision = null;
			this.page = null;
		}

		public void writeRevision(Revision revision) throws IOException {
			checkError();
			
			if (this.revision == null 
					|| revision.Timestamp.getTimeInMillis() 
							> this.revision.Timestamp.getTimeInMillis()) {
				this.revision = revision;
			}
		}
		
		//-------------------------------------------------------------------
		public void executeLater(Runnable task) {
			log.trace("executeLater: submitting task; queue status is "+executor.getQueue().size()+"/"+executor.getQueueCapacity());
			executor.execute(task);
		}
		
		protected void prepareImport(Siteinfo info) {
			NamespaceSet namespaces = Namespace.newEmptySet();
			
			if (info.Namespaces!=null) {
				Iterator it = info.Namespaces.orderedEntries();
				while (it.hasNext()) {
					Map.Entry e = (Map.Entry)it.next();
					namespaces.addNamespace((Integer)e.getKey(), (String)e.getValue());
				}
			}

			boolean titleCase = info.Case.equals("first-letter");

			importer.initialize(namespaces, titleCase); //XXX: not threadsafe!
			importer.setForceTitleCase(false); //NOTE: dump knows best!
		}
		
		protected void importPage(Page page, Revision revision) throws IOException, PersistenceException {
			RevisionInfo r = new RevisionInfo(corpus, page.Id, revision.Id, revision.Timestamp.getTime(), page.Title.Text, page.Title.Namespace);
			importer.handlePage(r, revision.Text);
		}
		
		protected void handleImportError(String message, Throwable e) {
			error = e;
			if (errorHandler!=null) errorHandler.handleError(XmlDumpDriver.this, message, e);
			else log.error(message, e);
		}
	}
	
	public XmlDumpDriver(Corpus corpus, URL dump, InputFileHelper inputHelper, LeveledOutput log, ErrorHandler<XmlDumpDriver, Throwable, RuntimeException> errorHandler, TweakSet tweaks) {
		if (dump==null) throw new NullPointerException();
		this.dump= dump;
		init(corpus, inputHelper, log, errorHandler, tweaks);
	}
	
	public XmlDumpDriver(Corpus corpus, InputStream in, LeveledOutput log, ErrorHandler<XmlDumpDriver, Throwable, RuntimeException> errorHandler, TweakSet tweaks) {
		if (in==null) throw new NullPointerException();
		this.in= in;
		init(corpus, null, log, errorHandler, tweaks);
	}
	
	private int importQueueCapacity = 0;
	private InputFileHelper inputHelper;
	
	private void init(Corpus corpus, InputFileHelper inputHelper, LeveledOutput log, ErrorHandler<XmlDumpDriver, Throwable, RuntimeException> errorHandler, TweakSet tweaks) {
		if (log==null) throw new NullPointerException();
		if (tweaks==null) throw new NullPointerException();
		if (inputHelper==null && in==null) throw new NullPointerException();
		
		this.tweaks = tweaks;
		this.log = log;
		this.inputHelper = inputHelper;
		this.errorHandler = errorHandler;
		this.corpus = corpus;
		
		importQueueCapacity = tweaks.getTweak("dumpdriver.pageImportQueue", 8);
	}
	
	public void run(WikiWordPageProcessor importer) throws IOException, SQLException, InterruptedException, PersistenceException {
			DumpWriter sink = new Sink(importer, importQueueCapacity);
			
			try {
				if (in==null) in = inputHelper.openURL(dump);
				XmlDumpReader reader = new XmlDumpReader(in, sink);
				
				importer.beforePages();
				
				reader.readDump();
							
				importer.afterPages();
				
				in.close();
			}
			finally {
				sink.close(); //NOTE: make sure the executor queue is terminated
			}
	}
		
}
