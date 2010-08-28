package net.psammead.util.reflect;

import java.lang.reflect.Array;
import java.lang.reflect.Constructor;
import java.lang.reflect.Field;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.List;

import net.psammead.util.DebugUtil;
import net.psammead.util.annotation.FullyStatic;
import net.psammead.util.reflect.Assignability;
import net.psammead.util.reflect.ReflectException;

@FullyStatic 
public final class ReflectUtil {
	/** fully static utility class, may no be instantiated */
	private ReflectUtil() {}
	
	//==============================================================================
	//## execute
	
	/** constructs an Array */
	public static Object array(String className, Object[] values) throws ReflectException {
		return array(clazz(className), values);
	}
	
	/** constructs an Array */
	public static Object array(Class<?> clazz, Object[] values) throws ReflectException {
		final int		size	= values.length;
		final Object	out		= Array.newInstance(clazz, size);
		try { System.arraycopy(values, 0, out, 0, size); }
		catch (ArrayStoreException e) { throw new ReflectException(e); }
		return out;
	}
	
	/** instantiates an object */
	public static Object object(String className, Object[] arguments) throws ReflectException  {
		return object(clazz(className), arguments);
	}
	
	/** instantiates an object */
	public static Object object(Class<?> clazz, Object[] arguments) throws ReflectException  {
		try { return constructor(clazz, types(arguments)).newInstance(arguments); }
		catch (InstantiationException e)	{ throw new ReflectException(e); }
		catch (IllegalAccessException e)	{ throw new ReflectException(e); }
		catch (InvocationTargetException e)	{ throw new ReflectException(e); }
	}
	
	/** calls a named method on a target object */
	public static Object call(Object target, String methodName, Object[] arguments) throws ReflectException {
		Method	method	= method(target.getClass(), methodName, types(arguments));
		try { return method.invoke(target, arguments); }
		catch (IllegalAccessException e)	{ throw new ReflectException(e); }
		catch (InvocationTargetException e)	{ throw new ReflectException(e); }
	}
	
	//==============================================================================
	//## lookup
	
	/** look up a class */
	public static Class<?> clazz(String name) throws ReflectException {
		try { return Class.forName(name); }
		catch (ClassNotFoundException e) { throw new ReflectException(e); }
	}
	
	/**
	 * maps an array of Objects to an array of their Class objects
	 * null values get mapped to a null class
	 */
	public static Class<?>[] types(Object[] values) {
		final int		size	= values.length;
		final Class<?>[]	out		= new Class[size];
		for (int i=0; i<size; i++) {
			final Object	value	= values[i];
			out[i]	= value != null ? value.getClass() : null;
		}
		return out;
	}
	
	/**
	 * finds a constructor for given types. prefers an exact match. 
	 * throws an ReflectException when no constructor or multiple constructors are found 
	 */
	public static Constructor<?> constructor(Class<?> clazz, Class<?>[] argTypes) throws ReflectException {
		final List<Constructor<?>>	maybe			= new ArrayList<Constructor<?>>();
		final Constructor<?>[] 		constructors	= clazz.getConstructors();
		for (Constructor<?> constructor : constructors) {
			final Class<?>[] 		paramTypes		= constructor.getParameterTypes();
			final Assignability	assignability	= assignable(argTypes, paramTypes);
			if (assignability == Assignability.exact)	return constructor;
			if (assignability.betterThan(Assignability.incompatible)) {
				maybe.add(constructor);
			}
		}
		
		int	possibilities	= maybe.size();
		if (possibilities == 1)		return maybe.get(0);
		if (possibilities == 0)		throw new ReflectException("cannot find constructor for " + description(clazz, DebugUtil.shortType(clazz), argTypes));
		final StringBuilder b = new StringBuilder("found ambiguous constructors for " + description(clazz, DebugUtil.shortType(clazz), argTypes));
		for (Constructor<?> constructor : maybe)	b.append("\n").append(constructor.toString());
		throw new ReflectException(b.toString());
	}
	
	/** 
	 * finds a method for given argument types. prefers an exact match. 
	 * throws an ReflectException when no method or multiple methods are found 
	 */
	public static Method method(Class<?> clazz, String name, Class<?>[] argTypes) throws ReflectException {
		final List<Method>	maybe	= new ArrayList<Method>();
		final Method[] 		methods	= clazz.getMethods();
		for (Method method : methods) {
			if (!name.equals(method.getName()))	continue;
			final Class<?>[] 		paramTypes		= method.getParameterTypes();
			final Assignability	assignability	= assignable(argTypes, paramTypes);
			if (assignability == Assignability.exact)	return method;
			if (assignability.betterThan(Assignability.incompatible)) {
				maybe.add(method);
			}
		}
		
		int	possibilities	= maybe.size();
		if (possibilities == 1)		return maybe.get(0);
		if (possibilities == 0)		throw new ReflectException("cannot find method for " + description(clazz, name, argTypes));
		final StringBuilder b = new StringBuilder("found ambiguous methods for " + description(clazz, name, argTypes));
		for (Method method : maybe)	b.append("\n").append(method.toString());
		throw new ReflectException(b.toString());
	}
	
	/** finds a named field */
	public static Field field(Class<?> clazz, String name) throws ReflectException {
		try {
			return clazz.getField(name);
		}
		catch (SecurityException e) {
			throw new ReflectException(e);
		}
		catch (NoSuchFieldException e) {
			throw new ReflectException(e);
		}
	}
	
	//==============================================================================
	//## assignability
	
	public static Assignability assignable(Class<?>[] values, Class<?>[] targets) {
		if (values.length != targets.length)	return Assignability.incompatible;
		Assignability	max	= Assignability.exact;
		for (int i=0; i<values.length; i++) {
			final Assignability	here	= assignable(values[i], targets[i]);
			if (max.betterThan(here))	max	= here;
		}
		return max;
	}

	public static Assignability assignable(Class<?> value, Class<?> target) {
			 if (value == target)					return Assignability.exact;
		else if (value == null)						return Assignability.nullref;	
		else if (target.isAssignableFrom(value))	return Assignability.assignable;
		else if (coercable(value, target))			return Assignability.coercable;
		else										return Assignability.incompatible;
	}
	
	/** one value is a primitive, the other is not */
	public static boolean coercable(Class<?> value, Class<?> target) {
		return value == Character.TYPE	&& target == Character.class
			|| value == Character.class	&& target == Character.TYPE
			|| value == Byte.TYPE		&& target == Byte.class
			|| value == Byte.class		&& target == Byte.TYPE
			|| value == Short.TYPE		&& target == Short.class
			|| value == Short.class		&& target == Short.TYPE
			|| value == Integer.TYPE	&& target == Integer.class
			|| value == Integer.class	&& target == Integer.TYPE
			|| value == Long.TYPE		&& target == Long.class
			|| value == Long.class		&& target == Long.TYPE
			|| value == Boolean.TYPE	&& target == Boolean.class
			|| value == Boolean.class	&& target == Boolean.TYPE;
	}
	
	//==============================================================================
	//## helper
	
	public static String description(Class<?> clazz, String method, Class<?>[] argTypes) {
		final StringBuilder out	= new StringBuilder();
		out.append("class: ").append(clazz.getName()).append(", ");
		out.append("method: ").append( method).append(", ");
		out.append("args: [");
		boolean	first	= true;
		for (Class<?> argType : argTypes) {
			if (!first)	out.append(", ");
			first	= false;
			if (argType != null)	out.append(argType.getName());
			else					out.append("<null>");
		}
		out.append("]");
		return out.toString();
	}
}
