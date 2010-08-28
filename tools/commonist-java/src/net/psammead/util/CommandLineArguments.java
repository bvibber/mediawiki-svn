package net.psammead.util;

import java.util.*;

/** 
* argument parser
*
* usage example:<pre>
*	var	dir		= null;
*	var	charset	= null;
*	var	cla	= new CommandLineArguments(mainArgs);
*	var	h	= cla.flag("-h");	if (h)			{ usage(); return 0; }
*	var	d	= cla.value("-d");	if (d != null)	dir		= new File(d);
*	var	c	= cla.value("-c");	if (c != null)	charset	= c;
*	var	dsc	= cla.unbound();	if (dsc.Empty)	{ usage(); return 1; }
*</pre>
*/
public class CommandLineArguments {
	private List<String>	arguments;

	public CommandLineArguments(String[] arguments) {
		this.arguments	= new ArrayList<String>(Arrays.asList(arguments));
	}
	
	/** get a flag without an argument */
	public boolean flag(String name) {
		return arguments.remove(name);
	}
	
	/** get a String value or null when it does not exist */
	public String value(String name) {
		final int	index	= arguments.indexOf(name);
		if (index == -1)					return null;
		if (index + 1 >= arguments.size())	return null;
		arguments.remove(index);				
		return arguments.remove(index);
	}
	
	/** the number of currently unbound arguments */
	public int unboundCount() {
		return arguments.size();
	}
	
	/** get the first currently unbound argument */
	public String nextUnbound() {
		if (arguments.size() < 1)	return null;
		final String	out	= arguments.get(0);
		arguments.remove(0);
		return out;
	}
	
	/** get arguments not used in flag() or value() until now */
	public String[] unbound() {
		final String[]	out	= arguments.toArray(new String[0]);
		arguments.clear();
		return out;
	}
	
	/** get arguments not used in flag() or value() until now */
	public List<String> unboundList() {
		final List<String>	out	= new ArrayList<String>(arguments); 
		arguments.clear();
		return out;
	}
}
