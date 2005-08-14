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

namespace MediaWiki.Search {

	using System;
	using System.Collections;
	using System.IO;

	using Lucene.Net.Analysis;
	using Lucene.Net.Analysis.DE;
	using Lucene.Net.Analysis.RU;
	using Lucene.Net.Analysis.Standard;
	using Lucene.Net.Index;

	public struct KeyValue {
		public string Key;
		public string Value;
	}
	
	/**
	 * @author Kate Turner
	 *
	 */
	public class SearchState {
		protected static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);

		protected string mydbname;
		protected string indexpath;
		protected Configuration config;
		protected Analyzer analyzer = null;
		
		protected void Init(string dbname) {
			config = Configuration.Open();
			log.Debug(dbname + ": opening state");
			indexpath = string.Format(config.GetIndexString("indexpath"),
					dbname );
			analyzer = GetAnalyzerForLanguage(config.GetLanguage(dbname));
			log.Info(dbname + " using analyzer " + analyzer.GetType().FullName);
			mydbname = dbname;
		}
		
		/**
		 * @param language
		 * @return
		 */
		protected Analyzer GetAnalyzerForLanguage(string language) {
			if (language.Equals("de"))
				return new GermanAnalyzer();
			if (language.Equals("eo"))
				return new EsperantoAnalyzer();
			if (language.Equals("ru"))
				return new RussianAnalyzer();
			return new EnglishAnalyzer();
		}
		
		/**
		 * Create a fresh new index.
		 * Any existing database will be overwritten.
		 * @throws IOException
		 */
		public void InitializeIndex() {
			if (!File.Exists(indexpath)) {
				log.Info("Creating directory " + indexpath);
				System.IO.Directory.CreateDirectory(indexpath);
			}
			log.Info("Creating new index for " + mydbname);
			new IndexWriter(indexpath, analyzer, true).Close();
		}
	}
}
