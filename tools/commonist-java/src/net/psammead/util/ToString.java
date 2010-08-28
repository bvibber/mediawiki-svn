package net.psammead.util;

import java.lang.reflect.Field;
import java.lang.reflect.Modifier;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import java.util.Map;

/** debug strings for arbitrary objects */
public final class ToString {
//	private static enum foo { a,b }
//	public static void main(String[] args) {
//		System.out.println(stringifyAny(foo.a));
//	}
	
    private final Object		object;
    private final List<Part>	parts;

    public ToString(Object object) {
        this.object	= object;
        parts	= new ArrayList<Part>();
    }
    
    /** appends a Part */
    public ToString append(String name, Object value) {
        parts.add(new Part(name, value));
        return this;
    }
    
    /** appends a public field Part */
    public ToString appendField(String name) {
    	final Class<?>	objectClass = object.getClass();
    	final Field[] 	classFields	= objectClass.getFields();
    	for (Field field : classFields) {
    		if (field.getName().equals(name)) {
    			if (!Modifier.isPublic(field.getModifiers())) {
    				//  BETTER use field.setAccessible(true); ??
    				throw new IllegalArgumentException("field is not public: " + name + " in class: " + objectClass.getName());
    			}
    			final Object value;
				try {
					value = field.get(object);
					append(name, value);
					return this;
				}
				catch (IllegalAccessException e) {
					throw new RuntimeException("field not accessible: " + name + " in class: " + objectClass.getName(), e);
				}
    		}
    	}
    	throw new IllegalArgumentException("field does not exist: " + name + " in class: " + objectClass.getName());
    }
    
    @Override
	public String toString() {
    	if (object == null)	return NULL_LITERAL;
    	final StringBuilder   b = new StringBuilder();
        boolean		first	= true;
    	for (Part part : parts) {
    		if (first)	first	= false;
    		else		b.append(SEPARATOR);
        	b.append(part.toString());
        }
        return DebugUtil.shortClassName(object) 
        		+ "{ " + b.toString() + " }";
    }
    
    //-------------------------------------------------------------------------
    
	private static final String	NULL_LITERAL	= "<null>";
	private static final String	SEPARATOR		= ", ";
	
    /** stringify an arbitrary object */
    public static String stringifyAny(Object value) {
	  		 if (value == null)						return NULL_LITERAL;
	 	else if (value instanceof Map<?,?>)			return stringifyMap((Map<?, ?>)value);
	 	else if (value instanceof Map.Entry<?,?>)	return stringifyMapEntry((Map.Entry<?, ?>)value);
	  	else if (value instanceof Iterable<?>) 		return stringifyIterable((Iterable<?>)value);
	  	else if (value instanceof Object[])			return stringifyArray((Object[])value);
	  	else if (value instanceof byte[])			return Arrays.toString((byte[])value);
	  	else if (value instanceof short[])			return Arrays.toString((short[])value);
	  	else if (value instanceof int[])			return Arrays.toString((int[])value);
	  	else if (value instanceof long[])			return Arrays.toString((long[])value);
	  	else if (value instanceof boolean[])		return Arrays.toString((boolean[])value);
	  	else if (value instanceof char[])			return Arrays.toString((char[])value);
	  	else if (value instanceof float[])			return Arrays.toString((float[])value);
	  	else if (value instanceof double[])			return Arrays.toString((double[])value);
	  	else if (value instanceof Enum<?>) 			return stringifyEnum((Enum<?>)value);
	  	else if (value instanceof String) 			return JavaLiteral.encodeString((String)value);
	  	else if (value instanceof Character) 		return JavaLiteral.encodeChar((Character)value);
	  	else										return value.toString();
	}
    
	private static String stringifyMap(Map<?, ?> map) {
    	return DebugUtil.shortClassName(map) 
    			+ "[ " + stringifyInner(map.entrySet()) + " ]";
    }
    
    private static String stringifyMapEntry(Map.Entry<?, ?> mapEntry) {
    	return stringifyAny(mapEntry.getKey()) 
    			+ " => " + stringifyAny(mapEntry.getValue());
    }
    
    private static String stringifyIterable(Iterable<?> iterable) {
    	return DebugUtil.shortClassName(iterable)  
    			+ "[ " + stringifyInner(iterable) + " ]";
    }
    
    private static String stringifyArray(Object[] array) {
    	return DebugUtil.shortClassName(array)  
    			+ "[ " + stringifyInner(Arrays.asList(array)) + " ]";
	}
    
    private static String stringifyEnum(Enum<?> enum_) {
    	return DebugUtil.shortClassName(enum_) 
    			+ "{ name=" + enum_.toString() + " }";
	}

    
    private static String stringifyInner(Iterable<?> iterable) {
    	final StringBuilder	b	= new StringBuilder();
    	boolean		first	= true;
    	for (Object element : iterable) {
    		if (first)	first	= false;
    		else		b.append(SEPARATOR);
    		b.append(stringifyAny(element));
    	}
    	return b.toString();
    }
    
    private static class Part {
        public final String name;
        public final Object value;
        
        public Part(String name, Object value) {
            this.name   = name;
            this.value  = value;
        }
        
        @Override
		public String toString() {
        	return name + "=" + stringifyAny(value);
        }
    }
}