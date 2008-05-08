/* Copyright (c) 2008 River Tarnell <river@wikimedia.org>. */
/*
 * Permission is granted to anyone to use this software for any purpose,
 * including commercial applications, and to alter it and redistribute it
 * freely. This software is provided 'as-is', without any express or implied
 * warranty.
 */
 
/* @(#) $Id$ */

package imgservserver;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.util.Properties;

public class Main {
    public static void main(String[] args) {
		int i = 0;
		String configfile = null;
		
		while (i < args.length) {
			if (args[i].equals("-c")) {
				if (i + 1 >= args.length) {
					System.err.println("% Option '-c' requires an argument.");
					System.exit(1);
				}
				
				configfile = args[i + 1];
				i += 2;
			} else {
				System.err.printf("%% Option '%s' not recognised.\n", args[i]);
				System.exit(1);
			}
		}
		
		Properties config = new Properties();
		
		try {
			if (configfile != null)
				config.load(new FileInputStream(new File(configfile)));
		} catch (Exception e) {
			System.err.printf("%% Cannot load configuration file \"%s\": %s\n",
					configfile, e.getMessage());
			System.exit(1);
		}

		Configuration c = new Configuration(config);
		
		try {
			RequestListener listener = new RequestListener(c);
			listener.run();
		} catch (IOException e) {
			System.out.printf("% Error occurred in request loop: %s",
					e.getMessage());
		}
    }

}
