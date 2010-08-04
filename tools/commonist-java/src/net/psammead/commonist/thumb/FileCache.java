package net.psammead.commonist.thumb;

import java.io.BufferedReader;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.Iterator;
import java.util.LinkedList;
import java.util.Map;

import net.psammead.util.Logger;
import net.psammead.util.StringUtil;

/** caches drived Files indexed by their original Files */
public final class FileCache {
	private static final Logger log = new Logger(FileCache.class);
	
	private final File	list;
	private final File	directory;
	private final int	cachedFiles;
	
	private final LinkedList<File>	entryList;
	private final Map<File,File>	entryMap;

	/** a cache for derived files mapped to their originals */
	public FileCache(File list, File directory, int cachedFiles) {
		this.list			= list;
		this.directory		= directory;
		this.cachedFiles	= cachedFiles;
		
		entryList	= new LinkedList<File>();
		entryMap	= new HashMap<File,File>();
		
		directory.mkdirs();
	}
	
	/** get a cachefile or return null */
	public File get(File original) {
		final File	cached	= entryMap.get(original);
		if (cached == null)	{
			return null;
		}
		
		if (!cached.exists() || original.lastModified() > cached.lastModified()) {
			remove(original);
			return null;
		}
		
		// move to the end of the list (LRU)
		entryList.remove(original);
		entryList.add(original);
		return cached;
	}
	
	/** create a new cachefile */
	public File put(File original) {
		flush();
		
		// insert a new entry
		File	cached	= cacheFile();
		log.info("caching original: " + original);
		log.info("cached thumbnail: " + cached);
		entryList.add(original);
		entryMap.put(original, cached);
		return cached;
	}

	/** remove an entry */
	public void remove(File original) {
		final File	cached	= entryMap.get(original);
		log.debug("removing original: " + original);
		entryList.remove(original);
		entryMap.remove(original);
		if (cached != null && cached.exists()) {
			log.info("deleting cached: " +  cached);
			cached.delete();
		}
	}
	
	/** load cache metadata */
	public void load() throws IOException {
		log.info("loading");
		clear();
		if (!list.exists())	return;
		
		log.debug("reading metadata");
		BufferedReader	in	= null;
		try {
			in	= new BufferedReader(new InputStreamReader(new FileInputStream(list), "UTF-8"));
			for (;;) {
				final String	separator	= in.readLine();
				if (separator == null)	break;
				final File	original	= new File(in.readLine());
				final File	cached		= new File(in.readLine());
				entryList.add(original);
				entryMap.put(original, cached);
			}
		}
		finally {
			if (in != null) {
				try { in.close(); }
				catch (Exception e) { log.error("cannot close", e); }
			}
		}
	}
	
	/** store cache metadata */
	public void save() throws IOException {
		log.info("saving");
		cleanup();

		log.debug("writing metadata");
		Writer	out	= null;
		try {
			out	= new OutputStreamWriter(new FileOutputStream(list), "UTF-8");
			for (Iterator<File> it=entryList.iterator(); it.hasNext();) {
				final File	original	= it.next();
				final File	cached		= entryMap.get(original);
				out.write("\n");
				out.write(original.getPath());
				out.write("\n");
				out.write(cached.getPath());
				out.write("\n");
			}
		}
		finally {
			if (out != null) {
				try { out.close(); }
				catch (Exception e) { log.error("cannot close", e); }
			}
		}
	}
	
	/** clear cache metadata */
	private void clear() {
		entryList.clear();
		entryMap.clear();
	}
	
	/** remove the oldest cache entry and delete its file */
	private void flush() {
		if (entryList.isEmpty() || entryList.size() <= cachedFiles)	return;
		
		final File	oldOriginal	= entryList.removeFirst();
		final File	oldCached	= entryMap.remove(oldOriginal);
		log.debug("flushing original: " + oldOriginal);
		if (oldCached != null && oldCached.exists()) {
			log.info("deleting cached: " +  oldCached);
			oldCached.delete();
		}
	}
	
	/** 
	  * delete stale entries from the entryList and entryMap
	  * and all cachefiles not in the entryMap and  
	  */
	private void cleanup() {
		// stale entries from the entryList and entryMap
		final Collection<File> originalFiles = new ArrayList<File>(entryList);
		for (Iterator<File> it=originalFiles.iterator(); it.hasNext();) {
			final File	original = it.next();
			if (!original.exists()) {
				log.warn("original disappeared: " + original);
				entryList.remove(original);
				entryMap.remove(original);
			}
		}

		// delete all cachefiles not in the entryMap
		final Collection<File>	entries	= entryMap.values();
		final File[]				listed	= directory.listFiles();
		for (int i=0; i<listed.length; i++) {
			final File	cached	= listed[i];
			if (!entries.contains(cached)) {
				log.info("deleting cached: " + cached);
				cached.delete();
			}
		}
	}
	
	/** create a new cachefile */ 
	private File cacheFile() {
		for (;;) {
			final String	name	= StringUtil.randomString("0123456789abcdefghijklmnopqrstuvwxyz", 14);
			final File	cached	= new File(directory, name);
			if (!cached.exists())	return cached;
		}
	}
}
