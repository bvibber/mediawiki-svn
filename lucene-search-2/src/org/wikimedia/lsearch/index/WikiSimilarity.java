package org.wikimedia.lsearch.index;

import org.apache.log4j.Logger;
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
	static Logger log = Logger.getLogger(WikiSimilarity.class);
	/**
	 * For content: 
	 *  * length norm is a linear function, with f(1) = 1
	 * and f(10000) = 0.5
	 * 
	 * For titles / title aliases:
	 *  * 1/sqrt(term^3)
	 *  
	 * For redirect / keywords:
	 *  * no length norm
	 * 
	 */
	@Override
	public float lengthNorm(String fieldName, int numTokens) {
		//log.debug("Called lengthNorm("+fieldName+","+numTokens+")");
		if(fieldName.equals("contents")){
			if( numTokens > 10000) return 0.5f;
			else{
				float f = (float)((-0.00005*numTokens)+1.00005);
				//log.debug("Length-norm: "+f+", numtokens: "+numTokens);
				return f;
			}			
		} else if(fieldName.equals("title") || fieldName.equals("stemtitle") || fieldName.startsWith("alttitle")){
			float f = (float) (1.0 / (Math.sqrt(numTokens) * numTokens));
			//log.debug("Length-norm: "+f+", numtokens: "+numTokens);
			return f;
		} else if(fieldName.startsWith("redirect") || fieldName.startsWith("keyword") || fieldName.startsWith("related")  || fieldName.startsWith("anchor") || fieldName.startsWith("context")){
			return 1;
		} else
			return super.lengthNorm(fieldName,numTokens);
		
	}
	

}
