package de.brightbyte.wikiword.query;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStreamWriter;
import java.io.Writer;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import de.brightbyte.data.cursor.DataSet;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.ConsoleApp;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.model.AbstractConceptOutput;
import de.brightbyte.wikiword.model.ConceptOutput;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.LocalConcept;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.rdf.RdfOutput;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.GlobalConceptStore;
import de.brightbyte.wikiword.store.LocalConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;

public class QueryConsole extends ConsoleApp<WikiWordConceptStore> {

	public QueryConsole() {
		super(true, true);
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

		public void writeGlobalConcept(GlobalConcept concept) throws PersistenceException {
			println();
			println("* (", concept.getRelevance(), ":", concept.getCardinality(), ") #", concept.getId(), ": ", concept.getName());
			writeConceptRelations(concept);
			println();
		}

		public void writeLocalConcept(LocalConcept concept) throws PersistenceException {
			println();
			println("* (", concept.getRelevance(), ":", concept.getCardinality(), ") #", concept.getId(), ": ", concept.getName());
			writeLocalConceptDescription(concept);
			writeConceptRelations(concept);
			println();
		}
		
		public void writeLocalConceptDescription(LocalConcept concept) throws PersistenceException {
			println(" - Definition:", concept.getDefinition());
			printlst(" - Terms", concept.getTerms());
		}
		
		public void writeConceptRelations(WikiWordConcept concept) throws PersistenceException {
			printlst(" - Similar", concept.getSimilar());
			printlst(" - Related", concept.getRelated());
			printlst(" - Broader", concept.getBroader());
			printlst(" - Narrower", concept.getNarrower());
			printlst(" - InLinks", concept.getInLinks());
			printlst(" - OutLinks", concept.getOutLinks());
			printlst(" - LangLinks", concept.getLanglinks());
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

					if (a instanceof WikiWordConceptReference) {
						WikiWordConceptReference r = (WikiWordConceptReference)a;
						int id = r.getId();
						String n = r.getName();
						
						if (n==null && c < maxAutoResolve) {
							WikiWordConcept x = ((WikiWordConceptStore)conceptStore).getConcept(id);
							r = x.getReference();
							n = r.getName();
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
	public void runCommand(List<String> params) throws Exception {
		String cmd = params.get(0);
		cmd = cmd.trim().toLowerCase();
		
		String format = null;
		File target = null;
		
		if (params.size()>1 && params.get(params.size()-1).startsWith(">")) {
			target = new File( params.get(params.size()-1).substring(1).trim() );
			params = params.subList(0, params.size()-1);
		}
		
		if (params.size()>1 && params.get(params.size()-1).startsWith("|")) {
			format = params.get(params.size()-1).substring(1).trim();
			params = params.subList(0, params.size()-1);
		}
		
		ConsoleOutput out = getConceptOutput(format, target);
		
		try {
			if (cmd.equals("statistics") || cmd.equals("stats")) {
				dumpStats();
			}
			else if (cmd.equals("m") || cmd.equals("mng") || cmd.equals("meanings")) {
				if (isGlobalThesaurus()) {
					String lang = params.get(1);
					String term = params.get(2);
					listMeaningsGlobal(lang, term, out);
				}
				else {
					String term = params.get(1);
					listMeaningsLocal(term, out);
				}
			}
			else if (cmd.equals("s") || cmd.equals("cat") || cmd.equals("show")) {
				if (params.size()>2 && isGlobalThesaurus()) {
					String id = params.get(1);
					String lang = params.get(2);
					showConcept(Integer.parseInt(id), lang, out);
				}
				else {
					String id = params.get(1);
					showConcept(Integer.parseInt(id), out);
				}
			}
			else if (cmd.equals("ls") || cmd.equals("list")) {
				listConcepts(out);
			}
		}
		finally {
			out.close();
		}
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
		DataSet<LocalConcept> meanings = getLocalConceptStore().getConceptInfoStore().getAllConcepts();
		out.writeConcepts(meanings);
	}		
	
	public void listMeaningsLocal(String term, ConsoleOutput out) throws PersistenceException {
		DataSet<LocalConcept> meanings = getLocalConceptStore().getMeanings(term);
		out.writeConcepts(meanings);
	}		

	public void listMeaningsGlobal(String lang, String term, ConsoleOutput out) throws PersistenceException {
		DataSet<GlobalConcept> meanings = getGlobalConceptStore().getMeanings(lang, term);
		out.writeConcepts(meanings);
	}		

	public void showConcept(int id, ConsoleOutput out) throws PersistenceException {
		WikiWordConcept c = conceptStore.getConcept(id);
		out.writeConcept(c);
	}		

	public void showConcept(int id, String lang, ConsoleOutput out) throws PersistenceException {
		GlobalConcept c = getGlobalConceptStore().getConcept(id);
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
		
		LocalConcept lc = lstore.getConcept(id);
		if (out.getOutput() instanceof ConceptDumper) {
			((ConceptDumper)out.getOutput()).setMaxAutoResolve(0);
		}
			
		out.writeConcept(lc);
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
