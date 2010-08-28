package net.psammead.mwapi.net;

import java.net.MalformedURLException;
import java.net.URL;
import java.util.Collection;
import java.util.List;

import net.htmlparser.jericho.Attribute;
import net.htmlparser.jericho.Attributes;
import net.htmlparser.jericho.CharacterReference;
import net.htmlparser.jericho.Element;
import net.htmlparser.jericho.FormField;
import net.htmlparser.jericho.FormFields;
import net.htmlparser.jericho.HTMLElementName;
import net.htmlparser.jericho.Segment;
import net.htmlparser.jericho.Source;
import net.htmlparser.jericho.StartTag;
import net.htmlparser.jericho.StartTagType;
import net.htmlparser.jericho.Tag;

/** Jericho-HTML functions */
public final class JerichoUtil {
	/** fully static utility class, shall not be instantiated */
	private JerichoUtil() {}
	
	/** create a jericho Source logging to stderr */
	public static Source createSource(String html, net.psammead.util.Logger logger) {
		final Source	source	= new Source(html);
		source.setLogger(new JerichoLogger(logger));
		return source;
	}
	
	/** returns null if no elements exist */
	public static Element firstElement(Segment source, String tagName) {
		final List<Element> elements = source.getAllElements(tagName);
		return elements.isEmpty() ? null : elements.get(0);
	}
	
	public static  String firstElementText(Segment source, String tagName) {
		// TODO deprecated?
		return firstElement(source, tagName).getTextExtractor().toString();	// instead of getContent()
	}
	
	//-------------------------------------------------------------------------
	//## form helper for mandatory nodes
	
	public static Element fetchElementByAttributeValue(Segment parent, String tagName, String attributeName, String value) throws IllegalFormException {
		final Element element = findElementByAttributeValue(parent, tagName, attributeName, value);
		if (element == null)	throw new IllegalFormException("element not found: " + tagName + "[@" + attributeName + "=\"" + value + "\"]");
		return element;
	}
	
	public static Element fetchForm(Segment segment, String name, String id, int index) throws IllegalFormException {
		final Element	form	= findForm(segment, name, id, index);
		if (form == null)	throw new IllegalFormException("form not found: " + name);
		return form;
	}
	
	public static URL fetchActionURL(URL formURL, Element form) throws IllegalFormException {
		final String	actionPath	= fetchAttributeValue(form, "action");
		try {
			return new URL(formURL, actionPath);
		}
		catch (MalformedURLException e) {
			throw new IllegalFormException("action url is broken", e);
		}
	}
	
	public static boolean fetchBooleanField(FormFields fields, String name) throws IllegalFormException {
		final FormField	field	= fields.get(name);
		if (field == null)	throw new IllegalFormException("field not found: " + name);
		return field.getValues().size() != 0;
	}
	
	public static String fetchStringField(FormFields fields, String name) throws IllegalFormException {
		final FormField	field	= fields.get(name);
		if (field == null)		throw new IllegalFormException("field not found: " + name);
		final Collection<String>	values	= field.getValues();
		// TODO: a submit-field does not return a value :(
		if (values.size() == 0)	throw new IllegalFormException("values empty for field: " + name);
		return values.iterator().next();
	}

	public static String fetchAttributeValue(StartTag startTag, String name) throws IllegalFormException {
		final String	value	= startTag.getAttributeValue(name);
		if (value == null)	throw new IllegalFormException("attribute not found: " + name);
		return value;
	}
	
	public static String fetchAttributeValue(Element element, String name) throws IllegalFormException {
		final String	value	= element.getAttributeValue(name);
		if (value == null)	throw new IllegalFormException("attribute not found: " + name);
		return value;
	}
	
	//-------------------------------------------------------------------------
	//## find nodes or return null
	
	/** gets an element with a certain attribute or null */
	public static Element findElementByAttributeValue(Segment parent, String tagName, String attributeName, String value) {
		final List<Element>	children	= parent.getAllElements(tagName);
		for (Element child : children) {
			final Attributes	attributes	= child.getAttributes();
			if (attributes == null)	continue;
			final Attribute	attribute	= attributes.get(attributeName);
			if (attribute == null)	continue;
			if (value.equals(attribute.getValue()))	return child;
		}
		return null;
	}
	
	/**
	 * finds a form in a Source by its name, id or index or returns null if not found
	 * name and id may be null, index may be -1 to skip matching this kriterium 
	 */
	public static Element findForm(Segment segment, String name, String id, int index) {
		List<Element>	forms	= segment.getAllElements("form");
		if (name != null) {
			for (Element form : forms) {
				//if (name.equals(attributeValue(form.getStartTag(), "name")))	return form;
				if (name.equals(form.getAttributeValue("name")))	return form;
			}
		}
		if (id != null) {
			for (Element form : forms) {
				//if (id.equals(attributeValue(form.getStartTag(), "id")))		return form;
				if (id.equals(form.getAttributeValue("id")))		return form;
			}
		}
		if (index >= 0 && index < forms.size()) {
			return forms.get(index);
		}
		return null;
	}
	
	//-------------------------------------------------------------------------
	//## helper
	
	/** print out forms as parsed with jericho-html */
	public static void debugForms(String text) {
		final Source	source	= new Source(text);
		
		final List<Element>	formElements	= source.getAllElements("form");
		for (Element formElement : formElements) {
			final Attributes	formAttributes	= formElement.getStartTag().getAttributes();
			if (formAttributes == null)	continue;
			
			System.err.println("---------------------------------------------------");
			System.err.println("### " + formAttributes.get("name"));
			final FormFields	formFields	= formElement.getFormFields();
			for (FormField formField : formFields) {
				System.err.println(formField.getDebugInfo());
			}
		}
	
		/*
		// debug simple
		FormFields	formFields	= source.findFormFields();
		for (FormField formField : formFields) {
			print(formField.DebugInfo);
		}
		*/
	}
	
	/** like the TextExtractor, but without collapsing whitespace */ 
	public static String decodedTextOnly(Source source, Segment segment) {
		final StringBuffer sb=new StringBuffer(segment.length());
		int textBegin=segment.getBegin();
		final List<Tag> tags = segment.getAllTags();
		for (Tag tag : tags) {
			final int textEnd=tag.getBegin();
			if (textEnd<textBegin) continue;
			while (textBegin<textEnd) sb.append(source.charAt(textBegin++));
			if (tag.getTagType()==StartTagType.NORMAL) {
				final StartTag startTag=(StartTag)tag;
				if (tag.getName()==HTMLElementName.SCRIPT || tag.getName()==HTMLElementName.STYLE) {
					textBegin=startTag.getElement().getEnd();
					continue;
				}
			}
			textBegin=tag.getEnd();
		}
		while (textBegin<segment.getEnd()) sb.append(source.charAt(textBegin++));
		final String decodedText=CharacterReference.decode(sb,false);
		return decodedText;
	}
}
