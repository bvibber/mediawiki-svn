package net.psammead.commonist;

import net.psammead.util.Logger;

/** base class for UI tasks */
public abstract class Task {
	private static final Logger log = new Logger(Task.class);
	
	public static final class AbortedException extends Exception {}
	
	private boolean alive;
	private boolean aborted;
	private final Thread	thread;
	private Thread	waitFor;
	
	public Task() {
		waitFor	= null;
		thread	= new Thread(
			new Runnable() {
				public void run() {
					Task.this.run();
				}
			}
		);
		// see http://java.sun.com/developer/JDCTechTips/2005/tt0727.html#1
		thread.setPriority(Thread.NORM_PRIORITY);
	}
	
	/** start the task in its own thread */
	public synchronized void start() {
		aborted	= false;
		alive	= true;
		thread.start();
	}
	
	/** request the task to abort */
	public synchronized void abort() {
		if (!alive)	return;
		aborted	= true;
	}
	
	/** start another task and request this one to stop */
	public synchronized void replace(Task task) {
		task.start(thread);
		abort();
	}
	
	/** for use within the task */
	protected synchronized void check() throws AbortedException {
		if (aborted)	throw new AbortedException();
	}
	
	/** this method does the real work, implement in subclasses */
	protected abstract void execute();
	
	/** optionally waits, until another Task has finished and start this Task */
	private synchronized void start(Thread waitFor) {
		this.waitFor	= waitFor;
		start();
	}
	
	/** executes this Task, but waits until a Task to be replaced is finished before */
	private void run() {
		if (maybeWaitForReplaced()) {
			execute();
		}
		alive	= false;
	}
	
	/** waits until a Task to be replaced is finished, if there is such a Task */
	private boolean maybeWaitForReplaced() {
		if (waitFor == null)	return true;
		
		try {
			waitFor.join();
			return true;
		}
		catch (InterruptedException e) {
			log.error("task interrupted", e);
			return false;
		}
	}
}
