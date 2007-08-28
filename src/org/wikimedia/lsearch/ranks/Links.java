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
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.document.Document;
import org.apache.lucene.document.Field;
import org.apache.lucene.index.IndexReader;
import org.apache.lucene.index.IndexWriter;
import org.apache.lucene.index.Term;
import org.apache.lucene.index.TermDocs;
import org.apache.lucene.index.TermEnum;
import org.wikimedia.lsearch.analyzers.SplitAnalyzer;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.beans.Title;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.index.WikiIndexModifier;
import org.wikimedia.lsearch.util.Localization;

public class Links {
	protected IndexId iid;
	protected String langCode;
	protected IndexWriter writer = null;
	protected HashMap<String,Integer> nsmap = new HashMap<String,Integer>();
	protected HashSet<String> interwiki = new HashSet<String>();
	protected IndexReader reader = null;
	protected String path;
	
	public static final char DELIMITER = '\0';
	
	private Links(IndexId iid){
		this.iid = iid;
		this.langCode = GlobalConfiguration.getInstance().getLanguage(iid);
	}
	
	public static Links createNew(IndexId iid) throws IOException{
		Links links = new Links(iid);
		links.path = iid.getTempPath();
		links.writer = WikiIndexModifier.openForWrite(links.path,true);
		links.reader = IndexReader.open(links.path);
		links.nsmap = Localization.getLocalizedNamespaces(links.langCode);
		links.interwiki = Localization.getInterwiki();		
		return links;
	}
	
	/** Add more entries to namespace mapping (ns_name -> ns_index) */
	public void addToNamespaceMap(HashMap<String,Integer> map){
		for(Entry<String,Integer> e : map.entrySet()){
			nsmap.put(e.getKey().toLowerCase(),e.getValue());
		}
	}
	
	/** Write all changes, call after batch-adding of titles and articles 
	 * @throws IOException */
	public void flush() throws IOException{
		// close & optimize
		reader.close();
		writer.optimize();
		writer.close();		
		// reopen
		writer = WikiIndexModifier.openForWrite(path,false);
		reader = IndexReader.open(path);		
	}
	
	/** Add a title to enable proper link analysis when adding articles 
	 * @throws IOException */
	public void addTitle(Title t) throws IOException{
		Document doc = new Document();
		doc.add(new Field("namespace",Integer.toString(t.getNamespace()),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("title",t.getTitle(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("key",t.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		writer.addDocument(doc);		
	}
	
	/** Add links and other info from article 
	 * @throws IOException */
	public void addArticleInfo(Article a) throws IOException{
		Pattern linkPat = Pattern.compile("\\[\\[(.*?)(\\|(.*?))?\\]\\]");
		String text = a.getContents();
		int namespace = Integer.parseInt(a.getNamespace());
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
				int fragment = link.lastIndexOf('#');
				if(fragment != -1)
					link = link.substring(0,fragment);
				System.out.println("Got link "+link+"|"+anchor);
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
					linkkeys.add(target); // for outlink storage
					pagelinks.add(target+"|"); // for backlinks
					if(anchor != null && !"".equals(anchor))
						pagelinks.add(target+"|"+anchor); // for efficient anchortext extraction
				}
			}
		}
		// index article		
		Analyzer an = new SplitAnalyzer(DELIMITER);
		Document doc = new Document();
		doc.add(new Field("namespace",a.getNamespace(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("title",a.getTitle(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		doc.add(new Field("article_key",a.getKey(),Field.Store.YES,Field.Index.UN_TOKENIZED));
		if(redirectsTo != null)
			doc.add(new Field("redirect",redirectsTo,Field.Store.YES,Field.Index.UN_TOKENIZED));
		else
			doc.add(new Field("links",join(pagelinks,DELIMITER),Field.Store.NO,Field.Index.TOKENIZED));
		doc.add(new Field("links_stored",join(linkkeys,DELIMITER),Field.Store.YES,Field.Index.NO));
		
		writer.addDocument(doc,an);
	}
	
	protected String join(Collection<String> strs, char join){
		StringBuilder sb = new StringBuilder();
		boolean first = true;
		for(String s : strs){
			if(!first)
				sb.append(join);
			sb.append(s);
			first = false;
		}
		return sb.toString();
	}
	
	/** Find the target key to title (ns:title) to which the links is pointing to 
	 * @throws IOException */
	protected String findTargetLink(int ns, String title) throws IOException{
		String key;
		if(title.length() == 0)
			return null;
		// try exact match
		key = ns+":"+title;
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		// try lowercase 
		key = ns+":"+title.toLowerCase();
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		// try lowercase with first letter upper case
		if(title.length()==1) 
			key = ns+":"+title.toUpperCase();
		else
			key = ns+":"+title.substring(0,1).toUpperCase()+title.substring(1).toLowerCase();
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		// try title case
		key = ns+":"+WordUtils.capitalize(title);
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		// try upper case
		key = ns+":"+title.toUpperCase();
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		// try capitalizing at word breaks
		key = ns+":"+WordUtils.capitalize(title,new char[] {' ','-','(',')','}','{','.',',','?','!'});
		if(reader.docFreq(new Term("key",key)) != 0)
			return key;
		
		return null;
	}
	
	/** Get number of backlinks to this title */
	public int getNumInLinks(Title t) throws IOException{
		return reader.docFreq(new Term("links",t.getKey()+"|"));
	}
	
	/** Get all article titles that redirect to given title */
	public ArrayList<Title> getRedirectsTo(Title t) throws IOException{
		ArrayList<Title> ret = new ArrayList<Title>();
		TermDocs td = reader.termDocs(new Term("redirect",t.getKey()));
		while(td.next()){
			ret.add(new Title(reader.document(td.doc()).get("article_key")));
		}
		return ret;
	}
	
	/** Get anchor texts for given title 
	 * @throws IOException */
	public ArrayList<AnchorText> getAnchorText(Title t) throws IOException{
		ArrayList<AnchorText> ret = new ArrayList<AnchorText>();
		String key = t.getKey();
		TermEnum te = reader.terms(new Term("links",key+"|"));
		while(te.next()){
			if(!te.term().text().startsWith(key) || !te.term().field().equals("links"))
				break;
			ret.add(new AnchorText(te.term().text().substring(key.length()),te.docFreq()));			
		}
		return ret;
	}
	
	static public class AnchorText {
		public String text;
		public int freq;
		public AnchorText(String text, int freq) {
			this.text = text;
			this.freq = freq;
		}		
	}
	
	/** Get all article titles linking to given title 
	 * @throws IOException */
	public ArrayList<Title> getInLinks(Title t) throws IOException{
		ArrayList<Title> ret = new ArrayList<Title>();
		TermDocs td = reader.termDocs(new Term("links",t.getKey()+"|"));
		while(td.next()){
			ret.add(new Title(reader.document(td.doc()).get("article_key")));
		}
		return ret;
	}
	
	/** Get links from this article to other articles */
	public ArrayList<Title> getOutLinks(Title t) throws IOException{
		ArrayList<Title> ret = new ArrayList<Title>();
		TermDocs td = reader.termDocs(new Term("article_key",t.getKey()));
		if(td.next()){
			String links = reader.document(td.doc()).get("links_stored");
			for(String key : links.split(""+DELIMITER)){
				ret.add(new Title(key));
			}
		}
		return ret;
	}
}
