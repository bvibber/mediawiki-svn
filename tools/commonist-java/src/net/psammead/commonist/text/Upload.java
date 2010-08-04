package net.psammead.commonist.text;

import java.io.File;

import net.psammead.commonist.data.CommonData;
import net.psammead.commonist.data.ImageData;
import net.psammead.commonist.data.LicenseData;
import net.psammead.commonist.util.TextUtil2;
import net.psammead.mwapi.Location;

/** metadata of a single file upload */
public final class Upload {
	private final CommonData	common;
	private	final ImageData	image;
	private Upload	previous;
	private Upload	next;
	private Throwable	error;
	private Location	location;
	
	public Upload(CommonData common, ImageData image) {
		this.common	= common;
		this.image	= image;
		previous	= null;
		next		= null;
		location	= null;
		error		= null;
	}

	//------------------------------------------------------------------------------
	//## real properties
	
	public Upload getNext() {
		return next;
	}

	public void setNext(Upload next) {
		this.next = next;
	}

	public Upload getPrevious() {
		return previous;
	}

	public void setPrevious(Upload previous) {
		this.previous = previous;
	}

	public Location getLocation() {
		return location;
	}

	public void setLocation(Location location) {
		this.location = location;
	}
	
	public Throwable getError() {
		return error;
	}

	public void setError(Throwable error) {
		this.error = error;
	}
	
	//------------------------------------------------------------------------------
	//## synthetic differentiating properties
	
	public String getCommonDescription() {
		return common.description;
	}
	
	public String getCommonCategories() {
		return new ParsedCategories(common.categories).wikiText;
	}
	
	public String getIndividualDescription() {
		return image.description;
	}
	
	public String getIndividualCategories() {
		return new ParsedCategories(image.categories).wikiText;
	}
	
	//------------------------------------------------------------------------------
	//## synthetic properties
	
	public String getSource() {
		return common.source;
	}
	
	public String getDate() {
		return common.date;
	}
	
	public String getAuthor() {
		return common.author;
	}
	
	public LicenseData getLicense() {
		return common.license;
	}
	
	public String getDescription() {
		return TextUtil2.joinNonEmpty(getCommonDescription(), getIndividualDescription(), "\n");
	}
	
	public String getCategories() {
		return TextUtil2.joinNonEmpty(getCommonCategories(), getIndividualCategories(), "\n");
	}
	
	public String getName() {
		return image.name;
	}

	public File getFile() {
		return image.file;
	}

	/** replaces forbidden characters with '_' */ 
	public String getTitle() {
		final char[]	chars	= image.name.toCharArray();
		for (int i=0; i<chars.length; i++) {
			char	c	= chars[i];
			if (c == ' '
			|| c < 32	|| c == 127
			|| c == '<' || c == '>'
			|| c == '[' || c == ']'
			|| c == '{' || c == '}'
			|| c == '|'
//			|| c == ':'	|| c == '?'
//			|| c == '/'	|| c == '\\'
//			|| c == '+'	|| c == '%'
			)
				chars[i]	= '_';
		}
		// if (out.getBytes("UTF-8") > 256) ...
		return new String(chars);
	}
}
