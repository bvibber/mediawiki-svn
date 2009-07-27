package de.brightbyte.wikiword.integrator.data;


public class TriplifiedPropertyFeatureBuilder<R> extends PropertyMappingFeatureBuilder<R>  {

	protected String nameField;
	protected String valueField;

	/*
	public static TriplifiedPropertyFeatureBuilder<Record> forRecords(String authorityField, String idField, String nameField, String valueField, PropertyMapping<Record> qualifierMapping) {
		return new TriplifiedPropertyFeatureBuilder<Record>(
				new Record.Accessor<String>(authorityField, String.class),
				new Record.Accessor<Object>(idField, Object.class),
				new Record.Accessor<String>(nameField, String.class),
				new Record.Accessor<Object>(valueField, Object.class),
				qualifierMapping
				);
	}
	*/
	
	public TriplifiedPropertyFeatureBuilder(String authorityField, String idField, String nameField, String valueField) {
		super(authorityField, idField);
		if (nameField==null) throw new NullPointerException();
		if (valueField==null) throw new NullPointerException();
		
		this.authorityField = authorityField;
		this.idField = idField;
		this.nameField = nameField;
		this.valueField = valueField;
	}

	public void addFeatures(R rec, FeatureSet features) {
		if (authorityField!=null) {
			if (!features.hasFeature(authorityField)) {
				addFeature(rec, authorityField, features);
			}
		}

		if (!features.hasFeature(idField)) {
			addFeature(rec, idField, features);
		}

		String k = requireValue(rec, nameField, String.class);
		Object v = requireValue(rec, valueField, Object.class);

		PropertyMapping<R> qualifierMapping = getQualifierMappings(valueField);
		DefaultRecord qualifiers = null;
		
		if (qualifierMapping!=null) {
			qualifiers = new DefaultRecord();
			addFields(rec, qualifierMapping, qualifiers);
		} 
		
		features.addFeature(k, v, qualifiers);
	}

	public boolean hasSameSubject(R prev, R next) {
		if (prev==next) return true;
		if (prev==null || next==null) return false;
		
		if (authorityField!=null) {
			String a = requireValue(prev, authorityField, String.class);
			String b = requireValue(next, authorityField, String.class);
			
			if (!a.equals(b)) return false;
		}

		String x = requireValue(prev, idField, String.class);
		String y = requireValue(next, idField, String.class);
		
		if (!x.equals(y)) return false;
		
		return true;
	}
	
}
