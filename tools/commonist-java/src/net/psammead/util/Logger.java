package net.psammead.util;

import java.io.*;

/** minimalistic logging system */
public final class Logger {
	public static enum Severity {
		NONE (0, "none"),
		DEBUG(1, "debug"),
		INFO (2, "info"),
		WARN (3, "warn"),
		ERROR(4, "error");

	    public final int	number;
	    public final String	name;
	    
	    Severity(int number, String name) {
	        this.number	= number;
	        this.name	= name;
	    }
	    
	    @Override
		public String toString() {
	    	return name;
	    }
	}

	private final PrintWriter	out;
	private final Object		source;
	
	public Logger(Object source) {
		this.out	= new PrintWriter(System.err);
		this.source = source;
	}

	public Logger(PrintWriter out, Object source) {
		this.out	= out;
		this.source = source;
	}
	
	public Logger(PrintStream out, Object source) {
		this(new PrintWriter(out), source);
	}
	
	public PrintWriter logWriter() {
		return out;
	}

	public void debug(String message) { log(Severity.DEBUG,	message, null); }
	public void info(String message)  { log(Severity.INFO,	message, null); }
	public void warn(String message)  { log(Severity.WARN,	message, null); }
	public void error(String message) { log(Severity.ERROR,	message, null); }
	
	public void debug(String message, Throwable error)	  { log(Severity.DEBUG,	  message, error); }
	public void info(String message, Throwable error)	  { log(Severity.INFO,	  message, error); }
	public void warn(String message, Throwable error)	  { log(Severity.WARN,	  message, error); }
	public void error(String message, Throwable error)	  { log(Severity.ERROR,	  message, error); }

	public synchronized void log(Severity level, String message, Throwable error) {
		if (level != Severity.NONE)		out.print(level.name + "\t");
		if (source != null)				out.print(sourceString(source) + "\t");
		if (message != null)			out.print(message);
		if (level != Severity.NONE 
				|| source != null 
				|| message != null)		out.println("");
		if (error != null)				error.printStackTrace(out);
		out.flush();
	}
	
	private String sourceString(Object source) {
		if (source == null)				return "<null>";
		if (source instanceof Class<?>)	return ((Class<?>)source).getName();
		return source.toString();
	}
}
