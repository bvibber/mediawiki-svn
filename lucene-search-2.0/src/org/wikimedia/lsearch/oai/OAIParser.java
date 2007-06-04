package org.wikimedia.lsearch.oai;


import java.io.IOException;
import java.io.InputStream;

import javax.xml.parsers.ParserConfigurationException;
import javax.xml.parsers.SAXParser;
import javax.xml.parsers.SAXParserFactory;

import org.mediawiki.importer.Title;
import org.mediawiki.importer.XmlDumpReader;
import org.xml.sax.Attributes;
import org.xml.sax.SAXException;
import org.xml.sax.helpers.DefaultHandler;

/**  
 * Parses OAI-PMH xml messages. Parsing of embedded MediaWiki 
 * tags is handled by XmlDumpReader. 
 * 
 * How XmlDumpReader is used is as follows: this class
 * cuts out the parts of xml file that are OAI specific, 
 * and glues together all the mediawiki pages into what
 * appears as a continious stream. For this stream
 * calls to sax parser methods are delegated to XmlDumpReader.
 * 
 * Note: implementation is very lazy and messy :(
 * 
 * @author rainman
 *
 */
public class OAIParser extends DefaultHandler {
	protected XmlDumpReader dumpReader;
	protected InputStream in;
	protected IndexUpdatesCollector collector;
	/** parsing state */
	protected boolean inRecord, inHeader, inMetadata, inResponseDate;
	protected boolean inDump, inIdentifier, inResumptionToken;
	protected String oaiId,pageId,resumptionToken,responseDate;
	protected boolean beginMW; // beginning of mediawiki stream
	protected String mwUri, mwLocalName, mwQName;
	protected boolean isDeleted, inReferences, inRedirect, inRedirectTitle, inRedirectRef;
	protected String references, redirectTitle, redirectRef;
	
	
	public OAIParser(InputStream in, IndexUpdatesCollector collector){
		dumpReader = new XmlDumpReader(null,collector);
		this.in = in;
		this.collector = collector;
		inDump = false; inIdentifier = false; inResumptionToken = false;
		inRecord = false; inHeader = false; inMetadata = false;
		inResponseDate = false; inReferences = false;
		oaiId = ""; resumptionToken = ""; responseDate = "";
		beginMW = true; references = "";
		inRedirect = false; inRedirectTitle= false; inRedirectRef = false;
		redirectTitle = ""; redirectRef = "";
	}
	
	public void parse() throws IOException{
		try {
			SAXParserFactory factory = SAXParserFactory.newInstance();
			SAXParser parser = factory.newSAXParser();
	
			parser.parse(in, this);
		} catch (ParserConfigurationException e) {
			throw new IOException(e.getMessage());
		} catch (SAXException e) {
			throw new IOException(e.getMessage());
		}
		collector.close();
	}

	@Override
	public void startElement(String uri, String localName, String qName, Attributes attributes) throws SAXException {
		if(inDump && qName.equals("upload"))
			inDump = false; // mwdumper isn't parsing upload tag ... 
		else if(inDump && qName.equals("references")){
			inDump = false; // lsearch syntax
			inReferences = true;
			references = "";
		} else if(inDump && qName.equals("redirect")){ 
			inDump = false;
			inRedirect = true;
			redirectTitle = "";
			redirectRef = "";
		} else if(inDump)
			dumpReader.startElement(uri, localName, qName, attributes);
		else if(inRedirect && qName.equals("title"))
			inRedirectTitle = true;
		else if(inRedirect && qName.equals("references"))
			inRedirectRef = true;
		else if(qName.equals("record"))
			inRecord = true;
		else if(qName.equals("header") && inRecord){
			inHeader = true;
			String attr =  attributes.getValue("status");
			if(attr!=null && attr.equals("deleted"))
				isDeleted = true;
			else 
				isDeleted = false;
		} else if(qName.equals("identifier") && inHeader){
			oaiId = "";
			inIdentifier = true;
		} else if(qName.equals("metadata"))
			inMetadata = true;
		else if(qName.equals("mediawiki") && inMetadata){
			inDump = true;
			if(beginMW)
				dumpReader.startElement(uri, localName, qName, attributes);
			beginMW = false;
		} else if(qName.equals("resumptionToken")){
			resumptionToken = "";
			inResumptionToken = true;
		} else if(qName.equals("responseDate")){
			responseDate = "";
			inResponseDate = true;
		}
	}
	
	@Override
	public void endElement(String uri, String localName, String qName) throws SAXException {
		if(inDump && qName.equals("mediawiki")){
			inDump = false;
			// save this for end of stream
			mwUri = uri; mwLocalName = localName; mwQName = qName;			
		} else if(inDump)
			dumpReader.endElement(uri, localName, qName);
		else if(qName.equals("upload"))
			inDump = true; // we ignored upload tag / parsed references, we can now resume
		else if(!inRedirect && qName.equals("references")){
			inDump = true;
			inReferences = false;
			if(!references.equals(""))
				collector.addReferences(Integer.parseInt(references));
		} if(qName.equals("redirect")){
			inDump = true;
			int ref = 0;
			if(!redirectRef.equals(""))
				ref = Integer.parseInt(redirectRef);
			collector.addRedirect(redirectTitle,ref);
			inRedirect = false;
		} else if(inRedirect && qName.equals("title")) 
			inRedirectTitle = false;
		else if(inRedirect && qName.equals("references"))
			inRedirectRef = false;
		else if(qName.equals("record"))
			inRecord = false;
		else if(qName.equals("header"))
			inHeader = false;
		else if(qName.equals("metadata"))
			inMetadata = false;
		else if(qName.equals("identifier")){
			String[] parts = oaiId.split(":");
			if(parts.length!=4){
				System.out.println("Warning: unrecognized format of OAI id: "+oaiId);
			} else{
				pageId = parts[3];
				if(isDeleted)
					collector.addDeletion(Long.parseLong(pageId));
			}
			inIdentifier = false;
		} else if(qName.equals("resumptionToken"))
			inResumptionToken = false;
		else if(qName.equals("responseDate"))
			inResponseDate = false;
	}

	@Override
	public void characters(char[] ch, int start, int length) throws SAXException {
		if(inIdentifier){
			// we don't need to worry too much about perfomance here since ids are short
			oaiId += new String(ch,start,length); 
		} else if(inDump){
			dumpReader.characters(ch,start,length);
		} else if(inResumptionToken){
			resumptionToken += new String(ch,start,length);
		} else if(inResponseDate){
			responseDate += new String(ch,start,length);
		} else if(inReferences){
			references += new String(ch,start,length);
		} else if(inRedirectTitle){
			redirectTitle += new String(ch,start,length);
		} else if(inRedirectRef){
			redirectRef += new String(ch,start,length);
		}
	}
	

	@Override
	public void endDocument() throws SAXException {
		// send last mediawiki tag
		dumpReader.endElement(mwUri, mwLocalName, mwQName);
	}

	public String getResumptionToken() {
		return resumptionToken;
	}

	public IndexUpdatesCollector getCollector() {
		return collector;
	}

	public String getResponseDate() {
		return responseDate;
	}
	
	
	
}
