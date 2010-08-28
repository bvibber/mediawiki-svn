package net.psammead.util.apple;

import java.lang.reflect.*;
import net.psammead.util.Logger;
import net.psammead.util.annotation.FullyStatic;

/** provides a way to install an apple quit handler without referring to apple classes directly */
@FullyStatic 
public final class AppleQuit {
	private static final Logger log = new Logger(System.err, AppleQuit.class);
	
	/** fully static utility class, shall not be instantiated */
	private AppleQuit() {}
	
	public static interface Handler {
		void applicationQuit();
	}

	/** tries to install an com.apple.mrj.com.apple.mrj.MRJQuitHandler */
	public static void install(final Handler handler) {
		try {
			final Class<?>	utilsClass		= Class.forName("com.apple.mrj.MRJApplicationUtils");
			final Class<?>	handlerClass	= Class.forName("com.apple.mrj.MRJQuitHandler");
			final Object	adapterInstance	= Proxy.newProxyInstance(
					AppleQuit.class.getClassLoader(),
					new Class[] { handlerClass }, 
					new InvocationHandler() {
						public Object invoke(Object proxy, Method method, Object[] args) throws Throwable {
							log.info("apple quithandler executing");
							handler.applicationQuit();
							return null;
						}
					});
			final Method	registerMethod	= utilsClass.getMethod("registerQuitHandler", new Class[] { handlerClass });
			registerMethod.invoke(null, new Object[] { adapterInstance });
			log.info("apple quithandler installed");
		}
		catch (ClassNotFoundException e) {
			// not an apple? no problem ;)
			log.info("apple quithandler not installed");
		}
		catch (Exception e) {
			log.error("cannot install apple quithandler", e);
		}
	}
}
