function swapUrl(base) {
	/* Hardcore special cases... use "virtual" hostnames for some flipping. */
	var specialCases = [
		["www.mediawiki.org", "mediawiki.wikipedia.org"],
		["wikimediafoundation.org", "foundation.wikipedia.org"]];
	/* Sites we need to swap in 'wikipedia' in place of 'wikimedia': */
	var specialWikis = [
		'advisory',
		'auditcom',
		'board',
		'chair',
		'chapcom',
		'collab',
		'comcom',
		'commons',
		'exec',
		'grants',
		'incubator',
		'internal',
		'langcom',
		'meta',
		'office',
		'otrs-wiki',
		'quality',
		'searchcom',
		'spcom',
		'species',
		'usability',
		'wikimania2005',
		'wikimania2006',
		'wikimania2007',
		'wikimania2008',
		'wikimania2009',
		'wikimania2010',
		'wikimaniateam',
		'wikimania'];
	var specials = specialWikis.join('|');
	
	if (base[4] == 's') {
		base = base.replace(
			/^https:\/\/secure\.wikimedia\.org\/(.*?)\/(.*?)\/(.*)$/,
			"http://$2.$1.org/$3");
		for (var i = 0; i < specialCases.length; i++) {
			base = base.replace(
				"http://" + specialCases[i][1],
				"http://" + specialCases[i][0]);
		}
		base = base.replace(
			new RegExp("^http:\/\/(" + specials + ")\.wikipedia"),
			"http://$1.wikimedia");
	} else {
		for (var i = 0; i < specialCases.length; i++) {
			base = base.replace(
				"http://" + specialCases[i][0],
				"http://" + specialCases[i][1]);
		}
		base = base.replace(
			new RegExp("^http:\/\/(" + specials + ")\.wikimedia"),
			"http://$1.wikipedia");
		base = base.replace(
			/^http:\/\/(.*?)\.(.*?)\.org\/(.*)$/,
			"https://secure.wikimedia.org/$2/$1/$3");
	}
	return base;
}
document.location=swapUrl(document.location.toString());
