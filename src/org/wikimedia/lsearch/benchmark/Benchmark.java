package org.wikimedia.lsearch.benchmark;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;
import java.net.URL;
import java.net.URLConnection;
import java.net.URLEncoder;

/** Simple benchmarking tool */
public class Benchmark extends Thread {
	protected String host;
	protected int port;
	protected String database;
	protected String verb;
	protected int runs;
	protected int threads;
	protected Terms terms;
	protected int words;
	protected String namespace;
	protected String namespaceFilter;
	protected static boolean sample;
	protected int limit;
	
	protected int thread; // current thread
		
	// shared data between benchmark threads
	protected static long totalTimes[];
	protected static int totalResults[];
	protected static int activeThreads;
	protected static long startTime;
	protected static long endTime;
	
	public final static long reportInterval = 1000;
	
	protected static Collector collector;
	
	protected static Object sharedLock = new Object();
	
	/** Use this to construct the main thread */
	public Benchmark(String host, int port, String database, String verb, Terms terms, int words, String namespace, String namespaceFilter, int limit) {
		this(host,port,database,verb,terms,words,namespace,namespaceFilter,limit,0,0);
	}
	
	/** Use this to construct a benchmark thread */
	public Benchmark(String host, int port, String database, String verb, Terms terms, int words, String namespace, String namespaceFilter, int limit, int runs, int thread) {
		this.host = host;
		this.port = port;
		this.database = database;
		this.verb = verb;
		this.runs = runs;
		this.terms = terms;
		this.thread = thread;
		this.words = words;
		this.namespace = namespace;
		this.namespaceFilter = namespaceFilter;
		this.limit = limit;
	}

	/** Start benchmarking on main thread */
	public void startBenchmark(int threads, int runs){
		this.runs = runs;
		this.threads = threads;
		totalTimes = new long[threads];
		totalResults = new int[threads];
		activeThreads = threads;
		startTime = System.currentTimeMillis();
		
		collector = new Collector(100,threads*runs,threads);
		
		for(int i=0;i<threads;i++)
			new Benchmark(host,port,database,verb,terms,words,namespace,namespaceFilter,limit,runs,i).start();
		
		// wait until all thread finish
		while(activeThreads != 0){
			try {
				Thread.sleep(100);
			} catch (InterruptedException e) {
				e.printStackTrace();
			}
		}
	}
	
	/** Benchmarker thread */
	@Override
	public void run() {
		long start = System.currentTimeMillis();
		int res = 0;
		
		for(int i=0;i<runs;i++){
			long begin = System.currentTimeMillis();
			int gotRes = search();
			res += gotRes;
			long end = System.currentTimeMillis();
			collector.add(gotRes,end-begin);
		}
		
		long end = System.currentTimeMillis();
		
		synchronized(sharedLock){
			totalTimes[thread] = end-start;
			totalResults[thread] = res;
			activeThreads--;
			if(activeThreads == 0)
				endTime = System.currentTimeMillis();
		}
	}
	
	@SuppressWarnings("deprecation")
	protected int search(){
		String query = "";
		if(verb.equals("prefix")){
			int num = (int)(Math.random()*8);
			String t = terms.next();
			query = namespaceFilter+":"+t.substring(0,Math.min(num,t.length()));
		} else{
			for(int i=0;i<words;i++){
				if(!query.equals(""))
					query += " OR ";
				query += terms.next();
			}
		}
		String urlString;
		if(namespace.equals("")){
			query = URLEncoder.encode(query).replaceAll("\\+","%20");
			urlString = "http://"+host+":"+port+"/"+verb+"/"+database+"/"+query+"?limit="+limit+"&namespaces="+namespaceFilter;
		} else{
			query = URLEncoder.encode(namespace+":"+query).replaceAll("\\+","%20");
			urlString = "http://"+host+":"+port+"/"+verb+"/"+database+"/"+query+"?limit="+limit;
		}
		if(sample){
			System.out.println("url ~ "+urlString);
			sample = false;
		}
		try {
			URL url;
			url = new URL(urlString);
			URLConnection conn = url.openConnection();
			BufferedReader in = new BufferedReader(
                              new InputStreamReader(
                              		conn.getInputStream()));
			String inputLine;
			int resCount = verb.equals("prefix")? 0 : -1;
			
			while ((inputLine = in.readLine()) != null){
				if(resCount == -1)
					resCount = Integer.parseInt(inputLine);
				if(verb.equals("prefix"))
					resCount ++ ;
			}
			in.close();
			
			if(resCount == -1){
				throw new Exception("Error during search");
			} else 
				return resCount;
		} catch (Exception e) {
			System.out.println("Error in thread "+thread+" on url: "+urlString);
			//e.printStackTrace();
			return 0;
		}
	}

	public void printReport(){
		long time = endTime-startTime;
		long truns = runs*threads;
		long tres = 0;
		for(int i=0;i<threads;i++){
			tres += totalResults[i];
			if(totalResults[i]==0)
				System.out.println("Warning thread "+i+" got 0 results!");
		}
		System.out.println();
		System.out.println("For total of "+truns+" runs ("+runs+" runs per "+threads+" threads) on "+database+" with "+words+" words:");
		System.out.println("* total time: "+time+"ms");
		System.out.println("* time per request: "+((double)time/truns)+"ms");
		System.out.println("* total results: "+tres);
		System.out.println("* results per request: "+((double)tres/truns));
	}

	public static void main(String[] args) {
		String host = "127.0.0.1";
		int port = 8123;
		String database = "enwiki";
		String verb = "search";
		String namespace = "";
		String namespaceFilter= "0";
		String lang = "en";
		int runs = 5000;
		int threads = 10;
		int words = 1;
		sample = true;
		String wordfile = null;
		Terms terms;
		int defaultLimit = 20;
		
		for(int i = 0; i < args.length; i++) {
			if (args[i].equals("-h")) {
				host = args[++i];
			} else if (args[i].equals("-p")) {
				port = Integer.parseInt(args[++i]);
			} else if (args[i].equals("-d")) {
				database = args[++i];
			} else if (args[i].equals("-t")) {
				threads = Integer.parseInt(args[++i]);
			} else if (args[i].equals("-c")) {
				runs = Integer.parseInt(args[++i]);
			} else if (args[i].equals("-v")) {
				verb = args[++i];
			} else if (args[i].equals("-wf")) {
				wordfile = args[++i];
			} else if (args[i].equals("-n") || args[i].equals("-ns")) {
				namespace = args[++i];
			} else if (args[i].equals("-f") ) {
				namespaceFilter = args[++i];
				namespace ="";
			} else if (args[i].equals("-w")) {
				words = Integer.parseInt(args[++i]);
			} else if (args[i].equals("-lm")) {
				defaultLimit = Integer.parseInt(args[++i]);
			} else if (args[i].equals("-l") || args[i].equals("-lang")) {
				lang = args[++i];
			} else if (args[i].equals("-s") || args[i].equals("-sample")) {
				sample = true;
			} else if (args[i].equals("--help")) {
				System.out.println("Usage: java Benchmark <options>\n"+
				                   "  -h  host (default: "+host+")\n"+
				                   "  -p  port (default: "+port+")\n"+
				                   "  -d  database (default: "+database+")\n"+
				                   "  -t  threads (defaut: "+threads+")\n"+
				                   "  -c  count (default: "+runs+")\n"+
				                   "  -w  number of words in query (default: "+words+")\n"+
				                   "  -v  verb (default: "+verb+")\n"+
				                   "  -n  namespace (default: "+namespace+")\n"+
				                   "  -f  namespace filter (default: "+namespaceFilter+")\n"+
				                   "  -l  language (default: "+lang+")\n"+
				                   "  -s  show sample url (default: "+sample+")\n"+
				                   "  -lm  limit number of results (default: "+defaultLimit+")\n"+
				                   "  -wf <file> use file with search terms (default: none)\n");
				return;
			} else{
				System.out.println("Unrecognized switch: "+args[i]);
				return;
			}
		}
		if(wordfile != null)
			terms = new StreamTerms(wordfile);
		else if("en".equals(lang) || "de".equals(lang) || "es".equals(lang) || "fr".equals(lang) || "it".equals(lang) || "pt".equals(lang))
			terms = new WordTerms("./lib/dict/terms-"+lang+".txt.gz");		
		else if(lang.equals("sample"))
			terms = new SampleTerms();
		else
			terms = new WordTerms("./test-data/words-wikilucene.ngram.gz");
		
		System.out.println("Running benchmark on "+database+" "+host+":"+port+" with "+threads+" theads each "+runs+" runs, "+words+" words, filter: "+((namespace == "")? namespaceFilter : namespace)+", lang "+lang);
		Benchmark bench = new Benchmark(host, port, database, verb, terms, words, namespace, namespaceFilter, defaultLimit);
		bench.startBenchmark(threads,runs);
		bench.printReport();
	}
}
