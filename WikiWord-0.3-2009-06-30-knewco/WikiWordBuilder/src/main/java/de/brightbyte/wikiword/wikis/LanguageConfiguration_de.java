package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.LanguageConfiguration;

public class LanguageConfiguration_de extends LanguageConfiguration {

	public LanguageConfiguration_de() {
		sentenceTailGluePattern = Pattern.compile(
				"(?:^|\\s)(?:"+
                "Dr|Prof|Emerit|jur|h\\.c|[IVX]+|"+
                "z\\."+SPACE_CHARS+"?B|zB|"+
                "z\\."+SPACE_CHARS+"?T|zT|"+
                "u\\."+SPACE_CHARS+"?U|"+
                "u\\."+SPACE_CHARS+"?A|"+
                "v\\."+SPACE_CHARS+"?Chr?|"+
                "z\\."+SPACE_CHARS+"?Zt?|zZt|"+
                "i\\."+SPACE_CHARS+"?A|i\\."+SPACE_CHARS+"?d\\."+SPACE_CHARS+"?R|"+
                "resp|bzw|evtl|ca|et\\."+SPACE_CHARS+"al|"+
                "eng|engl|fr|frant|lat|latein|gr|griech|"+
                "syn|var|"+
                "St|Hl|Nr|"+
                "z|v|u|o|Chr|"+
                "\\d{1,2}|"+
                "\\p{L}(?:\\.\\p{L})+|\\p{L}*\\p{Lu}"+ //XXX: gives a lot of false positives!
                ")$"
		);
	}

}
