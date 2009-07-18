package de.brightbyte.wikiword.integrator.data;

import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import de.brightbyte.data.Functor;


public class MultiMangler<R> implements Functor<R, R> {

	protected List<Functor<R, R>> manglers;
	
	public MultiMangler(Functor<R, R>... manglers) {
		this(new ArrayList<Functor<R, R>>(Arrays.asList(manglers))); //NOTE: must be a modifyable list
	}
	
	public MultiMangler(List<Functor<R, R>> manglers) {
		this.manglers = manglers;
	}
	
	public void addMangler(Functor<R, R> m) {
		manglers.add(m);
	}

	@Override
	public int hashCode() {
		final int PRIME = 31;
		int result = 1;
		result = PRIME * result + ((manglers == null) ? 0 : manglers.hashCode());
		return result;
	}

	@Override
	public boolean equals(Object obj) {
		if (this == obj)
			return true;
		if (obj == null)
			return false;
		if (getClass() != obj.getClass())
			return false;
		final MultiMangler other = (MultiMangler) obj;
		if (manglers == null) {
			if (other.manglers != null)
				return false;
		} else if (!manglers.equals(other.manglers))
			return false;
		return true;
	}

	public R apply(R fts) {
		for (Functor<R, R> m: manglers) {
			fts = m.apply(fts);
		}
		
		return fts;
	}

}
