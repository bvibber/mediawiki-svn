This is a patch that will allow you to run the Wikidata extension on MediaWiki
trunk r32000 (i.e. outside of the Wikidata branch).

$wgExtraNamespaces = array(
	16 => 'Expression',
	17 => 'Expression_talk',
	22 => 'Portal',
	23 => 'Portal_talk',
	24 => 'DefinedMeaning',
	25 => 'DefinedMeaning_talk',
	26 => 'Search',
	27 => 'Search_talk',
	28 => 'NeedsTranslationTo',
	29 => 'NeedsTranslationTo_talk',
	30 => 'Partner',
	31 => 'Partner_talk'
);

$wgNamespaceAliases = array(
	4 => 'Development_wiki',
	5 => 'Development_wiki_talk',
	16 => 'GEMET',
	17 => 'GEMET_talk',
	16 => 'WiktionaryZ',
	17 => 'WiktionaryZ_talk',
);

$wgNamespacesWithSubpages = array(
	0  => 0, 
	1  => 1, 
	2  => 1, 
	3  => 1, 
	4  => 0, 
	5  => 1, 
	6  => 0, 
	7  => 1, 
	8  => 0, 
	9  => 1, 
	10 => 0, 
	11 => 1, 
	12 => 0, 
	13 => 1, 
	14 => 0, 
	15 => 1, 
	16 => 0, 
	17 => 1, 
	22 => 0, 
	23 => 1, 
	24 => 0, 
	25 => 1, 
	26 => 0, 
	27 => 1, 
	28 => 0, 
	29 => 1, 
	30 => 0, 
	31 => 1, 
);

$wgMetaNamespace = 'Meta';

$wgWikiDataHandlerPath = "$IP/extensions/Wikidata/OmegaWiki/";
$wgWikiDataHandlerClasses = array(
	28 => 'NeedsTranslationTo',
	24 => 'DefinedMeaning',
	16 => 'OmegaWiki',
	26 => 'Search'
);
