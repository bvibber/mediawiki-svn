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
	using System.Collections;
	using System.Data;
	using System.Text;

	/**
	 * @author Kate Turner
	 *
	 */
	public class ArticleIterator : IEnumerator {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		private IDataReader rs;
		private string dbname;
		private bool atend;
		private Encoding encoding;
		private bool latin1;
		
		public ArticleIterator(string dbname_, IDataReader rs_) {
			rs = rs_;
			dbname = dbname_;
			atend = rs.Read();
			
			latin1 = Configuration.Open().IsLatin1(dbname);
			encoding = Encoding.GetEncoding("iso-8859-1");
		}
		
		private string Recode(string str) {
			if (latin1) {
				return str;
			} else {
				return Encoding.UTF8.GetString(encoding.GetBytes(str));
			}
		}
		
		public object Current {
			get {
				if (atend) {
					throw new InvalidOperationException("Gone beyond end of query results.");
				}
				try {
					string pageNamespace =        rs.GetString(0) ;
					string title         = Recode(rs.GetString(1));
					string contents      = Recode(rs.GetString(2));
					string timestamp     =        rs.GetString(3) ;
					log.Debug(title);
					return new Article(dbname, pageNamespace, title, contents, timestamp);
				} catch (DataException e) {
					Console.WriteLine("Warning: SQL exception: " + e.Message);
					return null;
				}
			}
		}
		
		public bool MoveNext() {
			atend = !rs.Read();
			if (atend) {
				log.Debug("Got to end of result set; closing reader.");
				rs.Close();
			}
			return !atend;
		}
		
		public void Reset() {
			throw new InvalidOperationException("Can't reset the SQL thingy.");
		}
	}
}
