package net.psammead.util.ui;

import java.lang.reflect.InvocationHandler;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.lang.reflect.Proxy;

import javax.swing.SwingUtilities;

import net.psammead.util.annotation.FullyStatic;

@FullyStatic 
public final class EDTUtil {
	private EDTUtil() {}
	
	/** 
	 * wrap an object implementing an interface in a Proxy calling methods of
	 * the wrapped objects thru {@link SwingUtilities#invokeAndWait(Runnable)}
	 */
	@SuppressWarnings("unchecked")
	public static <W> W invokeAndWait(final Class<W> iface, final W wrap) {
		return (W)Proxy.newProxyInstance(
			iface.getClassLoader(),
			new Class[] { iface },
			new Invocation<W>(wrap));
	}
	
	private static class Invocation<WRAP> implements InvocationHandler {
		private final WRAP		wrap;

		public Invocation(WRAP wrap) {
			this.wrap		= wrap;
		}
		
		public Object invoke(Object proxy, final Method method, final Object[] args) throws Throwable {
			final Execution<WRAP>	execution	= new Execution<WRAP>(wrap, method, args);
			SwingUtilities.invokeAndWait(execution);
			if (execution.thr != null)	throw execution.thr;
			return execution.out;
		}
	}
	
	private static class Execution<WRAP> implements Runnable {
		public volatile Object		out;
		public volatile Throwable	thr;
		
		private final Method	method;
		private final WRAP		target;
		private final Object[]	args;
		
		public Execution(WRAP target, Method method, Object[] args) {
			this.target = target;
			this.method = method;
			this.args	= args;
			
			out = null;
			thr = null;
		}
		
		public void run() {
			try {
				out = method.invoke(target, args);
			}
			catch (InvocationTargetException e) {
				thr = e.getTargetException();
			}
			catch (Exception e) {
				thr = e;
			}
		}
	}
}
