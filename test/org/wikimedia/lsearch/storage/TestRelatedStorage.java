package org.wikimedia.lsearch.storage;

import java.io.IOException;
import java.util.ArrayList;

import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.ranks.Links;
import org.wikimedia.lsearch.related.RelatedTitle;
import org.wikimedia.lsearch.spell.api.Dictionary;
import org.wikimedia.lsearch.spell.api.Dictionary.Word;
import org.wikimedia.lsearch.storage.RelatedStorage;

public class TestRelatedStorage {
	public static void main(String args[]) throws IOException{
		Configuration.open();
		String path1 = "/usr/local/var/ls2/snapshot/wikilucene.related/20070905204528/";
		String path2 = "/usr/local/var/ls2/snapshot/wikilucene.related/20070905205406/";
		IndexId iid = IndexId.get("wikilucene");
		RelatedStorage r1 = new RelatedStorage(iid,path1);
		RelatedStorage r2 = new RelatedStorage(iid,path2);
		Links l = Links.openForRead(iid,iid.getLinks().getImportPath());
		
		Dictionary d = l.getKeys();
		Word w;
		while((w = d.next()) != null){
			String key = w.getWord();
			ArrayList<RelatedTitle> rt1 = r1.getRelated(key);
			ArrayList<RelatedTitle> rt2 = r2.getRelated(key);
			if(rt1==null || rt2==null || rt1.size() != rt2.size()){
				print(key,rt1,rt2);
			} else{
				//if(!rt1.equals(rt2))
				//	print(key,rt1,rt2);
			}
		}
		
		
	}

	private static void print(String key, ArrayList<RelatedTitle> rt1, ArrayList<RelatedTitle> rt2) {
		System.out.println("Found differences for key "+key);
		System.out.println(rt1);
		System.out.println(rt2);		
	}
}
