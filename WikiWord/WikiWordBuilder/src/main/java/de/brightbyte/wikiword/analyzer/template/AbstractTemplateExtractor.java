package de.brightbyte.wikiword.analyzer.template;

import de.brightbyte.wikiword.analyzer.mangler.TextArmor;


public abstract class AbstractTemplateExtractor implements TemplateExtractor {
	private Context context;
	private TextArmor armor;
	
	public AbstractTemplateExtractor(Context context, TextArmor armor) {
		if (context==null) throw new NullPointerException();
		if (armor==null) throw new NullPointerException();
		
		this.context = context;
		this.armor = armor;
	}

	protected String getMagicTemplateId(CharSequence n) {
		return context.getMagicTemplateId(n);
	}

	protected boolean isRelevantTemplate(CharSequence name) {
		return context.isRelevantTemplate(name);
	}

	protected CharSequence normalizeTitle(CharSequence name) {
		return context.normalizeTitle(name);
	}

	protected CharSequence stripMarkup(CharSequence p, boolean unarmor) {
		p = context.stripMarkup(p);
		if (unarmor) p = unarmor(p);
		return p;
	}
	
	protected CharSequence unarmor(CharSequence p) {
		p = armor.unarmor(p);
		return p;
	}
	
	
}
