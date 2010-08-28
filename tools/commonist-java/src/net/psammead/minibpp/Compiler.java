package net.psammead.minibpp;

import java.io.*;
import net.psammead.minibpp.parser.Parser;
import net.psammead.minibpp.parser.ParseException;

/** compiles one level of BeanShellPP syntax */
public class Compiler {
	/** compiles from stdin to stdout with the system default encoding */
	public static void main(String[] args) throws ParseException, IOException {
		// no error handling necessary ;)
		BufferedReader	in	= new BufferedReader(new InputStreamReader(System.in));
		PrintWriter		out	= new PrintWriter(new OutputStreamWriter(System.out));
		Compiler		c	= new Compiler();
		c.filter(in, out);
		in.close();
		out.close();
	}
	 
	//------------------------------------------------------------------------------
	
	private int		depth;
	private int		lineNumber;

	private char	echoChar		= '#';
	private char	exactCmd		= '\'';
	private char	magicCmd		= '\"';
	private char	defaultCmd		= '\"';
	private char	magicIdentifier	= '$';

	//------------------------------------------------------------------------------
	
	/** default constructor with depth 1 */
	public Compiler() {
		this(1);
	}

	/** no idea what depth means. 0 does nothing, 1 compiles one level of # */
	public Compiler(int depth) {
		this.depth	= depth;
		lineNumber	= 0;
	}
	
	/** compile a Template from a Reader into a String. the reader will not be closed! */
	public String compile(Reader in) throws ParseException, IOException {
		BufferedReader	br	= new BufferedReader(in);
		StringWriter	sw	= new StringWriter();
		PrintWriter		pw	= new PrintWriter(sw);
		filter(br, pw);
		pw.close();
		return sw.toString();
	}
	
	/** compiles a template in String form */
	public String compile(String template) throws ParseException, IOException {
		Reader	in	= new StringReader(template);
		String	out	= compile(in);
		in.close();	return out;
	}
	
	/** compile a Template from a File into a String. */
	public String compile(File file, String charSet) throws ParseException, IOException {
		Reader	in	= new InputStreamReader(new FileInputStream(file), charSet);
		String	out	= compile(in);
		in.close();	return out;
	}
	
	/** compile a Template file */
	public void compile(File input, File output, String charSet) throws ParseException, IOException {
		BufferedReader	in	= null;
		PrintWriter		out	= null;
		try {
			in	= new BufferedReader(new InputStreamReader(new FileInputStream(input), charSet));
			out	= new PrintWriter(new OutputStreamWriter(new FileOutputStream(output), charSet));
			filter(in, out);
		}
		finally {
			if (in  != null)	try { in.close(); }		catch (Exception e) {}
			if (out != null)	try { out.close(); }	catch (Exception e) {}
		}
	}

	//------------------------------------------------------------------------------
	
	/** compiles what it gets line by line from the Reader into the Writer */
	public void filter(BufferedReader in, PrintWriter out) throws ParseException, IOException {
		for (;;) {
			String line = in.readLine();
			if (line == null)	break;
			decorate(line, out);
		}
		//out.flush();
	}

	/** returns the current line number */
	public int getLine() {
		return lineNumber;
	}

	//------------------------------------------------------------------------------
	
	/** compiles a single line */
	private void decorate(String line, PrintWriter out) throws ParseException, IOException {
		int count = 0;
		while (count < line.length() && line.charAt(count) == echoChar) {
			++count;
		}
		if (count >= depth) {
			echo(line, out);
			return;
		}
		char	cmd;
		String	data;
		if (count == depth - 1 && line.length() > count) {
			cmd = line.charAt(count);
			if (cmd == exactCmd || cmd == magicCmd) {
				data	= line.substring(0, count) + line.substring(count + 1);
			}
			else {
				if ((defaultCmd == magicCmd) && hasMagic(line)) {
					cmd	= magicCmd;
				}
				else {
					cmd	= exactCmd;
				}
				data = line;
			}
		}
		else {
			cmd		= exactCmd;
			data	= line;
		}

		if (cmd == exactCmd || !hasMagic(data)) {
			exact(data, out);
		}
		else {
			magic(data, out);
		}
	}

	private void exact(String line, PrintWriter out) throws IOException {
		out.print("out.println(");
		out.print(literal(line));
		out.println(");");
	}

	private void magic(String line, PrintWriter out) throws ParseException, IOException {
		Context context = new Context(out);
		Parser.parse(new StringReader(line), context);
	}

	private void echo(String line, PrintWriter out) throws IOException {
		String output = line.substring(depth);
		out.println(output);
	}

	private boolean hasMagic(String line) {
		return line.indexOf(magicIdentifier) >= 0;
	}
	
	/** format String as a java literal */
	static String literal(String s) {
		StringBuffer out = new StringBuffer();
		out.append('\"');
		for (int i = 0; i < s.length(); ++i) {
			char	x	= s.charAt(i);
			// encodeTo(s.charAt(i), out);
			switch (x) {
				case '\'':	out.append("\\\'");	break;
				case '\"':	out.append("\\\"");	break;
				case '\\':	out.append("\\\\");	break;
				case '\t':	out.append("\\t");	break;
				case '\n':	out.append("\\n");	break;
				case '\r':	out.append("\\r");	break;
				case '\f':	out.append("\\f");	break;
				default:
					if (x >= 32 && x < 127)	out.append(x);
					else {
						out.append("\\u");
						String	hex	= Integer.toHexString(x);
						for (int j=hex.length(); j<4; i++)	out.append('0');
						out.append(hex);
					}
			}
		}
		out.append('\"');
		return out.toString();
	}
}
