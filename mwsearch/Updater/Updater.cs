/*
 * Copyright 2004 Kate Turner
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

namespace MediaWiki.Search.Updater {
	using System;
	using System.Data;
	using System.IO;

	using log4net;
	using log4net.Config;
	using log4net.Util;
	
	using MediaWiki.Search.Prefix;
	
	/**
	 * @author Kate Turner
	 *
	 */
	public class MWSearch {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		const int
			 DOING_FULL_UPDATE	= 1
			,DOING_INCREMENT 	= 2
			;
		static int what = -1;
		const int
			 MW_NEW = 1
			,MW_OLD = 2;
		static int mw_version = MW_NEW;
		static Configuration config;
		
		public static void Main(string[] args) {	
			BasicConfigurator.Configure();
			LogLog.InternalDebugging = true;
			
			//if (args.Length < 1) {
				//Console.WriteLine("Must specify database name");
				//return;
			//}
			
			for (int i = 0; i < args.Length;) {
				if (args[i].Equals("--rebuild"))
					what = DOING_FULL_UPDATE;
				else if (args[i].Equals("--increment"))
					what = DOING_INCREMENT;
				else if (args[i].Equals("--configfile"))
					Configuration.SetConfigFile(args[++i]);
				else break;
				++i;
			}

			config = Configuration.Open();
			if (what == -1) {
				Console.WriteLine("No action specified");
				return;
			}
			
			Console.WriteLine(
					"MWSearch Lucene search indexer - standalone index rebuilder.\n" +
					"Version 20050406, copyright 2004 Kate Turner.\n");
			string[] dbnames = config.GetArray("mwsearch", "databases");
			foreach (string dbname in dbnames) {
				SearchState state;
				try {
					state = SearchState.ForWiki(dbname);
				} catch (Exception e) {
					Console.WriteLine("Error opening search index: " + e.ToString());
					return;
				}
				
				/*
				PrefixMatcher matcher;
				try {
					matcher = PrefixMatcher.ForWiki(dbname); 
				} catch (Exception e) {
					Console.WriteLine("Error opening title index: " + e.ToString());
					return;
				}
				*/
				
				Console.WriteLine("{0}: running {1} update...",
						dbname, (what == DOING_INCREMENT ? "incremental" : "full"));
				
				DateTime now = DateTime.UtcNow;
				long numArticles = 0;

				try {
					Console.Out.Flush();
					/*
					if (what == DOING_FULL_UPDATE) {
						// Clear out and (re)create the prefix list
						matcher.CreateTable();
					}
					*/
					DatabaseConnection conn = DatabaseConnection.ForWiki(dbname);
					ArticleList articles = conn.EnumerateArticles();
					log.Debug("Opened enumeration...");
					Console.Out.Flush();
					foreach (Article article in articles) {
						if((numArticles % 10) == 0)
							log.Debug("updating article #" + numArticles);
						state.AddArticle(article);
						//matcher.AddArticle(article);
						if ((++numArticles % 1000) == 0) {
							Console.WriteLine("{0}... ({1}/sec)", numArticles,
								numArticles/(DateTime.UtcNow - now).TotalSeconds);
							Console.Out.Flush();
						}
					}
					/*
					if (what == DOING_FULL_UPDATE) {
						log.Info(dbname + ": Adding prefix index");
						matcher.CreateIndex();
					}
					*/
					conn.Close();
				} catch (DataException e) {
					Console.WriteLine("Error: DB error: " + e.ToString());
					return;
				} catch (IOException e) {
					Console.WriteLine("Error: IO error: " + e.ToString());
				} catch (Exception e) {
					Console.WriteLine("Error: error: " + e.ToString());
				}
				TimeSpan totaltime = DateTime.UtcNow - now;
				//System.out.printf("%s: indexed %d articles in %f seconds (%.2f articles/sec)\n",
				//		dbname, numArticles, totaltime, numArticles/totaltime);
				Console.WriteLine("{0}: indexed {1} articles in {2} seconds ({3} articles/sec)\n",
					dbname, numArticles, totaltime, numArticles/totaltime.TotalSeconds);
			}
		}
		
	}
}
