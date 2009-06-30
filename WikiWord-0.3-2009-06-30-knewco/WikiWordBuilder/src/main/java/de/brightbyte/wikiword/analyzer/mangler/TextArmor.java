package de.brightbyte.wikiword.analyzer.mangler;

import java.util.ArrayList;
import java.util.Collections;
import java.util.Iterator;
import java.util.List;
import java.util.ListIterator;

import de.brightbyte.util.StringUtils;

/**
 * An TextArmor object allows an Armorer to associate placeholders the
 * the bits of text they replaced, and can be used later to put thoses
 * texts back into place, by replacing the placeholders. 
 */
public class TextArmor implements Iterable<ArmorEntry> {
	protected List<ArmorEntry> list;
	
	public void put(String marker, String value) {
		if (list==null) list = new ArrayList<ArmorEntry>();
		list.add(new ArmorEntry(marker, value));
	}
	
	public int size() {
		return list==null ? 0 : list.size();
	}
	
	public Iterator<ArmorEntry> iterator() {
		if (list==null) return Collections.<ArmorEntry>emptyList().iterator();
		
		final ListIterator<ArmorEntry> it = list.listIterator(list.size());
		
		//NOTE: inverse iterator!
		return new Iterator<ArmorEntry>(){
		
			public void remove() {
				it.remove();
			}
		
			public ArmorEntry next() {
				return it.previous();
			}
		
			public boolean hasNext() {
				return it.hasPrevious();
			}
		
		}; 
	}

	public CharSequence unarmor(CharSequence text) {
		if (text==null) return text;
		if (list==null) return text;
		
		//prescan and bypass
		int c = text.length();
		int i=0;
		for (; i<c; i++) {
			if (text.charAt(i) == Armorer.ARMOR_MARKER_CHAR) break;
		}
		
		if (i>=c) {
			return text;
		}
		
		//NOTE: armor must be undone in-sequence, in reverse order it was applied!
		
		for (ArmorEntry e: this) {
			text = StringUtils.replace(text, e.getMarker(), e.getValue());
		}
		
		return text;
	}
	
}