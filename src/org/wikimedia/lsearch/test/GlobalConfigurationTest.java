/*
 * Created on Feb 9, 2007
 *
 */
package org.wikimedia.lsearch.test;

import java.io.IOException;
import java.net.InetAddress;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Hashtable;
import java.util.Properties;

import org.wikimedia.lsearch.config.GlobalConfiguration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.search.NamespaceFilter;

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
		
		public Properties getGlobalProps(){
			return globalProperties;
		}
		
		public Hashtable<String,String> getOaiRepo(){
			return oaiRepo;
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
			
			Hashtable nspart1 = (Hashtable) ((Hashtable) database.get("njawiki")).get("nspart1");
			assertEquals("false",nspart1.get("optimize"));
			assertEquals("5",nspart1.get("mergeFactor"));
			
			// search
			Hashtable search = testgc.getSearch();
			ArrayList sr = (ArrayList) search.get("192.168.0.2"); 
			
			String[] ssr = (String[]) sr.toArray(new String [] {} );
			
			assertEquals("entest.mainpart",ssr[0]);
			assertEquals("entest.restpart",ssr[1]);
			assertEquals("rutest",ssr[2]);
			assertEquals(6,ssr.length);
			
			// search groups
			Hashtable<Integer,Hashtable<String,ArrayList<String>>> sg = testgc.getSearchGroups();
			
			Hashtable<String,ArrayList<String>> g0 = sg.get(new Integer(0));
			assertEquals("{192.168.0.5=[entest.mainpart, entest.restpart], 192.168.0.2=[entest.mainpart]}",g0.toString());
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
			assertTrue(ir.contains("entest.mainpart.sub1"));
			assertTrue(ir.contains("entest.mainpart.sub2"));
			assertTrue(ir.contains("entest.mainpart.sub3"));
			assertEquals(22,sir.length);
			
			// indexLocation
			Hashtable indexLocation = testgc.getIndexLocation();
			
			assertEquals("192.168.0.5",indexLocation.get("entest.mainpart"));
			assertEquals("192.168.0.2",indexLocation.get("entest.ngram"));
			
			
			// this should be the nonloopback address
			InetAddress host = testgc.getMyHost();
			String hostAddr = host.getHostAddress();
			String hostName = host.getHostName();
			System.out.println("Verify internet IP: "+hostAddr+", and hostname: "+hostName);
			
			// test prefixes 
			Hashtable<String,NamespaceFilter> p = testgc.getNamespacePrefixes();
			assertEquals(17,p.size());
			
			// check global properties
			Properties prop = testgc.getGlobalProps();
			assertEquals("wiki wiktionary test",prop.get("Database.suffix"));
			assertEquals("wiki rutest",prop.get("KeywordScoring.suffix"));
			
			// check languages and keyword stuff
			assertEquals("en",testgc.getLanguage("entest"));
			assertEquals("sr",testgc.getLanguage("srwiki"));
			assertFalse(testgc.useKeywordScoring("frtest"));
			assertTrue(testgc.useKeywordScoring("srwiki"));
			assertTrue(testgc.useKeywordScoring("rutest"));
			
			// test oai repo stuff
			Hashtable<String,String> oairepo = testgc.getOaiRepo();
			assertEquals("http://$lang.wiktionary.org/w/index.php",oairepo.get("wiktionary"));
			assertEquals("http://localhost/wiki-lucene/phase3/index.php",oairepo.get("frtest"));
			assertEquals("http://$lang.wikipedia.org/w/index.php",oairepo.get("<default>"));
			
			assertEquals("http://sr.wikipedia.org/w/index.php?title=Special:OAIRepository",testgc.getOAIRepo("srwiki"));
			assertEquals("http://localhost/wiki-lucene/phase3/index.php?title=Special:OAIRepository",testgc.getOAIRepo("frtest"));
			
			// InitialiseSettings test
			assertEquals("sr",testgc.getLanguage("rswikimedia"));
			assertEquals("http://rs.wikimedia.org/w/index.php?title=Special:OAIRepository",testgc.getOAIRepo("rswikimedia"));
			assertEquals("http://commons.wikimedia.org/w/index.php?title=Special:OAIRepository",testgc.getOAIRepo("commonswiki"));
			
			// test suggest tag
			Hashtable<String,String> sug = testgc.getDBParams("entest","spell");
			assertEquals("1",sug.get("wordsMinFreq"));
			assertEquals("2",sug.get("phrasesMinFreq"));
			
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
		
		IndexId detest = IndexId.get("detest");
		assertFalse(detest.isLogical());
		
		// check nssplit
		IndexId njawiki = IndexId.get("njawiki");
		assertTrue(njawiki.isLogical());
		assertFalse(njawiki.isSplit());
		assertTrue(njawiki.isNssplit());
		assertEquals(3,njawiki.getSplitFactor());
		assertEquals("njawiki.nspart3",njawiki.getPartByNamespace("4").toString());
		assertEquals("njawiki.nspart1",njawiki.getPartByNamespace("0").toString());
		assertEquals("njawiki.nspart2",njawiki.getPartByNamespace("12").toString());
		assertEquals("[192.168.0.1]",njawiki.getSearchHosts().toString());
		
		IndexId njawiki2 = IndexId.get("njawiki.nspart2");
		assertFalse(njawiki2.isLogical());
		assertFalse(njawiki2.isSplit());
		assertTrue(njawiki2.isNssplit());
		assertEquals(3,njawiki2.getSplitFactor());
		assertEquals(2,njawiki2.getPartNum());
		assertEquals("[192.168.0.1]",njawiki2.getSearchHosts().toString());
		
		IndexId sug = IndexId.get("entest.spell");
		assertTrue(sug.isSpell());
		assertFalse(sug.isLogical());
		assertEquals(sug,sug.getSpell());
		
		IndexId sub1 = IndexId.get("entest.mainpart.sub1");
		assertFalse(sub1.isLogical());
		assertEquals(3,sub1.getSubdivisionFactor());
		assertFalse(sub1.isFurtherSubdivided());
		assertTrue(sub1.isSubdivided());
		assertEquals(1,sub1.getSubpartNum());
		assertNull(sub1.getImportPath());
		
		IndexId enmain = IndexId.get("entest.mainpart");
		assertEquals(sub1,enmain.getSubpart(0));
		assertTrue(enmain.isFurtherSubdivided());
		assertFalse(enmain.isSubdivided());
		assertEquals(3,enmain.getSubdivisionFactor());
		assertNull(enmain.getImportPath());
		
		IndexId hmpart1 = IndexId.get("hmwiki.nspart1");
		assertTrue(hmpart1.isFurtherSubdivided());
		assertNull(hmpart1.getImportPath());
		
		assertEquals("[hmwiki.nspart1.sub1, hmwiki.nspart1.sub2]",hmpart1.getPhysicalIndexIds().toString());
		assertEquals("[hmwiki.nspart3, hmwiki.nspart1.sub1, hmwiki.nspart2, hmwiki.nspart1.sub2]",IndexId.get("hmwiki").getPhysicalIndexIds().toString());
		
		IndexId hmsub1 = IndexId.get("hmwiki.nspart1.sub1");
		assertTrue(hmsub1.isSubdivided());
		assertNotNull(hmsub1.getImportPath());
		assertEquals(2,hmsub1.getSubdivisionFactor());
		assertEquals("192.168.0.2",hmsub1.getIndexHost());
		
	}
}
