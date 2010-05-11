package de.brightbyte.wikiword.analyzer.template;

import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import de.brightbyte.data.MultiMap;
import de.brightbyte.data.ValueListMultiMap;
import de.brightbyte.util.StringUtils;
import de.brightbyte.wikiword.analyzer.AnalyzerUtils;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;
import de.brightbyte.xml.HtmlEntities;

public class FlatTemplateExtractor extends AbstractTemplateExtractor {
	
	private Matcher templateMarkerMatcher = Pattern.compile("\\{\\{([^|]+?)(?=\\||\\}\\}|\\{\\{)|\\}\\}").matcher("");
	private Matcher templateParamMatcher = Pattern.compile("\\||\\{\\{!\\}\\}").matcher("");

	public FlatTemplateExtractor(Context context, TextArmor armor) {
		super(context, armor);
	}

	public MultiMap<String, TemplateData, List<TemplateData>> extractTemplates(CharSequence text) {
		MultiMap<String, TemplateData, List<TemplateData>> templates = new ValueListMultiMap<String, TemplateData>();
		
		templateMarkerMatcher.reset(text);
		
		int len = text.length();
		int pos = 0;
		int level = 0;
		CharSequence current = null;
		StringBuilder buff = new StringBuilder();
		
		while (pos<len) {
			if (!templateMarkerMatcher.find(pos)) pos = len;
			else {
				String marker = templateMarkerMatcher.group(0);
				//System.out.println("@@@ "+pos+":"+m.start()+":"+m.end()+": "+marker);
				
				if (marker.charAt(0)=='{') {
					int p = templateMarkerMatcher.end();
					level ++;
					if (level==1) {
						current = templateMarkerMatcher.group(1);
						//System.out.println(">>> "+current);
						buff.setLength(0);
					}
					else {
						buff.append(text.subSequence(pos, templateMarkerMatcher.start()));
						//System.out.println(">++ "+text.substring(pos, m.start()));
					}
					
					pos = p;
				}
				else if (level>0) {
					level --;
					if (level==0) {
						int p = templateMarkerMatcher.end();
						buff.append(text.subSequence(pos, templateMarkerMatcher.start()));
						//System.out.println("<++ "+text.substring(pos, m.start()));
						pos =  p;
						
						if (current!=null && current.length()>0 && current.charAt(0)!='#') {
							TemplateData data = null;
							
							String name = current.toString();

							//check for magic template, like {{DISPLAYTITLE:foo}}
							//FIXME: duplicated in DeepTemplateExtractor
							int idx = name.indexOf(':'); 
							if (idx>0) {
								String n = name.substring(0, idx);
								n = getMagicTemplateId(n);
								
								if (n!=null) {
									CharSequence v = AnalyzerUtils.trim( name.substring(idx+1) );
									
									data = new TemplateData(n);
									data.setParameter("0", v);
									
									name = n;
								}
							}

							name = normalizeTitle(name).toString();
						
							//process only relevant templates
							if (data != null || isRelevantTemplate(name)) {
								if (buff.length()>0) {
									if (data==null) data = new TemplateData(name);
									splitTemplateParams(buff.substring(1), data);
								}
								else {
									if (data==null) data = new TemplateData(name);
								}
								
								templates.put(name, data);
								//System.out.println("<<< "+current);
							}
						}
					}
					else {
						pos =  templateMarkerMatcher.end();
						//System.out.println("<?? ");
					}
				}
				else {
					pos =  templateMarkerMatcher.end();
					//System.out.println("<?? ");
				}
			}
		}
		
		return templates ;
	}

	protected void splitTemplateParams(CharSequence p, TemplateData data) {
		int len = p.length();
		if (len==0) return ;
		
		p = stripMarkup(p, false);
		
		templateParamMatcher.reset(p);

		int pos = 0;
		int i = 1;
		while (pos<len) {
			CharSequence s;
			if (templateParamMatcher.find()) {
				s = p.subSequence(pos, templateParamMatcher.start());
				pos = templateParamMatcher.end();
			}
			else {
				s = p.subSequence(pos, p.length());
				pos= len;
			}
			
			CharSequence v;
			CharSequence k; 
			int idx = StringUtils.indexOf('=', s);
			if (idx<0) {	
				k = String.valueOf(i++);
				v = AnalyzerUtils.trim( s );
			}
			else {
				k = AnalyzerUtils.trim( s.subSequence(0, idx) );
				v = AnalyzerUtils.trim( s.subSequence(idx+1, s.length()) );
				
				try {
					i = Math.max(i, Integer.parseInt(k.toString())); //XXX: The Right Thing? Check MediaWiki's behavior
				}
				catch (NumberFormatException ex) { /*ignore*/ }
			}
			
			v = unarmor(v);
			
			k = HtmlEntities.decodeEntities(k);
			v = HtmlEntities.decodeEntities(v);
			data.setParameter(k.toString(), v);
		}
	}

}
