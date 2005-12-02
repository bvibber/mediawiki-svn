// created on 11/4/2005 at 12:33 AM

namespace MediaWiki.Search.Daemon {
	using System;
	using System.IO;
	using System.Collections;
	using System.Net;
	using System.Net.Sockets;
	using System.Text;
	using System.Text.RegularExpressions;
	using System.Web;

	using Lucene.Net.Analysis;
	using Lucene.Net.Documents;
	using Lucene.Net.Index;
	using Lucene.Net.QueryParsers;
	using Lucene.Net.Search;
	
	using MediaWiki.Search.Prefix;

	/**
	 * Represents a single client.  Performs one search, sends the
	 * results, then exits.
	 * @author Kate Turner
	 *
	 */
	public class HttpHandler {
		protected static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		/** Client input stream */
		StreamReader istrm;
		/** Client output stream */
		StreamWriter ostrm;
		
		protected string method;
		protected string rawUri;
		protected Uri uri;
		protected IDictionary headers;
		
		protected string contentType = "text/html";
		bool headersSent;
		
		public HttpHandler(Stream stream) {
			istrm = new StreamReader(stream);
			ostrm = new StreamWriter(stream);
		}
		
		private static int _openCount = 0;
		private static object _countLock = new object();
		
		public static int OpenCount {
			get {
				lock (_countLock) {
					return _openCount;
				}
			}
		}
		
		private static void _Enter() {
			lock (_countLock) {
				_openCount++;
			}
		}
		
		private static void _Leave() {
			lock (_countLock) {
				_openCount--;
			}
		}
		
		
		public void Run(object par) {
			Run();
		}
		
		public void Run() {
			//using (log4net.NDC.Push(client.Client.RemoteEndPoint)) {
			headersSent = false;
			try {
				_Enter();
				Handle();
				log.Debug("request handled.");
			} catch (IOException e) {
				log.Error("net error: " + e.Message);
			} catch (Exception e) {
				log.Error(e);
			} finally {
				if (!headersSent) {
					try {
						SendError(500, "Internal server error", "An internal error occurred.");
					} catch (IOException e) { }
				}
				// Make sure the client is closed out.
				try {  ostrm.Close(); } catch { }
				try {  istrm.Close(); } catch { }
				_Leave();
			}
		}
		
		protected void Handle() {
			/* Simple HTTP protocol; accepts GET requests only.
			 * URL path format is /operation/database/searchterm
			 * The path should be URL-encoded UTF-8 (standard IRI).
			 * 
			 * Additional paramters may be specified in a query string:
			 *   namespaces: comma-separated list of namespace numeric keys to subset results
			 *   limit: maximum number of results to return
			 *   offset: number of matches to skip before returning results
			 * 
			 * Content-type is text/plain and results are listed.
			 */
			string request = ReadInputLine();
			if (!request.StartsWith("GET ")) {
				SendError(400, "Bad Request",
				               "Programmer too lazy to add support for non-GET requests" );
				log.Warn("Bad request: " + request);
				return;
			}
			// Ignore any remaining headers...
			for (string headerline = ReadInputLine(); !headerline.Equals("");)
				headerline = ReadInputLine();
			
			string[] bits = request.Split(' ');
			rawUri = bits[1];
			try {
				uri = new Uri("http://localhost:8123" + rawUri);
			} catch (UriFormatException e) {
				SendError(400, "Bad Request",
				               "Couldn't make sense of the given URI.");
				log.Warn("Bad URI in request: " + rawUri);
				return;
			}
			
			DoStuff();
		}
		
		protected virtual void DoStuff() {
			SendError(404, "Not Found", "Forgot to implement DoStuff() method!");
		}
		
		protected void SendHeaders(int code, string message) {
			if (headersSent) {
				log.WarnFormat("Asked to send headers, but already sent! ({0} {1})", code, message);
				return;
			}
			SendOutputLine(String.Format("HTTP/1.1 {0} {1}", code, message));
			SendOutputLine("Content-Type: " + contentType);
			SendOutputLine("Connection: Close");
			SendOutputLine("");
			headersSent = true;
		}
		
		protected void SendError(int code, string message, string detail) {
			contentType = "text/html";
			SendHeaders(code, message);
			SendOutputLine(@"<!DOCTYPE html PUBLIC ""-//W3C//DTD XHTML 1.0 Transitional//EN"" ""http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"">
<html xmlns=""http://www.w3.org/1999/xhtml"" xml:lang=""en"" lang=""en"">
<head>
<title>Error: " + code + @" " + message + @"</title>
</head>
<body>
<h1>" + code + @" " + message + @"</h1>
<p>" + detail + @"</p>
<hr />
<p><i>MWDaemon on " + Environment.MachineName + @"</i></p>
</body>
</html>");
		}
	    
	    protected void SendOutputLine(string sout) {
	        log.DebugFormat(">>>{0}", sout);
	        ostrm.WriteLine(sout);
		}
	 	
	    protected string ReadInputLine() {
			string sin = istrm.ReadLine();
			log.DebugFormat("<<<{0}", sin);
			return sin;
	    }
	}
}
