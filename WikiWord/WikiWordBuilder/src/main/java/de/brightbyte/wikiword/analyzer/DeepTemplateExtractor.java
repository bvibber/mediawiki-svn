package de.brightbyte.wikiword.analyzer;

import java.io.File;
import java.io.IOException;
import java.text.ParsePosition;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.audit.DebugUtil;
import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueListMultiMap;
import de.brightbyte.data.ValueSetMultiMap;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.IOUtil;
import de.brightbyte.util.SubstringMatcher;
import de.brightbyte.xml.HtmlEntities;

public class DeepTemplateExtractor extends AbstractTemplateExtractor {
	
	public static final Factory factory = new Factory() {
		public TemplateExtractor newTemplateExtractor(Context context, AbstractAnalyzer.TextArmor armor) {
			return new DeepTemplateExtractor(context, armor);
		}
	};
	
	public DeepTemplateExtractor(Context context, AbstractAnalyzer.TextArmor armor) {
		super(context, armor);
	}

	public MultiMap<String, TemplateData, List<TemplateData>> extractTemplates(CharSequence text) {
		documentScanner.reset(text);
		markerScanner.reset(text);
		pos.setIndex(0);
		this.templates = null;
		
		parseDocument();
		
		if (templates==null) templates = ValueListMultiMap.empty();
		return templates ;
	}

	protected ParsePosition pos = new ParsePosition(0);
	protected SubstringMatcher documentScanner = new SubstringMatcher(pos, "{{"); 
	protected SubstringMatcher markerScanner = new SubstringMatcher(pos, "{{", "}}", "|", "[[", "]]", "="); 
	protected MultiMap<String, TemplateData, List<TemplateData>> templates;
	protected MultiMap<CharSequence, CharSequence, Set<CharSequence>> containerFields = null;
	
	protected void parseDocument() {
		
		while (documentScanner.find()) {
			parseTemplate(null);
		}
	}

	protected boolean isRelevantTemplate(String prefix, String name) {
		if (prefix!=null) name = prefix+"::"+name;
		return isRelevantTemplate(name);
	}
	
	protected void putTemplate(String prefix, String name, TemplateData data) {
		if (prefix!=null) name = prefix+"::"+name;
		
		if (data==null) data = TemplateData.empty;
		if (templates==null) templates = new ValueListMultiMap<String, TemplateData>();
		templates.put(name, data);
	}
	
	protected void parseTemplate(String prefix) {
		String name = parseName();
		if (name==null) return; //fail
		
		TemplateData data = null;
		
		//check for magic template, like {{DISPLAYTITLE:foo}}
		//FIXME: duplicated in FlatTemplateExtractor
		int idx = name.indexOf(':'); 
		if (idx>0) {
			String n = name.substring(0, idx);
			n = getMagicTemplateId(n);
			
			if (n!=null) {
				CharSequence v = AbstractAnalyzer.trim( name.substring(idx+1) );
				
				data = new TemplateData();

				v = HtmlEntities.decodeEntities(v);
				data.setParameter("0", v);

				name = n;
				putTemplate(prefix, name, data);
			}
		}

		name = normalizeTitle(name).toString();
		
		boolean interresting = data!=null || isRelevantTemplate(prefix, name);
		
		while (true) {
			int m = markerScanner.matchedGroup(); //last thing seen by parseName() or parseParameter(), should be "}}" or "|"
			
			if (m<=0) { //EOF
				return; //fail, unexpected eof
			}
			else if (m!=3 && m!=2) { //not |
				throw new RuntimeException("Oops! Parser state got confuddeled!");
			}
			
			if (interresting) {
				if (data==null) {
					if (m==2) data = TemplateData.empty;
					else data = new TemplateData();
					
					putTemplate(prefix, name, data);
				}
			}

			if (m==2) { //}}
				//end of template found
				return;
			}
			
			parseParameter(name, data);
		}
		
		
	}
	
	protected String parseName() {
		int start = pos.getIndex();
		int end = -1;
		
		loop:
		while (markerScanner.find()) {
			int m = markerScanner.matchedGroup();
			
			switch (m) {
			case 1: //{{
				parseTemplate(null); //recurse
				break;
			case 2: //}}
			case 3: //|
				end = markerScanner.start(); //name complete
				break loop;
			case 4: //[[
			case 5: //]]
				break loop; //fail
			case 6: //=
				//noop, literal
				break;
			}
		}
		
		if (end<=start) { //empty is NOT allowed
			return null; //fail
		}
		else {
			return markerScanner.getText().subSequence(start, end).toString();
		}
	}

	protected void parseParameter(String templateName, TemplateData data) {
		int start = pos.getIndex();
		int end = -1;
		int m = -1;
		
		CharSequence n = data==null ? null : data.nextParameterName();
		String prefix = getPrefix(templateName, n);
		
		loop:
		while (markerScanner.find()) {
			m = markerScanner.matchedGroup();
			
			switch (m) {
			case 1: //{{
				parseTemplate(prefix); //recurse
				break;
			case 2: //}}
			case 3: //|
			case 6: //=
				end = markerScanner.start(); //parameter complete
				break loop;
			case 4: //[[
				parseLink(prefix); //descend
				break; 
			case 5: //]]
				//noop, literal
				break; 
			}
		}
		
		if (end<start) { //empty is allowed
			return; //fail
		}
		else {
			if (data!=null) {
				n = markerScanner.getText().subSequence(start, end);
				n = AbstractAnalyzer.trim(n);
			}
			
			if (m!=6) { //no =, so not a named param
				if (data!=null) {
					n = stripMarkup(n, true);
					n = HtmlEntities.decodeEntities(n);
					
					data.addParameter(n); 
				}
				return;
			}
		}

		n = HtmlEntities.decodeEntities(n);
		prefix = getPrefix(templateName, n);
		
		//start another loop for parsing the value
		start = pos.getIndex();
		end = -1;
		
		loop:
		while (markerScanner.find()) {
			m = markerScanner.matchedGroup();
			
			switch (m) {
			case 1: //{{
				parseTemplate(prefix); //recurse
				break;
			case 2: //}}
			case 3: //|
				end = markerScanner.start(); //parameter complete
				break loop;
			case 4: //[[
				parseLink(prefix); //descend
				break; 
			case 5: //]]
			case 6: //=
				//noop, literal
				break; 
			}
		}
	
		CharSequence v = null;
		
		if (end<start) { //empty is allowed
			return; //fail
		}
		else {
			if (data!=null) {
				v = markerScanner.getText().subSequence(start, end);

				v = AbstractAnalyzer.trim(v);
				v = stripMarkup(v, true);
				
				v = HtmlEntities.decodeEntities(v);
				
				data.setParameter(n, v); 
			}
		}
		
	}

	private String getPrefix(CharSequence template, CharSequence parameter) {
		if (containerFields==null) return null;
		if (!containerFields.contains(template, parameter)) return null; 
			
		return template + "." + parameter;
	}
	
	public void addContainerField(CharSequence template, CharSequence parameter) {
		if (containerFields==null) containerFields = new ValueSetMultiMap<CharSequence, CharSequence>();
		containerFields.put(template, parameter);
	}

	private void parseLink(String prefix) {
		int m;
		
		loop:
		while (markerScanner.find()) {
			m = markerScanner.matchedGroup();
			
			switch (m) {
			case 1: //{{
				parseTemplate(prefix); //descend
				break;
			case 2: //}}
				//assume missing ]], end of link.
				markerScanner.setIndex(markerScanner.start()); //pushback
				break loop;
			case 3: //|
			case 6: //=
				//noop, literal
				break; 
			case 4: //[[
				parseLink(prefix); //recurse //XXX: this shouldn't happen before the first |
				break loop;
			case 5: //]]
				break loop; //end of link
			}
		}
		
	}
	
	public static void main(String[] args) throws IOException {
		String t = IOUtil.slurp(new File(args[0]), "UTF-8");
		
		final Matcher relevant = Pattern.compile("Chembox Identifiers|Chembox new").matcher("");
		//extractor.documentScanner.setTrace(ConsoleIO.output);
		//extractor.markerScanner.setTrace(ConsoleIO.output);
		
		TemplateExtractor.Context context = new TemplateExtractor.Context() {
			
			public CharSequence stripMarkup(CharSequence p) {
				return p.toString().trim().replaceAll("(?s:\\s+)|<br([^\\w].*?)?>", " ").replaceAll("\\[\\[.*?\\]\\]|\\{\\{.*?\\}\\}|<.*?>", "").replaceAll("\\{\\{.*?\\}\\}", "");
			}
		
			public CharSequence normalizeTitle(CharSequence name) {
				return name.toString().trim();
			}
		
			public String getMagicTemplateId(CharSequence n) {
				if (n.equals("DISPLAYTITLE")) return n.toString();
				return null;
			}

			public boolean isRelevantTemplate(CharSequence name) {
				relevant.reset(name);
				return relevant.matches();
			}
		
		};
		
		DeepTemplateExtractor extractor = new DeepTemplateExtractor(context, new AbstractAnalyzer.TextArmor());
				
		MultiMap<String, TemplateData, List<TemplateData>> templates = extractor.extractTemplates(t);
		
		DebugUtil.dump("", templates, ConsoleIO.output);
	}

}
