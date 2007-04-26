package org.wikimedia.lsearch.index;

import org.apache.lucene.search.DefaultSimilarity;

/**
 * A custom similarity measure, same as default, with except of
 * field length norm, instead of 1/sqrt(numTokens), a linear function
 * is used. This is done to not favor short articles (e.g. stubs)
 * 
 * @author rainman
 *
 */
public class WikiSimilarity extends DefaultSimilarity {

	/**
	 * The length norm is a linear function, with f(1) = 1
	 * and f(10000) = 0.2
	 */
	@Override
	public float lengthNorm(String fieldName, int numTokens) {
		if(fieldName.equals("contents")){
			if( numTokens > 10000) return 0.2f;
			else
				return (float)((-0.00008*numTokens)+1.00008);
		} else
			return super.lengthNorm(fieldName,numTokens);
		
	}

}
