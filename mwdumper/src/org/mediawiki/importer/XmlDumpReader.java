/*
 * MediaWiki import/export processing tools
 * Copyright 2005 by Brion Vibber
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * $Id$
 */

package org.mediawiki.importer;

import java.io.IOException;
import java.io.InputStream;
import java.util.Calendar;
import java.util.GregorianCalendar;
import java.util.TimeZone;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

public class XmlDumpReader  extends DefaultHandler {
	InputStream input;
	DumpWriter writer;
	
	private StringBuffer buffer;
	
	Siteinfo siteinfo;
	Page page;
	boolean pageSent;
	Contributor contrib;
	Revision rev;
	int nskey;
	
	/**
	 * Initialize a processor for a MediaWiki XML dump stream.
	 * Events are sent to a single DumpWriter output sink, but you
	 * can chain multiple output processors with a MultiWriter.
	 * @param inputStream Stream to read XML from.
	 * @param writer Output sink to send processed events to.
	 */
	public XmlDumpReader(InputStream inputStream, DumpWriter writer) {
		input = inputStream;
		this.writer = writer;
	}
	
	/**
	 * Reads through the entire XML dump on the input stream, sending
	 * events to the DumpWriter as it goes. May throw exceptions on
	 * invalid input or due to problems with the output.
	 * @throws IOException
	 */
	public void readDump() throws IOException {
		try {
			SAXParserFactory factory = SAXParserFactory.newInstance();
			SAXParser parser = factory.newSAXParser();
	
			parser.parse(input, this);
		} catch (ParserConfigurationException e) {
			throw new IOException(e.getMessage());
		} catch (SAXException e) {
			throw new IOException(e.getMessage());
		}
		writer.close();
	}
	
	// --------------------------
	// SAX handler interface methods:
	
	public void startElement(String uri, String localname, String qName, Attributes attributes) throws SAXException {
		// Clear the buffer for character data; we'll initialize it
		// if and when character data arrives -- at that point we
		// have a length.
		buffer = null;
		try {
			// frequent tags:
			if (qName.equals("revision")) openRevision();
			else if (qName.equals("contributor")) openContributor();
			else if (qName.equals("page")) openPage();
			// rare tags:
			else if (qName.equals("mediawiki")) openMediaWiki();
			else if (qName.equals("siteinfo")) openSiteinfo();
			else if (qName.equals("namespaces")) openNamespaces();
			else if (qName.equals("namespace")) openNamespace(attributes);
		} catch (IOException e) {
			throw new SAXException(e);
		}
	}
	
	public void characters(char[] ch, int start, int length) {
		if (buffer == null)
			buffer = new StringBuffer(length);
		buffer.append(ch, start, length);
	}
	
	public void endElement(String uri, String localname, String qName) throws SAXException {
		try {
			// frequent tags:
			if (qName.equals("id")) readId();
			else if (qName.equals("revision")) closeRevision();
			else if (qName.equals("timestamp")) readTimestamp();
			else if (qName.equals("text")) readText();
			else if (qName.equals("contributor")) closeContributor();
			else if (qName.equals("username")) readUsername();
			else if (qName.equals("ip")) readIp();
			else if (qName.equals("comment")) readComment();
			else if (qName.equals("minor")) readMinor();
			else if (qName.equals("page")) closePage();
			else if (qName.equals("title")) readTitle();
			else if (qName.equals("restrictions")) readRestrictions();
			// rare tags:
			else if (qName.equals("mediawiki")) closeMediaWiki();
			else if (qName.equals("siteinfo")) closeSiteinfo();
			else if (qName.equals("sitename")) readSitename();
			else if (qName.equals("base")) readBase();
			else if (qName.equals("generator")) readGenerator();
			else if (qName.equals("case")) readCase();
			else if (qName.equals("namespaces")) closeNamespaces();
			else if (qName.equals("namespace")) closeNamespace();
		} catch (IOException e) {
			throw new SAXException(e);
		}
	}

	// ----------
	
	void openMediaWiki() throws IOException {
		siteinfo = null;
		writer.writeStartWiki();
	}
	
	void closeMediaWiki() throws IOException {
		writer.writeEndWiki();
		siteinfo = null;
	}
	
	// ------------------
		
	void openSiteinfo() {
		siteinfo = new Siteinfo();
	}
	
	void closeSiteinfo() throws IOException {
		writer.writeSiteinfo(siteinfo);
	}

	private String bufferContents() {
		return buffer == null ? "" : buffer.toString();
	}
	
	void readSitename() {
		siteinfo.Sitename = bufferContents();
	}
	
	void readBase() {
		siteinfo.Base = bufferContents();
	}
	
	void readGenerator() {
		siteinfo.Generator = bufferContents();
	}
	
	void readCase() {
		siteinfo.Case = bufferContents();
	}
	
	void openNamespaces() {
		siteinfo.Namespaces = new NamespaceSet();
	}
	
	void openNamespace(Attributes attribs) {
		nskey = Integer.parseInt(attribs.getValue("key"));
	}
	
	void closeNamespace() {
		siteinfo.Namespaces.add(nskey, bufferContents());
	}

	void closeNamespaces() {
		// NOP
	}
	
	// -----------
	
	void openPage() {
		page = new Page();
		pageSent = false;
	}
	
	void closePage() throws IOException {
		if (pageSent)
			writer.writeEndPage();
		page = null;
	}
	
	void readTitle() {
		page.Title = new Title(bufferContents(), siteinfo.Namespaces);
	}
	
	void readId() {
		int id = Integer.parseInt(bufferContents());
		if (contrib != null)
			contrib.Id = id;
		else if (rev != null)
			rev.Id = id;
		else if (page != null)
			page.Id = id;
		else
			throw new IllegalArgumentException("Unexpected <id> outside a <page>, <revision>, or <contributor>");
	}
	
	void readRestrictions() {
		page.Restrictions = bufferContents();
	}
	
	// ------
	
	void openRevision() throws IOException {
		if (!pageSent) {
			writer.writeStartPage(page);
			pageSent = true;
		}
		
		rev = new Revision();
	}
	
	void closeRevision() throws IOException {
		writer.writeRevision(rev);
		rev = null;
	}

	void readTimestamp() {
		rev.Timestamp = parseUTCTimestamp(bufferContents());
	}

	void readComment() {
		rev.Comment = bufferContents();
	}

	void readMinor() {
		rev.Minor = true;
	}

	void readText() {
		rev.Text = bufferContents();
	}
	
	// -----------
	void openContributor() {
		contrib = null;
	}
	
	void closeContributor() {
		if (contrib == null)
			throw new IllegalArgumentException("Invalid contributor");
		
		rev.Contributor = contrib;
		contrib = null;
	}


	void readUsername() {
		contrib = new Contributor(bufferContents());
	}
	
	void readIp() {
		contrib = new Contributor(bufferContents());
	}
	
	private static final TimeZone utc = TimeZone.getTimeZone("UTC");
	private static Calendar parseUTCTimestamp(String text) {
		// 2003-10-26T04:50:47Z
		// We're doing this manually for now, though DateFormatter might work...
		String trimmed = text.trim();
		GregorianCalendar ts = new GregorianCalendar(utc);
		ts.set(
			Integer.parseInt(trimmed.substring(0,0+4)),     // year
			Integer.parseInt(trimmed.substring(5,5+2)) - 1, // month is 0-based!
			Integer.parseInt(trimmed.substring(8,8+2)),     // day
			Integer.parseInt(trimmed.substring(11,11+2)),   // hour
			Integer.parseInt(trimmed.substring(14,14+2)),   // minute
			Integer.parseInt(trimmed.substring(17,17+2)));  // second
		return ts;
	}
}
