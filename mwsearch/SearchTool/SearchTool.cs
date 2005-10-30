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
	
	using org.mediawiki.importer;
	
	public class SearchTool {
		private static Configuration config;
		
		public static void Main(string[] args) {
			Console.WriteLine("MediaWiki Lucene search indexer - index manipulation tool.\n");
			
			string configSection = "Updater";
			ArrayList databases = new ArrayList();
			string action = null;
			string source = null;
			
			for (int i = 0; i < args.Length; i++) {
				if (args[i] == "--daemon") {
					configSection = "Daemon";
				} else if (args[i] == "--optimize") {
					action = "optimize";
				} else if (args[i].StartsWith("--import")) {
					string[] bits = args[i].Split(new char[] {'='}, 2);
					action = "import";
					source = bits[1];
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
			
			if (action == "optimize") {
				foreach (string dbname in databases) {
					try {
						SearchWriter state = new SearchWriter(dbname);
						Console.WriteLine("Optimizing " + dbname);
						state.Optimize();
					} catch (Exception e) {
						Console.WriteLine(e);
					}
				}
			} else if (action == "import") {
				if (databases.Count > 1) {
					Console.WriteLine("Warning! Only the first database given will be imported (" + databases[0] + ")");
				}
				ImportDump(source, (string)databases[0]);
			}
			
			Console.WriteLine("Done!");
		}
		
		static void ImportDump(string dumpfile, string database) {
			java.io.InputStream input = new java.io.BufferedInputStream(
				new java.io.FileInputStream(dumpfile));

			SearchWriter state = new SearchWriter(database);
			state.InitializeIndex();
			
			XmlDumpReader reader = new XmlDumpReader(input, new SearchImporter(state));
			reader.readDump();
			
			state.Close();
			state.Optimize();
		}
	}
}
