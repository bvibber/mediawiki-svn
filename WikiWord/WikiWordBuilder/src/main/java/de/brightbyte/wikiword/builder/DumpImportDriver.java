package de.brightbyte.wikiword.builder;

import java.io.BufferedInputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.InterruptedIOException;
import java.net.URL;
import java.net.URLConnection;
import java.sql.SQLException;
import java.util.Iterator;
import java.util.Map;
import java.util.concurrent.TimeUnit;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.zip.GZIPInputStream;

import org.apache.commons.compress.bzip2.CBZip2InputStream;
import org.mediawiki.importer.DumpWriter;
import org.mediawiki.importer.Page;
import org.mediawiki.importer.Revision;
import org.mediawiki.importer.Siteinfo;
import org.mediawiki.importer.XmlDumpReader;

import de.brightbyte.io.IOUtil;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.job.BlockingJobQueue;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.Namespace;
import de.brightbyte.wikiword.NamespaceSet;
import de.brightbyte.wikiword.TweakSet;

/**
 * DumpImportDriver implements ImportDriver for reading content from 
 * MediaWiki XML dumps.
 */
public class DumpImportDriver implements ImportDriver {
		
	protected URL dump;
	protected InputStream in;
	protected TweakSet tweaks;
	protected LeveledOutput log;

	protected class Sink implements DumpWriter {
		
		protected WikiWordImporter importer;
		protected BlockingJobQueue executor = null;
		
		public Sink(WikiWordImporter importer, int queueCapacity) {
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
								log.error("exception while processing page `"+p.Title.toString()+"` (id="+p.Id+", rev="+r.Id+", date="+r.Timestamp.getTime()+")", e);
								error = e;
								executor.shutdownNow();
								
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
			importer.handlePage(page.Id, page.Title.Namespace, page.Title.Text, revision.Text, revision.Timestamp.getTime());
		}
		
	}
	
	public DumpImportDriver(URL dump, LeveledOutput log, TweakSet tweaks) {
		if (dump==null) throw new NullPointerException();
		this.dump= dump;
		init(log, tweaks);
	}
	
	public DumpImportDriver(InputStream in, LeveledOutput log, TweakSet tweaks) {
		if (in==null) throw new NullPointerException();
		this.in= in;
		init(log, tweaks);
	}
	
	private int importQueueCapacity = 0;
	private String externalBunzip = null;
	private String externalGunzip = null;
	
	private void init(LeveledOutput log, TweakSet tweaks) {
		if (log==null) throw new NullPointerException();
		if (tweaks==null) throw new NullPointerException();
		
		this.tweaks = tweaks;
		this.log = log;
		
		importQueueCapacity = tweaks.getTweak("dumpdriver.pageImportQueue", 8);
		externalBunzip = tweaks.getTweak("dumpdriver.externalBunzip", "/bin/bunzip2");
		externalGunzip = tweaks.getTweak("dumpdriver.externalGunzip", "/bin/gunzip");
	}
	
	public void runImport(WikiWordImporter importer) throws IOException, SQLException, InterruptedException, PersistenceException {
		importer.reset();
		
		//trackerChunk();
		
		importer.prepare();

		if (importer.getAgenda().beginTask("runImport", "readDump")) {
			DumpWriter sink = new Sink(importer, importQueueCapacity);
			
			try {
				if (in==null) in = openURL(dump);
				XmlDumpReader reader = new XmlDumpReader(in, sink);
				
				reader.readDump();
							
				importer.afterPages();
				importer.getAgenda().endTask("runImport", "readDump");
				
				in.close();
			}
			finally {
				sink.close(); //NOTE: make sure the executor queue is terminated
			}
		}
		
		if (importer.getAgenda().beginTask("runImport", "finish")) {
			importer.finish();
			importer.getAgenda().endTask("runImport", "finish");
		}
	}

	protected InputStream openURL(URL u) throws IOException {
		String p = u.getProtocol();
		
		if (p.equals("file")) {
			File f = new File(u.getPath());
			return openFile(f);
		}
		else {
			URLConnection con = u.openConnection();
			String mime = con.getContentType();
			mime = mime.replaceAll(";.*$", "");
			InputStream in = con.getInputStream();
			
			if (mime.equals("application/x-gzip")) { 
				return  new GZIPInputStream(in); //FIXME: somehow, this doesn't seem to work. or was the external gunzipper the problem? check this!
			}
			else if (mime.equals("application/x-bzip2")) { 
				validateBZ2(in);
				return new CBZip2InputStream(in);
			}
			else if (mime.equals("application/xml")) {
				return in;
			}
			
			in.close();
			throw new IOException("MIME type not suitable for a wiki dump: "+mime);
		}
	}
	
	protected InputStream openFile(File file) throws IOException {
		String f = file.getAbsolutePath();
		
		if (f.equals("-"))
			return new BufferedInputStream(System.in);
		
		InputStream in = new BufferedInputStream(new FileInputStream(file));
		if (f.endsWith(".gz")) {
			if (externalGunzip!=null) return openProc(externalGunzip, file);
			else return new GZIPInputStream(in);
		}
		else if (f.endsWith(".bz2")) {
			if (externalBunzip!=null) {
				return openProc(externalBunzip, file);
			}
			else {
				validateBZ2(in);
				return new CBZip2InputStream(in);
			}
		}
		else
			return in;
	}
	
	protected static void validateBZ2(InputStream in) throws IOException {
		int first = in.read();
		int second = in.read();
		if (first != 'B' || second != 'Z')
			throw new IOException("Didn't find BZ file signature");
	}
	
	protected static final Pattern commandParamPattern = Pattern.compile("^(.*) +([^/\\\\]+)$");
	
	public static InputStream openProc(String command, File f) throws IOException {
		String[] cmd;
		
		Matcher m = commandParamPattern.matcher(command);
		if (m.matches()) {
			String[] p = m.group(2).trim().split("\\s+");

			cmd = new String[p.length+2];
			cmd[0] = m.group(1).trim();
			System.arraycopy(p, 0, cmd, 1, p.length);
			
			cmd[cmd.length-1] = f.getAbsolutePath();
		}
		else {
			cmd = new String[] {
					command,
					f.getAbsolutePath()
			};
		}
		
		Process proc = Runtime.getRuntime().exec(cmd);
		final InputStream err = proc.getErrorStream();
		
		//HACK!
		Thread slurper = new Thread("stderr slurper for "+proc) {
			@Override
			public void run() {
				try {
					IOUtil.pump(err, System.err);
				} catch (IOException e) {
					e.printStackTrace(System.err);
				}
			}
		};
		
		slurper.start();
		
		return new BufferedInputStream(proc.getInputStream());
	}	
}
