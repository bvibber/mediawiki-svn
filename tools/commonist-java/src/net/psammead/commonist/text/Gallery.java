package net.psammead.commonist.text;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;
import java.util.Map;

import net.psammead.commonist.data.CommonData;
import net.psammead.commonist.util.TextUtil2;
import net.psammead.util.TextUtil;

/** manages the content and text of a gallery page */
public final class Gallery {
	private	final Templates		templates;
	private	final CommonData	common;
	private	final List<Upload>	uploads;
	
	public Gallery(Templates templates, CommonData common) {
		this.templates	= templates;
		this.common		= common;
		uploads	= new ArrayList<Upload>();
	}
	
	/** returns all Uploads added with the add method */
	public List<Upload> getUploads() {
		return uploads;
	}
	
	/** adds an Upload */
	public void addUpload(Upload upload) {
		uploads.add(upload);
	}
	
	/** whether no uploads are available */
	public boolean isEmpty() {
		return uploads.isEmpty();
	}

	/** sets previous and next properties of all currently known uploads */
	public void chain() {
		Upload	previous	= null;
		for (Iterator<Upload> it=uploads.iterator(); it.hasNext();) {
			final Upload	current	= it.next();
			if (previous != null) {
				previous.setNext(current);
			}
			current.setPrevious(previous);
			previous	= current;
		}
	}
	
	/** edit summary for writing a gallery */
	public String gallerySummary() {
		// TODO: add version number
		final int	failureCount	= failures().size();
		return "commonist upload" + 
				(failureCount != 0 ? " (" + failureCount + " errors)" : "");
	}
	
	/** compiles into wikitext */
	public String galleryDescription() throws TemplateException {
		final String	template	= "commons".equals(common.wiki)
							? "gallery_commons.bpp"
							: "gallery_default.bpp";
		final Map<String,List<Upload>>	data	= new HashMap<String,List<Upload>>();
		data.put("uploads",		uploads);
		data.put("successes",	successes());
		data.put("failures",	failures());
		final String	out	=	templates.applyTemplate(template, data); 
		return TextUtil.trimLF(TextUtil2.restrictEmptyLines(out));
	}
	
	/** compiles an image description into wikitext */
	public String imageDescription(Upload upload) throws TemplateException {
		final String	template	= "commons".equals(common.wiki)
			 				? "image_commons.bpp"
							: "image_default.bpp";
		
		final Map<String,Upload>	data	= new HashMap<String,Upload>();
		data.put("upload",	upload);
		final String	out	=	templates.applyTemplate(template, data); 
		return TextUtil.trimLF(TextUtil2.restrictEmptyLines(out));
	}
	
	/** returns all Uploads without an error */
	private List<Upload> successes() {
		final List<Upload>	out	= new ArrayList<Upload>();
		for (Iterator<Upload> it=uploads.iterator(); it.hasNext();) {
			final Upload	upload	= it.next();
			if (upload.getError() == null) {
				out.add(upload);
			}
		}
		return out;
	}

	/** returns all Uploads with an error */	
	private List<Upload> failures() {
		final List<Upload>	out	= new ArrayList<Upload>();
		for (Iterator<Upload> it=uploads.iterator(); it.hasNext();) {
			final Upload	upload	= it.next();
			if (upload.getError() != null) {
				out.add(upload);
			}
		}
		return out;
	}

	//------------------------------------------------------------------------------
	//## synthetic properties
	
	public String getParsedCategories() {
		return new ParsedCategories(common.categories).wikiText;
	}
}