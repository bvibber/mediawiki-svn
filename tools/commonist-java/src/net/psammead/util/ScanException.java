package net.psammead.util;

/** something went wrong while tokenizing */
public class ScanException extends Exception {
	private String	text;
	private	int		position;
	
	/** create a TokenizerException with the text and the position parsed as the Exception occured */
	public ScanException(String message, String text, int position) {
		super(message);
		this.text		= text;
		this.position	= position;
	}
	
	/** return the text parsed as this Exception occured */
	public String getText() { 
		return text; 
	}
	
	/** return the parsing position when this Exception occured */
	public int getPosition() { 
		return position; 
	}
}