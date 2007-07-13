package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.HashMap;

import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenFilter;
import org.apache.lucene.analysis.TokenStream;

/**
 * Filter for Serbian dual-script text. Convert tokens to latin 
 * lowercase without diacritics, but preserve the original token 
 * if different (i.e. in cyrillic)
 * 
 * @author rainman
 * 
 */
public class SerbianFilter extends TokenFilter {
	private static final int MAX_WORD_LEN = 255;
	private final char[] buffer = new char[MAX_WORD_LEN+1];
	private int length;
	protected static String[] conv = null;
	protected Token nextToken;
	protected boolean aliasDiff;
	
	public void init(){
		conv = new String[65536];
		
		for(int i=0;i<65536;i++)
			conv[i] = null;
		
		conv['а'] = "a"; conv['б'] = "b";  conv['в'] = "v"; conv['г'] = "g";  conv['д'] = "d";
		conv['ђ'] = "dj"; conv['е'] = "e";  conv['ж'] = "z"; conv['з'] = "z";  conv['и'] = "i";
		conv['ј'] = "j"; conv['к'] = "k";  conv['л'] = "l"; conv['љ'] = "lj"; conv['м'] = "m";
		conv['н'] = "n"; conv['њ'] = "nj"; conv['о'] = "o"; conv['п'] = "p";  conv['р'] = "r";
		conv['с'] = "s"; conv['т'] = "t";  conv['ћ'] = "c"; conv['у'] = "u";  conv['ф'] = "f";
		conv['х'] = "h"; conv['ц'] = "c";  conv['ч'] = "c"; conv['џ'] = "dz"; conv['ш'] = "s";
		
		conv['А'] = "A"; conv['Б'] = "B";  conv['В'] = "V"; conv['Г'] = "G";  conv['Д'] = "D";
		conv['Ђ'] = "Dj"; conv['Е'] = "E";  conv['Ж'] = "Z"; conv['З'] = "Z";  conv['И'] = "I";
		conv['Ј'] = "J"; conv['К'] = "K";  conv['Л'] = "L"; conv['Љ'] = "Lj"; conv['М'] = "M";
		conv['Н'] = "N"; conv['Њ'] = "Nj"; conv['О'] = "O"; conv['П'] = "P";  conv['Р'] = "R";
		conv['С'] = "S"; conv['Т'] = "T";  conv['Ћ'] = "C"; conv['У'] = "U";  conv['Ф'] = "F";
		conv['Х'] = "H"; conv['Ц'] = "C";  conv['Ч'] = "C"; conv['Џ'] = "Dz"; conv['Ш'] = "S";
		
		conv['đ'] = "dj"; conv['Đ']="Dj";
	}
	
	public String convert(String text){
		length = 0;
		String cv;
		boolean diff = false;
		aliasDiff = false;
		for(char c : text.toCharArray()){
			 cv = conv[c];
			 if(cv == null){
				 buffer[length++] = c;
			 } else{
				 for(char ch : cv.toCharArray()){
					 buffer[length++] = ch;
					 diff = true;
					 if( c != 'đ' && c != 'Đ')
						 aliasDiff = true;
				 }
			 }
		}
		if(diff)
			return new String(buffer,0,length);
		else // if no conversion, return the original object
			return text;
	}
	
	public SerbianFilter(TokenStream input) {
		super(input);
		if( conv == null )
			init();
		nextToken = null;
	}

	@Override
	public Token next() throws IOException {
		Token token;
		
		if(nextToken != null){
			token = nextToken;
			nextToken = null;
			return token;
		}
		
		token = input.next();
		if (token == null)
			return null;
		else {
			String s = convert(token.termText());
			if( s != token.termText()){ // pointer comparison
				if(!aliasDiff){ // if diff is only đ we don't need an alias
					token = new Token(s,token.startOffset(),token.endOffset());
					return token;
				}
				nextToken = new Token(s,token.startOffset(),token.endOffset(),"alias");
				nextToken.setPositionIncrement(0);
			}			
			return token;
		}
	}

}
