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
	using System.Collections;
	using System.Threading;
	
	using MediaWiki.Search;

	public class UpdateThread {
		private static readonly log4net.ILog log = log4net.LogManager.GetLogger(System.Reflection.MethodBase.GetCurrentMethod().DeclaringType);
		
		static bool _isRunning = false;
		static bool _done = false;
		static bool _flushNow = false;
		static object _threadLock = new object();
		
		// If more than this number are queued, try to flush out updates
		static int _maxQueueCount;
		
		// If more than this many seconds have passed since last flush,
		// initiate a flush-out.
		static int _maxQueueTimeout;
		static DateTime _lastFlush = DateTime.UtcNow;
				
		// A hash table of hash tables, dbname -> title key -> UpdateRecord.
		// Run all accesses behind _threadLock for safety.
		static Hashtable _queuedUpdates;
		
		static UpdateThread() {
			_queuedUpdates = new Hashtable();
		}
		
		public static void Run(Configuration config) {
			_maxQueueCount = config.GetInt( "Updater", "maxqueuecount", 500 );
			_maxQueueTimeout = config.GetInt( "Updater", "maxqueuetimeout", 3600 );
			Start();
			while (!_done) {
				ApplyUpdates();
				Thread.Sleep(1000);
			}
			
			// Apply any remaining updates before we quit
			lock (_threadLock) {
				ApplyAll(_queuedUpdates);
			}
			
			log.Info("Updater thread ending, quit requested.");
		}
		
		public static void ApplyUpdates() {
			log.Debug("Checking for updates...");
			try {
				Hashtable workUpdates = null;
				lock (_threadLock) {
					if (!_isRunning && !_flushNow) {
						log.Debug("Update thread suspended.");
						return;
					}
					
					int queuedCount = Count;
					if (queuedCount == 0) {
						_flushNow = false;
						log.Debug("Nothing to do.");
						return;
					}
					
					TimeSpan delta = (DateTime.UtcNow - _lastFlush);
					if (!_flushNow
						&& delta.Seconds < _maxQueueTimeout
						&& queuedCount < _maxQueueCount) {
						log.DebugFormat("{0} queued items waiting, {1} since last flush...",
							queuedCount, delta);
						return;
					}
					
					workUpdates = SwitchOut();
				}
				ApplyAll(workUpdates);
			} catch (Exception e) {
				log.Error("Unexpected error in update thread: " + e);
				return;
			}
		}
		
		private static SearchState GetSearchState(string databaseName) {
			SearchState state = SearchState.ForWiki(databaseName);
			state.InitializeIfNew();
			return state;
		}
		
		private static Hashtable SwitchOut() {
			lock (_threadLock) {
				log.Info("Preparing to flush all indexes...");
				
				Hashtable workUpdates = _queuedUpdates;
				_queuedUpdates = new Hashtable();
				_lastFlush = DateTime.UtcNow;
				_flushNow = false;
				
				return workUpdates;
			}
		}
		
		private static void ApplyOn(string databaseName, ICollection queue) {
			try {
				log.Info("Applying updates to " + databaseName);
				SearchState state = GetSearchState(databaseName);
				foreach (UpdateRecord record in queue) {
					log.Info("Applying read pass: " + record);
					record.ApplyReads(state);
				}
				foreach (UpdateRecord record in queue) {
					log.Info("Applying write pass: " + record);
					record.ApplyWrites(state);
				}
				state.Reopen();
				log.Info("Closed updates on " + databaseName);
			} catch (Exception e) {
				log.Error("Unexpected error in update for " + databaseName + ": " + e);
				return;
			}
		}

		private static void ApplyAll(Hashtable workUpdates) {
			foreach (string dbname in workUpdates.Keys) {
				ApplyOn(dbname, ((Hashtable)workUpdates[dbname]).Values);
			}
		}
		
		public static void Stop() {
			lock (_threadLock) {
				if (_isRunning) {
					log.InfoFormat("Stopping update thread, {0} updates queued",
						Count);
					_isRunning = false;
				}
			}
		}
		
		public static void Start() {
			lock (_threadLock) {
				if (!_isRunning) {
					log.InfoFormat("Starting update thread, {0} updates queued",
						Count);
					_isRunning = true;
				}
			}
		}
		
		public static void Enqueue(UpdateRecord record) {
			lock (_threadLock) {
				if (_queuedUpdates[record.Database] == null)
					_queuedUpdates[record.Database] = new Hashtable();
				
				// Supersede any prior queued update for this same page
				// so we don't end up with duplicates in the index.
				((Hashtable)_queuedUpdates[record.Database])[record.Key] = record;
			}
			log.Info("Queued item: " + record);
		}
		
		public static void Quit() {
			log.Info("Quit requested.");
			_done = true;
			Stop();
		}
		
		public static int Count {
			get {
				lock (_threadLock) {
					int n = 0;
					foreach (string dbname in _queuedUpdates.Keys) {
						n += ((Hashtable)_queuedUpdates[dbname]).Count;
					}
					return n;
				}
			}
		}
		
		public static string GetStatus() {
			int count = Count;
			TimeSpan delta = (DateTime.UtcNow - _lastFlush);
			return string.Format("Updater {0} running; {1} item{2} queued. {3} since last flush.",
				(_isRunning ? "IS" : "IS NOT" ),
				count,
				(count == 1 ? "" : "s" ),
				delta);
		}
		
		public static void Flush() {
			log.Info("Flush requested.");
			_flushNow = true;
		}
	}
}
