/**
 * 
 */
package de.brightbyte.wikiword.analyzer.mangler;

import de.brightbyte.util.StringUtils;

public class ArmorEntry {
	protected String marker;
	protected String value;
	
	public ArmorEntry(String marker, String value) {
		if (marker==null) throw new NullPointerException();
		if (value==null) throw new NullPointerException();

		this.marker = marker;
		this.value = value;
	}
	
	public String getMarker() {
		return marker;
	}
	
	public String getValue() {
		return value;
	}
	
	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((marker == null) ? 0 : marker.hashCode());
		result = PRIME * result + ((value == null) ? 0 : value.hashCode());
		return result;
	}
	
	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (this.getClass() != obj.getClass())
			return false;
		final ArmorEntry other = (ArmorEntry) obj;
		if (marker == null) {
			if (other.marker != null)
				return false;
		} else if (!StringUtils.equals(marker, other.marker))
			return false;
		if (value == null) {
			if (other.value != null)
				return false;
		} else if (!StringUtils.equals(value, other.value))
			return false;
		return true;
	}
	
	@Override
	public String toString() {
		return marker + " => " + value;
	}
}