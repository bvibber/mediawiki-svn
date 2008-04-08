package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.util.ArrayList;
import java.util.BitSet;
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
	protected static String[] toLatin = null, toCyrillic = null;
	protected static HashMap<String,String> toCyrillicMap = null;
	protected static BitSet toCyrillicTwo = null; // pairs of two chars
	protected Token nextToken;
	protected boolean aliasDiff;
	
	public static synchronized void init(){
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
	
	public static synchronized void initVariants(){
		toLatin = new String[65536];
		toCyrillic = new String[65536];
		toCyrillicMap = new HashMap<String,String>();
		toCyrillicTwo = new BitSet();
		
		for(int i=0;i<65536;i++){
			toLatin[i] = null;
			toCyrillic[i] = null;
		}
		
		toLatin['а']="a"; toLatin['б']="b";  toLatin['в']="v"; toLatin['г']="g";  toLatin['д']="d";
		toLatin['ђ']="đ"; toLatin['е']="e";  toLatin['ж']="ž"; toLatin['з']="z";  toLatin['и']="i";
		toLatin['ј']="j"; toLatin['к']="k";  toLatin['л']="l"; toLatin['љ']="lj"; toLatin['м']="m";
		toLatin['н']="n"; toLatin['њ']="nj"; toLatin['о']="o"; toLatin['п']="p";  toLatin['р']="r";
		toLatin['с']="s"; toLatin['т']="t";  toLatin['ћ']="ć"; toLatin['у']="u";  toLatin['ф']="f";
		toLatin['х']="h"; toLatin['ц']="c";  toLatin['ч']="č"; toLatin['џ']="dž"; toLatin['ш']="š";

		toLatin['А']="A"; toLatin['Б']="B";  toLatin['В']="V"; toLatin['Г']="G";  toLatin['Д']="D";
		toLatin['Ђ']="Đ"; toLatin['Е']="E";  toLatin['Ж']="Ž"; toLatin['З']="Z";  toLatin['И']="I";
		toLatin['Ј']="J"; toLatin['К']="K";  toLatin['Л']="L"; toLatin['Љ']="Lj"; toLatin['М']="M";
		toLatin['Н']="N"; toLatin['Њ']="Nj"; toLatin['О']="O"; toLatin['П']="P";  toLatin['Р']="R";
		toLatin['С']="S"; toLatin['Т']="T";  toLatin['Ћ']="Ć"; toLatin['У']="U";  toLatin['Ф']="F";
		toLatin['Х']="H"; toLatin['Ц']="C";  toLatin['Ч']="Č"; toLatin['Џ']="Dž"; toLatin['Ш']="Š";
		
		toCyrillic['a']="а"; toCyrillic['b']="б"; toCyrillic['c']="ц"; toCyrillic['č']="ч"; toCyrillic['ć']="ћ";
		toCyrillic['d']="д"; toCyrillic['đ']="ђ"; toCyrillic['e']="е"; toCyrillic['f']="ф";
		toCyrillic['g']="г"; toCyrillic['h']="х"; toCyrillic['i']="и"; toCyrillic['j']="ј"; toCyrillic['k']="к";
		toCyrillic['l']="л"; toCyrillic['m']="м"; toCyrillic['n']="н"; 
		toCyrillic['o']="о"; toCyrillic['p']="п"; toCyrillic['r']="р"; toCyrillic['s']="с"; toCyrillic['š']="ш";
		toCyrillic['t']="т"; toCyrillic['u']="у"; toCyrillic['v']="в"; toCyrillic['z']="з"; toCyrillic['ž']="ж";

		toCyrillic['A']="А"; toCyrillic['B']="Б"; toCyrillic['C']="Ц"; toCyrillic['Č']="Ч"; toCyrillic['Ć']="Ћ";
		toCyrillic['D']="Д"; toCyrillic['Đ']="Ђ"; toCyrillic['E']="Е"; toCyrillic['F']="Ф";
		toCyrillic['G']="Г"; toCyrillic['H']="Х"; toCyrillic['I']="И"; toCyrillic['J']="Ј"; toCyrillic['K']="К";
		toCyrillic['L']="Л"; toCyrillic['M']="М"; toCyrillic['N']="Н"; 
		toCyrillic['O']="О"; toCyrillic['P']="П"; toCyrillic['R']="Р"; toCyrillic['S']="С"; toCyrillic['Š']="Ш";
		toCyrillic['T']="Т"; toCyrillic['U']="У"; toCyrillic['V']="В"; toCyrillic['Z']="З"; toCyrillic['Ž']="Ж";

		toCyrillicMap.put("DŽ","Џ"); toCyrillicMap.put("Lj","Љ"); toCyrillicMap.put("Nj","Њ"); 
		toCyrillicMap.put("LJ","Љ"); toCyrillicMap.put("Dž","Џ"); toCyrillicMap.put("nj","њ");
		toCyrillicMap.put("dž","џ"); toCyrillicMap.put("lj","љ"); toCyrillicMap.put("NJ","Њ");
		
		toCyrillicTwo.set('D'); toCyrillicTwo.set('d'); toCyrillicTwo.set('Ž'); toCyrillicTwo.set('ž');
		toCyrillicTwo.set('L'); toCyrillicTwo.set('l'); toCyrillicTwo.set('J'); toCyrillicTwo.set('j');
		toCyrillicTwo.set('N'); toCyrillicTwo.set('n');
	}
	
	/** get latin and cyrillic variant of the text */
	public static ArrayList<String> getVariants(String text){
		if(toLatin == null || toCyrillic==null)
			initVariants();
		if(text.length() == 0)
			return null;
		else if(text.length() == 1){
			ArrayList<String> ret = new ArrayList<String>();
			String l = toLatin[text.charAt(0)];
			if(l != null)
				ret.add(l);
			String c = toCyrillic[text.charAt(0)];
			if(c != null)
				ret.add(c);
			return ret;
		}
		StringBuilder lat = new StringBuilder();
		StringBuilder cyr = new StringBuilder();
		char c='\0', c1=text.charAt(0);
		for(int i=1;i<text.length()+1;i++){
			c = c1;
			c1 = i<text.length()? text.charAt(i) : '\0';
			String l = toLatin[c];
			if(l != null)
				lat.append(l);
			else
				lat.append(c);			
		}

		c='\0'; c1=text.charAt(0);
		for(int i=1;i<text.length()+1;i++){
			c = c1;
			c1 = i<text.length()? text.charAt(i) : '\0';
			String cl = null;
			// quick check if we should try the two-letter map
			if(toCyrillicTwo.get(c) && toCyrillicTwo.get(c1))
				cl = toCyrillicMap.get(""+c+c1);

			if(cl != null){
				i++;
				c = c1;
				c1 = i<text.length()? text.charAt(i) : '\0';
			} else  // single letter map
				cl = toCyrillic[c];
			if(cl != null)
				cyr.append(cl);
			else
				cyr.append(c);
		}
		ArrayList<String> ret = new ArrayList<String>();
		ret.add(lat.toString());
		ret.add(cyr.toString());
		return ret;
	}
	
	/** Convert to ascii */
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
