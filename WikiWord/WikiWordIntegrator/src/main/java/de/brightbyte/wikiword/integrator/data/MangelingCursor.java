package de.brightbyte.wikiword.integrator.data;

import de.brightbyte.data.Functor;
import de.brightbyte.data.cursor.ConvertingCursor;
import de.brightbyte.data.cursor.DataCursor;

public class MangelingCursor<R> extends ConvertingCursor<R, R> {

	public MangelingCursor(DataCursor<R> source, Functor<R, R> mangler) {
		super(source, mangler);
	}
	
	public MangelingCursor(DataCursor<R> source, Functor<R, R>... mangler) {
		super(source, new MultiMangler(mangler));
	}
	
}
