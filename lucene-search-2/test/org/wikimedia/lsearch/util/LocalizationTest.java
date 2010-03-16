package org.wikimedia.lsearch.util;

import java.net.URL;

import org.wikimedia.lsearch.config.Configuration;
import org.wikimedia.lsearch.config.IndexId;
import org.wikimedia.lsearch.util.Localization;

public class LocalizationTest {

	/**
	 * @param args
	 */
	public static void main(String[] args) {
		Configuration.open();
		String text = "#redirect [[mw]]";
		System.out.println(text+" => "+Localization.getRedirectTarget(text,"en"));
		text = "#reDIRECT [[mw nja]]";
		System.out.println(text+" => "+Localization.getRedirectTarget(text,"en"));
		text = "#REDIRECT [[MediaWiki]]\n[[Category:Something]]";
		System.out.println(text+" => "+Localization.getRedirectTarget(text,"en"));
		text = "#REDIRECT ]][[MediaWiki]]\n[[Category:Something]]";
		System.out.println(text+" => "+Localization.getRedirectTarget(text,"en"));
		text = "#ПРЕУСМЕРИ [[MediaWiki]]\n[[Category:Something]]";
		System.out.println(text+" => "+Localization.getRedirectTarget(text,"sr"));

		System.out.println(Localization.getRedirectTitle("#redirect [[Slika:Nesto.jpg]]",IndexId.get("srwiki")));
		
		System.out.println(Localization.getRedirectTitle("#REDIRECT [[:Category:Female porn stars#someone]]",IndexId.get("enwiki")));
		
	}

}
