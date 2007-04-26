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
	public Benchmark(String host, int port, String database, String verb, Terms terms, int words) {
		this(host,port,database,verb,terms,words,0,0);
	}
	
	/** Use this to construct a benchmark thread */
	public Benchmark(String host, int port, String database, String verb, Terms terms, int words, int runs, int thread) {
		this.host = host;
		this.port = port;
		this.database = database;
		this.verb = verb;
		this.runs = runs;
		this.terms = terms;
		this.thread = thread;
		this.words = words;
	}

	/** Start benchmarking on main thread */
	public void startBenchmark(int threads, int runs){
		this.runs = runs;
		this.threads = threads;
		totalTimes = new long[threads];
		totalResults = new int[threads];
		activeThreads = threads;
		startTime = System.currentTimeMillis();
		
		collector = new Collector(100,threads*runs);
		
		for(int i=0;i<threads;i++)
			new Benchmark(host,port,database,verb,terms,words,runs,i).start();
		
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
		for(int i=0;i<words;i++){
			if(!query.equals(""))
				query += " OR ";
			query += terms.next();
		}
		query = "main:"+URLEncoder.encode(query).replaceAll("\\+","%20");
		String urlString = "http://"+host+":"+port+"/"+verb+"/"+database+"/"+query+"?limit=20"; 
		try {
			URL url;
			url = new URL(urlString);
			URLConnection conn = url.openConnection();
			BufferedReader in = new BufferedReader(
                              new InputStreamReader(
                              		conn.getInputStream()));
			String inputLine;
			int resCount = -1;
			
			while ((inputLine = in.readLine()) != null){
				if(resCount == -1)
					resCount = Integer.parseInt(inputLine);
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
		String database = "wikilucene";
		String verb = "search";
		int runs = 5000;
		int threads = 10;
		int words = 2;
		//SampleTerms terms = new SampleTerms();
		Terms terms = new WordTerms("./test-data/words-wikilucene.ngram.gz");
		
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
				database = args[++i];
			} else if (args[i].equals("-w")) {
				words = Integer.parseInt(args[++i]);
			} else if (args[i].equals("--help")) {
				System.out.println("Usage: java Benchmark <options>\n"+
				                   "  -h  host (default: "+host+")\n"+
				                   "  -p  port (default: "+port+")\n"+
				                   "  -d  database (default: "+database+")\n"+
				                   "  -t  threads (defaut: "+threads+")\n"+
				                   "  -n  count (default: "+runs+")\n"+
				                   "  -w  number of words in query (default: "+words+")\n"+
				                   "  -v  verb (default: "+verb+")\n\n");
			}
		}
		System.out.println("Running benchmark on "+host+":"+port+" with "+threads+" theads each "+runs+" runs");
		Benchmark bench = new Benchmark(host, port, database, verb, terms, words);
		bench.startBenchmark(threads,runs);
		bench.printReport();
	}
}
