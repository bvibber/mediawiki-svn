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
	
	using log4net;
	using log4net.Config;
	using log4net.Util;
	
	using Nini.Config;

	/**
	 * @author Kate Turner
	 *
	 */
	public class Configuration {
		// static members...
		private static Configuration instance;
		private static string configfile = "/etc/mwsearch.conf";
		
		/** Which [section] is preferred for GetIndex* */
		private static string indexSection = "Search";
		
		public static void SetConfigFile(string file) {
			configfile = file;
		}
		
		public static void SetIndexSection(string section) {
			indexSection = section;
		}
		
		public static Configuration Open() {
			if (instance == null)
				instance = new Configuration();
			return instance;
		}
		
		// instance members...
		private IConfigSource props;

		private Configuration() {
			props = new IniConfigSource(configfile);
			
			if (GetBoolean("Logging", "debug")) {
				LogLog.InternalDebugging = true;
			}
			
			string logconfig = GetIndexString("logconfig");
			if (logconfig == null) {
				// debug!
				Console.WriteLine("Errors will be logged to console...");
				BasicConfigurator.Configure();
				//LogManager.GetLogger
			} else {
				FileInfo log = new FileInfo(logconfig);
				XmlConfigurator.ConfigureAndWatch(log);
			}
		}
		
		public string GetString(string section, string name) {
			return props.Configs[section].Get(name);
		}
		
		public string[] GetArray(string section, string name) {
			string s = GetString(section, name);
			if (s != null)
				return s.Split(' ');
			return null;
		}

		public bool GetBoolean(string section, string name) {
			string s = GetString(section, name);
			return s != null && s.Equals("true");
		}
		
		public int GetInt(string section, string name, int defaultValue) {
			string s = GetString(section, name);
			if (s == null)
				return defaultValue;
			try {
				return int.Parse(s);
			} catch (Exception e) {
				return defaultValue;
			}
		}
		
		public bool IsLatin1(string dbname) {
			foreach (string name in GetArray("Database", "latin1")) {
				if (dbname.Equals(name)) {
					return true;
				}
			}
			return false;
		}
		
		public string GetLanguage(string dbname) {
			string[] suffixes = GetArray("Database", "suffix");
			if (suffixes == null)
				return "en";
			foreach (string suffix in suffixes) {
				if (dbname.EndsWith(suffix))
					return dbname.Substring(0, dbname.Length - suffix.Length);
			}
			return "en";
		}
		
		public string[] Databases {
			get {
				string[] explicitList = GetArray("Search", "databases");
				if (explicitList != null)
					return explicitList;
				
				string dblistSource = GetString("Search", "databaselist");
				if (dblistSource == null)
					return new string[] { };
				
				TextReader dblistFile = File.OpenText(dblistSource);
				
				ArrayList dblist = new ArrayList();
				for (string line = dblistFile.ReadLine(); line != null; line = dblistFile.ReadLine()) {
					string trimmed = line.Trim();
					if (trimmed.Length > 0)
						dblist.Add(trimmed);
				}
				return (string[])dblist.ToArray(typeof(string));
			}
		}
		
		public string GetIndexString(string name) {
			string val = GetString(indexSection, name);
			if (val == null)
				return GetString("Search", name);
			else
				return val;
		}
	
	}
}
