package de.brightbyte.wikiword.processor;

import java.io.File;
import java.io.FileInputStream;
import java.io.IOException;
import java.io.InputStream;
import java.util.Map;
import java.util.Set;

import de.brightbyte.data.cursor.DataCursor;
import de.brightbyte.data.filter.StaticSetFilter;
import de.brightbyte.io.LineCursor;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.builder.NameMaps;

public class TitleSetFilter extends PageTitleFilter {
	
	protected static Set<String> slurpCursor(DataCursor<String> titleCursor) throws PersistenceException {
		Map<String, Integer> map = NameMaps.newMap("hash");
		
		final Integer ONE = new Integer(1);
		
		String s;
		while ((s = titleCursor.next()) != null) {
			map.put(s, ONE);
		}
		
		return map.keySet();
	}

	protected static Set<String> slurpLines(File f, String enc) throws PersistenceException {
		try {
			InputStream in = new FileInputStream(f);
			LineCursor cursor = new LineCursor(in, enc);
			
			Set<String> r = slurpCursor( cursor );
			
			cursor.close();
			in.close();
			
			return r;
		}  catch (IOException e) {
			throw new PersistenceException(e);
		}
	}

	@SuppressWarnings("unchecked")
	public TitleSetFilter(String name, Set titles) {
		super(name, new StaticSetFilter<CharSequence>(titles));
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
