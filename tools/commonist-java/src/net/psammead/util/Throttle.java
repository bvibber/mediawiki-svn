package net.psammead.util;

/** only lets through a call every minimumDelay millis */
public class Throttle {
	private	long	minimumDelay;
	private	long	next;
	
	public Throttle() {
		minimumDelay	= 0;
		next			= 0;
	}
	
	/** sets the time gate calls are kept apart for */
	public Throttle(long minimumDelay) {
		this();
		this.minimumDelay	= minimumDelay;
	}
	
	/** change the minimum delay time between two gate call ends */
	public long getMinimumDelay() {
		return minimumDelay;
	}
	
	/** change the minimum delay time between two gate call ends */
	public void setMinimumDelay(long minimumDelay) {
		this.minimumDelay	= minimumDelay;
	}
	
	/** if called may block until a minimum delay after a previous call occured */
	public synchronized void gate() throws InterruptedException {
		final long	now	= System.currentTimeMillis();
		if (now < next)	{ Thread.sleep(next - now); }
		next	= System.currentTimeMillis() + minimumDelay;
	}
}
