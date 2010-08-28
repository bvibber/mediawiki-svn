package net.psammead.mwapi;

import java.util.*;

/** base class for Exceptions thrown when something has gone wrong with the networking */ 
public abstract class MediaWikiException extends Exception {
	/** Constructs a new exception with the specified detail message. */
	public MediaWikiException(String message) {
		super(message);
	}
	
	/** Constructs a new exception with the specified detail message and cause. */
	public MediaWikiException(String message, Throwable cause) {
		super(message, cause);
	}

//	/** Constructs a new exception with the specified cause and a detail 
//		message of (cause==null ? null : cause.toString()) (which 
//		typically contains the class and detail message of cause). */
// 	public MediaWikiException(Throwable cause) {
//		super(cause);	
//	}
	
	//------------------------------------------------------------------------------
	//## factoids
	
	/** all stored Factoids */
	private final List<Factoid>	factoids	= new ArrayList<Factoid>();
	
	/** immutable factoid value */
	public static class Factoid {
		public final String	name;
		public final Object	value;
		public Factoid(String name, Object value) {
			this.name	= name;
			this.value	= value;
		}
		public String getName() {
			return name;
		}
		public Object getValue() {
			return value;
		}
	}
	
	/** adds a factoid about the failure for debugging */
	public MediaWikiException addFactoid(String name, Object value) {
		factoids.add(new Factoid(name, value));
		return this;
	}
	
	/** returns a clone of the List with all factoids in them */
	public List<Object> getFactoids() {
		return new ArrayList<Object>(factoids);
	}
	
	/** get the message, extended by the stored Factoids */
	@Override
	public String getMessage() {
		StringBuffer	out	= new StringBuffer(super.getMessage());
		for (Factoid	factoid : factoids) {
			out.append('\n')
					.append(factoid.name)
					.append('\t')
					.append(factoid.value != null ? String.valueOf(factoid.value) : "<null>");
			
		}
		return out.toString();
	}
}
