package org.wikimedia.lsearch.analyzers;

import java.io.IOException;
import java.io.Serializable;
import java.io.UnsupportedEncodingException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Set;

import org.apache.log4j.Logger;
import org.apache.lucene.analysis.Token;
import org.apache.lucene.analysis.TokenStream;
import org.wikimedia.lsearch.util.Buffer;
import org.wikimedia.lsearch.util.UnicodeDecomposer;
import org.wikimedia.lsearch.util.Utf8Set;


/**
 * Extended information about the token, used for highlighting
 * 
 * @author rainman
 *
 */
public class ExtToken extends Token implements Serializable {
	static Logger log = Logger.getLogger(ExtToken.class);
	/** position within the text */
	public enum Position { NORMAL, FIRST_SECTION, TEMPLATE, IMAGE_CAT_IW, EXT_LINK, HEADING, REFERENCE, BULLETINS, TABLE };
	/** type of token */
	public enum Type { TEXT, GLUE, SENTENCE_BREAK, MINOR_BREAK, URL };
	protected Position pos = Position.NORMAL;
	protected Type type = Type.TEXT;
	/** if the token text is different, this will hold the original text for reconstruction */
	protected String original = null;
	/** serialized data for stub tokens */
	protected byte[] serialized = null; 
	/** position of term text in the serialized buffer if this is stub */
	protected int termTextStart = -1, termTextEnd = -1;
	/** pos of original in serialized for stubs */
	protected int originalStart = -1, originalEnd = -1;
	public enum CaseType { NONE, TITLE, UPPER };
	protected CaseType originalCase = CaseType.NONE;

	public ExtToken(){
		super("",0,0);
	}
	
	public ExtToken(String text, int start, int end) {
		super(text, start, end);
	}

	public ExtToken(String text, int start, int end, String typ) {
		super(text, start, end, typ);
	}
	
	public ExtToken(String text, int start, int end, Type type, Position pos) {
		super(text, start, end);
		this.type = type;
		this.pos = pos;
	}
	
	/** make stub */
	public ExtToken(byte[] serialized, int termStart, int termEnd, int start, int end, Type type, Position pos) {
		super(null, start, end);
		this.serialized = serialized;
		this.type = type;
		this.pos = pos;
		this.termTextStart = termStart;
		this.termTextEnd = termEnd;
	}
	
	public ExtToken(String text, int start, int end, Type type, Position pos, String original) {
		super(text, start, end);
		this.type = type;
		this.pos = pos;
		this.original = original;
	}
	
	public ExtToken(String text, int start, int end, String typ, Type type, Position pos) {
		super(text, start, end, typ);
		this.type = type;
		this.pos = pos;
	}
	
	public ExtToken(String text, int start, int end, String typ, Type type, Position pos, String original) {
		super(text, start, end, typ);
		this.type = type;
		this.pos = pos;
		this.original = original;
	}
	
	public Position getPosition() {
		return pos;
	}

	public void setPosition(Position pos) {
		this.pos = pos;
	}

	public Type getType() {
		return type;
	}

	public void setType(Type type) {
		this.type = type;
	}

	public String getOriginal() {
		return original;
	}

	public void setOriginal(String original) {
		this.original = original;
	}
	
	public void unstub(){
		if(isStub()){
			try {
				setTermText(new String(serialized,termTextStart,termTextEnd-termTextStart,"utf-8"));
				if(originalStart != -1 && originalEnd != -1){
					original = new String(serialized,originalStart,originalEnd-originalStart,"utf-8");
				} else if (originalCase == CaseType.TITLE)
					setOriginalInTitleCase();
				else if(originalCase == CaseType.UPPER)
					setOriginalInUpperCase();
			} catch (UnsupportedEncodingException e) {
				e.printStackTrace();
			}
		}
	}
	
	/** get text, original if available, otherwise termtext */
	public String getText(){
		if(isStub()){
			unstub();
		}
		if(original != null)
			return original;
		else
			return termText();
	}
	/** get length of the text (original or termtext) */
	public int getTextLength(){
		if(isStub()){
			if(originalStart != -1 && originalEnd != -1)
				return originalEnd - originalStart;
			else
				return termTextEnd - termTextStart;
		} else
			return getText().length();
	}
	
	public String toString(){
		return "\""+termText()+"\",t="+type+",p="+pos+(original!=null? ",o={"+original+"}" : "")+",i="+getPositionIncrement();
	}
	
		
	/**
	 * 
	 * Serialization 
	 * 
	 * @author rainman
	 *
	 */
	

	/** Serialize a token stream for highlight storage */
	public static byte[] serialize(TokenStream tokens) throws IOException{
		Buffer b = new Buffer();
		Position pos = Position.NORMAL, lastPos = null;
		Type lastType = null;
		boolean wroteFirst = false;
		
		Token tt = null;
		while((tt = tokens.next()) != null){
			lastPos = pos;
			
			ExtToken t = null;
			if(tt instanceof ExtToken)
				t = (ExtToken)tt;
			else{
				t = new ExtToken(tt.termText(),tt.startOffset(),tt.endOffset(),Type.TEXT,lastPos,null);
				t.setPositionIncrement(tt.getPositionIncrement());
			}			
			pos = t.pos;
			
			//System.out.println("TOKEN: "+t);
			
			if(!wroteFirst){
				if(t.type == Type.GLUE && t.termText().equals(" "))
					continue; // ignore first spaces				
				//b.write((byte)t.type.ordinal());
				b.write((byte)t.pos.ordinal());
				wroteFirst = true;
			}
			
			// control 8: two adjecent glue tokens
			/* if(t.type != Type.TEXT && t.type != Type.URL && lastType != Type.TEXT && lastType != Type.URL){
				b.writeControl(8);
			} */
			
			// control 5: URLs
			if(t.type == Type.URL){
				b.writeControl(5);
				b.writeStringWithLength(t.termText());
			} else {				
				if(t.getPositionIncrement() != 0){					
					b.writeString(t.termText());
					// control 9: end of term text
					b.writeControl(9);
				}
			}
			
			if(t.getPositionIncrement() > 0 && t.original != null){
				String w  = t.termText();
				if(t.original.equals(w.substring(0,1).toUpperCase()+w.substring(1))){
					// control 6: original is title case
					b.writeControl(6);
				} else if(t.original.equals(w.toUpperCase())){
					// control 7: original is upper case
					b.writeControl(7);
				} else{
					// control 1: original word
					b.writeControl(1);
					b.writeStringWithLength(t.original);
				}
			}
			// control 2: alias
			if(t.getPositionIncrement() == 0){
				b.writeControl(2);
				b.writeStringWithLength(t.termText());
			}
			// control 3: change in position
			if(lastPos != pos){
				b.writeControl(3);
				b.write((byte)pos.ordinal());
			}
			// control 4: sentence break
			if(t.type == Type.SENTENCE_BREAK){
				b.writeControl(4);
			}
			
			lastType = t.type;
				
		}		
		return b.getBytes();
	}
	
	
	private static int findControl(byte[] b, int cur){
		for(int i=cur;i<b.length;i++){
			if(b[i] == (byte)0xff)
				return i;
		}
		return b.length;
	}

	/**
	 * 
	 * Deserialization stuff
	 * 
	 */
	
	protected static UnicodeDecomposer decomposer = null;
	
	enum ParseType { TEXT, NUMBER, GAP};
	
	/** c - current char, prev - previous char */
	private static boolean isText(char c, char prev, char next){
		return Character.isLetterOrDigit(c) || (c=='\'' && Character.isLetter(prev) && Character.isLetter(next)) || decomposer.isCombiningChar(c); 
	}
	
	/** Get a single token from a string, beginning at position inx */
	private static ExtToken getToken(String s, int inx){
		Type type;
		char c = s.charAt(inx);
		char c1 = '\0';
		if(inx + 1 < s.length())
			c1 = s.charAt(inx+1);
		if(isText(c,'\0',c1))
			type = Type.TEXT;
		else if(FastWikiTokenizerEngine.isMinorBreak(c))
			type = Type.MINOR_BREAK;
		else
			type = Type.GLUE;
		
		int start = inx;
		int end = s.length();
		
		char prev = c;
		for(int i=inx+1;i<s.length();i++,prev=c){
			c = s.charAt(i);
			if(i+1 < s.length())
				c1 = s.charAt(i+1);
			if(type == Type.TEXT && !isText(c,prev,c1)){
				end = i;
				break;
			} else if(type != Type.TEXT){
				// minor break markers are not saved, we infere them
				if(FastWikiTokenizerEngine.isMinorBreak(c))
					type = Type.MINOR_BREAK;
				
				if(isText(c,prev,c1)){
					end = i;
					break;
				}					
			}
		}
		return new ExtToken(s.substring(start,end),start,end,type,null);
	}
	
	private static void tokenize(ArrayList<ExtToken> tokens, String s, Position pos){
		for(int i=0;i<s.length();){
			ExtToken t = getToken(s,i);
			t.setPosition(pos);
			tokens.add(t);
			i += t.termText().length();						
		}
	}
	
	private static char getUtf8Char(byte[] serialized, int start, int end){
		int ch, ch2, ch3;
		int cc = start;
		char tmpc;
		ch = (serialized[start]&0xff);
		switch (ch >> 4) {
		case 0:
		case 1:
		case 2:
		case 3:
		case 4:
		case 5:
		case 6:
		case 7:
			cc += 1;
			tmpc = (char)ch; // for debugging
			return tmpc;
		case 12: 
		case 13:
			cc += 2;
			if (cc > end)
				throw new IllegalStateException();
			else {
				ch2 = (int) (serialized[cc-1]&0xff);
				if (0x80 != (ch2 & 0xC0))
					throw new IllegalStateException();
				else {
					tmpc = (char)(((ch & 0x1F) <<6)|(ch2 & 0x3F));
					return tmpc;
				}
			}
		case 14:
			cc += 3;
			if (cc > end)
				throw new IllegalStateException();
			else {
				ch2 = (serialized[cc-2]&0xff);
				ch3 = (serialized[cc-1]&0xff);
				if ((0x80 != (ch2 & 0xC0)) || (0x80 != (ch3 & 0xC0)))
					throw new IllegalStateException();
				else {
					tmpc = (char)(((ch  & 0x0F) << 12)|
							((ch2 & 0x3F) << 6) |
							((ch3 & 0x3F) << 0));
					return tmpc;
				}
			}
		default:
			throw new IllegalStateException();		  
		}
	}

	private static ExtToken makeStubToken(byte[] serialized, int start, int end, int startOffset, int endOffset, Position pos){
		Type type;
		char c = getUtf8Char(serialized,start,end);
		char c1 = '\0';
		if(isText(c,'\0',c1))
			type = Type.TEXT;
		else{
			type = Type.GLUE;
			// FIXME: assumes minor breaks are ascii chars
			for(int i=start;i<end;i++){
				char cc = (char)serialized[i];
				if(FastWikiTokenizerEngine.isMinorBreak(cc)){
					type = Type.MINOR_BREAK;
					break;
				}
			}			
		}
		return new ExtToken(serialized,start,end,startOffset,endOffset,type,pos);
	}
	
	public static ArrayList<ExtToken> deserialize(byte[] serialized, Utf8Set terms, HashMap<Integer,Position> posMap){
		if(decomposer == null)
			decomposer = UnicodeDecomposer.getInstance();
		ArrayList<ExtToken> tokens = new ArrayList<ExtToken>();
		terms.setData(serialized);
		
		if(serialized==null || serialized.length == 0)
			return tokens;
		
		Position pos = posMap.get((int)serialized[0]);
		
		int cur = 1; // current position in serialized
		try {
			//System.out.println("SERIALIZED: "+new String(serialized,0,serialized.length,"utf-8"));
			for(;cur < serialized.length;){
				int controlInx = findControl(serialized,cur);
				if(controlInx != cur && controlInx+1 < serialized.length && serialized[controlInx+1]==9){					
					ExtToken tt = makeStubToken(serialized,cur,controlInx,cur,controlInx,pos);
					if(terms.contains(cur,controlInx)){
						tt.unstub(); // term in search, shouldn't be stubbed
					}
					if(tt != null)
						tokens.add(tt);
					//tokenize(tokens,s,pos);
				}
				ExtToken t = (tokens.size() == 0)? null : tokens.get(tokens.size()-1);
				
				cur = controlInx+1;
				if(cur >= serialized.length)
					break;
				int control = serialized[cur++];
				switch(control){
				case 1: // original
					{ int len = serialized[cur++]&0xff;
					t.setOriginalStart(cur);
					t.setOriginalEnd(cur+len);
					if(t.type != Type.TEXT || t.getPositionIncrement()==0)
						raiseException(serialized,cur,t,"Bad serialized data: trying to assign original string to nontext token or alias");
					cur += len;
					break; }
				case 2: // alias
					{ int len = serialized[cur++]&0xff;
					ExtToken tt = new ExtToken(serialized,cur,cur+len,t.startOffset(),t.endOffset(),t.type,t.pos);
					tt.setPositionIncrement(0);
					tokens.add(tt);
					if(t.type != Type.TEXT)						
						raiseException(serialized,cur,t,"Bad serialized data: trying to assign alias to nontext token");						
					cur += len;
					break; }
				case 3: // change pos
					pos = posMap.get((int)(serialized[cur++]&0xff));
					t.setPosition(pos);
					break;
				case 4: // sentence break
					if(t.type == Type.TEXT)
						raiseException(serialized,cur,t,"Bad serialized data: trying to assing a sentence break to text");
					t.setType(Type.SENTENCE_BREAK);
					break;
				case 5: // url
					{ int len = serialized[cur++]&0xff;
					ExtToken tt = new ExtToken(serialized,cur,cur+len,cur,cur+len,Type.URL,Position.EXT_LINK);
					tokens.add(tt);
					cur += len;
					break; }
				case 6: // original is title case
					t.setOriginalInTitleCase();
					break;
				case 7: // original is upper case
					t.setOriginalInUpperCase();
					break;					
				case 8:
					// nop, just double-glue token delimiter
					break;
				case 9:
					// nop, delimiter for tokens
					break;
				default:
					throw new RuntimeException("Unknown control sequence "+control);
				}				
				
			}
		} catch (IllegalStateException e) {
			e.printStackTrace();
		}

		
		return tokens;
	}

	private static void raiseException(byte[] serialized, int cur, ExtToken t, String string) {
		try {
			int len = Math.min(40,serialized.length-cur+10);
			log.error(string+", token="+t+", around: "+new String(serialized,cur-10,len,"utf-8"));
		} catch (UnsupportedEncodingException e) {
			e.printStackTrace();
		};
		throw new RuntimeException(string);
		
	}
	
	public boolean isStub(){
		return termText() == null;
	}
	
	public void setOriginalInTitleCase() {
		if(isStub())
			originalCase = CaseType.TITLE;
		else
			original = termText().substring(0,1).toUpperCase()+termText().substring(1);
	}
	
	public void setOriginalInUpperCase() {
		if(isStub())
			originalCase = CaseType.UPPER;
		else
			original = termText().toUpperCase();		
	}
	
	public CaseType getOriginalCase() {
		return originalCase;
	}

	public void setOriginalCase(CaseType originalCase) {
		this.originalCase = originalCase;
	}

	public int getOriginalEnd() {
		return originalEnd;
	}

	public void setOriginalEnd(int originalEnd) {
		this.originalEnd = originalEnd;
	}

	public int getOriginalStart() {
		return originalStart;
	}

	public void setOriginalStart(int originalStart) {
		this.originalStart = originalStart;
	}

	public int getTermTextEnd() {
		return termTextEnd;
	}

	public void setTermTextEnd(int termTextEnd) {
		this.termTextEnd = termTextEnd;
	}

	public int getTermTextStart() {
		return termTextStart;
	}

	public void setTermTextStart(int termTextStart) {
		this.termTextStart = termTextStart;
	}

	public byte[] getSerialized() {
		return serialized;
	}

	public void setSerialized(byte[] serialized) {
		this.serialized = serialized;
	}
	
	
}
