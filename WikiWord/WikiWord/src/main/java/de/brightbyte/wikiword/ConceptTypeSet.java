package de.brightbyte.wikiword;

import java.util.Enumeration;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Map;
import java.util.Properties;

public class ConceptTypeSet implements Iterable<ConceptType> {
	protected Map<Integer, ConceptType> byCode = new HashMap<Integer, ConceptType>();
	protected Map<String, ConceptType> byName = new HashMap<String, ConceptType>();
	
	public void addTypes(Properties props) {
		Enumeration en = props.propertyNames();
		while (en.hasMoreElements()) {
			String k = (String)en.nextElement();
			
			int nsp = Integer.parseInt(k);
			
			String v = props.getProperty(k);
				
			this.addType( new ConceptType(nsp, v) );
		}
	}
	
	public void addType(ConceptType type) {
		ConceptType tt = checkType(type.getCode());
		if (tt!=null && !tt.getName().equals(type.getName())) {
			throw new IllegalArgumentException("conflicting ConceptType code #"+type.getCode()+": defined as '"+tt.getName()+"', can not redefine as '"+type.getName()+"'");
		}

		tt = checkType(type.getName());
		if (tt!=null && tt.getCode()!=type.getCode()) {
			throw new IllegalArgumentException("conflicting ConceptType name '"+type.getName()+"': defined as #"+tt.getCode()+", can not redefine as #"+type.getCode());
		}

		byCode.put(type.getCode(), type);
		byName.put(type.getName(), type);
	}

	public ConceptType getType(int code) {
		ConceptType type = byCode.get(code);
		if (type==null) throw new IllegalArgumentException("unknown concept type code: "+code);
		return type;
	}

	public ConceptType getType(String name) {
		ConceptType type = byName.get(name);
		if (type==null) throw new IllegalArgumentException("unknown concept type name: "+name);
		return type;
	}
	
	protected ConceptType checkType(int code) {
		ConceptType type = byCode.get(code);
		return type;
	}

	protected ConceptType checkType(String name) {
		ConceptType type = byName.get(name);
		return type;
	}
	
	public Iterator<ConceptType> iterator() {
		return byCode.values().iterator();
	}
	
	public void addAll(ConceptTypeSet ct) {
		for (ConceptType t: ct.byCode.values()) {
			addType(t);
		}
	}
	
	@Override
	public String toString() {
		return byCode.toString();
	}
}
 