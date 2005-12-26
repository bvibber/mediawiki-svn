/*
 * Copyright 2004 Kate Turner
 * Ported to C# by Brion Vibber, April 2005
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

namespace MediaWiki.Search.Daemon {
	using System;
	using System.Collections;
	using System.IO;
	using System.Net;
	using System.Net.Sockets;
	using System.Threading;

	using MediaWiki.Search;

	/**
	 * @author Kate Turner
	 *
	 */
	public class Daemon {
		static int port = 8123;
		public static TcpListener sock;
		public static string indexPath;
		public static string[] dbnames;
		private static Configuration config;
		public static int numthreads;
		
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		private static Queue openConnections;
		
		public static void Main(string[] args) {
			Console.WriteLine(
					"MediaWiki Lucene search indexer - runtime search daemon.\n" +
					"Version 20050103, copyright 2004 Kate Turner.\n"
					);
			/*
			int i = 0;
			while (i < args.length - 1) {
				if (args[i].equals("-port"))
					port = Integer.valueOf(args[++i]).intValue();
				else if (args[i].equals("-configfile"))
					Configuration.setConfigFile(args[++i]);
				else if (args[i].equals("-mwversion")) {
					String vers = args[++i];
					if (vers.equals("old"))
						mw_version = MW_OLD;
					else if (vers.equals("new"))
						mw_version = MW_NEW;
					else {
						System.err.println("Unknown MediaWiki version " + vers);
						return;
					}
				} else break;
				++i;
			}
			*/
			config = Configuration.Open();
			
			indexPath = config.GetString("mwsearch", "indexpath");
			log.Info("Binding server to port " + port);
			
			try {
				sock = new TcpListener(port);
			} catch (Exception e) {
				log.Fatal("Error: bind error: " + e.Message);
				return;
			}
			sock.Start();
			
			log.Debug("Blah blah debug");
			log.Info("Blah blah info");
			log.Error("Blah blah error");
			log.Fatal("Blah blah fatal");
			
			Queue xxx = new Queue();
			openConnections = Queue.Synchronized(xxx);
			
			// start some worker threads
			int maxThreads = 25;
			IList threads = new ArrayList();
			for (int i = 0; i < maxThreads; i++) {
				log.Debug("Starting worker thread #" + i);
				Worker worker = new Worker(i);
				ThreadStart start = new ThreadStart(worker.Run);
				Thread thread = new Thread(start);
				threads.Add(thread);
				thread.Start();
			}
			
			// go!
			for (;;) {
				TcpClient client;
				try {
					log.Debug("Listening...");
					client = sock.AcceptTcpClient();
				} catch (Exception e) {
					log.Error("accept() error: " + e.Message);
					continue;
				}
				lock (openConnections) {
					log.Debug("Queueing connection...");
					openConnections.Enqueue(client);
					Monitor.Pulse(openConnections);
				}
			}

		}
		
		public static TcpClient NextClient() {
			lock (openConnections) {
				Monitor.Wait(openConnections);
				TcpClient client = (TcpClient)openConnections.Dequeue();
				return client;
			}
		}
	}
}
