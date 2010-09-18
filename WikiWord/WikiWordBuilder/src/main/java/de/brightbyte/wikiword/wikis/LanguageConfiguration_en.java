package de.brightbyte.wikiword.wikis;

import java.util.regex.Pattern;

import de.brightbyte.wikiword.analyzer.LanguageConfiguration;

public class LanguageConfiguration_en extends LanguageConfiguration {

	//TODO: list of abbreviations
	public LanguageConfiguration_en() {
		sentenceTailGluePattern = Pattern.compile(
				"(?:^|\\s)(?:"+
                "Mr|Mrs|Dr|Prof|Emerit|jur|h\\.c|[IVX]+|"+
                "i\\."+SPACE_CHARS+"?e|ie|"+
                "g\\."+SPACE_CHARS+"?g|"+
                "resp|asap|ca|et\\."+SPACE_CHARS+"al|"+
                "abbr|Acad|alt|Assn|b|c|Capt|cent|co|Col|Co?mdr|"+
                "Corp|Cpl|d|dept|dist|div|ed|est|gal|Gen|Gov|grad|"+
                "Rt|Hon|in|inc|Inst|Jr|Sr|Lib|long|Lt|Ltd|mts?|Mus|"+
                "Op|pl|pop|pseud|pt|pub|[rR]ev|Ser|Sgt|Sr|"+
                "uninc|Univ|vol|vs|wt|"+
                "eng|engl|fr|germ?|lat|gr|griech|"+
                "St|no|"+
                "\\d{1,2}|"+
                "\\p{L}(?:\\.\\p{L})+|\\p{L}*\\p{Lu}"+ //XXX: gives a lot of false positives!
                ")$"
		);
		
		this.nameGluePattern = Pattern.compile("of|on|in|the"); // common non-capitalized components of proper nouns
	}

}
