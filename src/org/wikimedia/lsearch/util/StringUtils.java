package org.wikimedia.lsearch.util;

public class StringUtils {
	/** reverse a string */
	public static String reverseString(String str){
		int len = str.length();
		char[] buf = new char[len];	
		for(int i=0;i<len;i++)
			buf[i] = str.charAt(len-i-1);
		return new String(buf,0,len);
	}
}
