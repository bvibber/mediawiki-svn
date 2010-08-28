package net.psammead.mwapi.connection;

import net.psammead.mwapi.Location;
import net.psammead.mwapi.NameSpace;
import net.psammead.mwapi.config.Family;
import net.psammead.mwapi.config.Site;
import net.psammead.mwapi.ui.UnsupportedWikiException;

/** uses the SiteManager to create Locations */
public final class LocationManager {
	private final ConfigManager configManager;
	
	public LocationManager(ConfigManager siteManager) {
		this.configManager	= siteManager;
	}
	
	/** creates a Location for an absolute link */
	public Location absoluteLocation(String link) throws UnsupportedWikiException {
		link	= TitleUtil.underscores(link);
		
		int	pos	= link.indexOf(':');
		if (pos == -1)	return null;
		final String	familyName	= link.substring(0, pos);
		link	= link.substring(pos+1);
		Family		family	= configManager.getFamily(familyName);
		if (family == null)	throw new UnsupportedWikiException("family unknown: " + link);
		
		final String	siteLanguage;
		final String	wiki;
		if (family.multilingual) {
			pos	= link.indexOf(':');
			if (pos == -1)	throw new UnsupportedWikiException("language missing: " + link);
			siteLanguage	= link.substring(0, pos);
			link		= link.substring(pos+1);
			wiki		= TitleUtil.buildWiki(family.name, siteLanguage);
		}
		else {
			siteLanguage	= family.defaultLanguage;	// is null
			wiki		= TitleUtil.buildWiki(family.name, siteLanguage);
		}
		
		return new Location(wiki, link);
	}
	
	/** creates a Location for a link relative to a base Location */
	public Location relativeLocation(Location baseLocation, String link) throws UnsupportedWikiException {
		link	= TitleUtil.underscores(link);
		
		// /subpage is appended to the title
		if (link.startsWith("/")) {
			return new Location(baseLocation.wiki, baseLocation.title + link);
		}
		
		// remove : from the start
		if (link.startsWith(":"))	link	= link.substring(1);
		
		// get the site we come from
		final Site	baseSite	= configManager.getSite(baseLocation.wiki);
		
		//### siteName und familyName immer lowercase halten?
		
		// split base wiki into familyName and siteLanguage (the latter may be null)
		String	wiki			= baseLocation.wiki;
		String	familyName		= wiki;
		String	siteLanguage	= null;
		int		pos				= wiki.indexOf(':');
		if (pos != -1) {
			familyName	= wiki.substring(0,pos);
			siteLanguage	= wiki.substring(pos+1);
		}

		// link contains no : then keep wiki from base and use link as title
		pos	= link.indexOf(':');
		if (pos == -1)	return new Location(wiki, link);
		
		// link may contain a family: then we use that family and possibly reset to the default language
		String	maybe	= link.substring(0, pos);
		Family	family	= configManager.getFamily(maybe);	// case-insensitive!
		final NameSpace	projectNS	= baseSite.nameSpace(NameSpace.PROJECT);
		
		//### !projectNS.matches(link) ist kaputt: das darf überhaupt kein NS sein, richtig?
		//### maybe.toLowerCase().equals(maybe) ist häßlich: unnötig wenn gar kein NS mehr greift?
		//### jetzt ist [[Commons:Commons:Categories]] kaputt :/
		//### [[Commons:Categories]] läuft auf einen redirect weil Commons großgeschrieben ist
		//### dafür geht [[MediaWiki:test]] und [[mediawiki:test]]
		
		// if the link starts with a family, we take this family
		if (family != null && !projectNS.matches(link) && maybe.toLowerCase().equals(maybe)) {	
			// change to the new family, but skip the family when the metaNs was used
			familyName	= family.name;
			
			// reset to the default language when the long name was used instead of the shortcut
			if (maybe.equalsIgnoreCase(family.name)) {
				siteLanguage	= family.defaultLanguage;	// may be null
			}
			// reset to the default language when we switched to a non-multilingual family
			else if (!family.multilingual) {
				siteLanguage	= family.defaultLanguage;
			}
			// reset to the default language when no language was known before
			else if (siteLanguage == null)	{
				siteLanguage	= family.defaultLanguage;
			}
			
			link	= link.substring(pos+1);
		}
		// we were wrong, the link contains no family, change to the base family
		else {
			family		= configManager.getFamily(familyName);	// case-insensitive!
			familyName	= family.name;
		}

		// link may contain a language
		pos	= link.indexOf(':');
		if (pos != -1) {
			maybe		= link.substring(0, pos);
			final Site	site	= configManager.getSite(family, maybe);	// case-insensitive!
			if (site != null) {
				siteLanguage	= maybe.toLowerCase();
				link		= link.substring(pos+1);
			}
		}
		
		
		// switch to the default site, when the one in siteName is not supported
		if (family.multilingual 
		&& siteLanguage != null	// TODO when can this become null?
		&& !family.supportedLanguages.contains(siteLanguage.toLowerCase())) {
			siteLanguage	= family.defaultLanguage;
		}
		
		// wiki is familyName or FamilyName ':' siteName when siteName is not null
		wiki	= familyName;
		if (siteLanguage != null)	wiki	+= ":" + siteLanguage;
		return new Location(wiki, link);
	}

	//==============================================================================
	
	/** 
	 * returns the regular (non-discussion) page for an article 
	 * or null when location is not a discussion page 
	 */
	public Location regularPageFor(Location location) throws UnsupportedWikiException {
		String		wiki	= location.wiki;
		String		title	= location.title;
		Site		site	= configManager.getSite(wiki);
		
		NameSpace	oldNS	= site.nameSpaceForTitle(title);
		if (oldNS.isRegular())	return null;
		NameSpace	newNS	= oldNS.toggleDiscussion();
		if (newNS == null)	return null;
		return new Location(wiki, 
				changeNameSpace(title, oldNS, newNS));
	}
	
	/** 
	 * returns the discussion page for an article 
	 * or null when it already is a discussion page 
	 * or no discussion page exists (Media and Special namespace)
	 */
	public Location discussionPageFor(Location location) throws UnsupportedWikiException {
		String		wiki	= location.wiki;
		String		title	= location.title;
		Site		site	= configManager.getSite(wiki);
		
		NameSpace	oldNS	= site.nameSpaceForTitle(title);
		if (oldNS.isDiscussion())	return null;
		NameSpace	newNS	= oldNS.toggleDiscussion();
		if (newNS == null)	return null;
		return new Location(wiki, 
				changeNameSpace(title, oldNS, newNS));
	}

	/** 
	 * returns the discussion page for an article or the article for a diskussion page
	 * or null for Special and Media pages where no counterpart exists
	 */
	public Location toggleDiscussion(Location location) throws UnsupportedWikiException {
		String		wiki	= location.wiki;
		String		title	= location.title;
		Site		site	= configManager.getSite(wiki);
		
		NameSpace	oldNS	= site.nameSpaceForTitle(title);
		NameSpace	newNS	= oldNS.toggleDiscussion();
		if (newNS == null)	return null;
		return new Location(wiki, 
				changeNameSpace(title, oldNS, newNS));
	}
	
	public String changeNameSpace(String title, NameSpace sourceNS, NameSpace targetNS) {
		title	= sourceNS.removeFrom(title);
		title	= targetNS.addTo(title);
		return title;
	}
}
