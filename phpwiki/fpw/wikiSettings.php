<?
# ATTENTION:
# To fit your local settings, PLEASE edit wikiLocalSettings.php ONLY!
# Change settings here ONLY if they're to become global in all wikipedias!

# Essential stuff, so the script knows where it is, whar database to use, etc.
#$wikiCurrentServer = "http://127.0.0.1" ;
$wikiCurrentServer = "http://" . getenv("SERVER_NAME");
$wikiSQLServer = "wiki" ; # The name of the database, actually...
$wikiArticleSource = "$wikiCurrentServer/wiki/$1" ;
$wikiLogoFile = "/wiki.png" ;
$wikiStarTrekImage = "/startrek.png" ;
$THESCRIPT = "/wiki.phtml" ; # The name and location of the script. The $PHP_SELF variable doesn't work with Netscape

# For the MySQL database
$wikiThisDBserver = "127.0.0.1" ;
$wikiThisDBuser = "root" ;
$wikiThisDBpassword = "" ;
$minSrchSize = 4;   # this is smallest word size that is indexed by the MySQL fulltext index
# (can be changed by recompiling MySQL and rebuilding the indexes.)
$wikiDBconnection = "";  # global variable to hold the current DB
			 # connection; should be empty initially.

# Cache system enabled by default
$useCachedPages = true ;

# Use English by default
$wikiLanguage = "en";

$wikiLocale = "";

# 

# Interwiki links
$wikiInterwiki = array ( "w"=>"http://www.wikipedia.com/wiki/$1" , "m"=>"http://meta.wikipedia.com/wiki.phtml?title=$1" ) ;

# Language interwiki links
$wikiOtherLanguages = array (
    "ar"=>"http://ar.wikipedia.com/wiki.cgi?$1" ,
    "ca"=>"http://ca.wikipedia.com/wiki.cgi?$1" ,
    "zh"=>"http://zh.wikipedia.com/wiki.cgi?$1" ,
    "dk"=>"http://dk.wikipedia.com/wiki.cgi?$1" ,
    "nl"=>"http://nl.wikipedia.com/wiki.cgi?$1" ,
    "de"=>"http://de.wikipedia.com/wiki.cgi?$1" ,
    "eo"=>"http://eo.wikipedia.com/wiki/$1" ,
    "fr"=>"http://fr.wikipedia.com/wiki.cgi?$1" ,
    "he"=>"http://he.wikipedia.com/wiki.cgi?$1" ,
    "hu"=>"http://hu.wikipedia.com/wiki.cgi?$1" ,
    "it"=>"http://it.wikipedia.com/wiki.cgi?$1" ,
    "ja"=>"http://ja.wikipedia.com/wiki/$1" ,
    "pl"=>"http://pl.wikipedia.com/wiki.cgi?$1" ,
    "pt"=>"http://pt.wikipedia.com/wiki.cgi?$1" ,
    "ru"=>"http://ru.wikipedia.com/wiki.cgi?$1" ,
    "simple"=>"http://simple.wikipedia.com/wiki.cgi?$1" ,
    "es"=>"http://es.wikipedia.com/wiki.cgi?$1" ,
    "sv"=>"http://sv.wikipedia.com/wiki.cgi?$1" ,
    "en"=>"http://www.wikipedia.com/wiki/$1" ,
    "aa"=>"http://aa.wikipedia.com/wiki.cgi?$1" ,
    "ab"=>"http://ab.wikipedia.com/wiki.cgi?$1" ,
    "af"=>"http://af.wikipedia.com/wiki.cgi?$1" ,
    "am"=>"http://am.wikipedia.com/wiki.cgi?$1" ,
    "as"=>"http://as.wikipedia.com/wiki.cgi?$1" ,
    "ay"=>"http://ay.wikipedia.com/wiki.cgi?$1" ,
    "az"=>"http://az.wikipedia.com/wiki.cgi?$1" ,
    "ba"=>"http://ba.wikipedia.com/wiki.cgi?$1" ,
    "be"=>"http://be.wikipedia.com/wiki.cgi?$1" ,
    "bh"=>"http://bh.wikipedia.com/wiki.cgi?$1" ,
    "bi"=>"http://bi.wikipedia.com/wiki.cgi?$1" ,
    "bn"=>"http://bn.wikipedia.com/wiki.cgi?$1" ,
    "bo"=>"http://bo.wikipedia.com/wiki.cgi?$1" ,
    "co"=>"http://co.wikipedia.com/wiki.cgi?$1" ,
    "cs"=>"http://cs.wikipedia.com/wiki.cgi?$1" ,
    "cy"=>"http://cy.wikipedia.com/wiki.cgi?$1" ,
    "da"=>"http://da.wikipedia.com/wiki.cgi?$1" ,
    "dz"=>"http://dz.wikipedia.com/wiki.cgi?$1" ,
    "el"=>"http://el.wikipedia.com/wiki.cgi?$1" ,
    "et"=>"http://et.wikipedia.com/wiki.cgi?$1" ,
    "eu"=>"http://eu.wikipedia.com/wiki.cgi?$1" ,
    "fa"=>"http://fa.wikipedia.com/wiki.cgi?$1" ,
    "fi"=>"http://fi.wikipedia.com/wiki.cgi?$1" ,
    "fj"=>"http://fj.wikipedia.com/wiki.cgi?$1" ,
    "fo"=>"http://fo.wikipedia.com/wiki.cgi?$1" ,
    "fy"=>"http://fy.wikipedia.com/wiki.cgi?$1" ,
    "ga"=>"http://ga.wikipedia.com/wiki.cgi?$1" ,
    "gl"=>"http://gl.wikipedia.com/wiki.cgi?$1" ,
    "gn"=>"http://gn.wikipedia.com/wiki.cgi?$1" ,
    "gu"=>"http://gu.wikipedia.com/wiki.cgi?$1" ,
    "ha"=>"http://ha.wikipedia.com/wiki.cgi?$1" ,
    "hi"=>"http://hi.wikipedia.com/wiki.cgi?$1" ,
    "hr"=>"http://hr.wikipedia.com/wiki.cgi?$1" ,
    "hy"=>"http://hy.wikipedia.com/wiki.cgi?$1" ,
    "ia"=>"http://ia.wikipedia.com/wiki.cgi?$1" ,
    "id"=>"http://id.wikipedia.com/wiki.cgi?$1" ,
    "ik"=>"http://ik.wikipedia.com/wiki.cgi?$1" ,
    "is"=>"http://is.wikipedia.com/wiki.cgi?$1" ,
    "iu"=>"http://iu.wikipedia.com/wiki.cgi?$1" ,
    "jv"=>"http://jv.wikipedia.com/wiki.cgi?$1" ,
    "ka"=>"http://ka.wikipedia.com/wiki.cgi?$1" ,
    "kk"=>"http://kk.wikipedia.com/wiki.cgi?$1" ,
    "kl"=>"http://kl.wikipedia.com/wiki.cgi?$1" ,
    "km"=>"http://km.wikipedia.com/wiki.cgi?$1" ,
    "kn"=>"http://kn.wikipedia.com/wiki.cgi?$1" ,
    "ko"=>"http://ko.wikipedia.com/wiki.cgi?$1" ,
    "ks"=>"http://ks.wikipedia.com/wiki.cgi?$1" ,
    "ku"=>"http://ku.wikipedia.com/wiki.cgi?$1" ,
    "ky"=>"http://ky.wikipedia.com/wiki.cgi?$1" ,
    "la"=>"http://la.wikipedia.com/wiki.cgi?$1" ,
    "ln"=>"http://ln.wikipedia.com/wiki.cgi?$1" ,
    "lo"=>"http://lo.wikipedia.com/wiki.cgi?$1" ,
    "lt"=>"http://lt.wikipedia.com/wiki.cgi?$1" ,
    "lv"=>"http://lv.wikipedia.com/wiki.cgi?$1" ,
    "mg"=>"http://mg.wikipedia.com/wiki.cgi?$1" ,
    "mi"=>"http://mi.wikipedia.com/wiki.cgi?$1" ,
    "mk"=>"http://mk.wikipedia.com/wiki.cgi?$1" ,
    "ml"=>"http://ml.wikipedia.com/wiki.cgi?$1" ,
    "mn"=>"http://mn.wikipedia.com/wiki.cgi?$1" ,
    "mo"=>"http://mo.wikipedia.com/wiki.cgi?$1" ,
    "mr"=>"http://mr.wikipedia.com/wiki.cgi?$1" ,
    "ms"=>"http://ms.wikipedia.com/wiki.cgi?$1" ,
    "my"=>"http://my.wikipedia.com/wiki.cgi?$1" ,
    "na"=>"http://na.wikipedia.com/wiki.cgi?$1" ,
    "ne"=>"http://ne.wikipedia.com/wiki.cgi?$1" ,
    "oc"=>"http://oc.wikipedia.com/wiki.cgi?$1" ,
    "om"=>"http://om.wikipedia.com/wiki.cgi?$1" ,
    "or"=>"http://or.wikipedia.com/wiki.cgi?$1" ,
    "pa"=>"http://pa.wikipedia.com/wiki.cgi?$1" ,
    "ps"=>"http://ps.wikipedia.com/wiki.cgi?$1" ,
    "qu"=>"http://qu.wikipedia.com/wiki.cgi?$1" ,
    "rm"=>"http://rm.wikipedia.com/wiki.cgi?$1" ,
    "rn"=>"http://rn.wikipedia.com/wiki.cgi?$1" ,
    "ro"=>"http://ro.wikipedia.com/wiki.cgi?$1" ,
    "rw"=>"http://rw.wikipedia.com/wiki.cgi?$1" ,
    "sa"=>"http://sa.wikipedia.com/wiki.cgi?$1" ,
    "sd"=>"http://sd.wikipedia.com/wiki.cgi?$1" ,
    "sg"=>"http://sg.wikipedia.com/wiki.cgi?$1" ,
    "sh"=>"http://sh.wikipedia.com/wiki.cgi?$1" ,
    "sk"=>"http://sk.wikipedia.com/wiki.cgi?$1" ,
    "sl"=>"http://sl.wikipedia.com/wiki.cgi?$1" ,
    "sm"=>"http://sm.wikipedia.com/wiki.cgi?$1" ,
    "sn"=>"http://sn.wikipedia.com/wiki.cgi?$1" ,
    "so"=>"http://so.wikipedia.com/wiki.cgi?$1" ,
    "sq"=>"http://sq.wikipedia.com/wiki.cgi?$1" ,
    "sr"=>"http://sr.wikipedia.com/wiki.cgi?$1" ,
    "ss"=>"http://ss.wikipedia.com/wiki.cgi?$1" ,
    "st"=>"http://st.wikipedia.com/wiki.cgi?$1" ,
    "su"=>"http://su.wikipedia.com/wiki.cgi?$1" ,
    "sw"=>"http://sw.wikipedia.com/wiki.cgi?$1" ,
    "ta"=>"http://ta.wikipedia.com/wiki.cgi?$1" ,
    "te"=>"http://te.wikipedia.com/wiki.cgi?$1" ,
    "tg"=>"http://tg.wikipedia.com/wiki.cgi?$1" ,
    "th"=>"http://th.wikipedia.com/wiki.cgi?$1" ,
    "ti"=>"http://ti.wikipedia.com/wiki.cgi?$1" ,
    "tk"=>"http://tk.wikipedia.com/wiki.cgi?$1" ,
    "tl"=>"http://tl.wikipedia.com/wiki.cgi?$1" ,
    "tn"=>"http://tn.wikipedia.com/wiki.cgi?$1" ,
    "to"=>"http://to.wikipedia.com/wiki.cgi?$1" ,
    "tr"=>"http://tr.wikipedia.com/wiki.cgi?$1" ,
    "ts"=>"http://ts.wikipedia.com/wiki.cgi?$1" ,
    "tt"=>"http://tt.wikipedia.com/wiki.cgi?$1" ,
    "tw"=>"http://tw.wikipedia.com/wiki.cgi?$1" ,
    "ug"=>"http://ug.wikipedia.com/wiki.cgi?$1" ,
    "uk"=>"http://uk.wikipedia.com/wiki.cgi?$1" ,
    "ur"=>"http://ur.wikipedia.com/wiki.cgi?$1" ,
    "uz"=>"http://uz.wikipedia.com/wiki.cgi?$1" ,
    "vi"=>"http://vi.wikipedia.com/wiki.cgi?$1" ,
    "vo"=>"http://vo.wikipedia.com/wiki.cgi?$1" ,
    "wo"=>"http://wo.wikipedia.com/wiki.cgi?$1" ,
    "xh"=>"http://xh.wikipedia.com/wiki.cgi?$1" ,
    "yi"=>"http://yi.wikipedia.com/wiki.cgi?$1" ,
    "yo"=>"http://yo.wikipedia.com/wiki.cgi?$1" ,
    "za"=>"http://za.wikipedia.com/wiki.cgi?$1" ,
    "zu"=>"http://zu.wikipedia.com/wiki.cgi?$1"
    ) ;

# Language interwiki names
$wikiLanguageNames = array (
	"af" => "Afrikaans",
	"ar" => "Araby",
	"es" => "Castellano",
	"ca" => "Catal&agrave;",
	"dk" => "Dansk",
	"de" => "Deutsch",
	"en" => "English",
	"simple" => "English (simplified)",
	"eo" => "Esperanto",
	"eu" => "Euskara",
	"fr" => "Fran&ccedil;ais",
	"zh" => "Hanyu",
	"sh" => "Hrvatskasrpski",
	"ia" => "Interlingua",
	"it" => "Italiano",
	"he" => "Ivrit",
	"hu" => "Magyar",
	"la" => "Latino",
	"nl" => "Nederlands",
	"ja" => "Nihongo",
	"no" => "Norsk",
	"pl" => "Polska",
	"pt" => "Portugu&ecirc;s",
	"ru" => "Russkiy",
	"sv" => "Svenska",
	"vo" => "Volap&uuml;k",

	# Native names; need double-checking
	"cs" => "Cesky",
	"el" => "Ellenika",
	"fa" => "Farsi",
	"fi" => "Suomi",
	"hr" => "Hrvatksa",
	"sr" => "Srpska",
	"tl" => "Tagalog",

	# Mostly English names; need checking!
	"aa" => "Afar",
	"ab" => "Abkhazian",
	"af" => "Afrikaans",
	"am" => "Amharic",
	"as" => "Assamese",
	"ay" => "Aymara",
	"az" => "Azerbaijani",
	"ba" => "Bashkir",
	"be" => "Belorussian",
	"bh" => "Bihari",
	"bi" => "Bislama",
	"bn" => "Bengali",
	"bo" => "Tibetan",
	"co" => "Corsican",
	"cy" => "Welsh",
	"dz" => "Bhutani",
	"et" => "Estonian",
	"eu" => "Euskara",
	"fj" => "Fijian",
	"fo" => "Faeroese",
	"fy" => "Frisian",
	"ga" => "Irish",
	"gl" => "Galician",
	"gn" => "Guarani",
	"gu" => "Gujarati",
	"ha" => "Hausa",
	"hi" => "Hindi",
	"hy" => "Armenian",
	"id" => "Bahasa Indonesia",
	"ik" => "Inupiak",
	"is" => "Icelandic",
	"iu" => "Inuktitut",
	"jv" => "Javanese",
	"ka" => "Georgian",
	"kk" => "Kazakh",
	"kl" => "Greenlandic",
	"km" => "Cambodian",
	"kn" => "Kannada",
	"ko" => "Korean",
	"ks" => "Kashmiri",
	"ku" => "Kurdish",
	"ky" => "Kirghiz",
	"ln" => "Lingala",
	"lo" => "Lao",
	"lt" => "Lithuanian",
	"lv" => "Latvian",
	"mg" => "Malagasy",
	"mi" => "Maori",
	"mk" => "Macdeonian",
	"ml" => "Malayalam",
	"mn" => "Mongolian",
	"mo" => "Moldavian",
	"mr" => "Marathi",
	"ms" => "Malay",
	"my" => "Burmese",
	"na" => "Nauru",
	"ne" => "Nepali",
	"oc" => "Occitan",
	"om" => "Oromo",
	"or" => "Oriya",
	"pa" => "Punjabi",
	"ps" => "Pashto",
	"qu" => "Quechua",
	"rm" => Rhaeto-Romance,
	"rn" => "Kirundi",
	"ro" => "Romanian",
	"rw" => "Kinyarwanda",
	"sa" => "Sanskrit",
	"sd" => "Sindhi",
	"sg" => "Sangro",
	"sk" => "Slovak",
	"sl" => "Slovene",
	"sm" => "Samoan",
	"sn" => "Shona",
	"so" => "Somali",
	"sq" => "Albanian",
	"ss" => "Siswati",
	"st" => "Sesotho",
	"su" => "Sudanese",
	"sw" => "Kiswahili",
	"ta" => "Tamil",
	"te" => "Telugu",
	"tg" => "Tajik",
	"th" => "Thai",
	"ti" => "Tigrinya",
	"tk" => "Turkmen",
	"tn" => "Setswana",
	"to" => "Tonga",
	"tr" => "Turkish",
	"ts" => "Tsonga",
	"tt" => "Tatar",
	"tw" => "Twi",
	"ug" => "Uighur",
	"uk" => "Ukrainian",
	"ur" => "Urdu",
	"uz" => "Uzbek",
	"vi" => "Vietnamese",
	"wo" => "Wolof",
	"xh" => "Xhosa",
	"yi" => "Yiddish",
	"yo" => "Yoruba",
	"za" => "Zhuang",
	"zu" => "Zulu"
    ) ;
$wikiOtherLanguagesText = "Other languages : $1" ;


# Now, load local site-specific settings
include_once ( "wikiLocalSettings.php" ) ;

#
# This file loads up the default English message strings
# and the default server configuration for the English wikipedia.
# This has to be done after the local settings have been read in,
# since variables such as $THESCRIPT are being used.
include_once ( "wikiTextEn.php" ) ;
if ( $wikiLanguage != "en" ) {
    include_once ( "wikiText" . ucfirst ( $wikiLanguage ) . ".php" ) ;
}

# Initialize list of available character encodings to the default if none was set up.
if ( ! isset ( $wikiEncodingCharsets ) ) $wikiEncodingCharsets = array($wikiCharset);
if ( ! isset ( $wikiEncodingNames ) ) $wikiEncodingNames = array($wikiCharset); # Localised names

# Namespace backgrounds
if (! isset ($wikiNamespaceBackground)) {
   $wikiNamespaceBackground = array () ;
   $wikiNamespaceBackground[$wikiTalk] = "#eeFFFF" ;
   $wikiNamespaceBackground["user_talk"] = $wikiNamespaceBackground[$wikiTalk] ;
   $wikiNamespaceBackground["wikipedia_talk"] = $wikiNamespaceBackground[$wikiTalk] ;
   $wikiNamespaceBackground[$wikiUser] = "#FFeeee" ;
   $wikiNamespaceBackground[$wikiWikipedia] = "#eeFFee" ;
   $wikiNamespaceBackground["log"] = "#FFFFcc" ;
   $wikiNamespaceBackground["special"] = "#eeeeee" ;
} 

# Functions

# Is there any reason to localise this function? Ever?
# Not for a language, but other servers, if others want to use this software!
function wikiLink ( $a ) {
    global $wikiArticleSource ;
    $a = str_replace ( " " , "+" , $a ) ;
    $a = str_replace ( "$1" , $a , $wikiArticleSource ) ;
    return $a ;
    }

?>
