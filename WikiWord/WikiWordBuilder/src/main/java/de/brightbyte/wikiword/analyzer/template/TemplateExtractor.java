package de.brightbyte.wikiword.analyzer.template;

import java.util.List;

import de.brightbyte.data.MultiMap;
import de.brightbyte.wikiword.analyzer.mangler.TextArmor;

public interface TemplateExtractor {
	public interface Context {
		public CharSequence normalizeTitle(CharSequence name);
		public String getMagicTemplateId(CharSequence n);
		public CharSequence stripMarkup(CharSequence p);
		public boolean isRelevantTemplate(CharSequence name);
	}
	
	public MultiMap<String, TemplateData, List<TemplateData>> extractTemplates(CharSequence text);
}
