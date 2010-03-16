package org.wikimedia.lsearch.test;

import org.wikimedia.lsearch.analyzers.WikiQueryParser;
import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.GlobalConfiguration;

import junit.framework.TestCase;

public abstract class WikiTestCase extends TestCase {
	protected Configuration config = null;
	protected GlobalConfiguration global = null;
	
	protected void setUp() throws Exception {
		if(config == null){
			Configuration.setConfigFile(System.getProperty("user.dir")+"/test-data/lsearch.conf.test");
			Configuration.setGlobalConfigUrl("file://"+System.getProperty("user.dir")+"/test-data/lsearch-global.test");
			config = Configuration.open();
			global = GlobalConfiguration.getInstance();
			WikiQueryParser.TITLE_BOOST = 2;
			WikiQueryParser.ALT_TITLE_BOOST = 6;
			WikiQueryParser.CONTENTS_BOOST = 1;
		}
	}

}
