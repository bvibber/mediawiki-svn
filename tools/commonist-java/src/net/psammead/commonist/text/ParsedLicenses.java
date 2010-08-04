package net.psammead.commonist.text;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.URL;
import java.util.ArrayList;
import java.util.List;
import java.util.regex.Matcher;
import java.util.regex.Pattern;

import net.psammead.commonist.data.LicenseData;
import net.psammead.util.Logger;

/** a set of licenses */
public final class ParsedLicenses {
	private static final Logger log = new Logger(ParsedLicenses.class);
	
	private static final Pattern	LICENSE_DESCRIPTOR	= Pattern.compile("(\\{\\{[^\\}]+\\}\\})\\s*(.*)");

	public final List<LicenseData>	licenseDatas; 

	public ParsedLicenses(URL url) throws IOException {
		licenseDatas	= new ArrayList<LicenseData>();
		
		BufferedReader	in	 = null;
		try {
			in	= new BufferedReader(new InputStreamReader(url.openStream(), "UTF-8"));
			for (;;) {
				String	line	= in.readLine();
				if (line == null)	break;
				line	= line.trim();
				if (line.length() == 0)		continue;
				if (line.startsWith("#"))	continue;
				Matcher	matcher	= LICENSE_DESCRIPTOR.matcher(line);
				if (!matcher.matches())	{ log.warn("could not parse: " + line); continue; }
				LicenseData	data	= new LicenseData(
						matcher.group(1),
						matcher.group(2));
				licenseDatas.add(data);
			}
		}
		finally {
			if (in != null)
			try { in.close(); }
			catch (Exception e) { log.error("cannot close", e); }
		}
	}
}
