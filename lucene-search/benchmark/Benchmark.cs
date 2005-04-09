/*
 * Copyright 2005 Brion Vibber
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id$
 */

using System;
using System.Collections;
using System.IO;
using System.Net;
using System.Net.Sockets;
using System.Text;
using System.Threading;
using System.Web;

class Benchmark
{
	public static void Main(string[] args)
	{
		Benchmark bench = new Benchmark("127.0.0.1", 8123, "entest");
		bench.runs = 100;
		//bench.Run();
		bench.RunSets(10);
		bench.Report();
	}
	
	private string host;
	private ushort port;
	private string database;
	private int runs;
	
	/* Access these only in lock(times){} */
	private ArrayList times;
	private int totalRequests;
	private int totalResults;
	private TimeSpan totalTime;
	/* -- */
	
	private int runningThreads = 0;
	private readonly object threadlock = new object();
	
	private Benchmark(string host, ushort port, string database) {
		this.host = host;
		this.port = port;
		this.database = database;
		this.times = new ArrayList();
	}
	
	private void RunSets(int threads) {
		lock (threadlock) {
			runningThreads = threads;
		}
		
		for(int i = 0; i < threads; i++) {
			ThreadPool.QueueUserWorkItem(Run);
		}
		// Wait for threads to clean up
		lock (threadlock) {
			while (runningThreads > 0) {
				Monitor.Wait(threadlock);
			}
		}
	}
	
	private void Run(object wtf) {
		Console.WriteLine("Starting thread w/ " + runs + " runs ...");
		Console.Out.Flush();
		try {
			for (int i = 0; i < runs; i++) {
				Search(SampleTerms.Next);
			}
		} finally {
			lock (threadlock) {
				--runningThreads;
				Monitor.Pulse(threadlock);
			}
		}
	}
	
	private void Search(string term) {
		DateTime start = DateTime.UtcNow;
		
		//Console.WriteLine("Connecting...");
		TcpClient client = new TcpClient(host, port);
		Stream stream = client.GetStream();
		StreamReader reader = new StreamReader(stream);
		StreamWriter writer = new StreamWriter(stream);
		
		//Console.WriteLine("Requesting...");
		string encterm = HttpUtility.UrlEncode(term, Encoding.UTF8);
		string req = database + "\n" +
			"SEARCH\n" +
			encterm + "\n";
		//Console.Write(req);
		writer.Write(req);
		writer.Flush();
		
		//Console.WriteLine("Getting results...");
		
		string numResults = reader.ReadLine();
		//Console.WriteLine(numResults);
		
		string remainder = reader.ReadToEnd();
		string[] lines = remainder.Split('\n'); // last is empty, as \n is terminator
		int numReceived = lines.Length - 1;
		
		// ...
		reader.Close();
		writer.Close();
		stream.Close();
		client.Close();
		
		TimeSpan delta = DateTime.UtcNow - start;
		Console.WriteLine("[{0}] '{1}' received {2} of {3} lines ({4} chars) in {5}.",
			Thread.CurrentThread.GetHashCode(),
			encterm, numResults, numReceived, remainder.Length, delta);
		
		lock(times) {
			times.Add(delta);
			totalTime += delta;
			totalResults += numReceived;
			++totalRequests;
		}
	}
	
	void Report() {
		Console.WriteLine("Made {0} total requests", totalRequests);
		Console.WriteLine("Received {0} total result lines", totalResults);
		Console.WriteLine("Spent {0} total on all requests", totalTime);
		Console.WriteLine("Average time per request: {0}", new TimeSpan(totalTime.Ticks / totalRequests));
		Console.WriteLine("Average time per result:  {0}", new TimeSpan(totalTime.Ticks / totalResults));
		
		times.Sort();
		Console.WriteLine("Fastest request: {0}", times[0]);
		Console.WriteLine("Slowest request: {0}", times[times.Count-1]); 
	}
}
