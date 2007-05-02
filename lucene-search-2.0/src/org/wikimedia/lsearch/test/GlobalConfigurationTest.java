/*
 * Created on Feb 9, 2007
 *
 */
package org.wikimedia.lsearch.test;

import java.io.IOException;
import java.net.Inet4Address;
import java.net.InetAddress;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Hashtable;

import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;

import junit.framework.TestCase;

/**
 * @author rainman
 *
 */
public class GlobalConfigurationTest extends TestCase {
	/**
	 * this is where the tests actually are because we 
	 * want to test protected methods as well
	 * 
	 * @author rainman
	 */
	public class TestGC extends GlobalConfiguration {
		public String testPreprocessLine(String line){
			return preprocessLine(line);
		}
				
		public Hashtable getSearch(){
			return search;
		}
		
		public Hashtable getIndex(){
			return index;
		}
		
		public Hashtable getDatabase(){
			return database;
		}
		
		public Hashtable getIndexLocation(){
			return indexLocation;
		}
		
		public InetAddress getMyHost(){
			return myHost;
		}
		
		public Hashtable<Integer,Hashtable<String,ArrayList<String>>> getSearchGroups(){
			return searchGroup;
		}
		
		
	}
	
	public static GlobalConfigurationTest.TestGC testgc = null;
	
	public void setUp() throws Exception {
		if(testgc == null)
			testgc = new GlobalConfigurationTest.TestGC();
	}
	
	public void testPreprocessLine(){
		String text = "entest: (mainsplit)";
		assertEquals(text,testgc.testPreprocessLine(text));
		
		String dburl = "file://"+System.getProperty("user.dir")+"/test-data/dbs.test";
		text = "{"+dburl+"}: (mainsplit)";
		assertEquals("entest,rutest,srtest,kktest: (mainsplit)",testgc.testPreprocessLine(text));
	}
	
	public void testReadURL(){
		String testurl = "file://"+System.getProperty("user.dir")+"/test-data/mwsearch-global.test";
		try {
			URL url = new URL(testurl);
			testgc.readFromURL(url,"/usr/local/var/mwsearch");
			
			// database
			Hashtable database = testgc.getDatabase();			
			Hashtable roles = (Hashtable) database.get("entest");
			assertNotNull(roles.get("mainsplit"));
			assertNotNull(roles.get("mainpart"));
			assertNotNull(roles.get("restpart"));
			
			Hashtable mainpart = (Hashtable) roles.get("mainpart");
			assertEquals("false",mainpart.get("optimize"));
			assertEquals("2",mainpart.get("mergeFactor"));
			assertEquals("10",mainpart.get("maxBufDocs"));
			
			Hashtable splitroles = (Hashtable) database.get("frtest");
			assertNotNull(splitroles.get("split"));
			assertNotNull(splitroles.get("part1"));
			assertNotNull(splitroles.get("part2"));
			assertNotNull(splitroles.get("part3"));
			
			// search
			Hashtable search = testgc.getSearch();
			ArrayList sr = (ArrayList) search.get("192.168.0.2"); 
			
			String[] ssr = (String[]) sr.toArray(new String [] {} );
			
			assertEquals("entest",ssr[0]);
			assertEquals("entest.mainpart",ssr[1]);
			assertEquals("entest.restpart",ssr[2]);
			assertEquals("rutest",ssr[3]);
			assertEquals(4,ssr.length);
			
			// search groups
			Hashtable<Integer,Hashtable<String,ArrayList<String>>> sg = testgc.getSearchGroups();
			
			Hashtable<String,ArrayList<String>> g0 = sg.get(new Integer(0));
			assertEquals("{192.168.0.5=[entest.mainpart, entest.restpart], 192.168.0.2=[entest, entest.mainpart]}",g0.toString());
			Hashtable<String,ArrayList<String>> g1 = sg.get(new Integer(1));
			assertEquals("{192.168.0.6=[frtest.part3, detest], 192.168.0.4=[frtest.part1, frtest.part2]}",g1.toString());
			
			// index
			Hashtable index = testgc.getIndex();
			ArrayList ir = (ArrayList) index.get("192.168.0.5"); 
			
			String[] sir = (String[]) ir.toArray(new String [] {} );
			
			assertEquals("entest",sir[0]);
			assertEquals("entest.mainpart",sir[1]);
			assertEquals("entest.restpart",sir[2]);
			assertEquals("detest",sir[3]);
			assertEquals("rutest",sir[4]);
			assertEquals("frtest",sir[5]);
			assertEquals(6,sir.length);
			
			// indexLocation
			Hashtable indexLocation = testgc.getIndexLocation();
			
			assertEquals("192.168.0.5",indexLocation.get("entest.mainpart"));
			assertEquals("192.168.0.2",indexLocation.get("entest.ngram"));
			
			
			// this should be the nonloopback address
			InetAddress host = testgc.getMyHost();
			String hostAddr = host.getHostAddress();
			String hostName = host.getHostName();
			System.out.println("Verify internet IP: "+hostAddr+", and hostname: "+hostName);
			
		} catch (MalformedURLException e) {
			e.printStackTrace();
		} catch (IOException e) {
			e.printStackTrace();
		}

	}
	
	public void testIndexIds(){
		IndexId entest = IndexId.get("entest");
		
		assertTrue(entest.isMainsplit());
		assertFalse(entest.isSingle());
		assertTrue(entest.isLogical());
		
		assertEquals("entest",entest.getDBname());
		assertEquals("entest",entest.toString());
		assertEquals("192.168.0.5",entest.getIndexHost());
		assertFalse(entest.isMyIndex());
		assertEquals(null,entest.getSnapshotPath());
		assertEquals("mainsplit",entest.getType());
		assertEquals("/mwsearch2/snapshot/entest",entest.getRsyncSnapshotPath());
		
		IndexId enrest = IndexId.get("entest.restpart");
		
		assertSame(enrest,entest.getRestPart());
		assertTrue(enrest.isMainsplit());
		assertFalse(enrest.isLogical());
		assertEquals("entest",enrest.getDBname());
		assertEquals("entest.restpart",enrest.toString());
		assertEquals("/mwsearch2/snapshot/entest.restpart",enrest.getRsyncSnapshotPath());
		assertFalse(enrest.isMyIndex());
		assertEquals("mainsplit",enrest.getType());
		assertEquals(null,enrest.getIndexPath());
		
		IndexId frtest = IndexId.get("frtest");
		assertTrue(frtest.isSplit());
		assertTrue(frtest.isLogical());
		assertEquals("frtest",frtest.getDBname());
		assertEquals("frtest",frtest.toString());
		assertFalse(frtest.isMyIndex());
		assertEquals(3,frtest.getSplitFactor());
		
		IndexId frpart2 = IndexId.get("frtest.part2");
		assertSame(frpart2,frtest.getPart(2));
		assertTrue(frpart2.isSplit());
		assertFalse(frpart2.isLogical());
		assertEquals(2,frpart2.getPartNum());
		assertEquals(3,frpart2.getSplitFactor());
		
		
	}
}
