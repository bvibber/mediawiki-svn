package org.wikimedia.lsearch.ranks;

import java.io.IOException;
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
	protected HashSet<String> interwiki = new HashSet<String>();
	protected IndexReader reader = null;
	protected String path;
	protected enum State { MODIFIED_TITLES, FLUSHED, MODIFIED_ARTICLES, READ };
	protected State state;
	protected Directory directory;
	
	private Links(IndexId iid){
		this.iid = iid;
		this.langCode = GlobalConfiguration.getInstance().getLanguage(iid);
	}
	
	public static Links openExisting(IndexId iid) throws IOException{
		Links links = new Links(iid);
		links.path = iid.getTempPath();
		log.info("Using index at "+links.path);
		links.writer = WikiIndexModifier.openForWrite(links.path,false);
		initWriter(links.writer);
		links.reader = IndexReader.open(links.path);
		links.nsmap = Localization.getLocalizedNamespaces(links.langCode);
		links.interwiki = Localization.getInterwiki();		
		links.state = State.FLUSHED;
		links.directory = links.writer.getDirectory();
		return links;
	}
	
	private static void initWriter(IndexWriter writer) {
		writer.setMergeFactor(20);
		writer.setMaxBufferedDocs(500);		
		writer.setUseCompoundFile(true);		
	}

	public static Links createNew(IndexId iid) throws IOException{
		Links links = new Links(iid);
		links.path = iid.getTempPath();
		log.info("Making index at "+links.path);
		links.writer = WikiIndexModifier.openForWrite(links.path,true);
		links.reader = IndexReader.open(links.path);
		links.nsmap = Localization.getLocalizedNamespaces(links.langCode);
		links.interwiki = Localization.getInterwiki();		
		links.state = State.FLUSHED;
		links.directory = links.writer.getDirectory();
		return links;
	}
	
	public static Links createNewInMemory(IndexId iid) throws IOException{
		Links links = new Links(iid);
		links.path = iid.getTempPath();
		log.info("Making index at "+links.path);
		links.writer = new IndexWriter(new RAMDirectory(),new SimpleAnalyzer(),true);
		links.reader = IndexReader.open(links.path);
		links.nsmap = Localization.getLocalizedNamespaces(links.langCode);
		links.interwiki = Localization.getInterwiki();		
		links.state = State.FLUSHED;
		links.directory = links.writer.getDirectory();
		return links;
	}
	
	/** Add more entries to namespace mapping (ns_name -> ns_index) */
	public void addToNamespaceMap(HashMap<String,Integer> map){
		for(Entry<String,Integer> e : map.entrySet()){
			nsmap.put(e.getKey().toLowerCase(),e.getValue());
		}
	}
	
	public void addToNamespaceMap(String namespace, int index){
		nsmap.put(namespace.toLowerCase(),index);
	}
	
	/** Write all changes, call after batch-adding of titles and articles 
	 * @throws IOException */
	public void flush() throws IOException{
		// close & optimize
		reader.close();
		if(writer != null){
			writer.optimize();
			writer.close();	
		}
		// reopen
		writer = new IndexWriter(directory, new SimpleAnalyzer(), false);
		initWriter(writer);
		reader = IndexReader.open(path);
		state = State.FLUSHED;
	}
	
	/**
	 * Flush, and stop using this instance for writing. 
	 * Can still read. 
	 * @throws IOException 
	 */
	public void flushForRead() throws IOException{
		// close & optimize
		reader.close();
		writer.optimize();
		writer.close();		
		// reopen
		reader = IndexReader.open(path);
		writer = null;
		state = State.READ;
	}
	
	/** Add a title to enable proper link analysis when adding articles 
	 * @throws IOException */
	public void addTitle(Title t) throws IOException{
		Document doc = new Document();
		doc.add(new Field("namespace",Integer.toString(t.getNamespace()),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("title",t.getTitle(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("title_key",t.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		writer.addDocument(doc);
		state = State.MODIFIED_TITLES;
	}
	
	/** Add links and other info from article 
	 * @throws IOException */
	public void addArticleInfo(String text, Title t) throws IOException{
		if(state == State.MODIFIED_TITLES)
			flush();
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		int namespace = t.getNamespace();
		Matcher matcher = linkPat.matcher(text);
		int ns; String title;
		boolean escaped;
		HashSet<String> pagelinks = new HashSet<String>();
		HashSet<String> linkkeys = new HashSet<String>();		
		
		Title redirect = Localization.getRedirectTitle(text,langCode);
		String redirectsTo = null;
		if(redirect != null){
			redirectsTo = findTargetLink(redirect.getNamespace(),redirect.getTitle());
		} else { 
			while(matcher.find()){
				String link = matcher.group(1);
				String anchor = matcher.group(2);
				if(anchor != null && anchor.length()>1 && anchor.substring(1).equalsIgnoreCase(title(link)))
					anchor = null; // anchor same as link text
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
					//System.out.println("Found "+link);
					linkkeys.add(target); // for outlink storage
					pagelinks.add(target+"|"); // for backlinks
					if(anchor != null && !"|".equals(anchor))
						pagelinks.add(target+anchor); // for efficient anchortext extraction
				}
			}
		}
		// index article
		StringList sl = new StringList(pagelinks);
		StringList lk = new StringList(linkkeys);
		Analyzer an = new SplitAnalyzer();
		Document doc = new Document();
		doc.add(new Field("namespace",t.getNamespaceAsString(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("title",t.getTitle(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("article_key",t.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		if(redirectsTo != null)
			doc.add(new Field("redirect",redirectsTo,Field.Store.YES,Field.Index.UN_TOKENIZED));
		else{
			doc.add(new Field("links",sl.toString(),Field.Store.NO,Field.Index.TOKENIZED));
			doc.add(new Field("links_stored",lk.toString(),Field.Store.YES,Field.Index.TOKENIZED));
		}
		
		writer.addDocument(doc,an);
		state = State.MODIFIED_ARTICLES;
	}
	public static HashSet<Character> separators = new HashSet<Character>();
	static{
		separators.add(' ');
		separators.add('\r');
		separators.add('\n');
		separators.add('\t');
		separators.add(':');
		separators.add('(');
		separators.add(')');
		separators.add('[');
		separators.add(']');
		separators.add('.');
		separators.add(',');
		separators.add(':');
		separators.add(';');
		separators.add('"');
		separators.add('+');
		separators.add('*');
		separators.add('!');
		separators.add('~');
		separators.add('$');
		separators.add('%');
		separators.add('^');
		separators.add('&');
		separators.add('_');
		separators.add('=');
		separators.add('|');
		separators.add('\\');	
	}
	
	/**
	 * Find a sentance boundaries 
	 * 
	 * @param text - raw text
	 * @param start - start index to search from
	 * @param reverse - if true, will lookup in reverse
	 * @param max - radius of search (if no boundary is found return last wordbreak)
	 * @return
	 */
	protected int findSentance(char[] text, int start, boolean reverse, int max){
		int inc = (reverse)? -1 : 1;
		int count = 0;
		int wordbreak = start;
		int i = start;
		for(;i>0 && i<text.length;i+=inc){
			char c = text[i];
			if(c == '.')
				return i;
			else if(c == '*' && ((i>1 && text[i-1]=='\n') || i==0))
				return i;
			else if(separators.contains(c))
				wordbreak = i;
			if(count >= max)
				return wordbreak; // more than max chars away, return the latest wordbreak
			count ++;
		}
		return i;
	}
	
	/** Find surrounding for a link - extract sentances, list items .... */
	protected String findContext(char[] text, int start, int end){
		// TODO: implement
		return null;
	}
	
	/** Find the target key to title (ns:title) to which the links is pointing to 
	 * @throws IOException */
	protected String findTargetLink(int ns, String title) throws IOException{
		String key;
		if(title.length() == 0)
			return null;
		// try exact match
		key = ns+":"+title;
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		// try lowercase 
		key = ns+":"+title.toLowerCase();
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		// try lowercase with first letter upper case
		if(title.length()==1) 
			key = ns+":"+title.toUpperCase();
		else
			key = ns+":"+title.substring(0,1).toUpperCase()+title.substring(1).toLowerCase();
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		// try title case
		key = ns+":"+WordUtils.capitalize(title);
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		// try upper case
		key = ns+":"+title.toUpperCase();
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		// try capitalizing at word breaks
		key = ns+":"+WordUtils.capitalize(title,new char[] {' ','-','(',')','}','{','.',',','?','!'});
		if(reader.docFreq(new Term("title_key",key)) != 0)
			return key;
		
		return null;
	}
	
	/** Get number of backlinks to this title */
	public int getNumInLinks(String key) throws IOException{
		return reader.docFreq(new Term("links",key+"|"));
	}
	
	/** Get all article titles that redirect to given title */
	public ArrayList<String> getRedirectsTo(String key) throws IOException{
		ArrayList<String> ret = new ArrayList<String>();
		TermDocs td = reader.termDocs(new Term("redirect",key));
		while(td.next()){
			ret.add(reader.document(td.doc()).get("article_key"));
		}
		return ret;
	}
	
	protected void ensureRead() throws IOException {
		if(state != State.READ)
			flushForRead();
	}

	
	/** If an article is a redirect 
	 * @throws IOException */
	public boolean isRedirect(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			if(reader.document(td.doc()).get("redirect")!=null)
				return true;
		}
		return false;
	}
	
	/** If article is redirect, get target, else null */
	public String getRedirectTarget(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			 return reader.document(td.doc()).get("redirect");
		}
		return null;
	}
	
	/** Get only anchors without frequency */
	public ArrayList<String> getAnchors(String key) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		TermEnum te = reader.terms(new Term("links",key+"|"));
		while(te.next()){
			String t = te.term().text(); 
			if(!t.startsWith(key) || !te.term().field().equals("links"))
				break;
			ret.add(t.substring(key.length()+1));			
		}
		return ret;
	}
	
	/** Get title part of the key (ns:title) */
	private String title(String key) {
		return key.substring(key.indexOf(':')+1);
	}

	/** Get anchor texts for given title 
	 * @throws IOException */
	public ArrayList<AnchorText> getAnchorText(String key) throws IOException{
		ensureRead();
		ArrayList<AnchorText> ret = new ArrayList<AnchorText>();
		TermEnum te = reader.terms(new Term("links",key+"|"));
		while(te.next()){
			if(!te.term().text().startsWith(key) || !te.term().field().equals("links"))
				break;
			ret.add(new AnchorText(te.term().text().substring(key.length()),te.docFreq()));			
		}
		return ret;
	}
	
	static public class AnchorText {
		public String text; /** ns:title **/
		public int freq;
		public AnchorText(String text, int freq) {
			this.text = text;
			this.freq = freq;
		}		
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<String> getInLinks(String key, HashMap<Integer,String> keyCache) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		TermDocs td = reader.termDocs(new Term("links",key+"|"));
		while(td.next()){
			ret.add(keyCache.get(td.doc()));
			//ret.add(reader.document(td.doc()).get("article_key"));
		}
		return ret;
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<CompactArticleLinks> getInLinks(CompactArticleLinks key, HashMap<Integer,CompactArticleLinks> keyCache) throws IOException{
		ensureRead();
		ArrayList<CompactArticleLinks> ret = new ArrayList<CompactArticleLinks>();
		TermDocs td = reader.termDocs(new Term("links",key+"|"));
		while(td.next()){
			ret.add(keyCache.get(td.doc()));
		}
		return ret;
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<String> getInLinks(String key) throws IOException{
		ensureRead();
		ArrayList<String> ret = new ArrayList<String>();
		TermDocs td = reader.termDocs(new Term("links",key+"|"));
		while(td.next()){
			ret.add(reader.document(td.doc()).get("article_key"));
		}
		return ret;
	}
	
	/** Get links from this article to other articles */
	public StringList getOutLinks(String key) throws IOException{
		ensureRead();
		TermDocs td = reader.termDocs(new Term("article_key",key));
		if(td.next()){
			return new StringList(reader.document(td.doc()).get("links_stored"));
		}
		return null;
	}
	
	public Dictionary getKeys() throws IOException{
		ensureRead();
		return new LuceneDictionary(reader,"article_key");
	}
	@Deprecated
	protected void cacheInLinks() throws IOException{
		if(state != State.FLUSHED)
			flush();
		log.info("Caching in-links");
		int count = 0;
		// docid -> key
		HashMap<Integer,String> keyCache = new HashMap<Integer,String>();
		Dictionary dict = new LuceneDictionary(reader,"article_key");
		Word w;
		// build key cache
		while((w = dict.next()) != null){
			String key = w.getWord();
			TermDocs td = reader.termDocs(new Term("article_key",key));
			if(td.next()){
				keyCache.put(td.doc(),key);
			} else
				log.error("Cannot find article for key "+key);
		}
		
		// get inlinks
		for(String key : keyCache.values()){
			ArrayList<String> in = getInLinks(key,keyCache);
			Document doc = new Document();
			doc.add(new Field("inlinks_key",key,Field.Store.YES,Field.Index.UN_TOKENIZED));
			doc.add(new Field("inlinks",new StringList(in).toString(),Field.Store.YES,Field.Index.UN_TOKENIZED));
			writer.addDocument(doc);
			count ++;
			if(count % 1000 == 0){
				System.out.println("Cached inlinks for "+count);
			}
		}
	}
	
	/** Get all article titles linking to given title (from inlinks cache) 
	 * @throws IOException */
	public Collection<String> getInLinksFromCache(String key) throws IOException{
		ensureRead();		
		TermDocs td = reader.termDocs(new Term("inlinks_key",key));
		while(td.next()){
			return new StringList(reader.document(td.doc()).get("inlinks")).toCollection();
		}
		return new ArrayList<String>();
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
}
