package de.brightbyte.wikiword;

import java.net.URI;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.util.ArrayList;
import java.util.List;

import de.brightbyte.db.DatabaseSchema;
import de.brightbyte.rdf.RdfPropertyInstance;
import de.brightbyte.rdf.RdfReference;
import de.brightbyte.rdf.RdfResource;
import de.brightbyte.rdf.RdfSink;
import de.brightbyte.rdf.RdfSinkException;
import de.brightbyte.rdf.RdfUtil;
import de.brightbyte.rdf.RdfVocabException;
import de.brightbyte.wikiword.store.LocalConceptStore;

/**
 * A RdfDumper transferts the data optained from a WikiStoreQuerior into a RdfSink.
 * That is, it represents a specific RDF Vocabularies and the rules to build a
 * set of RDF statements from the WikiStore data. 
 */
public class RdfDumper {
	
	protected LocalConceptStore queryor;
	
	protected class ValueAccess {
		protected String field;
		protected URI vocab;
		protected NameConverter converter;
		
		public ValueAccess(String field, URI vocab) {
			this(field, vocab, null);
		}
		
		public ValueAccess(String field, URI vocab, NameConverter converter) {
			if (field==null) throw new NullPointerException();

			this.field = field;
			this.vocab = vocab;
			this.converter = converter;
		}

		public URI getVocabulary() {
			return vocab;
		}

		public String getField() {
			return field;
		}

		public RdfResource getValue(ResultSet data) throws SQLException, RdfVocabException {
			Object v = data.getObject(field);
			if (v==null) return null; //FIXME: explicite null? here??
			if (v!=null && converter!=null) v = converter.convert(v);
			if (v!=null && vocab!=null) return sink.makeReference(vocab, v.toString());
			else return sink.makeLiteral(v.toString()); //FIXME: handle null!
		}
	}

	protected class RelationAccess extends ValueAccess {
		protected RdfReference relation;
		
		public RelationAccess(String field, URI vocab, RdfReference relation) {
			this(field, vocab, relation, null);
		}
		
		public RelationAccess(String field, URI vocab, RdfReference relation, NameConverter converter) {
			super(field, vocab, converter);
			
			if (relation==null) throw new NullPointerException();
			this.relation = relation;
		}

		public RdfReference getRelation() {
			return relation;
		}

		public RdfPropertyInstance getPropertyInstance(ResultSet data) throws RdfVocabException, SQLException {
			RdfResource v = getValue(data);
			return v == null ? null : new RdfPropertyInstance(getRelation(), v);
		}
	}

	/*
	public static class RdfReference {
		public final String value;

		public RdfReference(URI value) {
			this(getValueN3(value));
		}
		
		public RdfReference(URI base, String name) {
			this(getValueN3(RdfEntities.makeURI(base, name)));
		}
		
		public RdfReference(String prefix, String name) {
			this(prefix+":"+name);
		}
		
		public RdfReference(String prefix, String corpus, String name) {
			this(prefix, corpus+"/"+name);
		}
		
		public RdfReference(String value) {
			super();
			if (value==null) throw new NullPointerException();
			this.value = value;
		}
		
		public String toString() {
			return value;
		}

		@Override
		public int hashCode() {
			return value.hashCode();
		}

		@Override
		public boolean equals(Object obj) {
			if (this == obj) return true;
			if (obj == null) return false;
			
			return value.equals(obj.toString());
		}
	}
	*/
	
	public static interface NameConverter {
		public String convert(Object s);
	}
	
	/*
	public static class BaseUriFactory implements  NameConverter {
		protected URI base;
		
		public BaseUriFactory(URI base) {
			this.base = base;
		}
		
		public String convert(Object s) {
			return new RdfReference(RdfEntities.makeURI(base, s.toString()));
		}
	}

	protected NameConverter resourceNameConverter = new NameConverter() {
		public String convert(Object s) {
			return s==null ? null : getResourceLiteral(s.toString());
		}
	};
	
	protected NameConverter conceptNameConverter = new NameConverter() {
		public String convert(Object s) {
			return s==null ? null : getConceptLiteral(s.toString());
		}
	};
	*/
	
	protected NameConverter resourceTypeConverter = new NameConverter() {
		public String convert(Object s) {
			if (s==null) return null;
			int n = ((Number)s).intValue();
			return ResourceType.getType(n).toString();
		}
	};
	
	protected NameConverter conceptTypeConverter = new NameConverter() {
		public String convert(Object s) {
			if (s==null) return null;
			int n = ((Number)s).intValue();
			return queryor.getCorpus().getConceptTypes().getType(n).toString(); 
		}
	};
	
	protected RdfReference rdfType;
	
	protected RdfReference termRefersTo;
	protected RdfReference conceptReferences;
	protected RdfReference conceptIsBroader;
	protected RdfReference conceptIsEquivalent;
	protected RdfReference describedIn;

	protected RdfReference conceptHasType;
	protected RdfReference resourceHasType;

	protected RdfReference concept;
	protected RdfReference resource;
	
	private boolean useEntityTypes = true;

	/*
	private boolean useVocabPrefixes = true;
	private boolean useEntityPrefixes = false;

	private boolean useTerseAbout = true;
	private int n3Level = -23;
	
	public static final int N3_TRIPLES = 10;
	public static final int N3_TURTLE = 20;
	*/
	
	/*
	private String rdfPrefix = "rdf";
	private String conceptPrefix = "concept";
	private String resourcePrefix = "resource";
	private String wikiwordPrefix = "ww";
	*/
	
	private URI resourceBase;
	private URI conceptBase;

	private ValueAccess conceptIdentity;
	private RelationAccess[] conceptProperties;

	private ValueAccess resourceIdentity;
	private RelationAccess[] resourceProperties;

	private Corpus corpus;
	protected RdfSink sink;
	
	public RdfDumper(LocalConceptStore queryor, RdfSink sink, boolean useVocabPrefixes, boolean useEntityPrefixes) {
		this.sink = sink;
		this.queryor = queryor;
		corpus = queryor.getCorpus();
		
		try {
			initIds(useVocabPrefixes, useEntityPrefixes);
		} catch (RdfVocabException e) {
			throw new RuntimeException("inconsistant vocabulary declaration");
		}
	}
	
	private void initIds(boolean useVocabPrefixes, boolean useEntityPrefixes) throws RdfVocabException {
		resourceBase = RdfEntities.makeResourceURI(corpus.getDomain(), "");
		conceptBase = RdfEntities.makeLocalConceptURI(corpus.getDomain(), "");

		resourceBase = RdfEntities.makeResourceURI(corpus.getDomain(), "");
		conceptBase = RdfEntities.makeLocalConceptURI(corpus.getDomain(), "");

		if (useVocabPrefixes) {
			sink.addVocablulary("rdf", RdfUtil.rdfBase);
			sink.addVocablulary("ww", RdfEntities.wikiwordBase);
		}
		
		if (useEntityPrefixes) {
			sink.addVocablulary("resource", resourceBase);
			sink.addVocablulary("concept", conceptBase);
		}
		
		rdfType = sink.makeReference(RdfUtil.rdfBase, "type");
		
		termRefersTo = getWikiWordVocabId("termReferesTo");
		conceptReferences = getWikiWordVocabId("conceptReferences");
		conceptIsBroader = getWikiWordVocabId("conceptIsBroader");
		conceptIsEquivalent = getWikiWordVocabId("conceptIsEquivalent");
		describedIn = getWikiWordVocabId("describedIn");

		conceptHasType = getWikiWordVocabId("conceptHasType");
		resourceHasType = getWikiWordVocabId("resourceHasType");

		concept = getWikiWordVocabId("Concept");
		resource = getWikiWordVocabId("Resource");
		
		conceptIdentity = new ValueAccess("name", conceptBase);
		conceptProperties = new RelationAccess[] {
				new RelationAccess("resource_name", resourceBase, describedIn), 
				new RelationAccess("type", RdfEntities.conceptTypeBase, conceptHasType, conceptTypeConverter), 
		};
		
		resourceIdentity = new ValueAccess("name", resourceBase);
		resourceProperties = new RelationAccess[] {
				new RelationAccess("resource_name", resourceBase, describedIn), 
				new RelationAccess("type", RdfEntities.resourceTypeBase, resourceHasType, resourceTypeConverter), 
		};
	}
	
	
	protected RdfReference getWikiWordVocabId(String name) throws RdfVocabException {
		return sink.makeReference(RdfEntities.wikiwordBase, name);
	}
	
	/*
	protected RdfReference getRdfVocabId(String name) {
		if (useVocabPrefixes) return new RdfReference(rdfPrefix, name);
		else return new RdfReference(RdfEntities.rdfBase, name);
	}
	*/
	
	public void dumpConcepts(ResultSet res) throws SQLException, RdfSinkException, RdfVocabException {
		dumpEntities(concept, res, 
				conceptIdentity,
				conceptProperties);
	}
	
	public void dumpResources(ResultSet res) throws SQLException, RdfSinkException, RdfVocabException {
		dumpEntities(resource, res, 
				resourceIdentity,
				resourceProperties);
	}
	
	public void dumpRelationTermRefersTo(ResultSet res) throws SQLException, RdfSinkException, RdfVocabException {
		dumpSimpleRealtion(res, termRefersTo, 
				new ValueAccess("term_text", null),
				new ValueAccess("concept_name", conceptBase));
	}
	
	public void dumpRelationConceptBroader(ResultSet res) throws SQLException, RdfSinkException, RdfVocabException {
		dumpSimpleRealtion(res, conceptIsBroader, 
				new ValueAccess("narrow_name", conceptBase),
				new ValueAccess("broad_name", conceptBase));
	}
	
	protected void dumpSimpleRealtion(DatabaseSchema store, String sql, RdfReference relation, 
			ValueAccess srcAccess, 
			ValueAccess tgtAccess) throws SQLException, RdfSinkException, RdfVocabException {
		
		ResultSet res = store.executeQuery("dumpSimpleRealtion("+relation+")", sql);
		try {
			dumpSimpleRealtion(res, relation, srcAccess, tgtAccess);
		}
		finally {
			res.getStatement().close();
		}
	}
	
	/*
	protected void dumpPrelude(Output out) throws IOException {
		if (useVocabPrefixes) {
			out.writeln("@prefix "+rdfPrefix+": "+RdfEntities.rdfBase);
			out.writeln("@prefix "+wikiwordPrefix+": "+RdfEntities.wikiwordBase);
		}
		if (useEntityPrefixes) {
			out.writeln("@prefix "+resourcePrefix+": "+RdfEntities.makeResourceURI(corpus.getDomain(), ""));
			out.writeln("@prefix "+conceptPrefix+": "+RdfEntities.makeConceptURI(corpus.getDomain(), ""));
		}
		if (useVocabPrefixes || useEntityPrefixes) {
			out.newline();
		}
	}
	*/
	
	protected void dumpSimpleRealtion(ResultSet res, RdfReference relation, 
									ValueAccess srcAccess, 
									ValueAccess tgtAccess) throws SQLException, RdfSinkException, RdfVocabException {
		
		sink.prepare();
		
		while (res.next()) {
			RdfResource subj = srcAccess.getValue(res);
			RdfResource obj = tgtAccess.getValue(res);
			
			//FIXME: handle null?
			sink.putStatement(subj, relation, obj);
		}
	}
	
	protected void dumpEntities(RdfReference type, ResultSet res, ValueAccess identity, RelationAccess[] relations) throws SQLException, RdfSinkException, RdfVocabException {

		sink.prepare();
				
		while (res.next()) {
			RdfReference subject = (RdfReference)identity.getValue(res); //XXX: ugly cast!
			
			List<RdfPropertyInstance> properties = new ArrayList<RdfPropertyInstance>(relations.length+1);
			for (RelationAccess acc : relations) {
				//FIXME: explicite NULL ??
				RdfPropertyInstance pi = acc.getPropertyInstance(res);
				if (pi!=null) properties.add(pi);
			}
			
			if (type!=null && useEntityTypes) properties.add(new RdfPropertyInstance(rdfType, type));
			
			sink.putAbout(subject, properties);
		}
	}

	/*
	protected String getAbout(RdfReference type, ValueAccess identity, RelationAccess[] relations, ResultSet data) throws SQLException {
		StringBuilder s = new StringBuilder();
		
		Object about = identity.getValue(data);
		boolean first = true;
		
		if (useEntityTypes) {
			s.append(getValueN3(about));
			s.append(' ');
			
			s.append(rdfType);
			s.append(' ');
	
			s.append(type);
			first = false;
		}
		
		if (useTerseAbout) {
			for (RelationAccess mapper: relations) {
				Object v = mapper.getValue(data);
				if (v==null) continue; //TODO: explicite null??
				
				if (first) {
					first = false;
					s.append(getValueN3(about));
					s.append(' ');
				}
				else {
					s.append(';');
					s.append('\n');
					s.append('\t');
				}

				s.append(getValueN3(mapper.getRelation()));
				s.append(' ');

				s.append(getValueN3(v));
			}

			s.append('.');
			s.append('\n');
		}
		else {
			if (!first) s.append(".\n");

			for (RelationAccess mapper: relations) {
				Object v = mapper.getValue(data);
				s.append( getTriple(about, mapper.getRelation(), v) );
				s.append('\n');
			}
		}
		
		return s.toString();
	}
	
	protected RdfReference getResourceLiteral(String rcName) {
		if (useEntityPrefixes && resourcePrefix!=null && isPlainName(rcName)) return new RdfReference(resourcePrefix, rcName);
		else return new RdfReference(getResourceURI(rcName));
	}
	
	protected RdfReference getConceptLiteral(String conceptName) {
		if (useEntityPrefixes && conceptPrefix!=null && isPlainName(conceptName)) return new RdfReference(conceptPrefix, conceptName);
		else return new RdfReference(getConceptURI(conceptName));
	}
	
	protected RdfReference getResourceTypeLiteral(ResourceType type) {
		if (useVocabPrefixes && wikiwordPrefix!=null) return new RdfReference(wikiwordPrefix, "ResourceType" + type.name());
		else return new RdfReference(RdfEntities.pageTypeBase, type.name());
	}
	
	protected RdfReference getConceptTypeLiteral(ConceptType type) {
		if (useVocabPrefixes && wikiwordPrefix!=null) return new RdfReference(wikiwordPrefix, "ConceptType" + type.name());
		else return new RdfReference(RdfEntities.conceptTypeBase, type.name());
	}
	*/

	/*
	protected URI getResourceURI(String rcName) {
		return RdfEntities.makeResourceURI(corpus.getDomain(), rcName);
	}
	
	protected URI getConceptURI(String conceptName) {
		return RdfEntities.makeConceptURI(corpus.getDomain(), conceptName);
	}
	*/
	
	/*
	public String getResourceDefinesTriple(String rcName, String conceptName) {
		return getTriple(getResourceURI(rcName), describedIn, getConceptURI(conceptName));
	}
	
	public String getTermRefersToTriple(String term, String conceptName) {
		return getTriple(term, termRefersTo, getConceptURI(conceptName));
	}
	
	public String getConceptReferencesTriple(String srcConcept, String tgtConcept) {
		return getTriple(getConceptURI(srcConcept), conceptReferences, getConceptURI(tgtConcept));
	}
	
	public String getConceptBroaderTriple(String srcConcept, String tgtConcept) {
		return getTriple(getConceptURI(srcConcept), conceptIsBroader, getConceptURI(tgtConcept));
	}
	
	public String getConceptEquivalentTriple(String srcConcept, String tgtConcept) {
		return getTriple(getConceptURI(srcConcept), conceptIsEquivalent, getConceptURI(tgtConcept));
	}
	
	public String getConceptLangLink(String srcConcept, String tgtCorpus, String tgtConcept) {
		return getTriple(getConceptURI(srcConcept), conceptIsEquivalent, RdfEntities.makeConceptURI(tgtCorpus, tgtConcept));
	}
	*/
	
	/*
	public String getTriple(Object left, RdfReference rel, Object right) {
		return getValueN3(left)+" "+getValueN3(rel)+" "+getValueN3(right)+".";
	}
	*/
	
	/*
	public static String getValueN3(Object v) {
		if (v instanceof URL) {
			try {
				v = ((URL)v).toURI();
			} catch (URISyntaxException e) {
				throw new IllegalArgumentException("bad url: "+v, e);
			}
		}
		else if (v instanceof ConceptType) {
			v = ((ConceptType)v).getURI();
		}
		else if (v instanceof ResourceType) {
			v = ((ResourceType)v).getURI();
		}
		
		if (v instanceof RdfReference) {
			return v.toString();
		}
		else if (v instanceof URI) {
			//String shorthand = getShorthandN3((URI)v);
			//if (shorthand!=null) return shorthand;
			//else
			return "<"+((URI)v).toASCIIString()+">";
		}
		//else if (v instanceof Number) return v.toString();
		//else if (v instanceof Boolean) return v.toString();
		else return "\""+escapeN3(v.toString())+"\"";
	}
	*/

	public static void main(String[] args) {
		
	}

}
