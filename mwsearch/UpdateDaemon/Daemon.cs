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

namespace MediaWiki.Search.UpdateDaemon {
	using System;
	using System.IO;
	using System.Runtime.Remoting;
	
	using CookComputing.XmlRpc;
	
	using MediaWiki.Search;
	
	public class Daemon : MarshalByRefObject {
		private static Configuration config;
		
		public static void Main(string[] args) {
			Console.WriteLine("MediaWiki Lucene search indexer - update daemon.\n");
			
			Configuration.SetIndexSection("Updater");
			config = Configuration.Open();
			
			string configFile = Path.Combine(AppDomain.CurrentDomain.BaseDirectory,
				"MWUpdateDaemon.exe.config");
			RemotingConfiguration.Configure(configFile);
			RemotingConfiguration.RegisterWellKnownServiceType(
				typeof(Daemon),
				"SearchUpdater",
				WellKnownObjectMode.Singleton);
			
			UpdateThread.Run(config);
		}
		
		[XmlRpcMethod("searchupdater.updatePage")]
		public bool UpdatePage(string databaseName, Title title, string text) {
			UpdateThread.Enqueue(new PageUpdate(databaseName, title, text));
			return true;
		}
		
		[XmlRpcMethod("searchupdater.deletePage")]
		public bool DeletePage(string databaseName, Title title) {
			UpdateThread.Enqueue(new PageDeletion(databaseName, title));
			return true;
		}
		
		[XmlRpcMethod("searchupdater.getStatus")]
		public string GetStatus() {
			return UpdateThread.GetStatus();
		}
		
		[XmlRpcMethod("searchupdater.stop")]
		public bool Stop() {
			UpdateThread.Stop();
			return true;
		}
		
		[XmlRpcMethod("searchupdater.start")]
		public bool Start() {
			UpdateThread.Start();
			return true;
		}
		
		[XmlRpcMethod("searchupdater.flushAll")]
		public bool FlushAll() {
			UpdateThread.Flush();
			return true;
		}
		
		[XmlRpcMethod("searchupdater.quit")]
		public bool Quit() {
			UpdateThread.Quit();
			return true;
		}
	
	}
}
