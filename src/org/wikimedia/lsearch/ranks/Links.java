package org.wikimedia.lsearch.ranks;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.ObjectInputStream;
import java.io.ObjectOutputStream;
import java.io.StringWriter;
import java.util.ArrayList;
import java.util.Collection;
import java.util.HashMap;
import java.util.HashSet;
import java.util.Map.Entry;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import org.apache.commons.lang.WordUtils;
import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.SimpleAnalyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.document.FieldSelector;
import org.apache.lucene.document.SetBasedFieldSelector;
import org.apache.lucene.index.CorruptIndexException;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;
import org.apache.lucene.store.Directory;
import org.apache.lucene.store.RAMDirectory;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.related.CompactArticleLinks;
import org.wikimedia.lsearch.search.NamespaceFilter;
import org.wikimedia.lsearch.spell.api.Dictionary;
import org.wikimedia.lsearch.spell.api.LuceneDictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.util.Localization;

public class Links {
	static Logger log = Logger.getLogger(Links.class);
	protected IndexId iid;
	protected String langCode;
	protected IndexWriter writer = null;
	protected HashMap<String,Integer> nsmap = null;
	protected HashSet<String> interwiki;
	protected HashSet<String> categoryLocalized;
	protected HashSet<String> imageLocalized;
	protected IndexReader reader = null;
	protected String path;
	protected enum State { FLUSHED, WRITE, MODIFIED, READ };
	protected State state;
	protected Directory directory = null;
	protected NamespaceFilter nsf; // default search
	protected ObjectCache cache;
	//protected ObjectCache refCache;
	protected FieldSelector keyOnly,redirectOnly,contextOnly,linksOnly;
	
	private Links(IndexId iid, String path, IndexWriter writer) throws CorruptIndexException, IOException{
		this.writer = writer;
		this.path = path;
		this.iid = iid;
		GlobalConfiguration global = GlobalConfiguration.getInstance(); 
		this.langCode = global.getLanguage(iid);
		String dbname = iid.getDBname();
		nsmap = Localization.getLocalizedNamespaces(langCode,dbname);
		interwiki = Localization.getInterwiki();
		categoryLocalized = Localization.getLocalizedCategory(langCode,dbname);
		imageLocalized = Localization.getLocalizedImage(langCode,dbname);
		state = State.FLUSHED;
		initWriter(writer);
		//reader = IndexReader.open(path);
		nsf = global.getDefaultNamespace(iid);
		cache = new ObjectCache(10000);
		// init cache manager
		/*CacheManager manager = CacheManager.create();
		cache = new Cache("links", 5000, false, false, 5, 2);
		manager.addCache(cache); */		
		keyOnly = makeSelector("article_key");
		redirectOnly = makeSelector("redirect");
		contextOnly = makeSelector("context");
		linksOnly = makeSelector("links");
	}
	
	protected FieldSelector makeSelector(String field){
		HashSet<String> onlySet = new HashSet<String>();		
		onlySet.add(field);
		return new SetBasedFieldSelector(onlySet, new HashSet<String>());
	}
	
	private void initWriter(IndexWriter writer) {
		if(writer != null){
			writer.setMergeFactor(20);
			writer.setMaxBufferedDocs(500);		
			writer.setUseCompoundFile(true);
			if(directory == null)
				directory = writer.getDirectory();
		}
	}
	
	/** Open the index path for updates */
	public static Links openForModification(IndexId iid) throws IOException{
		iid = iid.getLinks();
		String path = iid.getIndexPath();
		log.info("Using index at "+path);
		IndexWriter writer = WikiIndexModifier.openForWrite(path,false);
		return new Links(iid,path,writer);		
	}
	
	/** Open index at path for reading */
	public static Links openForRead(IndexId iid, String path) throws IOException {
		iid = iid.getLinks();
		log.info("Opening for read "+path);
		return new Links(iid,path,null);
	}
		
	/** Create new in the import path */
	public static Links createNew(IndexId iid) throws IOException{
		iid = iid.getLinks();
		String path = iid.getImportPath();
		log.info("Making index at "+path);
		IndexWriter writer = WikiIndexModifier.openForWrite(path,true);
		Links links = new Links(iid,path,writer);		
		return links;
	}
	
	/** Create new index in memory (RAMDirectory) */
	public static Links createNewInMemory(IndexId iid) throws IOException{
		iid = iid.getLinks();
		log.info("Making index in memory");
		IndexWriter writer = new IndexWriter(new RAMDirectory(),new SimpleAnalyzer(),true);
		Links links = new Links(iid,null,writer);		
		return links;
	}
	
	/** Add more entries to namespace mapping (ns_name -> ns_index) */
	public void addToNamespaceMap(HashMap<String,Integer> map){
		for(Entry<String,Integer> e : map.entrySet()){
			nsmap.put(e.getKey().toLowerCase(),e.getValue());
		}
	}
	
	/** Add a custom namespace mapping */
	public void addToNamespaceMap(String namespace, int index){
		nsmap.put(namespace.toLowerCase(),index);
	}
	
	/** Write all changes, optimize/close everything
	 * @throws IOException */
	public void flush() throws IOException{
		// close & optimize
		if(reader != null)
			reader.close();
		if(writer != null){
			writer.optimize();
			writer.close();	
		}
		state = State.FLUSHED;
	}
	
	/**
	 * Flush, and stop using this instance for writing. 
	 * Can still read. 
	 * @throws IOException 
	 */
	protected void flushForRead() throws IOException{		
		// close & optimize
		if(reader != null)
			reader.close();
		if(writer != null){
			writer.optimize();
			writer.close();	
		}
		log.debug("Opening index reader");
		// reopen
		reader = IndexReader.open(path);
		writer = null;
		state = State.READ;
	}
	
	/** Open the writer, and close the reader (if any) */
	protected void openForWrite() throws IOException{
		if(reader != null)
			reader.close();
		if(writer == null){
			if(directory == null)
				throw new RuntimeException("Opened for read, but trying to write");
			writer = new IndexWriter(directory,new SimpleAnalyzer(),false);
			initWriter(writer);
			reader = null;
			state = State.WRITE;
		}
	}
	
	protected void ensureRead() throws IOException {
		if(state != State.READ)
			flushForRead();
	}
	
	protected void ensureWrite() throws IOException {
		if(writer == null)
			openForWrite();
	}
	
	/** Modify existing article links info */
	public void modifyArticleInfo(String text, Title t) throws IOException{
		ensureWrite();
		writer.deleteDocuments(new Term("article_key",t.getKey()));
		addArticleInfo(text,t);
	}
	
	/** Add links and other info from article 
	 * @throws IOException */
	public void addArticleInfo(String text, Title t) throws IOException{
		ensureWrite();
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		int namespace = t.getNamespace();
		Matcher matcher = linkPat.matcher(text);
		int ns; String title;
		boolean escaped;

		HashSet<String> pagelinks = new HashSet<String>();
		// article link -> contexts
		HashMap<String,ArrayList<String>> contextMap = new HashMap<String,ArrayList<String>>();
		
		// use context only for namespace in default search
		boolean useContext = nsf.contains(t.getNamespace());
		
		ContextParser cp = new ContextParser(text,imageLocalized,categoryLocalized,interwiki);
		
		Title redirect = Localization.getRedirectTitle(text,langCode);
		String redirectsTo = null;
		if(redirect != null){
			redirectsTo = findTargetLink(redirect.getNamespace(),redirect.getTitle());
		} else { 
			while(matcher.find()){
				String link = matcher.group(1);
				ContextParser.Context context = useContext? cp.getNext(matcher.start(1)) : null;

				int fragment = link.lastIndexOf('#');
				if(fragment != -1)
					link = link.substring(0,fragment);
				//System.out.println("Got link "+link+anchor);
				if(link.startsWith(":")){
					escaped = true;
					link = link.substring(1);
				} else escaped = false;
				ns = 0; 
				title = link;			
				// check for ns:title syntax
				String[] parts = link.split(":",2);
				if(parts.length == 2 && parts[0].length() > 1){
					Integer inx = nsmap.get(parts[0].toLowerCase());
					if(!escaped && (parts[0].equalsIgnoreCase("category") || (inx!=null && inx==14)))
						continue; // categories, ignore
					if(inx!=null && inx < 0) 
						continue; // special pages, ignore
					if(inx != null){
						ns = inx;
						title = parts[1];
					}

					// ignore interwiki links
					if(interwiki.contains(parts[0]))
						continue;
				}
				if(ns == 0 && namespace!=0)
					continue; // skip links from other namespaces into the main namespace
				String target = findTargetLink(ns,title);				
				if(target != null){
					int targetNs = Integer.parseInt(target.substring(0,target.indexOf(':')));					
					pagelinks.add(target); // for outlink storage
					// register context of this link
					if(context != null && nsf.contains(targetNs)){
						ArrayList<String> ct = contextMap.get(target); 
						if(ct==null){
							ct = new ArrayList<String>();
							contextMap.put(target,ct);
						}
						ct.add(context.get(text));
					}
				}
			}
		}
		// index article
		StringList lk = new StringList(pagelinks);
		Analyzer an = new SplitAnalyzer();
		Document doc = new Document();
		doc.add(new Field("article_key",t.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		if(redirectsTo != null)
			doc.add(new Field("redirect",redirectsTo+"|"+t.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		else{
			doc.add(new Field("links",lk.toString(),Field.Store.COMPRESS,Field.Index.TOKENIZED));
		}
		if(contextMap.size() != 0){
			/*for(Entry<String,ArrayList<String>> e : contextMap.entrySet()){
				Document con = new Document();
				con.add(new Field("context_key",e.getKey()+"|"+t.getKey(),Field.Store.NO,Field.Index.UN_TOKENIZED));
				con.add(new Field("context",new StringList(e.getValue()).toString(),Field.Store.COMPRESS,Field.Index.NO));
				writer.addDocument(con,an);
			}*/
			// serialize the java object (contextMap) into context field
			//ByteArrayOutputStream ba = new ByteArrayOutputStream();
			//ObjectOutputStream ob = new ObjectOutputStream(ba);
			//ob.writeObject(contextMap);
			//doc.add(new Field("context",ba.toByteArray(),Field.Store.COMPRESS)); 
			doc.add(new Field("context",new StringMap(contextMap).serialize(),Field.Store.COMPRESS));
		}
		
		writer.addDocument(doc,an);
		state = State.MODIFIED;
	}
	
	/** Find the target key to title (ns:title) to which the links is pointing to 
	 * @throws IOException */
	protected String findTargetLink(int ns, String title) throws IOException{
		String key;
		if(title.length() == 0)
			return null;
		
		// first letter uppercase
		if(title.length()==1) 
			key = ns+":"+title.toUpperCase();
		else
			key = ns+":"+title.substring(0,1).toUpperCase()+title.substring(1);
		return key; // index everything, even if the target article doesn't exist
	}
	
	/** Get number of backlinks to this title */
	public int getNumInLinks(String key) throws IOException{
		ensureRead();
		/*String cacheKey = "getNumInLinks:"+key;
		Object ref = refCache.get(cacheKey);
		if(ref != null)
			return (Integer) ref;
		else{ */
			int r = reader.docFreq(new Term("links",key));
			//refCache.put(cacheKey,r);
			return r; 
		//}
	}
	
	@Deprecated
	/** Get all article titles that redirect to given title */
	public ArrayList<String> getRedirectsToOld(String key) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		TermDocs td = reader.termDocs(new Term("redirect",key));
		while(td.next()){
			ret.add(reader.document(td.doc(),keyOnly).get("article_key"));
		}
		return ret;
	}
	
	/** Get all article titles that redirect to given title */
	public ArrayList<String> getRedirectsTo(String key) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		String prefix = key+"|";
		TermEnum te = reader.terms(new Term("redirect",prefix));
		while(te.next()){
			String t = te.term().text();
			if(t.startsWith(prefix)){
				ret.add(t.substring(t.indexOf('|')+1));
			} else
				break;
		}
		return ret;
	}
	
	/** If an article is a redirect 
	 * @throws IOException */
	public boolean isRedirect(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			if(reader.document(td.doc(),redirectOnly).get("redirect")!=null)
				return true;
		}
		return false;
	}

	@Deprecated
	/** If article is redirect, get target, else null */
	public String getRedirectTargetOld(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			 return reader.document(td.doc(),redirectOnly).get("redirect");
		}
		return null;
	}
	
	/** If article is redirect, get target, else null */
	public String getRedirectTarget(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			String t = reader.document(td.doc(),redirectOnly).get("redirect");
			return t.substring(t.indexOf('|')+1);
		}
		return null;
	}

	
	/** Return the namespace of the redirect taget (if any) */
	public int getRedirectTargetNamespace(String key) throws IOException{
		ensureRead();
		String t = getRedirectTarget(key);
		if(t != null){
			return Integer.parseInt(t.substring(t.indexOf('|')+1,t.indexOf(':',t.indexOf('|'))));
		}
		return 0;
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<CompactArticleLinks> getInLinks(CompactArticleLinks key, HashMap<Integer,CompactArticleLinks> keyCache) throws IOException{
		ensureRead();
		ArrayList<CompactArticleLinks> ret = new ArrayList<CompactArticleLinks>();
		TermDocs td = reader.termDocs(new Term("links",key.toString()));
		while(td.next()){
			CompactArticleLinks cs = keyCache.get(td.doc());
			if(cs != null)
				ret.add(cs);
		}
		return ret;
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<String> getInLinks(String key) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		TermDocs td = reader.termDocs(new Term("links",key));
		while(td.next()){
			ret.add(reader.document(td.doc(),keyOnly).get("article_key"));
		}
		return ret;
	}
	
	/** Get links from this article to other articles */
	public StringList getOutLinks(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			return new StringList(reader.document(td.doc(),linksOnly).get("links"));
		}
		return null;
	}
	
	/** Get all contexts in which article <i>to<i/> is linked from <i>from</i>. 
	 *  Will return null if there is no context, or link is invalid.
	 * @throws ClassNotFoundException */
	@SuppressWarnings("unchecked")
	public ArrayList<String> getContext(String from, String to) throws IOException {
		ensureRead();
		String cacheKey = "getContext:"+from;
		//Element fromCache = cache.get(cacheKey);
		Object fromCache = cache.get(cacheKey);
		if(fromCache != null){
			//HashMap<String, ArrayList<String>> map = (HashMap<String, ArrayList<String>>) fromCache.getObjectValue();
			//HashMap<String, ArrayList<String>> map = (HashMap<String, ArrayList<String>>) fromCache;
			StringMap map = (StringMap) fromCache;
			return map.get(to);
		}
		TermDocs td = reader.termDocs(new Term("article_key",from));
		if(td.next()){
			byte[] serialized = reader.document(td.doc(),contextOnly).getBinaryValue("context");
			if(serialized == null)
				return null;
			StringMap map = new StringMap(serialized);
			try {				
				//ObjectInputStream in = new ObjectInputStream(new ByteArrayInputStream(serialized));						
				//HashMap<String, ArrayList<String>> map;
				//map = (HashMap<String, ArrayList<String>>) in.readObject();				
				// cache it !
				//cache.put(new Element(cacheKey,map));
				if(from.equals("0:1910") && to.equals("0:April")){
					int b =0;
					b++;
				}
				cache.put(cacheKey,map);
				return map.get(to);
			/* } catch (ClassNotFoundException e) {
				log.error("For getContext("+from+","+to+") got class not found exception: "+e.getMessage());
				e.printStackTrace(); // shouldn't happen! */
			} catch(Exception e){
				e.printStackTrace();
			}
			
		}
		
		return null;
	}
	
	/** Get all contexts in which article <i>to<i/> is linked from <i>from</i>. 
	 *  Will return null if there is no context, or link is invalid.
	 * @throws ClassNotFoundException */
	@SuppressWarnings("unchecked")
	public Collection<String> getContextOld(String from, String to) throws IOException {
		ensureRead();
		
		TermDocs td = reader.termDocs(new Term("context_key",to+"|"+from));
		if(td.next()){
			return new StringList(reader.document(td.doc()).get("context")).toCollection();					
		}
		
		return null;
	}
	
	/** Get a dictionary of all article keys (ns:title) in this index */
	public Dictionary getKeys() throws IOException{
		ensureRead();
		return new LuceneDictionary(reader,"article_key");
	}
	
	public Integer getDocId(String key) throws IOException {
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			return td.doc();
		}
		return null;
	}

	/** Close everything */
	public void close() throws IOException {
		if(writer != null)
			writer.close();
		if(reader != null)
			reader.close();
		if(directory != null)
			directory.close();		
	}

	public ObjectCache getCache() {
		return cache;
	}

	/*public ObjectCache getRefCache() {
		return refCache;
	} */
	
	
	
	
}
