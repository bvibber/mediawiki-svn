package org.mediawiki.importer;

import java.io.OutputStream;
import java.io.PrintStream;

public class SqlFileStream {
	protected PrintStream stream;
	
	public SqlFileStream(OutputStream output) {
		this.stream = new PrintStream(output);
	}
	
	public void writeComment(CharSequence sql) {
		stream.println(sql);
	}
	
	public void writeStatement(CharSequence sql) {
		stream.print(sql);
		stream.println(';');
	}
	
	public void close() {
		stream.flush();
		stream.close();
	}
}
