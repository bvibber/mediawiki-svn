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
import org.apache.log4j.Logger;
import org.apache.log4j.PropertyConfigurator;

public class Main {
	private static Logger logger = Logger.getLogger(ImageClient.class);

    public static void main(String[] args) {
		int i = 0;
		String configfile = null;
		
		PropertyConfigurator.configure("log4j.properties");
		
		while (i < args.length) {
			if (args[i].equals("-c")) {
				if (i + 1 >= args.length) {
					logger.fatal("Option '-c' requires an argument.");
					System.exit(1);
				}
				
				configfile = args[i + 1];
				i += 2;
			} else {
				logger.fatal("Option \""+args[i]+"\" not recognised.");
				System.exit(1);
			}
		}
		
		Properties config = new Properties();
		
		try {
			if (configfile != null)
				config.load(new FileInputStream(new File(configfile)));
		} catch (Exception e) {
			logger.fatal("Cannot load configuration file \""+configfile+"\": "+e.getMessage());
			System.exit(1);
		}

		Configuration c = new Configuration(config);
		
		try {
			logger.info("Startup successful.");
			RequestListener listener = new RequestListener(c);
			listener.run();
		} catch (IOException e) {
			logger.fatal("Error occurred in request loop: " + e.getMessage());
		}
    }

}
