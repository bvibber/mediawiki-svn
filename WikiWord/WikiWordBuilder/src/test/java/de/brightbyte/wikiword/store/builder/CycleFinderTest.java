package de.brightbyte.wikiword.store.builder;

import junit.framework.TestCase;
import de.brightbyte.data.IntRelation;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.LogOutput;
import de.brightbyte.util.PersistenceException;

public class CycleFinderTest extends TestCase {

	protected IntRelation newIntRelation(int capacity) {
		return new IntRelation(capacity);
	}
	
	protected int findCycles(IntRelation g) throws PersistenceException {
		CycleFinder cf = new CycleFinder(g, true);
		cf.setOut(new LogOutput(ConsoleIO.output));
		cf.findCycles();
		return cf.getCycleCount();
	}
	
	public void testFindCycles() throws PersistenceException {
		int c;
		IntRelation r;
		
		//small --------------------------
		r = newIntRelation(10);
		r.put(1, 11);
		
		c = findCycles(r);
		assertEquals(0, c);
		assertEquals(0, r.size());

		//-----------
		r = newIntRelation(10);
		r.put(1, 11);
		r.put(11, 111);
		
		c = findCycles(r);
		assertEquals(0, c);
		assertEquals(0, r.size());

		//-----------
		r = newIntRelation(10);
		r.put(1, 11);
		r.put(11, 111);
		r.put(111, 11);
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size());

		//-----------
		r = newIntRelation(10);
		r.put(1, 11);
		r.put(11, 111);
		r.put(11, 1);
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size());

		//simple tree --------------------------
		r = newIntRelation(10);

		r.put(1, 11);
		r.put(1, 12);
		r.put(11, 111);
		r.put(11, 112);
		r.put(11, 113);
		r.put(12, 121);
		r.put(12, 122);
		r.put(122, 1221);
		r.put(1221, 12211);
		r.put(12211, 122111);
		r.put(12211, 122112);
		
		c = findCycles(r);
		assertEquals(0, c);
		//assertEquals(0, r.size());
		assertEquals(0, r.get(1).length);

		//tree with looped root --------------------------
		r = newIntRelation(10);
		
		r.put(1, 1); //loop!
		r.put(1, 11);
		r.put(1, 12);
		r.put(11, 111);
		r.put(11, 112);
		r.put(11, 113);
		r.put(12, 121);
		r.put(12, 122);
		r.put(122, 1221);
		r.put(1221, 12211);
		r.put(12211, 122111);
		r.put(12211, 122112);
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size());

		//tree with looped leaf --------------------------
		r = newIntRelation(10);
		
		r.put(1, 11); 
		r.put(1, 12); //kept
		r.put(11, 111);
		r.put(11, 112); 
		r.put(11, 113);
		r.put(12, 121);
		r.put(12, 122); //kept
		r.put(122, 1221); //kept
		r.put(1221, 12211); //kept
		r.put(12211, 122111); 
		r.put(12211, 122112); //kept
		r.put(122112, 122112); //loop!
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size()); 
				
		//circle --------------------------
		r = newIntRelation(10);
		
		r.put(1, 2); 
		r.put(2, 3); 
		r.put(3, 4); 
		r.put(4, 1); 
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size());
		
		//pseudo-tree with inner cycle and cross-links --------------------------
		r = newIntRelation(10);
		
		r.put(1, 11); 
		r.put(1, 12); //kept
		r.put(11, 111); 
		r.put(11, 112); 
		r.put(11, 113); 
		r.put(12, 121); 
		r.put(12, 122);  //live
		r.put(122, 11); //cross-link 
		r.put(122, 1221); //live 
		r.put(122, 1222); 
		r.put(1221, 12211); 
		r.put(1221, 12); //backlink!
		r.put(1222, 113); //cross-link 
		r.put(12211, 122111); 
		r.put(12211, 122112); 
		
		c = findCycles(r);
		assertEquals(1, c);
		//assertEquals(0, r.size());
	}

}
