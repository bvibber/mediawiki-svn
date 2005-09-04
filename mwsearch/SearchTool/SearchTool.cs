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

namespace MediaWiki.Search.SearchTool {
	using System;
	using System.Collections;
	
	using MediaWiki.Search;
	
	public class SearchTool {
		private static Configuration config;
		
		public static void Main(string[] args) {
			Console.WriteLine("MediaWiki Lucene search indexer - index manipulation tool.\n");
			
			string configSection = "Updater";
			ArrayList databases = new ArrayList();
			string action = null;
			
			for (int i = 0; i < args.Length; i++) {
				if (args[i] == "--daemon") {
					configSection = "Daemon";
				} else if (args[i] == "--optimize") {
					action = "optimize";
				} else if (!args[i].StartsWith("--")) {
					databases.Add(args[i]);
				}
			}
			
			if (action == null) {
				Console.WriteLine("No action specified; try --optimize.");
				System.Environment.Exit(-1);
			}
			
			if (databases.Count == 0) {
				Console.WriteLine("Cowardly refusing to operate with no databases given.");
				System.Environment.Exit(-1);
			}
			
			Configuration.SetIndexSection(configSection);
			config = Configuration.Open();
			
			foreach (string dbname in databases) {
				SearchWriter state = new SearchWriter(dbname);
				Console.WriteLine("Optimizing " + dbname);
				state.Optimize();
			}
			
			Console.WriteLine("Done!");
		}
	}
}
