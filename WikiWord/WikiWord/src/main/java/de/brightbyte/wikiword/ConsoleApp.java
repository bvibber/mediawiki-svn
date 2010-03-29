package de.brightbyte.wikiword;

import java.util.ArrayList;
import java.util.Collections;
import java.util.List;

import de.brightbyte.io.Prompt;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.StructuredDataCodec;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class ConsoleApp<S extends WikiWordConceptStoreBase> extends StoreBackedApp<S> {

	protected Prompt prompt;
	protected StructuredDataCodec commandCodec;
	
	public ConsoleApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
		prompt = new Prompt();
		commandCodec = new StructuredDataCodec();
		commandCodec.setLenient(true);
	}

	@Override
	public void run() throws PersistenceException {
		runConsole();
	}

	public void runConsole() throws PersistenceException {
		echo("hello");

		while (true) {
			List<Object> params= promptCommand();
			if (params==null) break;
			if (params.size()==0) continue;
			
			params = new ArrayList<Object>(params); //modifyable
			
			String cmd = params.get(0).toString();
			cmd = cmd.trim().toLowerCase();
			
			if (cmd.equals("quit") || cmd.equals("exit") || cmd.equals("q")) break;
			
			try {
				beforeCommand(params);
				try {
					runCommand(params);
				} finally {
					afterCommand(params);
				}
			} catch (Exception e) {
				e.printStackTrace(prompt.getOut());
			} 
		}
		
		echo("bye");
		bye();
	}
	
	protected void bye() throws PersistenceException {
		// noop
	}

	public List<Object> promptCommand() {
		String s = prompt.prompt(">", "");
		if (s==null) return null;
		
		s = s.replaceAll("^\\s*|\\s*[;]\\s*$", "");
		if (s.length()==0) return Collections.emptyList();
		
		if (s.startsWith("#") || s.startsWith(";") || s.startsWith("//")) return Collections.emptyList();
		
		return commandCodec.decodeList(s);
	}

	public String prompt(String m, List<String> options, String def) {
		String s = prompt.prompt(m+">", options, def);
		
		return s;
	}

	protected void beforeCommand(List<Object> params) throws Exception {
		//noop
	}
	
	protected void afterCommand(List<Object> params) throws Exception {
		//noop
	}
	
	public abstract void runCommand(List<Object> params) throws Exception;

	protected void startEcho(String msg) {
		prompt.print(msg);
	}

	protected void continueEcho(String msg) {
		prompt.print(msg); 
	}

	protected void finishEcho(String msg) {
		prompt.println(msg);
	}

	protected void finishEcho() {
		finishEcho("");
	}

	protected void echo(String msg) {
		prompt.println(msg);
	}

	protected boolean confirm(String msg) {
		String r = prompt.prompt(msg, new String[] {"y", "n"},  "n");
		if (r==null || !r.equals("y")) {
			return false;
		}
		
		return true;
	}
	
}
