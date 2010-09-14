package de.brightbyte.wikiword.analyzer;

import java.util.List;

import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.PhraseOccuranceSet;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.query.QueryConsole;

public class AnalyzerConsole extends QueryConsole {

	private PlainTextAnalyzer plainTextAnalyzer;


	public AnalyzerConsole() {
		super();
	}

	protected void prepareApp() throws Exception {
		super.prepareApp();
		
		plainTextAnalyzer = PlainTextAnalyzer.getPlainTextAnalyzer(getCorpus(), tweaks);
		plainTextAnalyzer.initialize();
	}
	
	
	@Override
	public void runCommand(String cmd, List<Object> params, ConsoleOutput out) throws Exception {
			if (cmd.equals("phrases") || cmd.equals("p")) {
				Object s = params.get(1);
				PhraseOccuranceSet occurances = plainTextAnalyzer.extractPhrases(s.toString(), 5, 5);
				out.writeList(occurances);
				out.dumpPhraseTree(occurances.getRootNode());
			} else {
				super.runCommand(cmd, params, out);
			}
	}

	protected PhraseNode<? extends TermReference> getPhrases(String  s) {
		if (s.indexOf('|')>0 || s.indexOf(';')>0 ) {
			return super.getPhrases(s);
		} else {
			PhraseOccuranceSet occurances = plainTextAnalyzer.extractPhrases(s, 5, 5);
			return occurances.getRootNode();
		}
	}

	public static void main(String[] argv) throws Exception {
		AnalyzerConsole q = new AnalyzerConsole();
		q.launch(argv);
	}

}
