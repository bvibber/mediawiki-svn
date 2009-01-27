package de.brightbyte.wikiword.analyzer;


public abstract class AbstractTemplateExtractor implements TemplateExtractor {
	private Context context;
	private AbstractAnalyzer.TextArmor armor;
	
	public AbstractTemplateExtractor(Context context, AbstractAnalyzer.TextArmor armor) {
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
