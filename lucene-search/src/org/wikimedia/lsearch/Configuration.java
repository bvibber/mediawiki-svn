/*
 * Copyright 2004 Kate Turner
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy 
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell 
 * copies of the Software, and to permit persons to whom the Software is 
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in 
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 * 
 * $Id$
 */
package org.wikimedia.lsearch;

import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.IOException;
import java.util.Properties;
import java.util.Iterator;

/**
 * @author Kate Turner
 *
 */
public class Configuration {
	private static Configuration instance;
	private static String configfile = "./mwsearch.conf";
	public static void setConfigFile(String file) {
		configfile = file;
	}
	public static Configuration open() {
		if (instance == null)
			instance = new Configuration();
		return instance;
	}
	private Configuration() {
		props = new Properties();
		try {
			props.load(new FileInputStream(new File(configfile)));
		} catch (FileNotFoundException e3) {
			System.err.println("Error: config file " + configfile + " not found");
			return;
		} catch (IOException e3) {
			System.err.println("Error: IO error reading config: " + e3.getMessage());
			return;
		}
	}
	private Properties props;
	
	public String getString(String name) {
		return props.getProperty(name);
	}
	public String[] getArray(String name) {
		String s = props.getProperty(name);
		if (s != null)
			return s.split(" ");
		return null;
	}
	public boolean islatin1(String dbname) {
		String[] latin1dbs = getArray("mwsearch.latin1dbs");
		if (latin1dbs == null)
			return false;
		//for (String s : latin1dbs)
		for (int i = 0; i < latin1dbs.length; i++) {
			String s = latin1dbs[i];
			if (s.equals(dbname))
				return true;
		}
		return false;
	}
	public boolean getBoolean(String name) {
		String s = getString(name);
		return s != null && s.equals("true");
	}
}
