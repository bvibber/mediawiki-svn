package net.psammead.functional;

import net.psammead.util.annotation.FullyStatic;
import net.psammead.util.cache.SoftCache;
import net.psammead.util.cache.StrongCache;
import net.psammead.util.cache.WeakCache;
import net.psammead.util.cache.WeakAssociationCache;

@FullyStatic 
public final class Caches {
	private Caches() {}
	
	public static <S,T> Cache<S,T> strongCache(Function<? super S,? extends T> function) {
		return new StrongCache<S,T>(function);
	}
	
	public static <S,T> Cache<S,T> softCache(Function<? super S,? extends T> function) {
		return new SoftCache<S,T>(function);
	}
	
	public static <S,T> Cache<S,T> weakCache(Function<? super S,? extends T> function) {
		return new WeakCache<S,T>(function);
	}

	public static <S,T> Cache<S,T> weakAssociation(Function<? super S,? extends T> function) {
		return new WeakAssociationCache<S,T>(function);
	}
}
