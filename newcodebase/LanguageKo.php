<?

# The names of the namespaces can be set here, but the numbers
# are magical, so don't change or move them!  The Namespace class
# encapsulates some of the magic-ness.
#
/* private */ $wgNamespaceNamesKo = array(
	-1	=> "Special",
	0	=> "",
	1	=> "Talk",
	2	=> "User",
	3	=> "User_talk",
	4	=> "Wikipedia",
	5	=> "Wikipedia_talk",
	6	=> "Image",
	7	=> "Image_talk"
# Non-ASCII chars don't work yet in namespaces. This is a bug that will be fixed.
#	1	=> "토론",
#	2	=> "사용자",
#	3	=> "사용자토론",
#	4	=> "위키백과",
#	5	=> "위키백과토론",
#	6	=> "그림",
#	7	=> "그림토론"
);

/* private */ $wgDefaultUserOptionsKo = array(
	"quickbar" => 1, "underline" => 1, "hover" => 1,
	"cols" => 80, "rows" => 25, "searchlimit" => 20,
	"contextlines" => 5, "contextchars" => 50,
	"skin" => 0, "rcdays" => 3, "rclimit" => 50,
	"highlightbroken" => 1, "stubthreshold" => 0
);

/* private */ $wgQuickbarSettingsKo = array(
	"None", "Fixed left", "Fixed right", "Floating left"
);

/* private */ $wgSkinNamesKo = array(
	"Standard", "Nostalgia", "Cologne Blue"
);

/* private */ $wgUserTogglesKo = array(
	"hover"		=> "Show hoverbox over wiki links",
	"underline" => "고리에 밑줄치기",
	"highlightbroken" => "없는 문서로의 고리 돋보이기",
	"justify"	=> "Justify paragraphs",
	"hideminor" => "사소한 편집 최근 고침에서 숨기기",
	"numberheadings" => "Auto-number headings",
	"rememberpassword" => "세션동안 암호 기억",
	"editwidth" => "편집창너비 최대"
);

/* private */ $wgBookstoreListKo = array(
	"AddALL" => "http://www.addall.com/New/Partner.cgi?query=$1&type=ISBN",
	"PriceSCAN" => "http://www.pricescan.com/books/bookDetail.asp?isbn=$1",
	"Barnes & Noble" => "http://shop.barnesandnoble.com/bookSearch/isbnInquiry.asp?isbn=$1",
	"Amazon.com" => "http://www.amazon.com/exec/obidos/ISBN=$1"
);

/* These should preferably be the native names of the languages; the
   point is for people who speak them to be able to navigate to them
   from any language section of the Wikipedia. */
/* 아래 언어명은 그 언어 사용자들을 위한 것이므로, 한국어로 고치지 마세요. */
/* private */ $wgLanguageNamesKo = array(
    "af" => "Afrikaans",
	"ar" => "&#8238;&#1575;&#1604;&#1593;&#1585;&#1576;&#1610;&#1577;&#8236; (Araby)",
	"be" => "&#1041;&#1077;&#1083;&#1072;&#1088;&#1091;&#1089;&#1082;&#1080;",
	"br" => "Brezhoneg",
	"ca" => "Catal&#224;",
	"ch" => "Chamoru",
	"cs" => "&#268;esk&#225;",
	"cy" => "Cymraeg",
	"da" => "Dansk", # Note two different subdomains. 
	"dk" => "Dansk", # 'da' is correct for the language.
	"de" => "Deutsch",
	"el" => "&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940; (Ellenika)",
	"en" => "English",
	"simple" => "Simple English",
	"eo" => "Esperanto",
	"es" => "Espa&#241;ol",
	"et" => "Eesti",
	"eu" => "Euskara",
	"fa" => "&#8238;&#1601;&#1585;&#1587;&#1609;&#8236;(Farsi)",
	"fi" => "Suomi",
	"fr" => "Fran&#231;ais",
	"gu" => "&#2711;&#2753;&#2716;&#2736;&#2750;&#2724;&#2752; (Gujarati)",
	"he" => "&#1506;&#1489;&#1512;&#1497;&#1514; (Ivrit)",
	"hi" => "&#2361;&#2367;&#2344;&#2381;&#2342;&#2368; (Hindi)",
	"hr" => "Hrvatski",
	"hu" => "Magyar",
	"ia" => "Interlingua",
	"id" => "Indonesia",
	"is" => "&#205;slenska",
	"it" => "Italiano",
	"ja" => "&#26085;&#26412;&#35486; (Nihongo)",
	"ka" => "&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4312; (Kartuli)",
	"ko" => "한국어",
	"kw" => "Kernewek",
	"la" => "Latina",
	"lt" => "Lietuvi&#371;",
	"mg" => "Malagasy",
	"ms" => "Bahasa Melayu",
	"ne" => "&#2344;&#2375;&#2346;&#2366;&#2354;&#2368; (Nepali)",
	"nl" => "Nederlands",
	"no" => "Norsk",
	"pl" => "Polski",
	"pt" => "Portugu&#234;s",
	"ro" => "Rom&#226;n&#259;",
	"ru" => "&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081; (Russkij)",
	"sa" => "&#2360;&#2306;&#2360;&#2381;&#2325;&#2371;&#2340; (Samskrta)",
	"so" => "Soomaali",
	"sq" => "Shqiptare",
	"sr" => "Srpski",
	"sv" => "Svenska",
	"sw" => "Kiswahili",
	"tr" => "T&#252;rk&#231;e",
	"uk" => "&#1059;&#1082;&#1088;&#1072;&#1111;&#1085;&#1089;&#1100;&#1082;&#1072; (Ukrayins`ka)",
	"vo" => "Volap&#252;k",
	"xh" => "isiXhosa",
	"zh" => "&#20013;&#25991; (Zhongwen)"
);


/* private */ $wgWeekdayNamesKo = array(
	"일요일", "월요일", "화요일", "수요일", "목요일",
	"금요일", "토요일"
);

/* private */ $wgMonthNamesKo = array(
	"1월", "2월", "3월", "4월", "5월", "6월",
	"7월", "8월", "9월", "10월", "11월",
	"12월"
);

/* private */ $wgMonthAbbreviationsKo = array(
	"1월", "2월", "3월", "4월", "5월", "6월", "7월", "8월",
	 "9월", "10월", "11월",	"12월"
);

# All special pages have to be listed here: a description of ""
# will make them not show up on the "Special Pages" page, which
# is the right thing for some of them (such as the "targeted" ones).
#
# article 문서 user 사용자

/* private */ $wgValidSpecialPagesKo = array(
	"Userlogin"		=> "들어가기",
	"Userlogout"	=> "나오기",
	"Preferences"	=> "자기 설정",
	"Watchlist"		=> "눈여겨보는항목",
	"Recentchanges" => "최근 바뀜",
	"Upload"		=> "파일 올리기",
	"Imagelist"		=> "그림목록",
	"Listusers"		=> "등록된 사용자",
	"Statistics"	=> "누리터 통계",
	"Randompage"	=> "아무거나",

	"Lonelypages"	=> "외톨이 문서",
	"Unusedimages"	=> "외톨이 그림",
	"Popularpages"	=> "인기있는 문서",
	"Wantedpages"	=> "가장 필요한 문서",
	"Shortpages"	=> "짧은 문서",
	"Longpages"		=> "긴 문서",
	"Newpages"		=> "새 문서",
	"Allpages"		=> "모두 가나다순으로",

	"Ipblocklist"	=> "막힌 IP",
	"Maintenance" => "관리",
	"Specialpages"  => "특별문서",
	"Contributions" => "기여한 문서",
	"Emailuser"		=> "사용자에게 편지보내기",
	"Whatlinkshere" => "여기 걸린 문서",
	"Recentchangeslinked" => "고리 최근바뀜",
	"Movepage"		=> "문서 옮기기",
	"Booksources"	=> "외부 책방"
);

/* private */ $wgSysopSpecialPagesKo = array(
	"Blockip"		=> "Block an IP address",
	"Asksql"		=> "Query the database",
	"Undelete"		=> "Undelete Page"
);

/* private */ $wgDeveloperSpecialPagesKo = array(
	"Lockdb"		=> "Make database read-only",
	"Unlockdb"		=> "Restore database write access",
	"Debug"			=> "Debugging information"
);

/* private */ $wgAllMessagesKo = array(

# Bits of text used by many pages:
#
"mainpage"		=> "대문",
"about"			=> "About",
"aboutwikipedia" => "위키백과란",
"aboutpage"		=> "위키백과:About",
"help"			=> "도움말",
"helppage"		=> "위키백과:도움말",
"wikititlesuffix" => "위키백과",
"bugreports"	=> "Bug reports",
"bugreportspage" => "위키백과:Bug_reports",
"faq"			=> "잦은질문",
"faqpage"		=> "위키백과:잦은질문",
"edithelp"		=> "편집 도움말",
"edithelppage"	=> "위키백과:문서_편집_방법",
"cancel"		=> "취소",
"qbfind"		=> "찾기",
"qbbrowse"		=> "Browse",
"qbedit"		=> "편집",
"qbpageoptions" => "문서 옵션",
"qbpageinfo"	=> "문서 정보",
"qbmyoptions"	=> "자기 옵션",
"mypage"		=> "자기 문서",
"mytalk"		=> "자기 토론",
"currentevents" => "요즘 관심사",
"errorpagetitle" => "오류",
"returnto"		=> "$1 로 돌아가기.",
"fromwikipedia"	=> "위키백과, 우리 모두의 백과사전.",
"whatlinkshere"	=> "여기에 걸린 문서",
"help"			=> "도움말",
"search"		=> "찾기",
"history"		=> "문서역사",
"printableversion" => "인쇄용",
"editthispage"	=> "문서 고치기",
"deletethispage" => "문서 지우기",
"protectthispage" => "문서 보호",
"unprotectthispage" => "문서 보호 해제",
"talkpage"		=> "토론",
"subjectpage"	=> "본 문서",
"otherlanguages" => "다른 언어",
"redirectedfrom" => "($1에서 넘어옴.)",
"lastmodified"	=> "이 문서는 최근 $1 에 편집되었습니다.",
"viewcount"		=> "이 문서는 $1 번 접근되었습니다.",
"printsubtitle" => "(출처 http://www.wikipedia.org)",
"protectedpage" => "보호되는 문서",
"administrators" => "위키백과:관리자",
"sysoptitle"	=> "Sysop access required",
"sysoptext"		=> "The action you have requested can only be
performed by users with \"sysop\" status.
See $1.",
"developertitle" => "Developer access required",
"developertext"	=> "The action you have requested can only be
performed by users with \"developer\" status.
See $1.",
"nbytes"		=> "$1 바이트",
"go"			=> "가기",
"ok"			=> "확인",
"sitetitle"		=> "위키백과",
"sitesubtitle"	=> "우리 모두의 백과사전",
"retrievedfrom" => "Retrieved from \"$1\"",

# Main script and global functions
#
"nosuchaction"	=> "No such action",
"nosuchactiontext" => "The action specified by the URL is not
recognized by the Wikipedia software",
"nosuchspecialpage" => "그런 특별문서는 없습니다.",
"nospecialpagetext" => "You have requested a special page that is not
recognized by the Wikipedia software.",

# General errors
#
"error"			=> "Error",
"databaseerror" => "Database error",
"dberrortext"	=> "A database query syntax error has occurred.
This could be because of an illegal search query (see $5),
or it may indicate a bug in the software.
The last attempted database query was:
<blockquote><tt>$1</tt></blockquote>
from within function \"<tt>$2</tt>\".
MySQL returned error \"<tt>$3: $4</tt>\".",
"noconnect"		=> "Could not connect to DB on $1",
"nodb"			=> "Could not select database $1",
"readonly"		=> "Database locked",
"enterlockreason" => "Enter a reason for the lock, including an estimate
of when the lock will be released",
"readonlytext"	=> "The Wikipedia database is currently locked to new
entries and other modifications, probably for routine database maintenance,
after which it will be back to normal.
The administrator who locked it offered this explanation:
<p>$1",
"missingarticle" => "The database did not find the text of a page
that it should have found, named \"$1\".
This is not a database error, but likely a bug in the software.
Please report this to an administrator, making note of the URL.",
"internalerror" => "Internal error",
"filecopyerror" => "Could not copy file \"$1\" to \"$2\".",
"filerenameerror" => "Could not rename file \"$1\" to \"$2\".",
"filedeleteerror" => "Could not delete file \"$1\".",
"filenotfound"	=> "Could not find file \"$1\".",
"unexpected"	=> "Unexpected value: \"$1\"=\"$2\".",
"formerror"		=> "Error: could not submit form",	
"badarticleerror" => "This action cannot be performed on this page.",
"cannotdelete"	=> "Could not delete the page or image specified.",

# Login and logout pages
#
"logouttitle"	=> "들어가기",
"logouttext"	=> "위키백과에서 나왔습니다.
계속 이름없이 위키백과를 사용하거나, 먼저 들어갔던 또이름, 혹은 다른 또이름으로 들어갈 수 있습니다.\n",

"welcomecreation" => "<h2>$1 님, 환영합니다!</h2><p>또이름이 만들어 졌습니다.
사용자 설정을 편하게 맞추는 것도 잊지 마세요.",

"loginpagetitle" => "들어가기",
"yourname"		=> "당신의 또이름",
"yourpassword"	=> "당신의 암호",
"yourpasswordagain" => "다시한번 암호입력",
"newusersonly"	=> " (새로운 사용자만)",
"remembermypassword" => "세쎤동안 암호를 기억합니다.",
"loginproblem"	=> "<b>들어가는 데 문제가 발생했습니다.</b><br>다시 시도하세요!",
"alreadyloggedin" => "<font color=red><b>$1 님, 이미 들어와 있습니다!</b></font><br>\n",

"areyounew"		=> "위키백과에 처음이고, 이곳에 또이름을 갖고 싶다면, 또이름을 하나 만들어 또이름란에 써 넣고, 암호를 두번 써 넣으세요. 누리편지 주소를 반드시 넣을 필요는 없습니다. 암호를 잊었을 때, 편지로 암호를 받을 수 있습니다.<br>\n",

"login"			=> "들어가기",
"userlogin"		=> "들어가기",
"logout"		=> "나오기",
"userlogout"	=> "나오기",
"createaccount"	=> "또이름 새로 만들기",
"badretype"		=> "암호가 틀립니다.",
"userexists"	=> "써 넣은 또이름은 이미 사용중입니다. 다른 또이름을 고르세요.",
"youremail"		=> "당신의 누리편지",
"yournick"		=> "당신의 별명 (서명용)",
"emailforlost"	=> "암호를 잊었을 때, 새 암호를 누리편지로 받을 수 있습니다.",
"loginerror"	=> "들어가기 오류",
"noname"		=> "또이름이 틀립니다.",
"loginsuccesstitle" => "들어가기 성공",
"loginsuccess"	=> "\"$1\" 위키백과에 들어왔습니다.",
"nosuchuser"	=> "\"$1\" 또이름은 존재하지 않습니다.".
"철자가 틀리지 않았는지 확인하고, 아직 또이름을 안 만들었다면, 아래 폼으로 새 또이름을 만드세요.",
"wrongpassword"	=> "암호가 틀립니다. 다시 시도하세요.",
"mailmypassword" => "새 암호를 누리편지로 보냅니다.",
"passwordremindertitle" => "위키백과에서 보내는 새 암호",
"passwordremindertext" => "누군가가 (IP $1 을 사용했던, 아마도 당신이)
새 위키백과 암호를 보내달라고 부탁했습니다.
또이름 \"$2\" 의 암호는 이제 \"$3\" 입니다.
새 암호로 위키백과에 들어와서, 암호를 바꾸세요.",
"noemail"		=> "또이름 \"$1\" 는 누리편지주소정보가 없습니다.",
"passwordsent"	=> "\"$1\" 의 새로운 암호를 누리편지로 보냈습니다.
암호를 받고 다시 들어오세요.",

# Edit pages
#
"summary"		=> "편집내용 간추림",
"minoredit"		=> "사소한 편집",
"savearticle"	=> "저장",
"preview"		=> "미리보기",
"showpreview"	=> "미리보기",
"blockedtitle"	=> "사용자 접근금지",
"blockedtext"	=> "당신의 또이름이나 IP를 $1 가 막아놨습니다.
이유는 다음과 같습니다:<br>$2<p> 접근금지에 대해선 관리자와 상의하십시오.",
"newarticle"	=> "(새문서)",
"newarticletext" => "새문서에 내용을 써 넣으세요.",
"noarticletext" => "(현재 이 문서는 비었습니다.)",
"updated"		=> "(바뀜)",
"note"			=> "<strong>주의:</strong> ",
"previewnote"	=> "지금 미리보기에서 보이는 내용은 아직 저장되지 않았습니다!",
"previewconflict" => "This preview reflects the text in the upper
text editing area as it will appear if you choose to save.",
"editing"		=> "Editing $1", # 편집중? 편집하기?
"editconflict"	=> "고치기 충돌: $1",
"explainconflict" => "문서를 고쳐쓰는 동안에 다른 누군가가 문서를 바꾸어 놓았습니다.
위쪽이 현재 문서의 내용이고, 아래쪽이 당신이 고친 내용입니다.
당신이 고친내용을 현재 문서와 합쳐야 할 것입니다.
지금 \"저장하기\"를 누르면, 
<b>오직</b> 위쪽에 있는 내용만 저장될 것입니다.\n<p>",
"yourtext"		=> "당신이 고쳐쓴 것",
"storedversion" => "저장되어 있는 것",
"editingold"	=> "<strong>WARNING: You are editing an out-of-date
revision of this page.
If you save it, any changes made since this revision will be lost.</strong>\n",
"yourdiff"		=> "차이",
"copyrightwarning" => "위키백과에 당신이 기여한 것은 모두 the GNU Free Documentation License
(자세한 것은 $1 에서)에 따라 배포됩니다.
당신이 써 넣은 내용이 제한없이 고쳐지고, 재배포되는 것에 반대할 때에는, 여기에 쓰지 마시길 바랍니다.<br>
또, 여기 써 넣은 내용이 당신 스스로 쓴 것이거나, 유사한 공개 소스에서 복사해 온 것임을 약속해야 합니다.
<strong>저작권이 있는 내용을 허가없이 보내지 마십시오!</strong>",


# History pages
#
"revhistory"	=> "문서역사",
"nohistory"		=> "이 문서에는 역사가 없습니다.",
"revnotfound"	=> "변경사항이 없습니다.",
"revnotfoundtext" => "The old revision of the page you asked for could not be found.
Please check the URL you used to access this page.\n",
"loadhist"		=> "문서역사를 받고 있습니다.",
"currentrev"	=> "현재 revision",
"revisionasof"	=> "$1 의 Revision",
"cur"			=> "현재",
"next"			=> "다음",
"last"			=> "이전",
"orig"			=> "orig",
"histlegend"	=> "Legend: (현재) = 현재 버전과의 차이,
(이전) = 바로 이전 버전과의 차이, M = 사소한 고침",

# Diffs
#
"difference"	=> "(Difference between revisions)",
"loadingrev"	=> "loading revision for diff",
"lineno"		=> "$1 째 줄:",
"editcurrent"	=> "현재 버전의 문서를 고칩니다.",

# Search results
#
"searchresults" => "찾은 결과",
"searchhelppage" => "위키백과:찾기",
"searchingwikipedia" => "위키백과 찾기",
"searchresulttext" => "위키백과 찾기에 대해 자세한 정보는 $1 를 보세요.",
"searchquery"	=> "For query \"$1\"",
"badquery"		=> "Badly formed search query",
"badquerytext"	=> "We could not process your query.
This is probably because you have attempted to search for a
word fewer than three letters long, which is not yet supported.
It could also be that you have mistyped the expression, for
example \"fish and and scales\".
Please try another query.",
"matchtotals"	=> "The query \"$1\" matched $2 article titles
and the text of $3 articles.",
"titlematches"	=> "문서 제목 일치",
"notitlematches" => "제목과 일치하는 문서가 없습니다.",
"textmatches"	=> "문서 내용 일치",
"notextmatches"	=> "No article text matches",
"prevn"			=> "이전 $1",
"nextn"			=> "다음 $1",
"viewprevnext"	=> "View ($1) ($2) ($3).",
"showingresults" => "Showing below <b>$1</b> results starting with #<b>$2</b>.",
"nonefound"		=> "<strong>Note</strong>: unsuccessful searches are
often caused by searching for common words like \"have\" and \"from\",
which are not indexed, or by specifying more than one search term (only pages
containing all of the search terms will appear in the result).",
"powersearch" => "찾기",
"powersearchtext" => "
Search in namespaces :<br>
$1<br>
$2 List redirects &nbsp; Search for $3 $9",


# Preferences page
#
"preferences"	=> "설정",
"prefsnologin" => "나와 있습니다.",
"prefsnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to set user preferences.",
"prefslogintext" => "You are logged in as \"$1\".
당신의 내부 인식 번호는 $2.",
"prefsreset"	=> "Preferences have been reset from storage.",
"qbsettings"	=> "Quickbar settings", 
"changepassword" => "암호 바꾸기",
"skin"			=> "Skin",
"saveprefs"		=> "설정 저장",
"resetprefs"	=> "설정 처음으로",
"oldpassword"	=> "현재 암호",
"newpassword"	=> "새로운 암호",
"retypenew"		=> "새 암호 다시 쳐 넣기",
"textboxsize"	=> "문서상자 크기",
"rows"			=> "열수",
"columns"		=> "행수",
"searchresultshead" => "찾기 결과 설정",
"resultsperpage" => "Hits to show per page",
"contextlines"	=> "Lines to show per hit",
"contextchars"	=> "Characters of context per line",
"stubthreshold" => "Threshold for stub display",
"recentchangescount" => "최근 바뀜에 보이는 항목 수",
"savedprefs"	=> "새 설정이 저장되었습니다.",
"timezonetext"	=> "Enter number of hours your local time differs
from server time (UTC).",
"localtime"	=> "현지 시각",
"timezoneoffset" => "Offset",
"emailflag"		=> "다른 사용자로부터 누리편지 안 받음",

# Recent changes
#
"recentchanges" => "최근 바뀐 항목",
"recentchangestext" => "여기에서 가장 최근에 바뀐 문서 목록을 볼 수 있습니다.
[[위키백과:새내기_환영|새내기, 환영]]!
새내기들은 다음 문서를 읽어 보세요.: [[위키백과:잦은질문|위키백과 잦은질문]],
[[위키백과:정책과 지침|위키백과 정책]]
(특별히 [[위키백과:제목달기 규칙|제목달기 규칙]],
[[위키백과:중립적인 시각|중립적인 시각]]),
그리고 [[위키백과:Most common Wikipedia faux pas|most common Wikipedia faux pas]].

위키백과가 성공하길 바란다면, 다른 사람들이 사용을 제한하는 정보를 이곳에 올리지 않는 것이 중요합니다.' [[위키백과:저작권|저작권]].
The legal liability could really hurt the project, so please don't do it.
그리고, [http://meta.wikipedia.org/wiki/Special:Recentchanges 최근 메타 토론]도 보세요.",
"rcloaderr"		=> "최근 바뀜을 받고 있습니다.",
"rcnote"		=> "다음이 최근 <strong>$2</strong>일간 바뀐  <strong>$1</strong>개의 문서입니다. days.",
# "rclinks"		=> "최근 $3일 $2시간 동안에 바뀐 $1개",
"rclinks"		=> "Show last $1 changes in last $2 days.",
"rchide"		=> "in $4 form; $1 minor edits; $2 secondary namespaces; $3 multiple edits.",
"diff"			=> "차이",
"hist"			=> "역사",
"hide"			=> "숨김",
"show"			=> "show",
"tableform"		=> "table",
"listform"		=> "목록",
"nchanges"		=> "$1 바뀜",
"minoreditletter" => "少",
"newpageletter" => "新",

# Upload
#
"upload"		=> "올리기",
"uploadbtn"		=> "파일 올리기",
"uploadlink"	=> "그림 올리기",
"reupload"		=> "다시 올리기",
"reuploaddesc"	=> "다시 올리기 form으로",
"uploadnologin" => "나와있습니다.",
"uploadnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to upload files.",
"uploadfile"	=> "파일 올리기",
"uploaderror"	=> "올리기 오류",
"uploadtext"	=> "<strong>잠깐!</strong> 여기 그림을 올리기 전에,
위키백과의 <a href=\"" .
wfLocalUrlE( "Wikipedia:Image_use_policy" ) . "\">그림 사용 정책</a>읽고 따를 것을 명심해 주세요.
<p>To view or search previously uploaded images,
go to the <a href=\"" . wfLocalUrlE( "Special:Imagelist" ) .
"\">list of uploaded images</a>.
Uploads and deletions are logged on the <a href=\"" .
wfLocalUrlE( "Wikipedia:Upload_log" ) . "\">upload log</a>.
<p>Use the form below to upload new image files for use in
illustrating your articles.
On most browsers, you will see a \"Browse...\" button, which will
bring up your operating system's standard file open dialog.
Choosing a file will fill the name of that file into the text
field next to the button.
You must also check the box affirming that you are not
violating any copyrights by uploading the file.
Press the \"Upload\" button to finish the upload.
This may take some time if you have a slow internet connection.
<p>The preferred formats are JPEG for photographic images, PNG
for drawings and other iconic images, and OGG for sounds.
Please name your files descriptively to avoid confusion.
To include the image in an article, use a link in the form
<b>[[image:file.jpg]]</b> or <b>[[image:file.png|alt text]]</b>
or <b>[[media:file.ogg]]</b> for sounds.
<p>Please note that as with Wikipedia pages, others may edit or
delete your uploads if they think it serves the encyclopedia, and
you may be blocked from uploading if you abuse the system.",
"uploadlog"		=> "upload log",
"uploadlogpage" => "Upload_log",
"uploadlogpagetext" => "Below is a list of the most recent file uploads.
All times shown are server time (UTC).
<ul>
</ul>
",
"filename"		=> "파일명",
"filedesc"		=> "짧은설명",
"affirmation"	=> "I affirm that the copyright holder of this file
agrees to license it under the terms of the $1.",
"copyrightpage" => "위키백과:저작권",
"copyrightpagename" => "위키백과 저작권",
"uploadedfiles"	=> "파일 올리기",
"noaffirmation" => "You must affirm that your upload does not violate
any copyrights.",
"ignorewarning"	=> "경고 무시하고, 파일 저장",
"minlength"		=> "그림이름은 최소한 세글자여야 합니다.",
"badfilename"	=> "그림이름이 \"$1\"로 바뀌었습니다.",
"badfiletype"	=> "\".$1\" 형식은 권장하지 않습니다.",
"largefile"		=> "그림크기는 100k이하를 권장합니다.",
"successfulupload" => "올리기 성공",
"fileuploaded"	=> "\"$1\"가 올라갔습니다.
다음 고리($2)를 따라 가서, 설명문서에 파일에 대한 정보를 (어디서 구했는지, 누가 언제 만들었는지, 또 알고 있는 다른 사항들) 채우세요.",
"uploadwarning" => "올리기 경고",
"savefile"		=> "파일 저장",
"uploadedimage" => "uploaded \"$1\"",

# Image list
#
"imagelist"		=> "그림 목록",
"imagelisttext"	=> "다음은 $2순으로 정리된 $1개의 그림들 입니다.",
"getimagelist"	=> "그림 목록 가져오기",
"ilshowmatch"	=> "이름이 일치하는 그림 모두 보이기",
"ilsubmit"		=> "찾기",
"showlast"		=> "$2순으로 last $1개의 그림 보이기",
"all"			=> "모두",
"byname"		=> "이름",
"bydate"		=> "날짜",
"bysize"		=> "크기",
"imgdelete"		=> "지우기",

"imgdesc"		=> "desc",
"imglegend"		=> "Legend: (desc) = show/edit image description.",
"imghistory"	=> "그림역사",
"revertimg"		=> "rev",
"deleteimg"		=> "지우기",
"imghistlegend" => "Legend: (cur) = this is the current image, (del) = delete
this old version, (rev) = revert to this old version.
<br><i>Click on date to see image uploaded on that date</i>.",
"imagelinks"	=> "그림고리",
"linkstoimage"	=> "다음 문서들이 이 그림을 걸고있습니다:",
"nolinkstoimage" => "이 그림을 걸고 있는 문서는 없습니다.",

# Statistics
#
"statistics"	=> "통계",
"sitestats"		=> "누리터 통계",
"userstats"		=> "사용자 통계",
"sitestatstext" => "이곳 정보창고(db)에는 총 <b>$1</b>개의 문서가 있습니다.
이 숫자는 \"토론\" 문서, 위키백과 자체에 관한 문서, 최소한의 \"씨앗\" 문서, 넘기기, 그리고 아직 어린 문서들을 모두 포함한 것입니다.
그들을 제외하고, <b>$2</b>개의 문서가 있습니다.<p>
또, 무른모 업그레이드가 있었던 2002년 7월 20일 이래, 총 <b>$3</b>번 문서가 읽혔고, <b>$4</b>번 고쳐졌습니다. 
따라서, 평균적으론 한 문서를 <b>$5</b>번 고쳤고, 한 편집당 <b>$6</b>번 읽은 셈이 됩니다.",
"userstatstext" => "<b>$1</b>명의 사용자가 등록되어 있습니다.
이 중 <b>$2</b>명은 관리자입니다.($3 참조)",

# Maintenance Page
#
"maintenance"		=> "Maintenance page",
"maintnancepagetext"	=> "This page includes several handy tools for everyday maintenance. Some of these functions tend to stress the database, so please do not hit reload after every item you fixed ;-)",
"maintenancebacklink"	=> "Back to Maintenance Page",
"disambiguations"	=> "Disambiguation pages",
"disambiguationspage"	=> "Wikipedia:Links_to_disambiguating_pages",
"disambiguationstext"	=> "The following articles link to a <i>disambiguation page</i>. They should link to the appropriate topic instead.<br>A page is treated as dismbiguation if it is linked from $1.<br>Links from other namespaces are <i>not</i> listed here.",
"doubleredirects"	=> "Double Redirects",
"doubleredirectstext"	=> "<b>Attention:</b> This list may contain false positives. That usually means there is additional text with links below the first #REDIRECT.<br>\nEach row contains links to the first and second redirect, as well as the first line of the second redirect text, usually giving the \"real\" taget article, which the first redirect should point to.",
"selflinks"		=> "Pages with Self Links",
"selflinkstext"		=> "The following pages contain a link to themselves, which they should not.",
"missinglanguagelinks"  => "Missing Language Links",
"missinglanguagelinksbutton"    => "Find missing language links for",
"missinglanguagelinkstext"      => "These articles do <i>not</i> link to their counterpart in $1. Redirects and subpages are <i>not</i> shown.",


# Miscellaneous special pages
#
"orphans"		=> "외톨이 문서",
"lonelypages"	=> "외톨이 문서",
"unusedimages"	=> "안 쓰이는 그림",
"popularpages"	=> "인기있는 문서",
"nviews"		=> "$1 번 읽음",
"wantedpages"	=> "필요한 문서",
"nlinks"		=> "$1개의 고리",
"allpages"		=> "모든 문서",
"randompage"	=> "아무거나",
"shortpages"	=> "짧은 문서",
"longpages"		=> "긴 문서",
"listusers"		=> "사용자들",
"specialpages"	=> "특수 문서",
"spheading"		=> "특수 문서",
"sysopspheading" => "Special pages for sysop use",
"developerspheading" => "Special pages for developer use",
"protectpage"	=> "보호된 문서",
"recentchangeslinked" => "고리 보기",
"rclsub"		=> "(to pages linked from \"$1\")",
"debug"			=> "Debug",
"newpages"		=> "새 문서",
"movethispage"	=> "문서 옮기기",
"unusedimagestext" => "<p>다음중 어떤 그림은, 다른 언어의 위키백과등 다른 누리터에서 URL바로걸기로 사용하고 있을 수도 있습니다.",
"booksources"	=> "Book sources",
"booksourcetext" => "Below is a list of links to other sites that
sell new and used books, and may also have further information
about books you are looking for.
Wikipedia is not affiliated with any of these businesses, and
this list should not be construed as an endorsement.",

# Email this user
#
"mailnologin"	=> "누리편지주소 없음",
"mailnologintext" => "<a href=\"" .
  wfLocalUrl( "Special:Preferences" ) . "\">자기 설정</a>에서 옳바른 누리편지주소를
넣고 위키백과에 <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
and have a valid e-mail address in your 
to send e-mail to other users.",
"emailuser"		=> "사용자에게 편지보내기",
"emailpage"		=> "E-mail user",
"emailpagetext"	=> "If this user has entered a valid e-mail address in
is user preferences, the form below will send a single message.
The e-mail address you entered in your user preferences will appear
as the \"From\" address of the mail, so the recipient will be able
to reply.",
"noemailtitle"	=> "No e-mail address",
"noemailtext"	=> "This user has not specified a valid e-mail address,
or has chosen not to receive e-mail from other users.",
"emailfrom"		=> "From",
"emailto"		=> "To",
"emailsubject"	=> "Subject",
"emailmessage"	=> "Message",
"emailsend"		=> "Send",
"emailsent"		=> "E-mail sent",
"emailsenttext" => "Your e-mail message has been sent.",

# Watchlist
#
"watchlist"		=> "Watch list",
"watchlistsub"	=> "(for user \"$1\")",
"nowatchlist"	=> "You have no items on your watchlist.",
"watchnologin"	=> "Not logged in",
"watchnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to modify your watchlist.",
"addedwatch"	=> "Added to watchlist",
"addedwatchtext" => "The page \"$1\" has been added to your <a href=\"" .
  wfLocalUrl( "Special:Watchlist" ) . "\">watchlist</a>.
Future changes to this page and its associated Talk page will be listed there,
and the page will appear <b>bolded</b> in the <a href=\"" .
  wfLocalUrl( "Special:Recentchanges" ) . "\">list of recent changes</a> to
make it easier to pick out.</p>

<p>If you want to remove the page from your watchlist later, click \"Stop watching\" in the sidebar.",
"removedwatch"	=> "Removed from watchlist",
"removedwatchtext" => "The page \"$1\" has been removed from your watchlist.",
"watchthispage"	=> "Watch this page",
"unwatchthispage" => "Stop watching",
"notanarticle"	=> "Not an article",

# Delete/protect/revert
#
"deletepage"	=> "Delete page",
"confirm"		=> "Confirm",
"confirmdelete" => "Confirm delete",
"deletesub"		=> "(Deleting \"$1\")",
"confirmdeletetext" => "You are about to permanently delete a page
or image along with all of its history from the database.
Please confirm that you intend to do this, that you understand the
consequences, and that you are doing this in accordance with
[[Wikipedia:Policy]].",
"confirmcheck"	=> "Yes, I really want to delete this.",
"actioncomplete" => "Action complete",
"deletedtext"	=> "\"$1\" has been deleted.
See $2 for a record of recent deletions.",
"deletedarticle" => "deleted \"$1\"",
"dellogpage"	=> "Deletion_log",
"dellogpagetext" => "Below is a list of the most recent deletions.
All times shown are server time (UTC).
<ul>
</ul>
",
"deletionlog"	=> "deletion log",
"reverted"		=> "Reverted to earlier revision",
"deletecomment"	=> "Reason for deletion",
"imagereverted" => "Revert to earlier version was successful.",
"undelete" => "Restore deleted page",
"undeletepage" => "View and restore deleted pages",
"undeletepagetext" => "The following pages have been deleted but are still in the archive and
can be restored. The archive may be periodically cleaned out.",
"undeletearticle" => "Restore deleted article",
"undeleterevisions" => "$1 revisions archived",
"undeletehistory" => "If you restore the page, all revisions will be restored to the history.
If a new page with the same name has been created since the deletion, the restored
revisions will appear in the prior history, and the current revision of the live page
will not be automatically replaced.",
"undeleterevision" => "Deleted revision as of $1",
"undeletebtn" => "Restore!",
"undeletedarticle" => "restored \"$1\"",
"undeletedtext"   => "The article [[$1]] has been successfully restored.
See [[Wikipedia:Deletion_log]] for a record of recent deletions and restorations.",

# Contributions
#
"contributions"	=> "User contributions",
"contribsub"	=> "For $1",
"nocontribs"	=> "No changes were found matching these criteria.",
"ucnote"		=> "Below are this user's last <b>$1</b> changes in the last <b>$2</b> days.",
"uclinks"		=> "View the last $1 changes; view the last $2 days.",

# What links here
#
"whatlinkshere"	=> "여기 걸린 문서",
"notargettitle" => "No target",
"notargettext"	=> "You have not specified a target page or user
to perform this function on.",
"linklistsub"	=> "(List of links)",
"linkshere"		=> "The following pages link to here:",
"nolinkshere"	=> "No pages link to here.",
"isredirect"	=> "redirect page",

# Block/unblock IP
#
"blockip"		=> "Block IP address",
"blockiptext"	=> "Use the form below to block write access
from a specific IP address.
This should be done only only to prevent valndalism, and in
accordance with [[Wikipedia:Policy|Wikipedia policy]].
Fill in a specific reason below (for example, citing particular
pages that were vandalized).",
"ipaddress"		=> "IP Address",
"ipbreason"		=> "Reason",
"ipbsubmit"		=> "Block this address",
"badipaddress"	=> "The IP address is badly formed.",
"noblockreason" => "You must supply a reason for the block.",
"blockipsuccesssub" => "Block succeeded",
"blockipsuccesstext" => "The IP address \"$1\" has been blocked.
<br>See [[Special:Ipblocklist|IP block list]] to review blocks.",
"unblockip"		=> "Unblock IP address",
"unblockiptext"	=> "Use the form below to restore write access
to a previously blocked IP address.",
"ipusubmit"		=> "Unblock this address",
"ipusuccess"	=> "IP address \"$1\" unblocked",
"ipblocklist"	=> "List of blocked IP addresses",
"blocklistline"	=> "$1, $2 blocked $3",
"blocklink"		=> "block",
"unblocklink"	=> "unblock",
"contribslink"	=> "contribs",

# Developer tools
#
"lockdb"		=> "Lock database",
"unlockdb"		=> "Unlock database",
"lockdbtext"	=> "Locking the database will suspend the ability of all
users to edit pages, change their preferences, edit their watchlists, and
other things requiring changes in the database.
Please confirm that this is what you intend to do, and that you will
unlock the database when your maintenance is done.",
"unlockdbtext"	=> "Unlocking the database will restore the ability of all
users to edit pages, change their preferences, edit their watchlists, and
other things requiring changes in the database.
Please confirm that this is what you intend to do.",
"lockconfirm"	=> "Yes, I really want to lock the database.",
"unlockconfirm"	=> "Yes, I really want to unlock the database.",
"lockbtn"		=> "Lock database",
"unlockbtn"		=> "Unlock database",
"locknoconfirm" => "You did not check the confirmation box.",
"lockdbsuccesssub" => "Database lock succeeded",
"unlockdbsuccesssub" => "Database lock removed",
"lockdbsuccesstext" => "The Wikipedia database has been locked.
<br>Remember to remove the lock after your maintenance is complete.",
"unlockdbsuccesstext" => "The Wikipedia database has been unlocked.",

# SQL query
#
"asksql"		=> "SQL query",
"asksqltext"	=> "Use the form below to make a direct query of the
Wikipedia database.
Use single quotes ('like this') to delimit string literals.
This can often add considerable load to the server, so please use
this function sparingly.",
"sqlquery"		=> "Enter query",
"querybtn"		=> "Submit query",
"selectonly"	=> "Queries other than \"SELECT\" are restricted to
Wikipedia developers.",
"querysuccessful" => "Query successful",

# Move page
#
"movepage"		=> "Move page",
"movepagetext"	=> "Using the form below will rename a page, moving all
of its history to the new name.
The old title will become a redirect page to the new title.
Links to the old page title will not be changed, and the talk
page, if any, will not be moved.
<b>WARNING!</b>
This can be a drastic and unexpected change for a popular page;
please be sure you understand the consequences of this before
proceeding.",
"movearticle"	=> "Move page",
"movenologin"	=> "Not logged in",
"movenologintext" => "You must be a registered user and <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to move a page.",
"newtitle"		=> "To new title",
"movepagebtn"	=> "Move page",
"pagemovedsub"	=> "Move succeeded",
"pagemovedtext" => "Page \"[[$1]]\" moved to \"[[$2]]\".",
"articleexists" => "A page of that name already exists, or the
name you have chosen is not valid.
Please choose another name.",
"movedto"		=> "moved to",
"movetalk"		=> "Move \"talk\" page as well, if applicable.",
"talkpagemoved" => "The corresponding talk page was also moved.",
"talkpagenotmoved" => "The corresponding talk page was <strong>not</strong> moved.",

);

class LanguageKo extends Language {

	function getDefaultUserOptions () {
		global $wgDefaultUserOptionsKo ;
		return $wgDefaultUserOptionsKo ;
		}

	function getBookstoreList () {
		global $wgBookstoreListKo ;
		return $wgBookstoreListKo ;
	}

	function getNamespaces() {
		global $wgNamespaceNamesKo;
		return $wgNamespaceNamesKo;
	}

	function getNsText( $index ) {
		global $wgNamespaceNamesKo;
		return $wgNamespaceNamesKo[$index];
	}

	function getNsIndex( $text ) {
		global $wgNamespaceNamesKo;

		foreach ( $wgNamespaceNamesKo as $i => $n ) {
			if ( 0 == strcasecmp( $n, $text ) ) { return $i; }
		}
		return false;
	}

	function specialPage( $name ) {
		return $this->getNsText( Namespace::getSpecial() ) . ":" . $name;
	}

	function getQuickbarSettings() {
		global $wgQuickbarSettingsKo;
		return $wgQuickbarSettingsKo;
	}

	function getSkinNames() {
		global $wgSkinNamesKo;
		return $wgSkinNamesKo;
	}

	function getUserToggles() {
		global $wgUserTogglesKo;
		return $wgUserTogglesKo;
	}

	function getLanguageNames() {
		global $wgLanguageNamesKo;
		return $wgLanguageNamesKo;
	}

	function getLanguageName( $code ) {
		global $wgLanguageNamesKo;
		if ( ! array_key_exists( $code, $wgLanguageNamesKo ) ) {
			return "";
		}
		return $wgLanguageNamesKo[$code];
	}

	function getMonthName( $key )
	{
		global $wgMonthNamesKo;
		return $wgMonthNamesKo[$key-1];
	}

	function getMonthAbbreviation( $key )
	{
		global $wgMonthAbbreviationsKo;
		return $wgMonthAbbreviationsKo[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekdayNamesKo;
		return $wgWeekdayNamesKo[$key-1];
	}

	function userAdjust( $ts )
	{
		global $wgUser;
		
		# Default to Korean time?

		$diff = $wgUser->getOption( "timecorrection" );
		if ( ! $diff ) { $diff = 0; }
		if ( 0 == $diff ) { return $ts; }

		$t = mktime( ( (int)substr( $ts, 8, 2) ) + $diff,
		  (int)substr( $ts, 10, 2 ), (int)substr( $ts, 12, 2 ),
		  (int)substr( $ts, 4, 2 ), (int)substr( $ts, 6, 2 ),
		  (int)substr( $ts, 0, 4 ) );
		return date( "YmdHis", $t );
	}
 
	function date( $ts, $adj = false )
	{
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$d = $this->getMonthAbbreviation( substr( $ts, 4, 2 ) ) .
		  " " . (0 + substr( $ts, 6, 2 )) . ", " .
		  substr( $ts, 0, 4 );
		return $d;
	}

	function time( $ts, $adj = false )
	{
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$t = substr( $ts, 8, 2 ) . ":" . substr( $ts, 10, 2 );
		return $t;
	}

	function timeanddate( $ts, $adj = false )
	{
		return $this->time( $ts, $adj ) . " " . $this->date( $ts, $adj );
	}

	function rfc1123( $ts )
	{
		return date( "D, d M Y H:i:s T", $ts );
	}

	function getValidSpecialPages()
	{
		global $wgValidSpecialPagesKo;
		return $wgValidSpecialPagesKo;
	}

	function getSysopSpecialPages()
	{
		global $wgSysopSpecialPagesKo;
		return $wgSysopSpecialPagesKo;
	}

	function getDeveloperSpecialPages()
	{
		global $wgDeveloperSpecialPagesKo;
		return $wgDeveloperSpecialPagesKo;
	}

	function getMessage( $key )
	{
		global $wgAllMessagesKo, $wgAllMessagesEn;
		$m = $wgAllMessagesKo[$key];

		if ( "" == $m ) { return $wgAllMessagesEn[$key]; }
		else return $m;
	}
	
	function iconv( $in, $out, $string ) {
		# For most languages, this is a wrapper for iconv
		return iconv( $in, $out, $string );
	}
	
	function ucfirst( $string ) {
        #return ucfirst( $string );
		# For most languages, this is a wrapper for ucfirst()
		# But that doesn't work right in a UTF-8 locale
		include("utf8Case.php");
        return preg_replace (
        	"/^([\\x00-\\x7f]|[\\xc0-\\xff][\\x80-\\xbf]*)/e",
        	"strtr ( \"\$1\" , \$wikiUpperChars )",
        	$string );
	}
	
	# Inherit default checkTitleEncoding()

}

?>