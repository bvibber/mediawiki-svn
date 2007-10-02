package org.wikimedia.lsearch.highlight;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.net.Socket;
import java.text.ParseException;
import java.util.ArrayList;
import java.util.Arrays;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Analyzer;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.apache.lucene.search.Query;
import org.apache.lucene.search.highlight.Fragmenter;
import org.apache.lucene.search.highlight.Highlighter;
import org.apache.lucene.search.highlight.QueryScorer;
import org.apache.lucene.search.highlight.Scorer;
import org.apache.lucene.search.highlight.SimpleFragmenter;
import org.apache.lucene.search.highlight.SimpleHTMLFormatter;
import org.apache.lucene.search.highlight.TextFragment;
import org.wikimedia.lsearch.analyzers.Analyzers;
import org.wikimedia.lsearch.analyzers.FastWikiTokenizerEngine;
import org.wikimedia.lsearch.analyzers.FieldBuilder;
import org.wikimedia.lsearch.analyzers.FieldNameFactory;
import org.wikimedia.lsearch.analyzers.FilterFactory;
import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.analyzers.WikiTokenizer;
import org.wikimedia.lsearch.beans.Article;
import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;
/**
 * Highlight daemon that receives highlight requests.
 * The input syntax is:
 * QUERY <dbname> <search query>  // must be first line, the search query
 * 
 * HIGHLIGHT <size> <ns> <title> // text to highlight  
 * <data>  // the article of size length (size is in chars)
 * 
 * FETCH  // fetch highlighted text (in html)
 * 
 * The output syntax is:
 * HIGHLIGHTING <ns> <title>
 * <highlight-html> // the highlighted text (html in single line)
 * 
 * @author rainman
 *
 */
public class HighlightDaemon extends Thread {
	static Logger log = Logger.getLogger(HighlightDaemon.class);
	/** Client input stream */
	BufferedReader in;
	/** Client output stream */
	PrintWriter out;
	
	public HighlightDaemon(Socket s) {
		try {
			in = new BufferedReader(new InputStreamReader(s.getInputStream()));
			out = new PrintWriter(s.getOutputStream());			
		} catch (IOException e) {
			log.error("I/O in opening socket.");
		}
	}

	@Override
	public void run() {
		try{
			handle();
		} catch(ParseException e){
			log.warn("Error parsing request "+e.getMessage());
		} catch(IOException e){
			log.warn("I/O error handling request "+e.getMessage());
		} catch(Exception e){
			e.printStackTrace();
			log.warn("Error handling request "+e.getMessage());
		} finally {
			try {	in.close();	} catch (IOException e2) { }
			out.close();
		}
	}

	/**
	 * Handle requests
	 * @throws IOException 
	 * @throws org.apache.lucene.queryParser.ParseException 
	 */
	protected void handle() throws IOException, ParseException, org.apache.lucene.queryParser.ParseException {
		// expecting: QUERY <search query>
		String line = in.readLine();
		String[] parts = line.split(" ",3);
		if(parts.length != 3 || !parts[0].equals("QUERY"))
			throw new ParseException("Invalid syntax. Expecting \"QUERY <dbname> <search query>\" on the first line, but got "+line, 0);		
		String query = parts[2]; // search query string
		String dbname = parts[1];
		IndexId iid = IndexId.get(dbname);
		ArrayList<Article> articles = new ArrayList<Article>(); // articles to hightlight
		log.debug("Got query on "+dbname+" : "+query);
		int segments = 1; // how many fragments to show
		
		// expecting HIGHLIGHT <size> <ns> <title> or FETCH
		while(true){
			line = in.readLine();
			if(line.startsWith("HIGHLIGHT")){
				parts = line.split(" ",4);
				if(parts.length != 4)
					throw new ParseException("Invalid syntax. Expecting \"HIGHLIGHT <size> <ns> <title>\", but got "+line, 0);					
				int size = Integer.parseInt(parts[1]);
				int ns = Integer.parseInt(parts[2]);
				String title = parts[3];
				log.debug("Got HIGHLIGHT of size "+size+" on "+ns+":"+title);
				String content = readString(size);
				articles.add(new Article(0,ns,title,content,false,1,0));
			} else if(line.startsWith("FETCH")){
				parts = line.split(" ",2);
				if(parts.length == 2)
					segments = Integer.parseInt(parts[1]);
				log.debug("Got FETCH");
				break;
			}
		}
		
		// highlight all articles and return results
		GlobalConfiguration global = GlobalConfiguration.getInstance();
		boolean exactCase = global.exactCaseIndex(iid.getDBname());
		FieldBuilder.Case dCase = exactCase? FieldBuilder.Case.EXACT_CASE : FieldBuilder.Case.IGNORE_CASE;
		String lang = global.getLanguage(dbname);
		Analyzer analyzer = Analyzers.getSearcherAnalyzer(iid,exactCase);
		FieldBuilder.BuilderSet bs = new FieldBuilder(iid,dCase).getBuilder(dCase);
		WikiQueryParser parser = new WikiQueryParser(bs.getFields().contents(),
				new NamespaceFilter("0"),analyzer,bs,WikiQueryParser.NamespacePolicy.IGNORE,null);
		Query q = parser.parseFourPass(query,WikiQueryParser.NamespacePolicy.IGNORE,iid.getDBname());
		Scorer scorer = new QueryScorer(q);
		SimpleHTMLFormatter formatter = new SimpleHTMLFormatter("<span class=\"searchmatch\">","</span>");
		Highlighter highlighter = new Highlighter(formatter,scorer);
		highlighter.setTextFragmenter(new SimpleFragmenter(80));
		
		for(Article ar : articles){
			log.debug("Sending highlighted text for "+ar);
			String clean = new CleanupParser(ar.getContents(),iid).parse();
			TokenStream tokens = analyzer.tokenStream("contents",clean);
			out.println("HIGHLIGHTING "+ar.getNamespace()+" "+ar.getTitle());
			String[] highlighted = highlighter.getBestFragments(tokens,clean,segments);			
			if(highlighted == null)
				out.println(""); // query doesn't match!
			else{
				for(String h : highlighted){
					h = h.replace('\n',' ').replace('\r',' ').trim();
					if(h.startsWith(")") || h.startsWith(","))
						h = h.substring(1);
					log.debug("Highlighted text: "+h);
					out.print("..."+h+"... ");
				}
				out.println();
			}
		}		
		out.flush();
	}
	
	/** Read a string of certain size from input 
	 * @throws IOException */
	protected String readString(int size) throws IOException {
		char[] buf = new char[size];
		int i = 0;
		while(i != size){
			i += in.read(buf,i,size-i);
		}
		return new String(buf,0,size);
	}
	
	

}
