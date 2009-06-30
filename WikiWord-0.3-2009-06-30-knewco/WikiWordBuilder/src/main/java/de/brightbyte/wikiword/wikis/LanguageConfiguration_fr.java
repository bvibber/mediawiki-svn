package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.LanguageConfiguration;

public class LanguageConfiguration_fr extends LanguageConfiguration {

	//TODO: list of abbreviations 
	public LanguageConfiguration_fr() {
		sentenceTailGluePattern = Pattern.compile(
				"(?:^|\\s)(?:"+
                "Dr|Prof|Emerit|jur|h\\.c|[IVX]+|"+ //TODO: more!
                /*"z\\."+SPACE_CHARS+"?B|zB|"+
                "u\\."+SPACE_CHARS+"?U|"+
                "u\\."+SPACE_CHARS+"?A|"+
                "v\\."+SPACE_CHARS+"?Chr?|"+
                "z\\."+SPACE_CHARS+"?Zt?|zZt|"+
                "i\\."+SPACE_CHARS+"?A|i\\."+SPACE_CHARS+"?d\\."+SPACE_CHARS+"?R|"+
                "resp|bzw|evtl|ca|et\\."+SPACE_CHARS+"al|"+
                "eng|engl|fr|frant|lat|latein|gr|griech|"+
                "St|Nr|"+*/
                "\\d{1,2}|"+
                "\\p{L}(?:\\.\\p{L})+|\\p{L}*\\p{Lu}"+ //XXX: gives a lot of false positives!
                ")$"
		);
	}

}
