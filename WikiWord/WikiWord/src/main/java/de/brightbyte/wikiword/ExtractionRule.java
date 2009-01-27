package de.brightbyte.wikiword;


public enum ExtractionRule {
	TERM_FROM_LINK(10),     //old: 10
	TERM_FROM_DISAMBIG(30), //old: 50
	TERM_FROM_CAT_NAME(50), //old: 30  //for main articles on category subject (sortkey *, etc)
	TERM_FROM_SORTKEY(60),  //old: 40
	TERM_FROM_REDIRECT(80), //old: 60
	TERM_FROM_IDENTIFIER(88),    
	TERM_FROM_TITLE(90),    //old: 20
	BROADER_FROM_SUFFIX(110), 
	BROADER_FROM_CAT(120),  
	BROADER_FROM_SECTION(130),  
	;

	private int code;
	
	private ExtractionRule(int code) {
		this.code = code;
	}
	
	public int getCode() {
		return code;
	}

	public static ExtractionRule getRule(int code) {
		for (ExtractionRule v : values()) {
			if (code == v.getCode()) return v;
		}
		
		throw new IllegalArgumentException("unknown code "+code+" for enumeration "+ExtractionRule.class.getName());
	}
	
}
