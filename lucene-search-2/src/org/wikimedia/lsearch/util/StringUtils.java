package org.wikimedia.lsearch.util;

import java.util.regex.Pattern;

public class StringUtils {
	/** reverse a string */
	public static String reverseString(String str){
		int len = str.length();
		char[] buf = new char[len];	
		for(int i=0;i<len;i++)
			buf[i] = str.charAt(len-i-1);
		return new String(buf,0,len);
	}
	
	/** Convert wildcard with * into regexp */
	public static String wildcardToRegexp(String wildcard){
		return wildcard.replace(".","\\.").replace("*",".*?");
	}
	
	public static Pattern makeRegexp(String wildcard){
		return Pattern.compile(wildcardToRegexp(wildcard));
	}
}
