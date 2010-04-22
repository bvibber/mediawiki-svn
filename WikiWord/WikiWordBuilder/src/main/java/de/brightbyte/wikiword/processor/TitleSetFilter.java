package de.brightbyte.wikiword.processor;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.KeyValueLookup;
import de.brightbyte.data.KeyValueStore;
import de.brightbyte.data.Lookup;
import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.filter.LookupFilter;
import de.brightbyte.data.filter.StaticSetFilter;
import de.brightbyte.io.LineCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.builder.NameMaps;

public class TitleSetFilter extends PageTitleFilter {
	protected final static Integer ONE = new Integer(1);
	
	protected static Lookup<String, Integer> slurpCursor(DataCursor<String> titleCursor) throws PersistenceException {
		KeyValueStore<String, Integer> store = NameMaps.<Integer>newStore("string", "en"); //XXX: language...
		
		String s;
		while ((s = titleCursor.next()) != null) {
			store.put(s, ONE);
		}
		
		if (store instanceof Lookup) return (Lookup<String, Integer>)store;
		else return new KeyValueLookup<String, Integer>(store);
	}

	protected static Lookup<String, Integer> slurpLines(File f, String enc) throws PersistenceException {
		try {
			InputStream in = new FileInputStream(f);
			LineCursor cursor = new LineCursor(in, enc);
			
			Lookup<String, Integer> r = slurpCursor( cursor );
			
			cursor.close();
			in.close();
			
			return r;
		}  catch (IOException e) {
			throw new PersistenceException(e);
		}
	}

	@SuppressWarnings("unchecked")
	public TitleSetFilter(String name, Lookup<String, Integer> titles) {
		super(name, new LookupFilter<CharSequence, Integer>(titles, ONE));
	}

	public TitleSetFilter(File titleFile, String enc) throws PersistenceException {
		this(titleFile.getName(), titleFile, enc);
	}
	
	public TitleSetFilter(String name, File titleFile, String enc) throws PersistenceException {
		this(name, slurpLines(titleFile, enc));
	}
	
	public TitleSetFilter(String name, DataCursor<String> titleCursor) throws PersistenceException {
		this(name, slurpCursor(titleCursor));
	}

}
