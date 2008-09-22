package org.wikimedia.lsearch.analyzers;

import java.util.HashMap;

/**
 * Porter-based singular filter for english
 * 
 * @author rainman
 *
 */
public class EnglishSingular implements Singular {
	public String getSingular(String word) {
		return findSingular(word);
	}
	
	protected static final String findSingular(String w){		
		// quick check 
		if(w.length() <= 3 || w.charAt(w.length()-1) != 's')
			return null;
		// exceptions (from porter2)
		if("news".equals(w) || "atlas".equals(w) || "cosmos".equals(w) 
				|| "bias".equals(w) || "andes".equals(w) || "aries".equals(w))
			return null;
		// don't strip posssesive form
		if(w.endsWith("'s")){
			//if(w.length() > 2)
			//	return w.substring(0,w.length()-2);
			return null;
		}
		// irregular forms
		String irregular = irregularSingular.get(w);
		if(irregular != null)
			return irregular;
		// similar to step 1a of porter2 stemmer
		if(w.endsWith("sses"))
			return w.substring(0,w.length()-2);
		else if(w.endsWith("ies")){
			if(w.length() == 4) // ties -> tie
				return w.substring(0,3);
			else // flies -> fly
				return w.substring(0,w.length()-3)+"y";
		} else if(w.endsWith("ss") || w.endsWith("us"))
			return null;
		else if(w.endsWith("xes"))
			return w.substring(0,w.length()-2);
		else if(w.endsWith("s"))
			return w.substring(0,w.length()-1);
		
		return null;
	}
	
	protected static final HashMap<String,String> irregularSingular = new HashMap<String,String>();
	static {
		// ves -> f
		irregularSingular.put("calves","calf");
		irregularSingular.put("elves","elf");
		irregularSingular.put("halves","half");
		irregularSingular.put("hooves","hoof");
		irregularSingular.put("knives","knife");
		irregularSingular.put("leaves","leaf");
		irregularSingular.put("lives","life");
		irregularSingular.put("loaves","loaf");
		irregularSingular.put("scarves","scarf");
		irregularSingular.put("selves","self");
		irregularSingular.put("sheaves","sheaf");
		irregularSingular.put("shelves","shelf");
		irregularSingular.put("thieves","thief");
		irregularSingular.put("wives","wife");
		irregularSingular.put("wolves","wolf");		
		// pure irregular
		irregularSingular.put("firemen","fireman");
		irregularSingular.put("feet","foot");
		irregularSingular.put("geese","goose");
		irregularSingular.put("lice","louse");
		irregularSingular.put("men","man");
		irregularSingular.put("mice","mouse");
		irregularSingular.put("teeth","tooth");
		irregularSingular.put("women","woman");		
		// old english
		irregularSingular.put("children","child");
		irregularSingular.put("oxen","ox");	
		// oes -> o
		irregularSingular.put("echoes","echo");
		irregularSingular.put("embargoes","embargo");
		irregularSingular.put("heroes","hero");
		irregularSingular.put("potatoes","potato");
		irregularSingular.put("tomatoes","tomato");
		irregularSingular.put("torpedoes","torpedo");
		irregularSingular.put("vetoes","veto");
		// ces -> x
		irregularSingular.put("apices","apex"); 	
		irregularSingular.put("appendices","appendix");
		irregularSingular.put("cervices","cervix");
		irregularSingular.put("indices","index"); 
		irregularSingular.put("matrices","matrix");
		irregularSingular.put("vortices","vortex"); 	
		// es -> is
		irregularSingular.put("analyses","analysis");
		irregularSingular.put("axes","axis");
		irregularSingular.put("bases","basis");
		irregularSingular.put("crises","crisis");
		irregularSingular.put("diagnoses","diagnosis");
		irregularSingular.put("emphases","emphasis");
		irregularSingular.put("hypotheses","hypothesis");
		irregularSingular.put("neuroses","neurosis");
		irregularSingular.put("oases","oasis");
		irregularSingular.put("parentheses","parenthesis");
		irregularSingular.put("synopses","synopsis");
		irregularSingular.put("theses","thesis");
		// ies -> ie
		irregularSingular.put("cookies","cookie");
	}


}
