package de.brightbyte.wikiword;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.Collections;
import java.util.List;

import de.brightbyte.io.Prompt;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.store.WikiWordConceptStoreBase;

public abstract class ConsoleApp<S extends WikiWordConceptStoreBase> extends StoreBackedApp<S> {

	protected Prompt prompt;
	
	public ConsoleApp(boolean allowGlobal, boolean allowLocal) {
		super(allowGlobal, allowLocal);
		prompt = new Prompt();
	}

	@Override
	public void run() throws PersistenceException {
		runConsole();
	}

	public void runConsole() throws PersistenceException {
		echo("hello");

		while (true) {
			List<String> params= promptCommand();
			if (params==null) break;
			if (params.size()==0) continue;
			
			params = new ArrayList<String>(params); //make modifiable
			
			String cmd = params.get(0);
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

	public List<String> promptCommand() {
		String s = prompt.prompt(">", "");
		if (s==null) return null;
		
		s = s.replaceAll("^\\s*|\\s*[;]\\s*$", "");
		if (s.length()==0) return Collections.emptyList();
		
		String[] ss = s.split("\\s+");
		return Arrays.asList(ss);
	}

	public String prompt(String m, List<String> options, String def) {
		String s = prompt.prompt(m+">", options, def);
		
		return s;
	}

	protected void beforeCommand(List<String> params) throws Exception {
		//noop
	}
	
	protected void afterCommand(List<String> params) throws Exception {
		//noop
	}
	
	public abstract void runCommand(List<String> params) throws Exception;

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
