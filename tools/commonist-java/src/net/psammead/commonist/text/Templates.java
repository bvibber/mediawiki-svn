package net.psammead.commonist.text;

import java.io.InputStreamReader;
import java.io.PrintWriter;
import java.io.Reader;
import java.io.StringReader;
import java.io.StringWriter;
import java.net.URL;
import java.util.Iterator;
import java.util.Map;

import net.psammead.commonist.util.Loader;
import net.psammead.minibpp.Compiler;
import net.psammead.util.Logger;
import bsh.Interpreter;

/** compiles image metadata into a [[Template:Information]] for commons or something similar for other wikis */
public final class Templates {
	private static final Logger log = new Logger(Templates.class);

	private static final String	TEMPLATE_ENCODING	= "UTF-8";
	
	private final Loader	loader;
	
	public Templates(Loader loader) {
		this.loader	= loader;
	}
	
	public String applyTemplate(String templatePath, Map<String,?> data) throws TemplateException {
		// TODO: use mandatoryURL
		URL	url	= loader.optionalURL(templatePath);
		if (url == null)	throw new TemplateException("unknown template: " + templatePath);
		
		Reader	tin	= null;
		Reader	cin	= null;
		try {
			tin	= new InputStreamReader(
					url.openConnection().getInputStream(), 
					TEMPLATE_ENCODING);
			final Compiler	compiler	= new Compiler();
			final String	code		= compiler.compile(tin);


			final StringWriter	sout	= new StringWriter();
			final PrintWriter		xout	= new PrintWriter(sout);
			
			final Interpreter	interpreter	= new Interpreter();
			interpreter.set("out",	xout);
			for (Iterator<String> it=data.keySet().iterator(); it.hasNext();) {
				final String	key		= it.next();
				final Object	value	= data.get(key);
				interpreter.set(key, value);
			}

			cin	= new StringReader(code);
			interpreter.eval(cin, interpreter.getNameSpace(), url.toExternalForm());
			
			return sout.toString();
		}
		catch (Exception e) {
			log.error("cannot use template: " + url.toExternalForm(), e);
			throw new TemplateException("cannot use template: " + url.toExternalForm(), e);
		}
		finally {
			if (tin != null)
				try { tin.close(); }
				catch (Exception e) { log.error("cannot close template stream", e); }
			if (cin != null)
				try { cin.close(); }
				catch (Exception e) { log.error("cannot close template stream", e); }
		}
	}
}
