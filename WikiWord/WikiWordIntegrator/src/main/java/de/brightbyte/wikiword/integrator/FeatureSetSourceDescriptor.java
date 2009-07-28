package de.brightbyte.wikiword.integrator;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Collections;
import java.util.List;
import java.util.Map;

import de.brightbyte.abstraction.PropertyAccessor;
import de.brightbyte.data.Functor;
import de.brightbyte.db.SqlScriptRunner;
import de.brightbyte.text.Chunker;
import de.brightbyte.text.CsvLineChunker;
import de.brightbyte.wikiword.TweakSet;
import de.brightbyte.wikiword.builder.InputFileHelper;
import de.brightbyte.wikiword.integrator.data.DefaultPropertyMapping;
import de.brightbyte.wikiword.integrator.data.FeatureBuilder;
import de.brightbyte.wikiword.integrator.data.PropertyMapping;
import de.brightbyte.wikiword.integrator.data.Record;
import de.brightbyte.wikiword.integrator.data.RecordMangler;

public class FeatureSetSourceDescriptor extends TweakSet {
	
	public interface SqlQueryGenerator {
		public String makewQuery(FeatureSetSourceDescriptor fsd);
	}

	public FeatureSetSourceDescriptor() {
		this(null, null);
	}

	public FeatureSetSourceDescriptor(String prefix, TweakSet parent) {
		super(prefix, parent);
	}

	public void loadTweaks(URL u) throws IOException {
		super.loadTweaks(u);
		setBaseURL(u); //XXX: always?!
	}

	public String getAuthorityName() {
		String name = getTweak("authority", null);
		return name;
	}
	
	public String getAuthorityField() {
		String field = getTweak("authority-field", null);
		return field;
	}
	
	public String getDataEncoding() {
		return getTweak("encoding", "UTF-8");
	}

	public String getSqlQuery() {
		String sql = getTweak("query", null);
		
		if (sql==null) {
			SqlQueryGenerator generator = getTweak("query-generator", null);
			if (generator!=null) sql = generator.makewQuery(this); 
		}
		
		return sql;
	}

	public String getSourceFileName() { //FIXME
		return getTweak("file", null);
	}

	
	public void setBaseURL(URL baseURL) {
		setTweak(".baseURL", baseURL);
	}
	
	public URL getBaseURL() {
		try {
			URL u = getTweak(".baseURL", (URL)null);
			if (u==null) u = new File(".").toURI().toURL();
			return u;
		} catch (MalformedURLException e) {
			return null;
		}
	}
	
	
	public String[] getDataFields() {
		List<String> v = getTweak("fields", (List<String>)null);
		if (v==null) return null;
		return (String[]) v.toArray(new String[v.size()]);
	}
	

	public Map<String, Chunker> getDataFieldChunkers() { 
		return getTweak("field-chunkers", (Map<String, Chunker>)null);
	}
	
	public RecordMangler getRowMangler() { 
		return getTweak("row-mangler", (RecordMangler)null);
	}
	
	public String getPropertyValueField() {
		return requireTweak("property-value-field");
	}

	public String getPropertyNameField() {
		return getTweak("property-name-field", null);
	}

	public String getPropertySubjectField() {
		return requireTweak("property-subject-field");
	}

	public String getPropertySubjectNameField() {
		return getTweak("property-subject-name-field", null);
	}

	public boolean getSkipHeader() {
		return getTweak("csv-skip-header", false);
	}

	public String getSourceFileFormat() {
		String format = getTweak("file-format", null);
		
		if (format==null) {
			String n = getSourceFileName();
			if (n!=null) format = InputFileHelper.getFormat(n);
		}
		
		return format;
	}
	
	public Chunker getCsvLineChunker() {
		Chunker chunker = getTweak("csv-chunker", null);
		
		if (chunker==null) {
			char ch = getTweak("csv-separator", '\u008F');
			if (ch!='\u008F') chunker = new CsvLineChunker(ch, getTweak("csv-backslash-escape", false));
		}

		if (chunker==null) {
			if (getTweak("file-format", "tsv").equals("csv"))
				chunker = CsvLineChunker.csv;
		}
		
		if (chunker==null) chunker = CsvLineChunker.tsv;
		return chunker;
	}

	public List<Functor<String, String>> getScriptManglers() {
		ArrayList<Functor<String, String>> manglers = new ArrayList<Functor<String, String>>();  
		
		manglers.addAll( getTweak("sql-manglers", Collections.<Functor<String, String>>emptyList()) );
		
		Map<String, String>  subst = getTweak("sql-comment-subst", Collections.<String, String>emptyMap());
		
		for (Map.Entry<String, String>e: subst.entrySet()) {
			manglers.add(SqlScriptRunner.makeCommentSubstitutionMangler(e.getKey(), e.getValue()));
		}
		
		return manglers;
	}

	public String getSourceTable() {
		return getTweak("source-table", null);
	}

	public boolean getSkipBadRows() {
		return getTweak("csv-skip-bad-rows", false);
	}

	public FeatureBuilder<Record> getFeatureBuilder() {
		return getTweak("feature-builder", (FeatureBuilder<Record>)null);
	}

	public Map<String, Class> getQualifierFields() {
		return getTweak("qualifier-fields", (Map<String, Class>)null);
	}

	public Map<String, PropertyMapping<Record>> getQualifierMappings() {
		Map<String, Object> m = getTweak("qualifier-mappings", (Map<String, Object>)null);
		return m==null ? null : normalizeMapOfPropertyMappings(m);
	}

	@SuppressWarnings("unchecked")
	private Map<String, PropertyMapping<Record>> normalizeMapOfPropertyMappings(Map<String, Object> m) {
		if (m==null) return null;
		
		for (Map.Entry<String, Object> e: m.entrySet()) {
			Object o = e.getValue();
			if (o instanceof PropertyMapping) continue;
			
			if (o instanceof Map) o = createPropertyMapping((Map<String, Object>)o);
			else throw new IllegalArgumentException("value for "+e.getKey()+" must be a PropertyMapping or a Map");
			
			e.setValue(o);
		}
		
		return (Map<String, PropertyMapping<Record>>)(Object)m; //XXX: fugly cast, but actually safe
	}

	@SuppressWarnings("unchecked")
	private PropertyMapping createPropertyMapping(Map<String, Object> m) {
		PropertyMapping<Record> mapping = new DefaultPropertyMapping<Record>();
		for (Map.Entry<String, Object> e: m.entrySet()) {
			Object o = e.getValue();
			
			if (o instanceof PropertyAccessor) ; //noop
			else if (o instanceof List) o = createPropertyAccessor((List<Object>)o);
			else if (o instanceof String) o = new Record.Accessor<Object>((String)o, Object.class);
			else throw new IllegalArgumentException("value for "+e.getKey()+" must be a PropertyAccessor, a String or a List");
			
			mapping.addMapping(e.getKey(), (PropertyAccessor<Record, ?>)o);
		}
		
		return mapping;
	}

	@SuppressWarnings("unchecked")
	private Object createPropertyAccessor(List<Object> args) {
		if (args.size()<1) throw new IllegalArgumentException("empty arguments, can't create PropertyAccessor");
		
		Object t = args.size()<2 ? Object.class : args.get(1);
		if (!(t instanceof Class)) {
			if (t instanceof String) {
				try {
					t = Class.forName((String)t);
				} catch (ClassNotFoundException e) {
					throw new IllegalArgumentException("bad class name: "+t, e);
				}
			} else {
				throw new IllegalArgumentException("second argument must be a Class or String");
			}
		}
		
		String n = args.get(0).toString();
		
		return new Record.Accessor<Object>(n, (Class<Object>)t);
	}

}
