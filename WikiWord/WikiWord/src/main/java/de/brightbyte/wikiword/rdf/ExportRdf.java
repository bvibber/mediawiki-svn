package de.brightbyte.wikiword.rdf;

import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.OutputStream;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.Date;
import java.util.HashMap;
import java.util.Map;

import de.brightbyte.db.DatabaseAccess;
import de.brightbyte.db.DatabaseTable;
import de.brightbyte.db.DatabaseUtil;
import de.brightbyte.rdf.RdfException;
import de.brightbyte.rdf.RdfPlatform;
import de.brightbyte.rdf.RdfPlatforms;
import de.brightbyte.rdf.vocab.DC;
import de.brightbyte.rdf.vocab.OWL;
import de.brightbyte.rdf.vocab.RDF;
import de.brightbyte.rdf.vocab.SKOS;
import de.brightbyte.rdf.vocab.XSDatatypes;
import de.brightbyte.util.PersistenceException;
import de.brightbyte.util.Processor;
import de.brightbyte.wikiword.CliApp;
import de.brightbyte.wikiword.ConceptType;
import de.brightbyte.wikiword.Corpus;
import de.brightbyte.wikiword.DatasetIdentifier;
import de.brightbyte.wikiword.ExtractionRule;
import de.brightbyte.wikiword.StoreBackedApp;
import de.brightbyte.wikiword.model.WikiWordConcept;
import de.brightbyte.wikiword.model.WikiWordConceptReference;
import de.brightbyte.wikiword.schema.GlobalConceptStoreSchema;
import de.brightbyte.wikiword.schema.LocalConceptStoreSchema;
import de.brightbyte.wikiword.schema.StatisticsStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordConceptStoreSchema;
import de.brightbyte.wikiword.schema.WikiWordStoreSchema;
import de.brightbyte.wikiword.store.DatabaseConceptStores;
import de.brightbyte.wikiword.store.DatabaseGlobalConceptStore;
import de.brightbyte.wikiword.store.DatabaseLocalConceptStore;
import de.brightbyte.wikiword.store.DatabaseWikiWordConceptStore;
import de.brightbyte.wikiword.store.WikiWordConceptStore;
import de.brightbyte.wikiword.store.DatabaseWikiWordConceptStore.DatabaseStatisticsStore;

public class ExportRdf<V, R extends V, A, W> extends StoreBackedApp<WikiWordConceptStore> {
	
	protected RdfPlatform<V, R, A, W> platform;
	protected RdfOutput<V, R, A, W> output;
	
	protected boolean plainSkos = false;
	protected boolean dumpLinks = false;
	
	protected boolean noScore = false;
	protected boolean forceCutoff = false;
	protected int cutoff = 0;
	protected int minLangMatch = 2;
	
	protected RDF<V, R> rdf;
	protected WW<V, R> ww;
	protected SKOS<V, R> skos;
	protected OWL<V, R> owl;
	protected DC<V, R> dc;
	protected XSDatatypes<V, R> xs;
	
	public ExportRdf() {
		super(true, true);
	}

	@SuppressWarnings("unchecked")
	@Override
	protected void prepareApp() throws Exception {
		if (identifiers.getLocalDatasetQualifier().equals("*")) {
			warn(GENERIC_QUALIFIER_WARNING);
		}
		
		if (!isDatasetLocal() && !identifiers.globalConceptBaseURI(getStoreDataset()).matches(".*\\d.*")) {
			warn(GENERIC_COLLECTION_WARNING);
		}
		
		plainSkos = args.isSet("skos");
		dumpLinks = args.isSet("assoc");
		forceCutoff = args.isSet("force-cutoff");
		cutoff = args.getIntOption("cutoff", 0);
		noScore = args.isSet("noscore");
		minLangMatch = args.getIntOption("min-langmatch", 2);
		if (minLangMatch<1) minLangMatch = 1;
		
		if (plainSkos) info("Using plain SKOS vocabulary.");
		else info("Using WikiWord RDF vocabulary.");
		
		if (cutoff>1) {
			info("Using meaning cutoff value "+cutoff);
			if (forceCutoff) info("Cutoff applies to all meanings");
			else info("Cutoff applies to implicit meanings only");
		}
		else {
			info("Using all meanings");
		}

		info("Concepts considered similar if they have at leat "+minLangMatch+" language links in common");
		
		platform = RdfPlatforms.newPlatform( args.getStringOption("platform", null) );
		
		if (!plainSkos) ww = platform.aquireNamespace(WW.class);

		rdf = platform.aquireNamespace(RDF.class);
		skos = platform.aquireNamespace(SKOS.class);
		owl = platform.aquireNamespace(OWL.class);
		dc = platform.aquireNamespace(DC.class);
		xs = platform.aquireNamespace(XSDatatypes.class);
		
		platform.addNamespace(WikiWordIdentifiers.conceptTypeBaseURI(), "wwct");		
	}

	@Override
	protected void declareOptions() {
		super.declareOptions();
		
		declareOption("platform", null, true, String.class, "RDF platform class");
		declareOption("format", null, true, String.class, "RDF output format (e.g. turle, n3, xml)");
		declareOption("skos", null, false, Boolean.class, "Activate plain SKOS mode (disable WikiWord vocabulary)");
		declareOption("assoc", null, false, Boolean.class, "Include hyperlinks as ww:assoc relation (no effect if used with --skos)");
		declareOption("noscore", null, false, Boolean.class, "Exclude all scores and ranks from the the output (redundant if used with --skos)");
		declareOption("cutoff", null, true, Integer.class, "Specifies the minimum frequency for term-associations. Increases precision at the cost of recall. " +
															"Typical values are 2 or 3, lower values disable the cutoff. " +
															"Per default only applies to implicit associations via usage as link-text.");
		declareOption("force-cutoff", null, false, Boolean.class, "Forces cutoff even for explicitly defined meanings (small boost in precision, big drop in recall)");
		declareOption("min-langmatch", null, true, Integer.class, "Specifies the minimum overlap of language links requited for concepts to be considered similar." +
				"Increases precision at the cost of recall. Typical values are 2 to 5, 1 causes a very broad interpretation of 'similar'. " +
				"Lower values are illegal.");
	}

	protected V newLabel(String s) throws RdfException {
		return platform.newLiteral(s, getCorpus().getLanguage());
	}
	
	protected interface IdentityProvider<R> {
		public R getResource(ResultSet rs) throws SQLException, RdfException;
		public R getConceptScheme(ResultSet rs) throws RdfException, SQLException;
	}
	
	protected static final String CONCEPT_IDENTITY_FIELD = "concept_identity";
	protected static final String CONCEPT_OTHER_FIELD = "concept_other";
	
	protected abstract class AbstractConceptProvider implements IdentityProvider<R> {
		protected R scheme;
		
		public AbstractConceptProvider(DatasetIdentifier ds) throws RdfException {
			this.scheme = aquireConceptScheme(ds); 
		}

		public R getConceptScheme(ResultSet rs) {
			return scheme;
		}
	}

	protected class LocalConceptProvider extends AbstractConceptProvider {
		protected String baseURI;
		protected String fieldName; 
		
		public LocalConceptProvider(Corpus corpus) throws RdfException {
			this(corpus, CONCEPT_IDENTITY_FIELD);
		}
		
		public LocalConceptProvider(Corpus corpus, String field) throws RdfException {
			super(corpus);
			this.baseURI = identifiers.localConceptBaseURI(corpus);
			this.fieldName = field;
		}

		public R getResource(ResultSet rs) throws SQLException, RdfException {
			String name = DatabaseUtil.asString(rs.getObject(fieldName));
			return platform.newResource(baseURI, WikiWordIdentifiers.localConceptID(name));
		}
	}

	protected class GlobalConceptProvider extends AbstractConceptProvider {
		protected String baseURI;
		protected String fieldName; 
		
		public GlobalConceptProvider(DatasetIdentifier ds) throws RdfException {
			this(ds, CONCEPT_IDENTITY_FIELD);
		}
		
		public GlobalConceptProvider(DatasetIdentifier ds, String field) throws RdfException {
			super(ds);
			this.baseURI = identifiers.globalConceptBaseURI(ds);
			this.fieldName = field;
		}

		public R getResource(ResultSet rs) throws SQLException, RdfException {
			int id = DatabaseUtil.asInt(rs.getObject(fieldName));
			return platform.newResource(baseURI, WikiWordIdentifiers.globalConceptID(id));
		}
	}

	protected class RemoteConceptProvider implements IdentityProvider<R> {
		protected String langField;
		protected String nameField;
		
		protected Map<String, LocalConceptProvider> providers = new HashMap<String, LocalConceptProvider>(); 
		
		public RemoteConceptProvider(String langField, String nameField) {
			this.langField = langField;
			this.nameField = nameField;
		}

		public R getResource(ResultSet rs) throws SQLException, RdfException {
			String lang = DatabaseUtil.asString(rs.getObject(langField));
			
			LocalConceptProvider p = aquireLocalConceptProvider(lang);
			return p.getResource(rs);
		}

		protected LocalConceptProvider aquireLocalConceptProvider(String lang) throws RdfException {
			LocalConceptProvider p = providers.get(lang);
			if (p==null) {
				Corpus c = Corpus.forName(getConfiguredCollectionName(), lang, tweaks);
				p = new LocalConceptProvider(c, nameField);
				providers.put(lang, p);
			}
			
			return p;
		}

		public R getConceptScheme(ResultSet rs) throws RdfException, SQLException {
			String lang = DatabaseUtil.asString(rs.getObject(langField));
			return aquireConceptScheme(aquireCorpus(lang));
		}
	}

	protected class RemoteResourceProvider implements IdentityProvider<R> {
		protected String langField;
		protected String nameField;
		
		public RemoteResourceProvider(String langField, String nameField) {
			this.langField = langField;
			this.nameField = nameField;
		}

		public R getResource(ResultSet rs) throws SQLException, RdfException {
			String lang = DatabaseUtil.asString(rs.getObject(langField));
			String name = DatabaseUtil.asString(rs.getObject(nameField));
			
			Corpus c = aquireCorpus(lang);
			R resource = platform.newResource(c.getURL().toString(), name);
			return resource;
		}

		public R getConceptScheme(ResultSet rs) throws RdfException, SQLException {
			String lang = DatabaseUtil.asString(rs.getObject(langField));
			return aquireConceptScheme(aquireCorpus(lang));
		}
	}

	protected Map<String, Corpus> corpora = new HashMap<String, Corpus>(); 
	
	protected Corpus aquireCorpus(String lang) {
		Corpus c = corpora.get(lang);
		if (c==null) {
			c = Corpus.forName(getConfiguredCollectionName(), lang, tweaks);
			corpora.put(lang, c);
		}
		
		return c;
	}
	
	protected Map<DatasetIdentifier, R> schemes = new HashMap<DatasetIdentifier, R>(); 
	
	protected R aquireConceptScheme(DatasetIdentifier c) throws RdfException {
		R sch = schemes.get(c);
		if (sch==null) {
			sch = platform.newResource(WikiWordIdentifiers.base.toString(), identifiers.datasetLName(c));
			schemes.put(c, sch);
		}
		
		return sch;
	}
	
	/*
	protected R newGlobalConceptResource(String s) throws RdfException {
		return platform.newResource(getDataset().getUri().toString(), s);
	}
	*/
	protected abstract class RelationProcessor implements Processor<ResultSet> {
		public void process(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			while (rs.next()) {
				processRow(rs);
			}
		}

		protected abstract void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException;
	}
	
	protected abstract class LocalRelationProcessor extends CommonRelationProcessor {
		protected String language;

		public LocalRelationProcessor(Corpus corpus) throws RdfException {
			this(new LocalConceptProvider(corpus), corpus.getLanguage());
		}
		
		public LocalRelationProcessor(IdentityProvider<R> provider, String language) {
			super(provider);
			this.language = language;
		}
	}
	
	protected abstract class CommonRelationProcessor extends RelationProcessor {
		protected IdentityProvider<R> conceptProvider;

		public CommonRelationProcessor(IdentityProvider<R> conceptProvider) {
			this.conceptProvider = conceptProvider;
		}
		
		protected R getConcept(ResultSet rs) throws SQLException, RdfException {
			return conceptProvider.getResource(rs);
		}
	}
	
	protected class ConceptStatsProcessor extends CommonRelationProcessor {

		public ConceptStatsProcessor(IdentityProvider<R> conceptProvider) {
			super(conceptProvider);
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			double idf = DatabaseUtil.asDouble(rs.getObject("idf"));
			if (idf>0) writeDoubleProperty(concept, ww.idfScore, idf);

			double lhs = DatabaseUtil.asDouble(rs.getObject("lhs"));
			if (lhs>0) writeDoubleProperty(concept, ww.lhsScore, lhs);

			double indegree = DatabaseUtil.asDouble(rs.getObject("in_degree"));
			if (indegree>0) writeDoubleProperty(concept, ww.inDegree, indegree);

			double outdegree = DatabaseUtil.asDouble(rs.getObject("out_degree"));
			if (outdegree>0) writeDoubleProperty(concept, ww.outDegree, outdegree);

			double linkdegree = DatabaseUtil.asDouble(rs.getObject("link_degree"));
			if (linkdegree>0) writeDoubleProperty(concept, ww.linkDegree, linkdegree);
			
			int idfrank = DatabaseUtil.asInt(rs.getObject("idf_rank"));
			if (idfrank>0) writeIntProperty(concept, ww.idfRank, idfrank);

			int lhsrank = DatabaseUtil.asInt(rs.getObject("lhs_rank"));
			if (lhsrank>0) writeIntProperty(concept, ww.lhsRank, lhsrank);

			int inrank = DatabaseUtil.asInt(rs.getObject("in_rank"));
			if (inrank>0) writeIntProperty(concept, ww.inRank, inrank);

			int outrank = DatabaseUtil.asInt(rs.getObject("out_rank"));
			if (outrank>0) writeIntProperty(concept, ww.outRank, outrank);

			int linkrank = DatabaseUtil.asInt(rs.getObject("link_rank"));
			if (linkrank>0) writeIntProperty(concept, ww.linkRank, linkrank);
		}
	}
	
	protected class ConceptOriginProcessor extends CommonRelationProcessor {
		protected IdentityProvider<R> localProvider;
		protected IdentityProvider<R> resourceProvider;
		
		
		public ConceptOriginProcessor(IdentityProvider<R> globalProvider, IdentityProvider<R> localProvider, IdentityProvider<R> resourceProvider) {
			super(globalProvider);
			
			this.localProvider = localProvider;
			this.resourceProvider = resourceProvider;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			
			R other = localProvider.getResource(rs);
			output.writeStatement(concept, owl.sameAs, other);
			output.writeStatement(other, owl.sameAs, concept);
			
			output.writeStatement(other, rdf.type, skos.Concept);
			output.writeStatement(other, skos.inScheme, localProvider.getConceptScheme(rs));
			
			R resource = resourceProvider.getResource(rs);
			output.writeStatement(concept, skos.definition, resource);
			//FIXME: use SKOS.isPrimarySubjectOf !
		}
	}
	
	protected class CommonConceptPropertyProcessor extends CommonRelationProcessor {

		protected String language;
		
		public CommonConceptPropertyProcessor(IdentityProvider<R> conceptProvider, String language) {
			super(conceptProvider);
			
			this.language = language; 
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			
			String name = DatabaseUtil.asString(rs.getObject("name"));
			
			if (plainSkos) {
				if (language!=null) {
					writeStringProperty(concept, skos.prefLabel, name, language);
				}
			}
			else {
				writeStringProperty(concept, ww.displayLabel, name, language);
				
				int t = DatabaseUtil.asInt(rs.getObject("type"));
				ConceptType type = conceptStore.getConceptType(((Number)t).intValue());
	
				R r = platform.newResource(WikiWordIdentifiers.conceptTypeBaseURI(), type.getName());
				output.writeStatement(concept, ww.type, r);
			}

			output.writeStatement(concept, rdf.type, skos.Concept);
			output.writeStatement(concept, skos.inScheme, conceptProvider.getConceptScheme(rs));
		}
	}
	
	protected class ConceptNameProcessor extends CommonRelationProcessor {

		protected String language;
		
		public ConceptNameProcessor(IdentityProvider<R> conceptProvider, String language) {
			super(conceptProvider);
			
			this.language = language; 
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			
			String name = DatabaseUtil.asString(rs.getObject("name"));
			
			if (plainSkos) {
				if (language!=null) {
					writeStringProperty(concept, skos.prefLabel, name, language);
				}
			}
			else {
				writeStringProperty(concept, ww.displayLabel, name, language);
			}
		}
	}
	
	protected class LocalConceptPropertyProcessor extends LocalRelationProcessor {
		protected Corpus corpus;

		public LocalConceptPropertyProcessor(Corpus corpus) throws RdfException {
			this(new LocalConceptProvider(corpus), corpus);
		}

		public LocalConceptPropertyProcessor(IdentityProvider<R> provider, Corpus corpus) {
			super(provider, corpus.getLanguage());
			this.corpus = corpus;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			
			String name = DatabaseUtil.asString(rs.getObject("name"));
			R resource = platform.newResource(corpus.getURL().toString(), name);
			output.writeStatement(concept, skos.definition, resource);
		}
	}
	
	protected class ConceptDefinitionProcessor extends LocalRelationProcessor {

		public ConceptDefinitionProcessor(Corpus corpus) throws RdfException {
			super(corpus);
		}

		public ConceptDefinitionProcessor(IdentityProvider<R> provider, String language) {
			super(provider, language);
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			String definition = DatabaseUtil.asString(rs.getObject("definition"));

			R concept = getConcept(rs);
			writeStringProperty(concept, skos.definition, definition, language);
		}

	};
	
	protected class ConceptRelationProcessor extends CommonRelationProcessor {
		protected IdentityProvider<R> otherProvider;
		protected R predicate;
		
		public ConceptRelationProcessor(IdentityProvider<R> provider, R predicate, IdentityProvider<R> otherProvider) {
			super(provider);
			this.otherProvider = otherProvider;
			this.predicate = predicate;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			R other = otherProvider.getResource(rs);
			output.writeStatement(concept, predicate, other);
		}
	};
	
	protected class ConceptRelatednessProcessor extends CommonRelationProcessor {
		protected IdentityProvider<R> otherProvider;
		
		public ConceptRelatednessProcessor(IdentityProvider<R> provider, IdentityProvider<R> otherProvider) {
			super(provider);
			this.otherProvider = otherProvider;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			R other = otherProvider.getResource(rs);
			
			int langref =  DatabaseUtil.asInt(rs.getObject("langref"), 0);
			int langmatch = DatabaseUtil.asInt(rs.getObject("langmatch"), 0);
			int bilink =   DatabaseUtil.asInt(rs.getObject("bilink"), 0);
			
			//NOTE: the inverse relations are in the db, so don't make them explicit here!
			
			if (langref>0 || langmatch>=minLangMatch) {
				if (plainSkos) output.writeStatement(concept, skos.related, other);
				else output.writeStatement(concept, ww.similar, other);
			}
			else if (bilink>0) {
				output.writeStatement(concept, skos.related, other);
			}
		}
	};
	
	protected class ConceptHierarchyProcessor extends CommonRelationProcessor {
		protected IdentityProvider<R> otherProvider;
		
		public ConceptHierarchyProcessor(IdentityProvider<R> provider, IdentityProvider<R> otherProvider) {
			super(provider);
			this.otherProvider = otherProvider;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			R concept = getConcept(rs);
			R other = otherProvider.getResource(rs);

			//NOTE: the inverse relations are not in the db, so make them explicit here!
			
			output.writeStatement(concept, skos.narrower, other);
			output.writeStatement(other, skos.broader, concept);
		}
	};
	
	protected class ConceptLinkProcessor extends ConceptRelationProcessor {
		
		public ConceptLinkProcessor(IdentityProvider<R> provider, IdentityProvider<R> otherProvider) {
			super(provider, ww.assoc, otherProvider);
		}
	};
	
	/*
	protected class ConceptResourceProcessor extends LocalRelationProcessor {

		protected Corpus corpus;

		public ConceptResourceProcessor(Corpus corpus) {
			this(corpus);
		}

		public ConceptResourceProcessor(IdentityProvider<R> provider, Corpus corpus) {
			super(provider, corpus.getLanguage());
			this.corpus = corpus;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			String rc = DatabaseUtil.asString(rs.getObject("name"));

			R concept = getConcept(rs);
			R resource = platform.newResource(corpus.getURL().toString(), rc);
			output.writeStatement(concept, skos.definition, resource);
		}

	};
	*/
	
	protected class ConceptMeaningProcessor extends LocalRelationProcessor {

		protected String nameField;
		
		public ConceptMeaningProcessor(Corpus corpus, String nameField) throws RdfException {
			super(corpus);
			this.nameField = nameField;
		}

		public ConceptMeaningProcessor(IdentityProvider<R> provider, String language, String nameField) {
			super(provider, language);
			this.nameField = nameField;
		}

		@Override
		protected void processRow(ResultSet rs) throws SQLException, RdfException, PersistenceException {
			String term = DatabaseUtil.asString(rs.getObject("term_text"));
			
			if (plainSkos && nameField!=null) {
				//NOTE: in plain skos mode we already have a prefLabel; we must not repeat that
				//      value as an altLabel
				String name = DatabaseUtil.asString(rs.getObject(nameField));
				if (name.equals(term)) return;
			}

			R concept = getConcept(rs);
			writeStringProperty(concept, skos.altLabel, term, language);
		}

	};
		
	protected void writeStringProperty(R subject, R predicate, String value, String language) throws RdfException, PersistenceException {
		V object = platform.newLiteral(value, language);
		output.writeStatement(subject, predicate, object);
	}

	protected void writeIntProperty(R subject, R predicate, int value) throws RdfException, PersistenceException {
		V object = platform.newLiteral(String.valueOf(value), xs._int);
		output.writeStatement(subject, predicate, object);
	}

	protected void writeDoubleProperty(R subject, R predicate, double value) throws RdfException, PersistenceException {
		V object = platform.newLiteral(String.valueOf(value), xs._double);
		output.writeStatement(subject, predicate, object);
	}

	protected OutputStream getExportTarget() throws IOException {
		String n = getTargetFileName();
		File f = new File(n);
		return new BufferedOutputStream(new FileOutputStream(f));
	}
	
	@Override
	protected void run() throws Exception {
		OutputStream os = getExportTarget();
		W w = platform.newWriter(os, args.getStringOption("format", "turtle"));
		
		if (isDatasetLocal()) {
			Corpus c = getCorpus();
			platform.setBaseURI(identifiers.localConceptBaseURI(c)); //concepts
			platform.addNamespace(c.getURL().toString(), c.getLanguage()+"wiki");    //documents
		}
		else {
			DatasetIdentifier ds = getStoreDataset();
			platform.setBaseURI(identifiers.globalConceptBaseURI(ds));
			
			DatabaseGlobalConceptStore st = (DatabaseGlobalConceptStore) conceptStore; //global concepts
			Corpus[] cc = st.getLanguages();
			for (Corpus c: cc) {
				platform.addNamespace(identifiers.localConceptBaseURI(c), c.getLanguage()); //local concepts
				platform.addNamespace(c.getURL().toString(), c.getLanguage()+"wiki");    //documents
			}
		}
		
		output = new RdfOutput<V, R, A, W>(identifiers, platform, w, getStoreDataset());
		output.startDocument();
		
		
		if (isDatasetLocal()) {
			DatabaseLocalConceptStore st = (DatabaseLocalConceptStore) conceptStore;
			LocalConceptProvider provider = new LocalConceptProvider(st.getCorpus(), CONCEPT_IDENTITY_FIELD); 
			LocalConceptProvider other = new LocalConceptProvider(st.getCorpus(), CONCEPT_OTHER_FIELD);
			R scheme = platform.newResource(WikiWordIdentifiers.base.toString(), identifiers.datasetLName(st.getDatasetIdentifier()));
			
			dumpSchemeInfo(st, scheme);
			dumpConceptsCommon(st, provider, other, true, st.getCorpus().getLanguage(), scheme);
			dumpConceptsLocal(st, provider);
		}
		else {
			DatabaseGlobalConceptStore st = (DatabaseGlobalConceptStore) conceptStore;
			GlobalConceptProvider provider = new GlobalConceptProvider(st.getDatasetIdentifier(), CONCEPT_IDENTITY_FIELD); 
			GlobalConceptProvider other = new GlobalConceptProvider(st.getDatasetIdentifier(), CONCEPT_OTHER_FIELD); 
			R scheme = platform.newResource(WikiWordIdentifiers.base.toString(), identifiers.datasetLName(st.getDatasetIdentifier()));

			dumpSchemeInfo(st, scheme);
			dumpConceptsCommon(st, provider, other, false, null, scheme);
			dumpConceptsGlobal(st, provider);
			
			Corpus[] cc = st.getLanguages();
			for (Corpus corpus: cc) {
				DatabaseLocalConceptStore lst = st.getLocalConceptStore(corpus);
				dumpConceptsOrigin(st, lst, provider); //note: use global concept provider.
			}
		}
		
		output.endDocument();
		
		output.close();
		platform.closeWriter(w);
	}
	
	public static final String RIGHTS_DISCLAIMER = "This data was automatically generated " +
		"by the WikiWord system, which uses the structure of Wikipedia as a basis for analysis. " +
		"It is believed that the generation of this data does not involve crative originality and " +
		"is thus not subject to copytight. Wherever text is cited from wikipedia, the copyright " +
		"to that text remains with the individual authors. Such original text is included in the " +
		"form of concept definitions, " +
		"each of which is accompanied by the URL of the definition in Wikipedia. This URL can be used " +
		"to access a list of the original authors.";
	
	public static final String DESCRIPTION_INTRO = "This is an thesaurus automatically generated from Wikipedia " +
			"by the WikiWord system.";

	public static final String GENERIC_QUALIFIER_WARNING = "No local dataset qualifier was defined! " +
	"This means that URIs used in generated RDF may be only locally unique." +
	"\n\tTo allow for globally usable URIs, plase set the value of " +
	"tweak rdf.dataset.qualifier to your domain name or another unique identifier.";
	
	public static final String GENERIC_COLLECTION_WARNING = "The collection name you are using " +
	"for export appears to be generic. " +
	"\n\tConsider using a collection name that contains a date or some similar " +
	"numbering scheme to ensure uniqueness.";
	
	protected void dumpSchemeInfo(DatabaseWikiWordConceptStore st, R scheme) throws RdfException, PersistenceException {
		DatasetIdentifier ds = st.getDatasetIdentifier();
		
		String date = new Date().toString(); //TODO: nicer format, time zone indicator, etc...
		String description = DESCRIPTION_INTRO;
		
		if (identifiers.getLocalDatasetQualifier().equals("*")) {
			description += "\n\tWARNING: "+GENERIC_QUALIFIER_WARNING;
		}
		
		output.writeStatement(scheme, dc.identifier, platform.newLiteral(identifiers.datasetURI(st.getDatasetIdentifier()), xs.anyURI));
		if (!identifiers.getLocalDatasetQualifier().equals("*")) {
			output.writeStatement(scheme, dc.creator, platform.newLiteral("WikiWord ("+identifiers.getLocalDatasetQualifier()+")", (String)null));
		}
		else {
			output.writeStatement(scheme, dc.creator, platform.newLiteral("WikiWord", (String)null));
		}
		output.writeStatement(scheme, dc.created, platform.newLiteral(date, xs.date));
		output.writeStatement(scheme, dc.title, platform.newLiteral(ds.getQName(), (String)null));
		output.writeStatement(scheme, dc.description, platform.newLiteral(description.replaceAll("\\s+", " "), "en"));
		output.writeStatement(scheme, dc.rights, platform.newLiteral(RIGHTS_DISCLAIMER.replaceAll("\\s+", " "), "en"));
		
		output.writeStatement(scheme, rdf.type, skos.ConceptScheme);
		
		if (ds instanceof Corpus) {
			Corpus c = (Corpus)ds; 
			output.writeStatement(scheme, dc.language, platform.newLiteral(c.getLanguage(), xs.language));
			output.writeStatement(scheme, dc.source, platform.newLiteral(((Corpus)ds).getURL().toString(), xs.anyURI));
		}
		
		if (st instanceof DatabaseGlobalConceptStore) {
			Corpus[] cc = ((DatabaseGlobalConceptStore)st).getLanguages();
			
			for (Corpus c: cc) {
				output.writeStatement(scheme, dc.source, platform.newLiteral(c.getURL().toString(), xs.anyURI));				

				R lsch = platform.newResource(WikiWordIdentifiers.base.toString(), identifiers.datasetLName(c));
				output.writeStatement(lsch, rdf.type, skos.ConceptScheme);
				output.writeStatement(lsch, dc.language, platform.newLiteral(c.getLanguage(), xs.language));
			}
		}
		
		//TODO: list top contents (roots)
		//      listHierarchyRoots
	}

	protected void dumpConceptsLocal(DatabaseLocalConceptStore store, IdentityProvider<R> provider) throws Exception {
		LocalConceptStoreSchema db = (LocalConceptStoreSchema) ((DatabaseWikiWordConceptStore)store).getDatabaseAccess();
		Corpus corpus = store.getCorpus();
		String lang = corpus.getLanguage();

		String sql;
		String where;
		String rest;
		
		where= null;
		rest= null;
		
		sql = "select *, name as "+CONCEPT_IDENTITY_FIELD
			+ " from "+db.getSQLTableName("concept");
		dumpRelation("names", sql, where, rest, db, "concept", "id", 1, new LocalConceptPropertyProcessor(provider, corpus));

		sql = "select *, name as "+CONCEPT_IDENTITY_FIELD
			+ " from "+db.getSQLTableName("definition")
		    + " join "+db.getSQLTableName("concept")+" on concept = id";
		dumpRelation("definitions", sql, where, rest, db, "definition", "concept", 1, new ConceptDefinitionProcessor(provider, lang));

		sql = "select *, concept_name as "+CONCEPT_IDENTITY_FIELD
			+ " from "+db.getSQLTableName("meaning");
		
		where = getCutoffCondition();
		
		dumpRelation("meaning", sql, where, rest, db, "meaning", "concept", 1, new ConceptMeaningProcessor(provider, lang, "concept_name"));
	}
	
	protected String getCutoffCondition() {
		if (cutoff<2) return null;
		
		String w = "freq >= "+cutoff;
		if (!forceCutoff) w+= " OR rule > "+ExtractionRule.TERM_FROM_LINK.getCode();
		return "("+w+")";
	}

	protected void dumpConceptsOrigin(DatabaseGlobalConceptStore gstore, DatabaseLocalConceptStore lstore, IdentityProvider<R> provider) throws Exception {
		GlobalConceptStoreSchema gdb = (GlobalConceptStoreSchema) ((DatabaseWikiWordConceptStore)gstore).getDatabaseAccess();
		LocalConceptStoreSchema ldb = (LocalConceptStoreSchema) ((DatabaseWikiWordConceptStore)lstore).getDatabaseAccess();
		Corpus corpus = lstore.getCorpus();
		String lang = corpus.getLanguage();

		String sql;
		String where;
		String rest;
		
		where= "O.lang = "+lstore.getDatabaseAccess().quoteString(lstore.getCorpus().getLanguage());
		rest= null;
		
		sql = "select D.*, global_concept as "+CONCEPT_IDENTITY_FIELD
			+ " from "+ldb.getSQLTableName("definition")+" AS D "
			+ " join "+gdb.getSQLTableName("origin")+" as O on local_concept = D.concept";
		dumpRelation("definitions-"+lang, sql, where, rest, gdb, "origin", "local_concept", 1, new ConceptDefinitionProcessor(provider, lang));

		sql = "select M.*, local_concept_name, global_concept as "+CONCEPT_IDENTITY_FIELD
			+ " from "+ldb.getSQLTableName("meaning")+" AS M "
			+ " join "+gdb.getSQLTableName("origin")+" as O on local_concept = M.concept";
		
		String w = getCutoffCondition();
		if (w!=null) where += " AND " + w;

		dumpRelation("meaning-"+lang, sql, where, rest, gdb, "origin", "local_concept", 1, new ConceptMeaningProcessor(provider, lang, "local_concept_name"));

		if (plainSkos) {
			sql = "select local_concept_name as name, global_concept as "+CONCEPT_IDENTITY_FIELD
				+ " from "+gdb.getSQLTableName("origin")+" as O";
			dumpRelation("name-"+lang, sql, where, rest, gdb, "origin", "local_concept", 1, new ConceptNameProcessor(provider, lang));
		}
	}
	
	protected void dumpConceptsCommon(DatabaseWikiWordConceptStore store, IdentityProvider<R> provider, IdentityProvider<R> other, boolean useNames, String language, R scheme) throws Exception {
		DatabaseWikiWordConceptStore st = (DatabaseWikiWordConceptStore)store;
		WikiWordConceptStoreSchema db = (WikiWordConceptStoreSchema)st.getDatabaseAccess();
		
		DatabaseStatisticsStore sstore = null;
		StatisticsStoreSchema sdb = null;
		
		if (!plainSkos && !noScore) {
			if (st.areStatsComplete()) {
				sstore = (DatabaseStatisticsStore)((DatabaseWikiWordConceptStore)store).getStatisticsStore();
				sdb = (StatisticsStoreSchema)sstore.getDatabaseAccess();
			}
			else {
				warn("Statistics not calcuated, will not output scores and ranks! Use BuildStatistics to generate.");
			}
		}

		String sql;
		String where;
		String rest;
		
		where= null;
		rest= null;
		
		//basic properties (type)
		sql = "select *, "+(useNames?"name":"id")+" as "+CONCEPT_IDENTITY_FIELD
			+ " from "+db.getSQLTableName("concept");
		dumpRelation("properties", sql, where, rest, db, "concept", "id", 1, new CommonConceptPropertyProcessor(provider, language));

		if (!plainSkos && sstore!=null) {
			//scores (degree)
			sql = "select *, "+(useNames?"concept_name":"concept")+" as "+CONCEPT_IDENTITY_FIELD
				+ " from "+sdb.getSQLTableName("degree");
			dumpRelation("degree", sql, where, rest, sdb, "degree", "concept", 1, new ConceptStatsProcessor(provider));
		}

		//hierarchy (broader)
		sql = "select "+(useNames?"broad_name":"broad")+" as "+CONCEPT_IDENTITY_FIELD
			+", "+(useNames?"narrow_name":"narrow")+" as "+CONCEPT_OTHER_FIELD
			+ " from "+db.getSQLTableName("broader");
		dumpRelation("broader", sql, where, rest, db, "broader", "narrow", 1, new ConceptHierarchyProcessor(provider, other));

		if (dumpLinks && !plainSkos) {
			//assoc (links)
			sql = "select "+(useNames?"anchor_name":"anchor")+" as "+CONCEPT_IDENTITY_FIELD
				+", "+(useNames?"target_name":"target")+" as "+CONCEPT_OTHER_FIELD
				+ " from "+db.getSQLTableName("link");
			dumpRelation("links", sql, where, rest, db, "link", "anchor", 3, new ConceptLinkProcessor(provider, other));
		}

		//relatednes / similarity
		if (useNames) {
			sql = "select R.*, 0 as langref" 
				+ ", A.name as "+CONCEPT_IDENTITY_FIELD
				+ ", B.name as "+CONCEPT_OTHER_FIELD
				+ " from "+db.getSQLTableName("relation")+" as R "
				+ " join "+db.getSQLTableName("concept")+" as A on A.id = concept1"
				+ " join "+db.getSQLTableName("concept")+" as B on B.id = concept2";
		}
		else {
			sql = "select R.*" 
				+ ", concept1 as "+CONCEPT_IDENTITY_FIELD
				+ ", concept2 as "+CONCEPT_OTHER_FIELD
				+ " from "+db.getSQLTableName("relation")+" as R ";
		}

		//NOTE: join on OR condition is insanely slow, double-join is much faster!
		
		//sql+= " left join "+db.getSQLTableName("broader")+" as H "
		//	+ " on (concept1 = broad and concept2 = narrow) or (concept2 = broad and concept1 = narrow)";
		//where = "narrow is null";

		sql+= " left join "+db.getSQLTableName("broader")+" as H1 "
			+ " on (concept1 = H1.broad and concept2 = H1.narrow) "
			+ " left join "+db.getSQLTableName("broader")+" as H2 "
			+ " on (concept2 = H2.broad and concept1 = H2.narrow)";
		where = "H1.narrow is null and H2.narrow is null";
		
		dumpRelation("relation", sql, where, rest, db, "relation", "concept1", 1, new ConceptRelatednessProcessor(provider, other));
		
		//TODO: optionally dump link table (--assoc -> ww:assoc)
	}
		
	protected void dumpConceptsGlobal(DatabaseGlobalConceptStore store, IdentityProvider<R> provider) throws Exception {
		RemoteConceptProvider remoteProvider = new RemoteConceptProvider("lang", "local_concept_name");
		RemoteResourceProvider resourceProvider = new RemoteResourceProvider("lang", "local_concept_name");
		WikiWordConceptStoreSchema db = (WikiWordConceptStoreSchema) ((DatabaseWikiWordConceptStore)store).getDatabaseAccess();

		String sql;
		String where;
		String rest;
		
		where= null;
		rest= null;
		
		sql = "select *, global_concept as "+CONCEPT_IDENTITY_FIELD+" from "+db.getSQLTableName("origin");
		dumpRelation("origin", sql, where, rest, db, "origin", "global_concept", 1, new ConceptOriginProcessor(provider, remoteProvider, resourceProvider));
	}
		
	protected void dumpRelation(String name, String sql, String where, String rest, WikiWordStoreSchema db, String chunkTable, String chunkField, int factor, Processor<ResultSet> processor) throws PersistenceException {
		DatabaseWikiWordConceptStore dbstore = ((DatabaseWikiWordConceptStore)conceptStore);
		DatabaseTable t = db.getTable(chunkTable);
		
		info("dumping relation: "+name+"; chunking on "+chunkTable+"."+chunkField);
		
		try {
			DatabaseAccess.ChunkedQuery query = new DatabaseAccess.SimpleChunkedQuery(db, "queryRelation", name, sql, where, rest, t, chunkField);
			db.executeChunkedQuery(query , dbstore.getQueryChunkSize()/factor, null, processor);
		} catch (SQLException e) {
			throw new PersistenceException(e);
		}
	}

	public boolean isPlainSkos() {
		return plainSkos;
	}

	public void setPlainSkos(boolean plainSkos) {
		this.plainSkos = plainSkos;
	}
	
	@Override
	protected void createStores() throws PersistenceException, IOException {
		conceptStore = DatabaseConceptStores.createConceptStore(getConfiguredDataSource(), getConfiguredDataset(), tweaks, true, true);
		registerStore(conceptStore);
	}
	
	public static void main(String[] argv) throws Exception {
		ExportRdf app = new ExportRdf();
		app.launch(argv);
	}
}
