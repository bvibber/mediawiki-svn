package de.brightbyte.wikiword.rdf;

import java.io.PrintWriter;
import java.io.Writer;

import de.brightbyte.rdf.GenericRdfalizer;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.RdfPlatforms;
import de.brightbyte.rdf.RdfProperties;
import de.brightbyte.rdf.Rdfalizer;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.model.AbstractConceptOutput;
import de.brightbyte.wikiword.model.GlobalConcept;
import de.brightbyte.wikiword.model.LocalConcept;

public class RdfOutput<V, R extends V, A, W> extends AbstractConceptOutput {
	protected RdfPlatform<V, R, A, W> platform;
	protected W writer;
	
	protected Rdfalizer<V, R, A, LocalConcept> localConceptRdfalizer;
	protected Rdfalizer<V, R, A, GlobalConcept> globalConceptRdfalizer;
	
	protected WikiWordIdentifiers identifiers;
	
	@SuppressWarnings("unchecked")
	public RdfOutput(WikiWordIdentifiers identifiers, String platform, Writer writer, String format, DatasetIdentifier ds) throws RdfException, PersistenceException {
		this(identifiers, (RdfPlatform<V, R, A, W>)RdfPlatforms.newPlatform(platform));
		init(this.platform.newWriter(writer, format), ds);
	}
	
	public RdfOutput(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, W> platform, W writer, DatasetIdentifier ds) throws RdfException {
		this(identifiers, platform);
		init(writer, ds);
	}
	
	private void init(W writer, DatasetIdentifier ds) throws RdfException {
		this.writer = writer;
		
		localConceptRdfalizer = new GenericRdfalizer<V, R, A, LocalConcept>(platform);
		globalConceptRdfalizer = new GenericRdfalizer<V, R, A, GlobalConcept>(platform);

		localConceptRdfalizer.addProperties(new LocalConceptSkosProperties<V, R, A>(identifiers, platform));
		globalConceptRdfalizer.addProperties(new GlobalConceptSkosProperties<V, R, A>(identifiers, platform));
	}
	
	private RdfOutput(WikiWordIdentifiers identifiers, RdfPlatform<V, R, A, W> platform) {
		this.platform = platform;
		this.identifiers = identifiers;
	}
	
	public void addLocalConceptProperties(RdfProperties<V, R, A, LocalConcept> props) {
		localConceptRdfalizer.addProperties(props);		
	}

	public void startDocument() throws PersistenceException, RdfException {
		platform.writeHead(writer);
	}
	
	public void endDocument() throws PersistenceException, RdfException {
		platform.writeFoot(writer);
	}
	
	public void writeStatement(R subject, R predicate, V object) throws RdfException, PersistenceException {
		platform.writeStatement(writer, subject, predicate, object);
	}
	
	public void writeLocalConcept(LocalConcept concept) throws PersistenceException {
		try {
			A about = localConceptRdfalizer.getRdf(identifiers.localConceptBaseURI(concept.getCorpus()), WikiWordIdentifiers.localConceptID(concept.getName()), concept);
			platform.writeResource(writer, about);
		} catch (RdfException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void writeGlobalConcept(GlobalConcept concept) throws PersistenceException {
		try {
			A about = globalConceptRdfalizer.getRdf(identifiers.globalConceptBaseURI(concept.getDatasetIdentifier()), WikiWordIdentifiers.globalConceptID(concept.getId()), concept);
			platform.writeResource(writer, about);
		} catch (RdfException e) {
			throw new PersistenceException(e);
		}
	}
	
	public void close() throws PersistenceException {
		try {
			platform.closeWriter(writer);
		} catch (RdfException e) {
			throw new PersistenceException(e);
		}
	}

	public void flush() throws PersistenceException {
		try {
			platform.flushWriter(writer);
		} catch (RdfException e) {
			throw new PersistenceException(e);
		}
	}

	public PrintWriter getWriter() {
		return null;
	}
	
	public interface Relation<T, V, R extends V, A, W> {
		public R subject(T row) throws RdfException;
		public R predicate(T row) throws RdfException;
		public V object(T row) throws RdfException;
	}

	public static abstract class AbstractRelation<T, V, R extends V, A, W> implements Relation<T, V, R, A, W> {
		protected RdfPlatform<V, R, A, W> platform; 
		protected R predicate;
		
		public AbstractRelation(RdfPlatform<V, R, A, W> platform, R predicate) {
			this.platform = platform;
			this.predicate = predicate;
		}

		public R predicate(T row) {
			return predicate;
		}
	}
	
}
