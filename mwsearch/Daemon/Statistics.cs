/*
 * Copyright 2006 Brion Vibber
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

namespace MediaWiki.Search.Daemon {
	
	using System;
	using System.Diagnostics;
	
	public class Statistics {
		/** Calculate rates as average over the last N milliseconds */
		long maxDelta;
		
		/** Number of items to keep in the ring buffer */
		int maxItems;
		
		/** Ring buffer use count and pointers... */
		int usedItems = 0;
		int start = 0;
		int end = -1;
		
		/** For each request, records if successfully handled or discarded due to overflow */
		bool[] handled;
		
		/** Timestamp for each request in the statistics buffer */
		long[] times;
		
		/** Milliseconds taken to service each request. */
		long[] deltas;
		
		/** Number of active threads at the time this request hit the wire */
		int[] activeThreads;
		
		object locker = new object();
		
		public Statistics(int maxItems, long maxDelta) {
			this.maxItems = maxItems;
			this.maxDelta = maxDelta;
			
			handled = new bool[maxItems];
			times = new long[maxItems];
			deltas = new long[maxItems];
			activeThreads = new int[maxItems];
		}
		
		private static long millis(DateTime time) {
			// ticks is in 100s of nanoseconds (10^-7)
			// we want milliseconds (10^-3)
			// epoch is different but we don't care
			return time.Ticks / 10000L;
		}
		
		public void Add(bool status, DateTime time, long delta, int threads) {
			lock (locker) {
				end++;
				if (end == maxItems)
					end = 0;
				if (usedItems == maxItems) {
					start++;
					if (start == maxItems)
						start = 0;
				} else {
					usedItems++;
				}
				handled[end] = status;
				times[end] = millis(time);
				deltas[end] = delta;
				activeThreads[end] = threads;
			}
		}
		
		/**
		 * Provide an array of data from rolling average of recent requests:
		 * 0: rate of requests handled successfully (req/sec)
		 * 1: rate of requests dropped because the queue got too long (req/sec)
		 * 2: amount of time taken to serve successfully requests (ms)
		 * 3: number of simultaneously active threads (count)
		 * @return
		 */
		public double[] Collect() {
			long handledSum = 0, discardSum = 0;
			long deltaSum = 0, threadSum = 0;
			long timeDelta = maxDelta;
			long now = millis(DateTime.UtcNow);
			if (end != -1) {
				lock(locker) {
					long availableDelta = times[end] - times[start];
					if (availableDelta < maxDelta)
						timeDelta = availableDelta;
					for (int i = 0, j = start; i < usedItems; i++) {
						if (now - times[j] <= maxDelta) {
							if (handled[j]) {
								handledSum++;
								deltaSum += deltas[j];
								threadSum += activeThreads[j];
							} else {
								discardSum++;
							}
						}
						j++;
						if (j == maxItems)
							j = 0;
					}
				}
			}
			//System.out.printf("XXX %d %d %d\n", handledSum, discardSum, timeDelta);
			double handleRate = (timeDelta == 0) ? 0.0 : (double)handledSum * 1000.0 / (double)timeDelta;
			double discardRate = (timeDelta == 0) ? 0.0 : (double)discardSum * 1000.0 / (double)timeDelta;
			double serviceTime = (handledSum == 0) ? 0.0 : (double)deltaSum / (double)handledSum;
			double threadCount = (handledSum == 0) ? 0.0 : (double)threadSum / (double)handledSum;
			return new double[] {handleRate, discardRate, serviceTime, threadCount};
		}
		
		/**
		 * Provide a formatted line summarizing the current data.
		 * @return
		 */
		public string Summarize() {
			double[] data = Collect();
			return string.Format("handle={0:F3} discard={1:F3} service={2:F3} threads={3:F4}",
					data[0], data[1], data[2], data[3]);
		}
		
		public string State() {
			lock (locker) {
				return string.Format("used={0} start={1} end={2}", usedItems, start, end);
			}
		}
		
		public void UpdateGanglia() {
			double[] data = Collect();
			SendGanglia("search_rate", data[0], "requests/sec");
			SendGanglia("search_discards", data[1], "requests/sec");
			SendGanglia("search_time", data[2], "ms");
			SendGanglia("search_threads", data[3], "threads");
		}
		
		private void SendGanglia(string name, double value, string units) {
			string command = string.Format(
					"gmetric --name '{0}' --value={1:F3} --type double --units '{2}' --dmax {3}",
					name, value, units, maxDelta / 1000L);
			Process.Start(command);
		}
		
		/**
		 * Test/demo method
		 * @param args
		 */
		/*
		public static void main(String[] args) {
			// Report data from last 20 seconds, up to 50 items
			Statistics stats = new Statistics(50, 20000L);
			Random rand = new Random();
			
			while (true) {
				if (rand.nextBoolean()) {
					boolean success = (rand.nextDouble() < 0.95);
					long timestamp = System.currentTimeMillis();
					long delta = (long)(rand.nextDouble() * 500.0);
					int threads = 1 + rand.nextInt(3);
					System.out.printf("bump: %b %d %d %d\n", success, timestamp, delta, threads);
					stats.add(success, timestamp, delta, threads);
							
				}
				System.out.println(stats.state());
				System.out.println(stats.summarize());
				try {
					Thread.sleep(rand.nextInt(1500));
				} catch (InterruptedException e) {
					// TODO Auto-generated catch block
					e.printStackTrace();
				}
			}
		}
		*/
	}
}
