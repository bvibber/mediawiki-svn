function swapUrl(base) {
	var specials = "meta|commons|office|internal|collab|usability";
	if (base[4] == 's') {
		base = base.replace(
			/^https:\/\/secure\.wikimedia\.org\/(.*?)\/(.*?)\/(.*)$/,
			"http://$2.$1.org/$3");
		base = base.replace(
			"http://mediawiki.wikipedia",
			"http://www.mediawiki");
		base = base.replace(
			new RegExp("^http:\/\/(" + specials + ")\.wikipedia"),
			"http://$1.wikimedia");
	} else {
		base = base.replace(
			"http://www.mediawiki",
			"http://mediawiki.wikipedia");
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
