package de.brightbyte.wikiword.query;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.util.ArrayList;
import java.util.Collection;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.LabeledVector;
import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.io.LeveledOutput;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConsoleApp;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.disambig.Disambiguator;
import de.brightbyte.wikiword.disambig.SlidingCoherenceDisambiguator;
import de.brightbyte.wikiword.disambig.StoredFeatureFetcher;
import de.brightbyte.wikiword.disambig.StoredMeaningFetcher;
import de.brightbyte.wikiword.disambig.Term;
import de.brightbyte.wikiword.model.AbstractConceptOutput;
import de.brightbyte.wikiword.model.ConceptFeatures;
import de.brightbyte.wikiword.model.ConceptOutput;
import de.brightbyte.wikiword.model.ConceptRelations;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.PhraseNode;
import de.brightbyte.wikiword.model.TermListNode;
import de.brightbyte.wikiword.model.TermReference;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.rdf.RdfOutput;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.FeatureStore;
import de.brightbyte.wikiword.store.GlobalConceptStore;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.ProximityStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore.ConceptQuerySpec;

public class QueryConsole extends ConsoleApp<WikiWordConceptStore> {

	protected Disambiguator disambiguator;
	protected ConceptQuerySpec minimalConceptSpec;
	protected ConceptQuerySpec resolvedConceptSpec;
	protected ConceptQuerySpec detailedConceptSpec;
	
	public QueryConsole() {
		super(true, true);
		
		minimalConceptSpec = new ConceptQuerySpec();
		resolvedConceptSpec = new ConceptQuerySpec();
		resolvedConceptSpec.setIncludeDefinition(true);
		resolvedConceptSpec.setIncludeResource(true);
		
		detailedConceptSpec = new ConceptQuerySpec();
		detailedConceptSpec.setIncludeDefinition(true);
		detailedConceptSpec.setIncludeResource(true);
		detailedConceptSpec.setIncludeRelations(true);
	}

	protected static class ConsoleOutput {
		protected ConceptOutput output;
		protected Writer writer;
		protected boolean persistent;
		
		public ConsoleOutput(ConceptOutput output, Writer writer, boolean persistent) {
			super();
			this.output = output;
			this.writer = writer;
			this.persistent = persistent;
		}
		
		public void init() throws PersistenceException {
			if (output instanceof RdfOutput) {
				 try {
					((RdfOutput)output).startDocument();
				} catch (RdfException e) {
					throw new PersistenceException(e);
				}
			}
		}
		
		public ConceptOutput getOutput() {
			return output;
		}
		
		public boolean isPersistent() {
			return persistent;
		}
		
		public Writer getWriter() {
			return writer;
		}

		public void close() throws PersistenceException {
			flush();
			
			if (output instanceof RdfOutput) {
				 try {
					((RdfOutput)output).endDocument();
				} catch (RdfException e) {
					throw new PersistenceException(e);
				}
			}
			
			if (!persistent) {
				output.close();
			}
		}

		public void flush() throws PersistenceException {
			output.flush();
		}

		public void writeConcept(WikiWordConcept concept) throws PersistenceException {
			output.writeConcept(concept);
		}

		public void writeConcepts(DataSet<? extends WikiWordConcept> meanings) throws PersistenceException {
			output.writeConcepts(meanings);
		}

		public void writeGlobalConcept(GlobalConcept concept) throws PersistenceException {
			output.writeGlobalConcept(concept);
		}

		public void writeLocalConcept(LocalConcept concept) throws PersistenceException {
			output.writeLocalConcept(concept);
		}

		public void writeFeatureVector(LabeledVector featureVector) throws PersistenceException  {
			//XXX: hack!
			try {
				writer.write(featureVector.toString());
				writer.write("\n");
				writer.flush();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}
		
		public void writeInterpretation(Map<String, ? extends WikiWordConcept> interp) throws PersistenceException  {
			write(interp.toString()); //FIXME
		}
		
		public void writeList(List<?> list) throws PersistenceException  {
			write(list.toString()); //FIXME
		}
	
		public void write(Object obj) throws PersistenceException  {
			try {
				writer.write(obj.toString());
				writer.write("\n");
				writer.flush();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}

		public void dumpPhraseTree(PhraseNode n) throws PersistenceException  {
			dumpPhraseTree(n, "");
		}
	
		public void dumpPhraseTree(PhraseNode n, String dent) throws PersistenceException  {
			write(dent+n);
			dent += " + ";
			Collection<PhraseNode> successors = n.getSuccessors();
			if (successors!=null) {
					for (PhraseNode m: successors) {
						dumpPhraseTree(m, dent);
					}
			}
		}
	
	}
	
	protected class ConceptDumper extends AbstractConceptOutput {

		protected Writer writer;
		private int maxAutoResolve = 6;
		
		public ConceptDumper(Writer wr) {
			this.writer = wr;
		}

		public void close() throws PersistenceException {
			try {
				writer.close();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}

		public void flush() throws PersistenceException {
			try {
				writer.flush();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}


		public void writeConceptReference(WikiWordConcept concept) throws PersistenceException {
			println("> (", concept.getRelevance(), ":", concept.getCardinality(), ") #", concept.getId(), ": ", concept.getName());
		}

		public void writeGlobalConcept(GlobalConcept concept) throws PersistenceException {
			println();
			println("* (", concept.getRelevance(), ":", concept.getCardinality(), ") #", concept.getId(), ": ", concept.getName());
			writeConceptRelations(concept.getRelations());
			println();
		}

		public void writeLocalConcept(LocalConcept concept) throws PersistenceException {
			println();
			println("* (", concept.getRelevance(), ":", concept.getCardinality(), ") #", concept.getId(), ": ", concept.getName());
			writeLocalConceptDescription(concept);
			writeConceptRelations(concept.getRelations());
			println();
		}
		
		public void writeLocalConceptDescription(LocalConcept concept) throws PersistenceException {
			println(" - Definition:", concept.getDefinition());
			printlst(" - Terms", concept.getTerms());
		}
		
		public void writeConceptRelations(ConceptRelations<? extends WikiWordConcept> relations) throws PersistenceException {
			if (relations==null) return;
			
			printlst(" - Similar", relations.getSimilar());
			printlst(" - Related", relations.getRelated());
			printlst(" - Broader", relations.getBroader());
			printlst(" - Narrower", relations.getNarrower());
			printlst(" - InLinks", relations.getInLinks());
			printlst(" - OutLinks", relations.getOutLinks());
			printlst(" - LangLinks", relations.getLanglinks());
		}
		
		public void println(Object... args) throws PersistenceException {
			StringBuilder s = new StringBuilder();
			
			for (Object a: args) {
				s.append(String.valueOf(a));
			}
			
			s.append("\n"); //FIXME: linebreak!

			try {
				writer.write(s.toString());
				writer.flush();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}
		
		public void printlst(String label, Object[] args) throws PersistenceException {
			StringBuilder s = new StringBuilder();
			
			if (args!=null) {
				int c = 0;
				for (Object a: args) {
					if (s.length()>0) s.append(", ");

					if (a instanceof WikiWordConcept) {
						WikiWordConcept r = (WikiWordConcept)a;
						int id = r.getId();
						String n = r.getName();
						
						if (n==null && c < maxAutoResolve) {
							WikiWordConcept x = ((WikiWordConceptStore)conceptStore).getConcept(id, resolvedConceptSpec);
							r = x;
							a = r;
						}

						s.append('#').append(id);
						
						if (n!=null) {
							s.append(": ").append(n);
						}
						c++;
					}
					else {
						s.append(String.valueOf(a));
					}
				}
			}
			
			s.insert(0, label + ": ");
			s.append("\n"); //FIXME: linebreak!
			
			try {
				writer.write(s.toString());
				writer.flush();
			} catch (IOException e) {
				throw new PersistenceException(e);
			}
		}

		public int getMaxAutoResolve() {
			return maxAutoResolve;
		}

		public void setMaxAutoResolve(int maxAutoResolve) {
			this.maxAutoResolve = maxAutoResolve;
		}
		
	}
	
	protected ConsoleOutput getConceptOutput(String format, File target) throws PersistenceException {
		try {
			if (target!=null) echo("writing data to "+target);
			Writer wr = target == null ? prompt.getOut() : new OutputStreamWriter(new BufferedOutputStream(new FileOutputStream(target)), "UTF-8");
			ConceptOutput out;
			
			if (format==null || format.equals("dump") || format.equals("text")) {
				 out = new ConceptDumper(wr);
			}
			else if (format.equals("rdf") || format.equals("turtle") || format.equals("n3")) {
				 out = new RdfOutput(identifiers, "default", wr, "turtle", getStoreDataset());
				 ((RdfOutput)out).startDocument();
			}
			else {
				throw new IllegalArgumentException("unknown format: "+format);
			}
				
			return new ConsoleOutput(out, wr, target!=null);
		} catch (IOException e) {
			throw new PersistenceException(e);
		} catch (RdfException e) {
			throw new PersistenceException(e);
		} catch (PersistenceException e) {
			throw new PersistenceException(e);
		}
	}

	@Override
	public void runCommand(List<Object> params) throws Exception {
		String cmd = params.get(0).toString();
		cmd = cmd.trim().toLowerCase();
		
		String format = null;
		File target = null;
		
		if (params.size()>1 && params.get(params.size()-1).toString().startsWith(">")) {
			target = new File( params.get(params.size()-1).toString().substring(1).trim() );
			params = params.subList(0, params.size()-1);
		}
		
		if (params.size()>1 && params.get(params.size()-1).toString().startsWith("|")) {
			format = params.get(params.size()-1).toString().substring(1).trim();
			params = params.subList(0, params.size()-1);
		}
		
		ConsoleOutput out = getConceptOutput(format, target);

		try {
			runCommand(cmd, params, out);
		} finally {
			out.close();
		}
	}
		
	public void runCommand(String cmd, List<Object> params, ConsoleOutput out) throws Exception {
			if (cmd.equals("statistics") || cmd.equals("stats")) {
				dumpStats();
			}
			else if (cmd.equals("m") || cmd.equals("mng") || cmd.equals("meanings")) {
				if (isGlobalThesaurus()) {
					String lang = params.get(1).toString();
					String term = params.get(2).toString();
					listMeaningsGlobal(lang, term, out);
				}
				else {
					String term = params.get(1).toString();
					listMeaningsLocal(term, out);
				}
			}
			else if (cmd.equals("s") || cmd.equals("cat") || cmd.equals("show")) {
				if (params.size()>2 && isGlobalThesaurus()) {
					int id = DatabaseUtil.asInt(params.get(1));
					String lang = params.get(2).toString();
					showConcept(id, lang, out);
				}
				else {
					int id = DatabaseUtil.asInt(params.get(1));
					showConcept(id, out);
				}
			}
			else if (cmd.equals("e") || cmd.equals("env") || cmd.equals("environment")) {
				if (params.size()>2 ) {
					int id = DatabaseUtil.asInt(params.get(1));
					String min = params.get(2).toString();
					showEnvironment(id, Double.parseDouble(min), out);
				}
				else {
					int id = DatabaseUtil.asInt(params.get(1));
					showEnvironment(id, 0, out);
				}
			}
			else if (cmd.equals("f") || cmd.equals("feat") || cmd.equals("features")) {
				int id = DatabaseUtil.asInt(params.get(1));
					showFeatureVector(id, out);
			}
			else if (cmd.equals("d") || cmd.equals("dis") || cmd.equals("disambig")  || cmd.equals("disambiguate")) {
				PhraseNode<? extends TermReference> root = getPhrases(params.get(1).toString());
				showDisambiguation(root, out);
			}
			else if (cmd.equals("ls") || cmd.equals("list")) {
				listConcepts(out);
			}
	}

	protected PhraseNode<? extends TermReference> getPhrases(String  s) {
			String[] ss = s.split("\\s\\[|;]\\s");
			List<Term> terms = new ArrayList<Term>(ss.length);
			for (String t: ss) {
				terms.add(new Term(t));
			}
			
			return new TermListNode<Term>(terms, 0);
	}

	public boolean isGlobalThesaurus() {
		return !isDatasetLocal(); 
	}
	
	protected GlobalConceptStore getGlobalConceptStore() {
		return (GlobalConceptStore)(Object)conceptStore; //XXX: FUGLY! generic my ass.
	}

	protected LocalConceptStore getLocalConceptStore() {
		return (LocalConceptStore)(Object)conceptStore; //XXX: FUGLY! generic my ass.
	}

	protected ProximityStore getProximityStore() throws PersistenceException {
		return conceptStore.getProximityStore();
	}

	protected FeatureStore<LocalConcept, Integer> getFeatureStore() throws PersistenceException {
		return conceptStore.getFeatureStore();
	}

	protected Disambiguator getDisambiguator() throws PersistenceException {
		if (disambiguator==null) {
			StoredMeaningFetcher meaningFetcher = new StoredMeaningFetcher(getLocalConceptStore());
			StoredFeatureFetcher<LocalConcept, Integer> featureFetcher = new StoredFeatureFetcher<LocalConcept, Integer>(getFeatureStore());
			disambiguator = new SlidingCoherenceDisambiguator( meaningFetcher, featureFetcher, 10 ); //FIXME: cache depth from config
			
			LeveledOutput.Trace trace = new LeveledOutput.Trace(out); 
			meaningFetcher.setTrace(trace);
			featureFetcher.setTrace(trace);
			disambiguator.setTrace(trace);
		}
		
		return disambiguator;
	}

	public void dumpStats() throws PersistenceException {
		Map<String, ? extends Number> m = ((WikiWordConceptStore)conceptStore).getStatisticsStore().getStatistics();
		
		List<String> nn = new ArrayList<String>(m.keySet());
		Collections.sort(nn);
		
		for (String n: nn) {
			Number v = m.get(n);
			prompt.getOut().write(n+": "+v+"\n"); //TODO: linebreak!
		}
	}
	
	public void listConcepts(ConsoleOutput out) throws PersistenceException {
		DataSet<? extends LocalConcept> meanings = getLocalConceptStore().getAllConcepts(minimalConceptSpec);
		out.writeConcepts(meanings);
	}		
	
	public void listMeaningsLocal(String term, ConsoleOutput out) throws PersistenceException {
		DataSet<? extends WikiWordConcept> meanings = getLocalConceptStore().getMeanings(term, resolvedConceptSpec);
		out.writeConcepts(meanings);
	}		

	public void listMeaningsGlobal(String lang, String term, ConsoleOutput out) throws PersistenceException {
		DataSet<GlobalConcept> meanings = getGlobalConceptStore().getMeanings(lang, term, resolvedConceptSpec);
		out.writeConcepts(meanings);
	}		

	public void showConcept(int id, ConsoleOutput out) throws PersistenceException {
		WikiWordConcept c = conceptStore.getConcept(id, detailedConceptSpec);
		out.writeConcept(c);
	}		

	public void showConcept(int id, String lang, ConsoleOutput out) throws PersistenceException {
		GlobalConcept c = getGlobalConceptStore().getConcept(id, detailedConceptSpec);
		out.writeConcept(c);
		
		LocalConcept lc = c.getLocalConcept(lang);
		if (lc!=null) {
			//FIXME: ugly hack to avoid failed auto-resolve, because the local concept is comming from a different store!
			if (out.getOutput() instanceof ConceptDumper) {
				((ConceptDumper)out.getOutput()).setMaxAutoResolve(0);
			}
			
			out.writeConcept(lc);
		}
	}		

	public void showConcept(String lang, int id, ConsoleOutput out) throws PersistenceException {
		LocalConceptStore lstore = getGlobalConceptStore().getLocalConceptStore(Corpus.forName(getStoreDataset().getCollection(), lang, tweaks));
		
		LocalConcept lc = lstore.getConcept(id, detailedConceptSpec);
		if (out.getOutput() instanceof ConceptDumper) {
			((ConceptDumper)out.getOutput()).setMaxAutoResolve(0);
		}
			
		out.writeConcept(lc);
	}		

	public void showEnvironment(int id, double min, ConsoleOutput out) throws PersistenceException {
		DataSet<WikiWordConcept> environment = getProximityStore().getEnvironment(id, min);
		List<WikiWordConcept> env = environment.load();
		Collections.sort(env, WikiWordConcept.byRelevance);
		
		for (WikiWordConcept e: env) {
			out.writeConcept(e);
		}
	}		

	public void showFeatureVector(int id, ConsoleOutput out) throws PersistenceException {
		ConceptFeatures conceptFeatures = getProximityStore().getConceptFeatures(id);
		out.writeFeatureVector(conceptFeatures.getFeatureVector());
	}		

	public void showDisambiguation(PhraseNode<? extends TermReference> root, ConsoleOutput out) throws PersistenceException {
		Disambiguator.Disambiguation r = getDisambiguator().disambiguate(root, null);
		out.writeInterpretation(r.getMeanings());
	}		

	public static void main(String[] argv) throws Exception {
		QueryConsole q = new QueryConsole();
		q.launch(argv);
	}

	@Override
	protected void createStores() throws PersistenceException, IOException {
		conceptStore = DatabaseConceptStores.createConceptStore(getConfiguredDataSource(), getConfiguredDataset(), tweaks, true, true);
		registerStore(conceptStore);
	}
}
