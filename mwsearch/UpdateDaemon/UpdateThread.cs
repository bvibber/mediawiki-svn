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
		static object _threadLock = new object();
		static Queue _updateQueue;
		
		static UpdateThread() {
			_updateQueue = Queue.Synchronized(new Queue());
		}
		
		public static void Run() {
			Start();
			while (!_done) {
				ApplyUpdates();
				Thread.Sleep(1000);
			}
			log.Info("Updater thread ending, quit requested.");
		}
		
		public static void ApplyUpdates() {
			log.Debug("Checking for updates...");
			while (true) {
				lock (_threadLock) {
					if (!_isRunning) {
						log.Debug("Update thread suspended.");
						return;
					}
					
					try {
						UpdateRecord next = (UpdateRecord)_updateQueue.Dequeue();
						SearchState state = GetSearchState(next.Database);
						log.Info("Applying: " + next);
						next.Apply(state);
					} catch (InvalidOperationException) {
						log.Debug("All done!");
						return;
					} catch (Exception e) {
						log.Error("Unexpected error in update thread: " + e);
						return;
					}
				}
			}
		}
		
		private static SearchState GetSearchState(string databaseName) {
			SearchState state = SearchState.ForWiki(databaseName);
			state.InitializeIfNew();
			return state;
		}
		
		public static void Stop() {
			lock (_threadLock) {
				if (_isRunning) {
					log.InfoFormat("Stopping update thread, {0} updates queued",
						_updateQueue.Count);
					_isRunning = false;
					
					try {
						int resetStatesCount = SearchState.ResetStates();
						log.InfoFormat("Reset {0} search index states, {1} updates queued",
							resetStatesCount, _updateQueue.Count);
					} catch (Exception e) {
						log.Error("Error resetting indexes: " + e);
					}
				}
			}
		}
		
		public static void Start() {
			lock (_threadLock) {
				if (!_isRunning) {
					log.InfoFormat("Starting update thread, {0} updates queued",
						_updateQueue.Count);
					_isRunning = true;
				}
			}
		}
		
		public static void Enqueue(UpdateRecord record) {
			log.Info("Queued item: " + record);
			_updateQueue.Enqueue(record);
		}
		
		public static void Quit() {
			log.Info("Quit requested.");
			_done = true;
			Stop();
		}
		
		public static string GetStatus() {
			int count = _updateQueue.Count;
			return string.Format("Updater {0} running; {1} item{2} queued.",
				(_isRunning ? "IS" : "IS NOT" ),
				count,
				(count == 1 ? "" : "s" ));
		}
		
		public static void Flush(string databaseName) {
			lock (_threadLock) {
				log.InfoFormat("Flushing index for {0}, {1} updates queued",
					databaseName, _updateQueue.Count);
				SearchState state = SearchState.ForWiki(databaseName);
				state.Reopen();
				log.InfoFormat("Done flushing {0}, {1} updates queued",
					databaseName, _updateQueue.Count);
			}
		}

		public static void FlushAll() {
			lock (_threadLock) {
				log.InfoFormat("Flushing all indexes, {0} updates queued",
					_updateQueue.Count);
				
				try {
					int resetStatesCount = SearchState.ResetStates();
					log.InfoFormat("Reset {0} search index states, {1} updates queued",
						resetStatesCount, _updateQueue.Count);
				} catch (Exception e) {
					log.Error("Error resetting indexes: " + e);
				}
			}
		}

	}
}
