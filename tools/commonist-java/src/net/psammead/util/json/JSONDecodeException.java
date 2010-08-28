package net.psammead.util.json;

/** the input is invalid */
public class JSONDecodeException extends Exception {
	public final String	text;
	public final int	offset;
	public final String	expectation;

	public JSONDecodeException(String input, int offset, String expectation) {
		super("at offset: " + offset + " expected: " + expectation);
		this.text			= input;
		this.offset			= offset;
		this.expectation	= expectation;
	}

	public String lookingAt() {
		final int	width	= 80;
		final int	end	= Math.min(offset+width, text.length());
		return text.substring(offset, end).replaceAll("[\r\n]", "§");
	}
}
