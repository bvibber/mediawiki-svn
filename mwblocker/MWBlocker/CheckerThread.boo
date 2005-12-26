// created on 9/6/2005 at 7:28 PM

namespace MediaWiki.Blocker

import System
import System.Collections
import System.Net
import System.Threading

class CheckerThread:
"""Nice simply infinite loop waiting for things to check.
Some asynchronous source of data needs to poke data into
the queue or we'll never get anywhere, of course."""
	
	static _checkedCount = 0
	static _positiveCount = 0
	static _queue = Queue.Synchronized(Queue())
	
	static def Run():
		while true:
			try:
				next = _queue.Dequeue() as Suspect
				if Checker(next).Check():
					_positiveCount++
				_checkedCount++
				print next
			except e as InvalidOperationException:
				// Nothing there; wait a bit.
				Thread.Sleep(1000)
	
	static def Enqueue(suspect as Suspect):
		_queue.Enqueue(suspect)
	
	static Status:
		get:
			return string.Format("{0} checks done, {1} positive. {1} queued", \
				_checkedCount, _positiveCount, _queue.Count)
