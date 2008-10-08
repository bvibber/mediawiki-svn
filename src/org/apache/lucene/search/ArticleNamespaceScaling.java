package org.apache.lucene.search;

import java.io.Serializable;
import java.util.Collections;
import java.util.Map;
import java.util.Map.Entry;

public class ArticleNamespaceScaling implements Serializable {
	protected float[] nsBoost = null;
	public static float talkPageScale = 0.25f;
	
	/** Initialize from ns -> boost map */
	public ArticleNamespaceScaling(Map<Integer,Float> map){
		int max = map.size()==0? 0 : Collections.max(map.keySet());
		nsBoost = new float[max+2];
		// default values
		for(int i=0;i<nsBoost.length;i++)
			nsBoost[i] = defaultValue(i);
		// custom
		for(Entry<Integer,Float> e : map.entrySet()){
			int ns = e.getKey();
			nsBoost[ns] = e.getValue();
			if(ns % 2 == 0 && !map.containsKey(ns+1)){
				// rescale default for talk page
				nsBoost[ns+1] = e.getValue() * talkPageScale;  
			}
		}
	}
	
	/** Get boost for namespace */
	public float scaleNamespace(int ns){
		if(ns >= nsBoost.length || ns < 0)
			return defaultValue(ns);
		else
			return nsBoost[ns];
	}
	
	/** Produce default boost for namespace */
	protected float defaultValue(int ns){
		if(ns % 2  == 0) // content ns
			return 1f;
		else // corresponding talk ns
			return talkPageScale;
	}
	
}
