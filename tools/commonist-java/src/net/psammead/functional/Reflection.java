package net.psammead.functional;

import java.lang.reflect.Constructor;
import java.lang.reflect.Field;
import java.lang.reflect.InvocationTargetException;
import java.lang.reflect.Method;
import java.util.ArrayList;
import java.util.List;

// TODO adapt to scgen format (no acceptors/donors)?

public final class Reflection {
	private Reflection() {}
	
	//## method call
	
	public static <R> Donor<R> method0(final Method method) {
			return new Donor<R>() { 
					public R get() { 
							return Reflection.<Object,R>invoke(method, null); }}; } 
	public static <P1,R> Function<P1,R> method1(final Method method) {
			return new Function<P1,R>() { 
					public R apply(final P1 p1) { 
							return Reflection.<P1,R>invoke(method, p1); } }; } 
	public static <P1,P2,R> Function<P1,Function<P2,R>> method2(final Method method) {
			return new Function<P1,Function<P2,R>>() {
					public Function<P2,R> apply(final P1 p1) {
							return new Function<P2,R>() {
									public R apply(final P2 p2) {
											return Reflection.<P1,R>invoke(method, p1, p2); }}; }}; }
	public static <P1,P2,P3,R> Function<P1,Function<P2,Function<P2,R>>> method3(final Method method) {
			return new Function<P1,Function<P2,Function<P2,R>>>() {
					public Function<P2,Function<P2,R>> apply(final P1 p1) {
							return new Function<P2,Function<P2,R>>() {
									public Function<P2,R> apply(final P2 p2) {
											return new Function<P2,R>() {
													public R apply(final P2 p3) {
															return Reflection.<P1,R>invoke(method, p1, p2); }}; }}; }}; } 
	public static <P1,P2,P3,P4,R> Function<P1,Function<P2,Function<P3,Function<P4,R>>>> method4(final Method method) {
			return new Function<P1,Function<P2,Function<P3,Function<P4,R>>>>() {
					public Function<P2,Function<P3,Function<P4,R>>> apply(final P1 p1) {
							return new Function<P2,Function<P3,Function<P4,R>>>() {
									public Function<P3,Function<P4,R>> apply(final P2 p2) {
											return new Function<P3,Function<P4,R>>() {
													public Function<P4,R> apply(final P3 p3) {
															return new Function<P4,R>() {
																	public R apply(final P4 p4) {
																			return Reflection.<P1,R>invoke(method, p1, p2, p3, p4); }}; }}; }}; }}; }
	
	//## constructor call

	public static <R> Donor<R> constructor0(final Constructor<R> constructor) {
			return new Donor<R>() { 
					public R get() { 
							return construct(constructor); }}; } 
	public static <P1,R> Function<P1,R> constructor1(final Constructor<R> constructor) {
			return new Function<P1,R>() { 
					public R apply(final P1 p1) { 
							return construct(constructor, p1); } }; } 
	public static <P1,P2,R> Function<P1,Function<P2,R>> constructor2(final Constructor<R> constructor) {
			return new Function<P1,Function<P2,R>>() {
					public Function<P2,R> apply(final P1 p1) {
							return new Function<P2,R>() {
									public R apply(final P2 p2) {
											return construct(constructor, p1, p2); }}; }}; }
	public static <P1,P2,P3,R> Function<P1,Function<P2,Function<P2,R>>> constructor3(final Constructor<R> constructor) {
			return new Function<P1,Function<P2,Function<P2,R>>>() {
					public Function<P2,Function<P2,R>> apply(final P1 p1) {
							return new Function<P2,Function<P2,R>>() {
									public Function<P2,R> apply(final P2 p2) {
										return new Function<P2,R>() {
												public R apply(final P2 p3) {
														return construct(constructor, p1, p2); }}; }}; }}; } 
	public static <P1,P2,P3,P4,R> Function<P1,Function<P2,Function<P3,Function<P4,R>>>> constructor4(final Constructor<R> constructor) {
			return new Function<P1,Function<P2,Function<P3,Function<P4,R>>>>() {
					public Function<P2,Function<P3,Function<P4,R>>> apply(final P1 p1) {
							return new Function<P2,Function<P3,Function<P4,R>>>() {
									public Function<P3,Function<P4,R>> apply(final P2 p2) {
											return new Function<P3,Function<P4,R>>() {
													public Function<P4,R> apply(final P3 p3) {
															return new Function<P4,R>() {
																	public R apply(final P4 p4) {
																			return construct(constructor, p1, p2, p3, p4); }}; }}; }}; }}; }
	
	//## field read
	
	public static <R> Donor<R> read0(final Field field) {
			return new Donor<R>() { 
					public R get() { 
							return Reflection.<Object,R>read(field, null); } }; } 
	public static <P1,R> Function<P1,Donor<R>> read1(final Field field) {
			return new Function<P1,Donor<R>>() { 
					public Donor<R> apply(final P1 p1) { 
							return new Donor<R>() {
									public R get() {
											return Reflection.<P1,R>read(field, p1); }}; }}; }
	
	//## field write
	
	public static <P1> Acceptor<P1> write0(final Field field) {
			return new Acceptor<P1>() { 
					public void set(P1 p1) { 
							write(field, null, p1); } }; } 
	public static <P1,P2> Function<P1,Acceptor<P2>> write1(final Field field) {
			return new Function<P1,Acceptor<P2>>() {
					public Acceptor<P2> apply(final P1 p1) {
							return new Acceptor<P2> () {
									public void set(final P2 p2) {
											 write(field, p1, p2); }}; }}; }
		

	
	//--------------------------------------------------------------------------
	//## accessor
	
	@SuppressWarnings("unchecked")
	private static final <Target,Result> Result invoke(Method method, Target target, Object... args) {
		try { return (Result)method.invoke(target, args); }
		catch (IllegalArgumentException e) 	{ throw new ReflectionException(e); }
		catch (IllegalAccessException e) 	{ throw new ReflectionException(e); }
		catch (InvocationTargetException e)	{ throw new ReflectionException(e); }
	}
	
	private static final <Result> Result construct(Constructor<Result> constructor, Object... args) {
		try { return constructor.newInstance(args);  }
		catch (IllegalArgumentException e) 	{ throw new ReflectionException(e); }
		catch (IllegalAccessException e) 	{ throw new ReflectionException(e); }
		catch (InvocationTargetException e) { throw new ReflectionException(e); }
		catch (InstantiationException e)    { throw new ReflectionException(e); }
	}
	
	@SuppressWarnings("unchecked")
	private static final <Target,Result> Result read(Field field, Target target) {
		try { return (Result)field.get(target); }
		catch (IllegalArgumentException e) 	{ throw new ReflectionException(e); }
		catch (IllegalAccessException e) 	{ throw new ReflectionException(e); }
	}
	
	private static final <Target,Value> Unit write(Field field, Target target, Value value) {
		try { field.set(target, value); }
		catch (IllegalArgumentException e) 	{ throw new ReflectionException(e); }
		catch (IllegalAccessException e) 	{ throw new ReflectionException(e); }
		return Unit.INSTANCE;
	}
	
	//--------------------------------------------------------------------------
	//## finder
	
	public static Method method(Class<?> clazz, String name, int arity) {
		List<Method> out = new ArrayList<Method>();
		for (Method method : clazz.getMethods()) {
			if (!method.getName().equals(name))						continue;
			if (method.getGenericParameterTypes().length != arity)	continue;
			out.add(method);
		}
		if (out.size() != 1)	throw new ReflectionException("expected a single, non-ambiguous method, class " + clazz.getName() + " has " + out.size() + " named " + name + " with arity " + arity);
		return out.get(0);
	}
	
	public static Constructor<?> constructor(Class<?> clazz, int arity) {
		List<Constructor<?>> out = new ArrayList<Constructor<?>>();
		for (Constructor<?> constructor : clazz.getConstructors()) {
			if (constructor.getGenericParameterTypes().length != arity)	continue;
			out.add(constructor);
		}
		if (out.size() != 1)	throw new ReflectionException("expected a single, non-ambiguous constructor, class " + clazz.getName() + " has " + out.size() + " with arity " + arity);
		return out.get(0);
	}
	
	public static Field field(Class<?> clazz, String name) {
		try { return clazz.getField(name); }
		catch (SecurityException e)    { throw new ReflectionException("expected to find a field, class " + clazz.getName() + " does not provide a field named " + name, e); }
		catch (NoSuchFieldException e) { throw new ReflectionException("expected to find a field, class " + clazz.getName() + " does not provide a field named " + name, e); }
	}
}
