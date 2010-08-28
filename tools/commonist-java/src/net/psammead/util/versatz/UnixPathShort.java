package net.psammead.util.versatz;

import java.io.File;
import java.io.IOException;
import java.util.ArrayList;
import java.util.Collections;
import java.util.Comparator;
import java.util.List;
import java.util.Map;

public final class UnixPathShort {
	public static void main(String[] args) throws IOException {
		for (String a : args) {
			System.out.println("-------" + a + " --------------"); 
			for (String s : shortPaths(new File(a))) {
				System.out.println(s);
			}
		}
//		System.out.println(commonPrefix(args));
	}
	
	public static final List<String> shortPaths(File file) throws IOException {
		final String				path	= file.getCanonicalPath();
		final Map<String,String>	env		= System.getenv();
		final List<String>			out		= new ArrayList<String>();
		for (String key : env.keySet()) {
			if ("PWD".equals(key))		continue;
			String	val		= env.get(key);
			String	prefix	= longestCommonPrefix(new String[] { val, path });
			if (prefix.length() == 0)	continue;
			String	rest	= path.substring(prefix.length());
			if (!rest.startsWith("/"))	continue;
			out.add("$" + key + rest);
		}
		Collections.sort(out, new Comparator<String>() {
			public int compare(String o1, String o2) {
				if (o1.length() < o2.length())	return -1;
				if (o1.length() > o2.length())	return +1;
				return o1.compareTo(o2);
			}
		});
		return out;
	}
	
	public static String longestCommonPrefix(String[] strings) {
		if (strings.length == 0)	throw new IllegalArgumentException("no string don't have a prefix");
		if (strings.length == 1)	return strings[0];
		
		int	position	= longestCommonPrefixLength(strings);
		return strings[0].substring(0, position);
	} 
	
	public static int longestCommonPrefixLength(String[] strings) {
		if (strings.length == 0)	throw new IllegalArgumentException("no string don't have a prefix");
		if (strings.length == 1)	return strings[0].length();
		
		final String	first	= strings[0];
		int	position	= 0;
		for (;;) {
			if (position >= first.length())	return position;
			char	firstChar	= first.charAt(position);
			for (int i=1; i<strings.length; i++) {
				String	other	= strings[i];
				if (position >= other.length())	return position;
				char	otherChar	= other.charAt(position);
				if (firstChar != otherChar)	return position;
			}
			position++;
		}
	} 
}
