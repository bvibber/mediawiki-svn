package net.psammead.util;

//BETTER should be modeled after the string-facilities of the icon language

/** 
 * scans Strings<br>
 * this class is the only one that survived from my early C++ days ;)<br> 
 * attention: instances of this class are not threadsafe.
 */
public class Scan {
	//------------------------------------------------------------------------------
	//## constants
	
	/** represents the not-existing position property value */
	public static final int		POSITION_ILLEGAL	= -1;
	
	// character set constants
	
	/** a single tabulator (TAB) character */
	public static final String	TAB			= "\t";
	/** a single carriage return (CR) character as used on the mac */
	public static final String	CR			= "\r";
	/** a single line feed (LF) character as used on unix */
	public static final String	LF			= "\n";
	/** a CR/LF combination as used on windows */
	public static final String	CRLF		= "\r\n";
	/** whitespace: space, TAB, CR and LF */
	public static final String	WHITE		= " \t\r\n";	//BETTER should contain additional unicode whitespace (?)
	/** lower- and uppercase english letters a-zA-Z */
	public static final String	ALPHA		= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	/** characters of a binary number: 0 and 1 */
	public static final String	BIN_NUM		= "01";
	/** characters of a a decimal number: 0-9 */
	public static final String	DEC_NUM		= "0123456789";
	/** characters of a hexadecimal number: 0-9a-fA-F */
	public static final String	HEX_NUM		= "0123456789abcdefABCDEF";
	/** like ALPHA + DEC_NUM: 0-9a-zA-Z */
	public static final String	ALPHANUM	= "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

	//------------------------------------------------------------------------------
	//## fields

	private String	data;
	private int		position;
	private int		back;
	private int		length;
	
	//------------------------------------------------------------------------------
	//## standard methods

	/** create a Tokenizer with an empty String */
	public Scan() {
		setText("");
	}
	
	/** create a Tokenizer with a preset String */
	public Scan(String text) {
		setText(text);
	}

	/** sets the String to tokenize */
	public void setText(String data) {
		this.data	= data;
		position	= 0;
		back		= 0;
		length		= data.length();
		if (length == 0)	position	= POSITION_ILLEGAL;
	}
	
	/** gets the text to Tokenize */
	public String getText() {
		return data;
	}
	
	/** gets the cursor position */
	public int getPosition() {
		return position;
	}
	
	/** sets the cursor position */
	public void setPosition(int position) {
		this.position = position;
	}
	
	/** moves the cursor position */
	public void movePosition(int delta) {
		position	+= delta;
	}
	
	/** returns true if the cursor stands behind the String to tokenize */
	public boolean isFinished() {
		return position == POSITION_ILLEGAL;
	}
	
	/** returns the number of characters left to parse */
	public int remainingChars() {
		if (position == POSITION_ILLEGAL)	return POSITION_ILLEGAL;
		else								return length - position;
	}
	
	//------------------------------------------------------------------------------
	//## tricks

	/** calculate the line number we are on (quite slow) returns -1 for empty strings */
	public int lineNumber() {
		if (length == 0)	return -1;
		final int	save	= position;
		int	line	= 0;
		position	= 0;
		for (;;) {
			// System.err.println("line=" + line + "\tposition=" + position +"\tsave=" + save);
			if (position >= save)	break;
			line();
			if (eol())	line++;
			else					break;
		}
		position	= save;
		return line;
	}

	//------------------------------------------------------------------------------
	//## XML tags

	/** returns the content of an XML tag, if there is one */
	public String tag() throws ScanException {
		if (!is("<")) return "";
		final StringBuilder s = new StringBuilder();
		for (;;) {
			s.append(scan(">\""));
			if (is(">")) break;
			if (!is("\"")) throw new ScanException("illegal quote: missing \"", data, position);
			s.append("\"");
			s.append(scan("\""));
			s.append("\"");
			if (!is("\"")) throw new ScanException("illegal quote: missing \"", data, position);
		}
		return s.toString();
	}

	/** returns an XML entity, if there is one */
	public String entity() throws ScanException {
		if (!is("&"))	return "";
		final String s = scan(WHITE + "<&;");
		if (!is(";"))	throw new ScanException("illegal entity: missing ;", data, position);
		return "&" + s + ";";
	}

	/** returns the text between XML tags, if there is any */
	public String text() {
		return scan("<");
	}
	
	//------------------------------------------------------------------------------
	//## convenience
	
	/** takes whitespace */
	public String skip() {
		return take(WHITE);
	}
	
	/** takes non-whitespace */
	public String word() {
		return scan(WHITE);
	}
	
	/** scans upto an eol */
	public String line() {
		return scan(CRLF);
	}
	
	/** checks for a DOS/UNIX/MAC end of line */
	public boolean eol() {
		//BETTER could be more efficient
		if (is(CRLF))	return true;
		if (is(LF))		return true;
		if (is(CR))		return true;
		return false;
	}
	
	/** parses a (long) decimal value */
	public long dec() throws ScanException {
		final int		p	= position; 
		final String	s	= (is("-") ? "-" : "") + take(DEC_NUM, 20);
		try { 
			return Long.parseLong(s); 
		}
		catch (NumberFormatException e) { 
			position	= p; 
			throw new ScanException("illegal number format", data, position);
		}
	}
	
	/** parses a (long) hexadecimal value */
	public long hex() throws ScanException {
		final int		p	= position; 
		final String	s	= take(HEX_NUM, 16);
		try { 
			return Long.parseLong(s, 16); 
		}
		catch (NumberFormatException e) { 
			position	= p;             
			throw new ScanException("illegal number format", data, position);
		}
	}
	
	/** parses a (long) binary value */
	public long bin() throws ScanException {
		final int		p	= position;
		final String	s	= take(BIN_NUM, 64);
		try { 
			return Long.parseLong(s, 2); 
		}
		catch (NumberFormatException e) { 
			position	= p;
			throw new ScanException("illegal number format", data, position);
		}
	}
	
	//------------------------------------------------------------------------------
	//## basics

	//BETTER could be more efficient
	
	/** returns everything until some stop word is reached */
	public String stopAt(String word) {
		final int	z	= word.length();
		if (z < 2)	return scan(word);
		final String 		first 	= word.substring(0,1);
		final StringBuilder	out		= new StringBuilder();
		for (;;) {
			out.append(scan(first));
			if (isFinished())	break;
			if (peek(z).equals(word)) break;
			is(first); out.append(first);
		}
		return out.toString();
	}
	
	/** 
	 * returns true if the given String is recognized.
	 * if this is the case, der cursor skips the String 
	 */
	public boolean is(String them) {
		int	len	= them.length();
		if (position == POSITION_ILLEGAL)	return false;	// am ende: immer falsch
		if (len	== 0)						return true;	// leerstring gibt's immer
		if (position + len > length)		return false;	// suchstring zu lang
		final String maybe	= data.substring(position, position+len);
		if (maybe.equals(them)) {
			back		= position;
			position	+= len;
			if (position >= length)	position	= POSITION_ILLEGAL;
			return true;
		}
		else return false;
	}

	/** takes all Characters matching Characters in a given String */
	public String take(String these) {
		if (position == POSITION_ILLEGAL)	return "";
		if (these.length() == 0)			return "";
		back	= position;
		while (position < length) {
			final char c = data.charAt(position);		// nächstes zeichen
			if (these.indexOf(c) == -1) break;	// wenn's in these nicht vorkommt: abbruch
			position ++;
		}
		if (position == back) return "";
		final String	s	= data.substring(back, position);
		if (position >= length)	position	= POSITION_ILLEGAL;
		return s;
	}

	/** 
	 * takes all Characters matching Characters in a given String
	 * and stops at a given maximum number of Characters 
	 */
	public String take(String these, int maxLength) {
		if (position == POSITION_ILLEGAL)	return "";
		if (these.length() == 0)			return "";
		back	= position;
		while (position < length && maxLength > 0) {
			char c = data.charAt(position);			// nächstes zeichen
			if (these.indexOf(c) == -1) break;		// wenn's in these nicht vorkommt: abbruch
			position ++;
			maxLength --;
		}
		if (position == back) return "";
		final String	s	= data.substring(back, position);
		if (position >= length)	position	= POSITION_ILLEGAL;
		return s;
	}

	/** takes all Characters not matching Characters in a given String */
	public String scan(String upto) {
		if (position == POSITION_ILLEGAL) return "";
		if (upto.length() == 0) {
			final String	s	= data.substring(position);
			position	= POSITION_ILLEGAL;
			return s;
		}
		back	= position;
		while (position < length) {
			final char c	= data.charAt(position);
			if (upto.indexOf(c) != -1) break;
			position ++;
		}
		if (position == back) return "";
		final String	s	= data.substring(back, position);
		if (position >= length)	position	= POSITION_ILLEGAL;
		return s;
	}
    	
	/**
	 * takes all Characters not matching Characters in a given String
	 * and stops at a given maximum number of Characters 
	 */
	public String scan(String upto, int maxLength) {
		if (position == POSITION_ILLEGAL) return "";
		if (upto.length() == 0) {
			final String	s	= data.substring(position);
			position	= POSITION_ILLEGAL;
			return s;
		}
		back	= position;
		while (position < length && maxLength > 0) {
			final char	c	= data.charAt(position);
			if (upto.indexOf(c) != -1) break;
			position ++; maxLength --;
		}
		if (position == back) return "";
		final String s = data.substring(back, position);
		if (position >= length)	position	= POSITION_ILLEGAL;
		return s;
	}
    	
	/** reads all remaining characters */
	public String remainder() {
		if (position == POSITION_ILLEGAL) return "";
		final String	s	= data.substring(position);
		position	= POSITION_ILLEGAL;
		return s;
	}

	/** reads a single character, returns -1 if the String is finished */
	public int read() {
		//if (position >= length) position = POSITION_ILLEGAL;
		if (position == POSITION_ILLEGAL) return -1;
		final char	c	= data.charAt(position);
		position ++;
		if (position >= length)	position	= POSITION_ILLEGAL;
		return c;
	}

	/** reads upto a given number of Characters */
	public String read(int len) {
		if (position == POSITION_ILLEGAL) return "";
		int max	= length - position;
		if (len > max)	len = max;
		int	p2	= position;
		position	+= len;
		if (position >= length)	position	= POSITION_ILLEGAL;
		return data.substring(p2, p2 + len);
	}
	
	/** 
	 * reads a single character, but doesn't advance the cursor 
	 * returns -1 if the String is finished
	 */
	public int peek() {
		if (position >= length) position = POSITION_ILLEGAL;
		if (position == POSITION_ILLEGAL)	return -1;
		return data.charAt(position);
	}

	/** reads upto a given number of Characters, but doesn't advance the cursor */
	public String peek(int len) {
		if (position == POSITION_ILLEGAL) return "";
		int	max	= length - position;
		if (len > max)	len = max;
		return data.substring(position, position + len);
	}
}
