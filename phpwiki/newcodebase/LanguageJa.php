<?php

include_once( "utf8Case.php" );

# The names of the namespaces can be set here, but the numbers
# are magical, so don't change or move them!  The Namespace class
# encapsulates some of the magic-ness.
# 名前空間の名前はここで設定できますが、番号は特別なので、
# 変更したり移動したりしないでね! 名前空間クラスは特殊性を幾らか
# 隠匿します。
#
/* private */ $wgNamespaceNamesJa = array(
	-1	=> "特別" /* "Special" */,
	0	=> "",
	1	=> "会話" /* "Talk" */,
	2	=> "利用者" /* "User" */,
	3	=> "利用者‐会話" /* "User_talk" */,
	4	=> "Wikipedia" /* "Wikipedia" */,
	5	=> "Wikipedia‐会話" /* "Wikipedia_talk" */,
	6	=> "画像" /* "Image" */,
	7	=> "画像‐会話" /* "Image_talk" */
);

/* private */ $wgQuickbarSettingsJa = array(
	"None", "Fixed left", "Fixed right", "Floating left"
);

/* private */ $wgSkinNamesJa = array(
	"標準", "ノスタルジア", "ケルンブルー"
);

/* private */ $wgUserTogglesJa = array(
	"hover"		=> "Show hoverbox over wiki links",
	"underline" => "Underline links",
	"highlightbroken" => "Highlight links to empty topics",
	"justify"	=> "Justify paragraphs",
	"hideminor" => "Hide minor edits in recent changes",
	"numberheadings" => "Auto-number headings",
	"rememberpassword" => "Remember password across sessions",
	"editwidth" => "Edit box has full width",
	"editondblclick" => "Edit pages on double click (JavaScript)",
	"watchdefault" => "Watch new and modified articles",
	"minordefault" => "Mark all edits minor by default"
);

/* Native/local names should be available where known for consistency
   and so that people can recognize them when jumping languages */
/* private */ $wgLanguageNamesJa = array(
	"ab"	=> "アブハズ語",
	"aa"	=> "アファル語",
	"af"	=> "アフリカーンス語",
	"sq"	=> "アルバニア語",
	"am"	=> "アムハラ語",
	"ar"	=> "アラビア語",
	"hy"	=> "アルメニア語",
	"as"	=> "アッサム語",
	"ay"	=> "アイマラ語",
	"az"	=> "アゼルバイジャン語",
	"ba"	=> "バシキール語",
	"eu"	=> "Euskara (バスク語)",
	"be"	=> "&#1041;&#1077;&#1083;&#1072;&#1088;&#1091;&#1089;&#1082;&#1080; (白ロシア語)",
	"bn"	=> "ベンガル語",
	"dz"	=> "ブータン語",
	"bh"	=> "ビハリ語",
	"bi"	=> "ビスラマ語",
	"my"	=> "ビルマ語",
	"km"	=> "カンボジア語",
	"ca"	=> "Catal&#224; (カタロニア語)",
	"zh"	=> "中国語",      # 漢語
	"co"	=> "コルシカ方言",
	"hr"	=> "Hrvatski (クロアチア語)",
	"cs"	=> "&#268;esk&#225; (チェコ語)",
	"da"	=> "Dansk (デンマーク語)", # Note two different subdomains. 
	"dk"	=> "Dansk (デンマーク語)", # 'da' is correct for the language.
	"nl"	=> "Nederlands (オランダ語)",
	"en"	=> "English (英語)",
	"simple" => "Simple English (簡単な英語)",
	"eo"	=> "Esperanto (エスペラント)",
	"et"	=> "Eesti (エストニア語)",
	"fo"	=> "フェロー語",
	"fj"	=> "フィジー語",
	"fi"	=> "フィンランド語",
	"fr"	=> "Fran&#231;ais (フランス語)",
	"fy"	=> "Frysk (フリジア語)",
	"gl"	=> "ガリシア語",
	"ka"	=> "&#4325;&#4304;&#4320;&#4311;&#4309;&#4308;&#4314;&#4312; (グルジア語)",
	"de"	=> "Deutsch (ドイツ語)",
	"el"	=> "&#917;&#955;&#955;&#951;&#957;&#953;&#954;&#940; (ギリシア語)",
	"kl"	=> "グリーンランド語",
	"gn"	=> "グアラニー語",
	"gu"	=> "&#2711;&#2753;&#2716;&#2736;&#2750;&#2724;&#2752; (グジャラート語)",
	"ha"	=> "ハウサ語",
	"he"	=> "&#1506;&#1489;&#1512;&#1497;&#1514; (ヘブライ語)",
	"hi"	=> "&#2361;&#2367;&#2344;&#2381;&#2342;&#2368; (ヒンディー語)",
	"hu"	=> "Magyar (ハンガリー語)",
	"is"	=> "アイスランド語",
	"id"	=> "インドネシア語",
	"ia"	=> "Interlingua (インターリングア)",
	"iu"	=> "イヌクティトット語",
	"ik"	=> "イヌピアック語",
	"ga"	=> "アイルランド語",
	"it"	=> "Italiano (イタリア語)",
	#"ja"	=> "日本語", # Local links
	"jv"	=> "ジャワ語",
	"kn"	=> "カンナダ語",
	"ks"	=> "カシミール語",
	"kk"	=> "カザフ語",
	"rw"	=> "ルワンダ語",
	"ky"	=> "キルギス語",
	"rn"	=> "キルンジ語",
	"ko"	=> "&#54620;&#44397;&#50612; (韓国語)",
	"lo"	=> "ラオ語",
	"la"	=> "ラテン語",
	"lv"	=> "ラトビア語",
	"ln"	=> "リンガラ語",
	"lt"	=> "Lietuvi&#371; (リトアニア語)",
	"mk"	=> "マケドニア語",
	"mg"	=> "マダガスカル語",
	"ms"	=> "Bahasa Melayu (マレー語)",
	"ml"	=> "マラヤーラム語",
	"mi"	=> "マオリ語",
	"mr"	=> "マラーティー語",
	"mo"	=> "モルドバ語",
	"mn"	=> "モンゴル語",
	"na"	=> "ナウル語",
	"ne"	=> "&#2344;&#2375;&#2346;&#2366;&#2354;&#2368; (ネパール語)",
	"no"	=> "Norsk (ノルウェー語)",
	"oc"	=> "オクシタン語", # 南仏の言語
	"or"	=> "オリヤー語",
	"om"	=> "オロモ語",
	"ps"	=> "パシュト語",
	"fa"	=> "&#8238;&#1601;&#1585;&#1587;&#1609;&#8236; (ペルシャ語)",
	"pl"	=> "Polski (ポーランド語)",
	"pt"	=> "Portugu&#234;s (ポルトガル語)",
	"pa"	=> "パンジャブ語",
	"qu"	=> "ケチュア語",
	"rm"	=> "レートローマン語",
	"ro"	=> "Rom&#226;n&#259; (ルーマニア語)",
	"ru"	=> "&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081; (ロシア語)",
	"sm"	=> "サモア語",
	"sg"	=> "サングロ語",
	"sa"	=> "&#2360;&#2306;&#2360;&#2381;&#2325;&#2371;&#2340; (サンスクリット)",
	"sr"	=> "セルビア語",
	"sh"	=> "セルボ・クロアチア語",
	"st"	=> "セソト語",
	"tn"	=> "セツワナ語",
	"sn"	=> "ショナ語",
	"sd"	=> "シンディ語",
	"si"	=> "シンハラ語",
	"ss"	=> "シスワティ語",
	"sk"	=> "スロバキア語",
	"sl"	=> "Slovensko (スロベニア語)",
	"so"	=> "ソマリア語",
	"es"	=> "Espa&#241;ol (スペイン語)",
	"su"	=> "スーダン語",
	"sw"	=> "スワヒリ語",
	"sv"	=> "Svenska (スウェーデン語)",
	"tl"	=> "タガログ語",
	"tg"	=> "タジク語",
	"ta"	=> "タミル語",
	"tt"	=> "タタール語",
	"te"	=> "テルグ語",
	"th"	=> "タイ語",
	"bo"	=> "チベット語",
	"ti"	=> "ティグリニャ語", # エチオピア北部
	"to"	=> "トンガ語",
	"ts"	=> "ツワナ語",
	"tr"	=> "T&#252;rk&#231;e (トルコ語)",
	"tk"	=> "トゥルクメン語",
	"tw"	=> "トウィ語",
	"ug"	=> "ウイグル語",
	"uk"	=> "&#1059;&#1082;&#1088;&#1072;&#1111;&#1085;&#1089;&#1100;&#1082;&#1072; (ウクライナ語)",
	"ur"	=> "ウルドゥ語",
	"uz"	=> "ウズベク語",
	"vi"	=> "ベトナム語",
	"vo"	=> "Volap&#252;k (ボラピュク語)", # 人工語
	"cy"	=> "ウェールズ語",
	"wo"	=> "ウォロフ語",
	"xh"	=> "ホサ語",
	"yi"	=> "イディッシュ語",
	"yo"	=> "ヨルバ語",
	"za"	=> "壮語",  # チワン語族(中国壮族のことばの総称) 
	"zu"	=> "ズールー語"
);

/* private */ $wgWeekdayNamesJa = array(
	"日曜日", "月曜日", "火曜日", "水曜日", "木曜日",
	"金曜日", "土曜日"
);

/* private */ $wgMonthNamesJa = array( # ???
	"一月", "二月", "三月", "四月", "五月", "六月",
	"七月", "八月", "九月", "十月", "十一月",
	"十二月"
);

/* private */ $wgMonthAbbreviationsJa = array(
	"1月", "2月", "3月", "4月", "5月", "6月",
	"7月", "8月", "9月", "10月", "11月", "12月"
);

# All special pages have to be listed here: a description of ""
# will make them not show up on the "Special Pages" page, which
# is the right thing for some of them (such as the "targeted" ones).

 
/* private */ $wgValidSpecialPagesJa = array(
	"Userlogin"		=> "",
	"Userlogout"	=> "",
	"Preferences"	=> "ユーザ設定を保存",
	"Watchlist"		=> "ウォッチリスト",
	"Recentchanges" => "最近更新したページ",
	"Upload"		=> "画像をアップロードする",
	"Imagelist"		=> "画像リスト",
	"Listusers"		=> "登録済みユーザ",
	"Statistics"	=> "サイトの統計",
	"Randompage"	=> "ランダム記事",

	"Lonelypages"	=> "孤立したページ",
	"Unusedimages"	=> "孤立した画像",
	"Popularpages"	=> "人気の記事",
	"Wantedpages"	=> "一番注目されている記事",
	"Shortpages"	=> "短い記事",
	"Longpages"		=> "長い記事",
	"Newpages"		=> "新しく登場した記事",
	"Allpages"		=> "タイトル別全ページ",

	"Ipblocklist"	=> "ブロックされたIPアドレス",
	"Maintenance" => "管理ページ",
	"Specialpages"  => "",
	"Contributions" => "",
	"Emailuser"		=> "",
	"Whatlinkshere" => "",
	"Recentchangeslinked" => "",
	"Movepage"		=> "",
	"Booksources"	=> "外部の参考文献"
);

/* private */ $wgSysopSpecialPagesJa = array(
	"Blockip"		=> "IPアドレスをブロック",
	"Asksql"		=> "データベースに問い合わせ",
	"Undelete"		=> "消去されたページを閲覧し、復帰させる"
);

/* private */ $wgDeveloperSpecialPagesJa = array(
	"Lockdb"		=> "データベースを読み出し専用にする",
	"Unlockdb"		=> "データベースを書き込み可能にする",
	"Debug"			=> "デバッグ情報"
);

/* private */ $wgAllMessagesJa = array(

# Bits of text used by many pages:
#
"mainpage"		=> "メインページ",
"about"			=> "About",
"aboutwikipedia" => "Wikipediaについて",
"aboutpage"		=> "Wikipedia:About",
"help"			=> "ヘルプ",
"helppage"		=> "Wikipedia:Help",
"wikititlesuffix" => "Wikipedia",
"bugreports"	=> "バグの報告",
"bugreportspage" => "Wikipedia:Bug_reports",
"faq"			=> "FAQ",
"faqpage"		=> "Wikipedia:FAQ",
"edithelp"		=> "ヘルプを編集",
"edithelppage"	=> "Wikipedia:How_does_one_edit_a_page",
"cancel"		=> "キャンセル",
"qbfind"		=> "検索",
"qbbrowse"		=> "閲覧",
"qbedit"		=> "編集",
"qbpageoptions" => "ページ・オプション",
"qbpageinfo"	=> "ページ情報",
"qbmyoptions"	=> "オプション",
"mypage"		=> "マイ・ページ",
"mytalk"		=> "マイ・トーク",
"currentevents" => "現在のイベント",
"errorpagetitle" => "エラー",
"returnto"		=> "$1 に戻る。",
"fromwikipedia"	=> "出典：フリー百科事典『Wikipedia（ウィキペディア）』",
"whatlinkshere"	=> "ここにリンクしているページ",
"help"			=> "ヘルプ",
"search"		=> "検索",
"history"		=> "履歴",
"printableversion" => "印刷用バージョン",
"editthispage"	=> "このページを編集",
"deletethispage" => "このページを削除",
"protectthispage" => "このページを保護",
"unprotectthispage" => "ページ保護解除",
"talkpage"		=> "トーク・ページ",
"subjectpage"	=> "サブジェクト・ページ",
"otherlanguages" => "他の言語",
"redirectedfrom" => "($1 から転送)",
"lastmodified"	=> "最終更新 $1。",
"viewcount"		=> "このページは $1 回アクセスされました。",
"printsubtitle" => "(From http://www.wikipedia.org)",
"protectedpage" => "保護されたページ",
"administrators" => "Wikipedia:Administrators",
"sysoptitle"	=> "シスオペによるアクセスが必要",
"sysoptext"		=> "あなたの要求した処理は \"sysop\" のみが実行できます。
 $1を参照してください。",
"developertitle" => "開発者によるアクセスが必要",
"developertext"	=> "あなたの要求した処理を実行できるのは、 \"developer\" のみです。 $1を参照してください。",
"nbytes"		=> "$1 bytes",
"go"			=> "Go",
"ok"			=> "OK",
"sitetitle"		=> "Wikipedia",
"sitesubtitle"	=> "フリー百科事典",
"retrievedfrom" => "Retrieved from \"$1\"",

# Main script and global functions
#
"nosuchaction"	=> "そのようなアクションはありません",
"nosuchactiontext" => "URLで指定されたアクションはWikipediaで認識できません。",
"nosuchspecialpage" => "そのような特別ページはありません。",
"nospecialpagetext" => "その特別ページのリクエストはWikipediaには理解できません。",

# General errors
#
"error"			=> "エラー",
"databaseerror" => "データベース・エラー",
"dberrortext"	=> "データベース検索の文法エラー。
検索クエリーが間違っているか($5 を参照),
あるいはソフトウエアのバグかもしれません。
最後に実行を試みたクエリー: 
<blockquote><tt>$1</tt></blockquote>
from within function \"<tt>$2</tt>\".
MySQL returned error \"<tt>$3: $4</tt>\".",
"noconnect"		=> "$1 のデータベースに接続できません。",
"nodb"			=> "$1 のデータベースを選択できません。",
"readonly"		=> "データベースはロックされています",
"enterlockreason" => "ロックする理由を入力して下さい。ロックが解除されるのがいつになるかの見積もりについても述べて下さい。",
"readonlytext"	=> "ウィキペディアデータベースは現在、新しい記事の追加や修正を受け付けない「ロック」状態になっています。これはおそらくは定期的なメンテナンスのためで、メンテナンス終了後は正常な状態に復帰します。
データベースをロックした管理者は次のような説明をしています：
<p>$1
<p>The Wikipedia database is currently locked to new
entries and other modifications, probably for routine database maintenance,
after which it will be back to normal.
The administrator who locked it offered this explanation:
<p>$1",
"missingarticle" => "データベースは、\"$1\"というタイトルのページの、存在するはずの文章を見つけることができませんでした。
これはデータベースのエラーではなく、ソフトウェアのバグだろうと思われます。
URLと共に管理者に報告して下さるようにお願いします。
The database did not find the text of a page
that it should have found, named \"$1\".
This is not a database error, but likely a bug in the software.
Please report this to an administrator, making note of the URL.",
"internalerror" => "内部処理エラー Internal error",
"filecopyerror" => "ファイルを\"$1\"から\"$2\"へコピーできませんでした。Could not copy file \"$1\" to \"$2\".",
"filerenameerror" => "ファイル名を\"$1\"から\"$2\"へ変更できませんでした。Could not rename file \"$1\" to \"$2\".",
"filedeleteerror" => "ファイル\"$1\"を削除できませんでした。Could not delete file \"$1\".",
"filenotfound"	=> "ファイルを\"$1\"は見つかりませんでした。Could not find file \"$1\".",
"unexpected"	=> "エラー：\"$1\" と \"$2\" が同じです。Unexpected value: \"$1\"=\"$2\".",
"formerror"		=> "エラー：フォームが提出できませんでした。 Error: could not submit form",	
"badarticleerror" => "この動作はこのページではとることができません。 This action cannot be performed on this page.",
"cannotdelete"	=> "指定されたページ、またはイメージを削除できませんでした。 Could not delete the page or image specified.",

# Login and logout pages
#
"logouttitle"	=> "ユーザ ログアウト",
"logouttext"	=> "
ログアウトしました。
ウィキペディアを匿名で使い続うことができます。
あるいはログインして元の、あるいは別のユーザーとして使うこともできます。
<P>You are now logged out.
You can continue to use Wikipedia anonymously, or you can log in
again as the same or as a different user.\n",

"welcomecreation" => "<h2>$1 さん、ようこそ!</h2><p>あなたのアカウントができました。
Wikipediaの個人オプションを修正することをお忘れなく。",

"loginpagetitle" => "ユーザ・ログイン",
"yourname"		=> "あなたのユーザ名",
"yourpassword"	=> "あなたのパスワード",
"yourpasswordagain" => "パスワード再入力",
"newusersonly"	=> " (新規ユーザのみ)",
"remembermypassword" => "セッションをまたがってパスワードを保持する。",
"loginproblem"	=> "<b>ログインでエラーが発生しました。</b><br>再度実行してください。",
"alreadyloggedin" => "<font color=red><b>ユーザ $1 は、すでにログイン済みです。</b></font><br>\n",

"areyounew"		=> "もし Wikipedia を使うのが初めてで、ユーザアカウントを登録したい方は、ユーザ名、パスワード、さらにパスワードを再入力してください。
電子メールアドレスの登録は、必須ではありません。しかし、もしパスワードを忘れたときには、電子メールでパスワードを取り寄せることができるので便利です。<br>\n",


"login"			=> "ログイン",
"userlogin"		=> "ログイン",
"logout"		=> "ログアウト",
"userlogout"	=> "ログアウト",
"createaccount"	=> "新規アカウント作成",
"badretype"		=> "両方のパスワードが一致しません。",
"userexists"	=> "そのユーザ名はすでに使われています。ほかの名前をお選びください。",
"youremail"		=> "電子メール",
"yournick"		=> "ニックネーム (署名用)",
"emailforlost"	=> "パスワードを忘れたときには、あたらしいパスワードを電子メールで受け取ることが出来ます。",
"loginerror"	=> "ログイン・エラー",
"noname"		=> "ユーザ名を正しく指定していません。",
"loginsuccesstitle" => "ログイン成功",
"loginsuccess"	=> "あなたは現在 Wikipedia に \"$1\" としてログインしています。",
"nosuchuser"	=> " \"$1\" というユーザは見当たりません。
綴りが正しいか再度確認するか、下記のフォームを使ってアカウントを作成してください。",
"wrongpassword"	=> "パスワードが間違っています。再度入力してください。",
"mailmypassword" => "新しいパスワードを、メールで送る",
"passwordremindertitle" => "Password reminder from Wikipedia",
"passwordremindertext" => "だれかさん ($1 のIPアドレスの人)から、Wikipediaのログインパスワードの再発行の依頼がありました。
ユーザ \"$2\" のパスワードを、 \"$3\" に変更しました。
ログイン後、別のパスワードに変更しましょう。",
"noemail"		=> "ユーザ \"$1\" のメールアドレスは登録されていません。",
"passwordsent"	=> "あたらしいパスワードは \"$1\" さんの登録済みメールアドレスにお送りしました。メールを受け取ったら、再度ログインしてください。",

# Edit pages
#
"summary"		=> "サマリー",
"minoredit"		=> "これは細部の修正です。",
"savearticle"	=> "ページを保存",
"preview"		=> "プレビュー",
"showpreview"	=> "プレビューを実行",
"blockedtitle"	=> "ユーザはブロックされています。",
"blockedtext"	=> "あなたのユーザ名またはIPアドレスは $1 によってブロックされています。
その理由はつぎのとおりです。:<br>$2<p>詳細は管理者にお問い合わせください。",
"newarticle"	=> "(新規)",
"newarticletext" => "あたらしい記事を書き込んでください。",
"noarticletext" => "(このページには現在記事がありません。)",
"updated"		=> "(更新)",
"note"			=> "<strong>注釈:</strong> ",
"previewnote"	=> "これはプレビューです。まだ保存されていません！",
"previewconflict" => "このプレビューは、上の文章編集エリアの文章を保存した場合に
どう見えるようになるかを示すものです。
<p>This preview reflects the text in the upper
text editing area as it will appear if you choose to save.",
"editing"		=> "Editing $1",
"editconflict"	=> "編集競合: $1",
"explainconflict" => "あなたがこのページを編集し始めてから誰か他の人が
このページを変更してしまいました。
上の文章エリアは現在の最新の状態を反映しています。
あなたの加える変更の内容は下の文章エリアに示されています。
変更内容を、上の文章エリアの内容に組み込んで下さい。
<b>上の文章エリアの内容だけ<b>が、\"Save page\"をクリックした時に
保存されることになります。\n<p>",
"yourtext"		=> "あなたの文章",
"storedversion" => "保存されたバージョン",
"editingold"	=> "<strong>警告：あなたはこのページの古いバージョンを
編集しています。もしもこの文章を保存すると、このバージョン以降に追加された
全ての変更が無効になってしまいます。</strong>
<p><strong>WARNING: You are editing an out-of-date
revision of this page.
If you save it, any changes made since this revision will be lost.</strong>\n",
"yourdiff"		=> "違い",
"copyrightwarning" => "Wikipediaに投稿された文書は、すべて GNU Free Documentation License によって発行されたものとみなされますので、留意してください。
<p>(詳細は $1 を参照, また、参考までに非公式日本語訳はhttp://www.opensource.jp/fdl/fdl.ja.html　を参照)。
あなたの文章が他人によって自由に編集、配布されることを望まない場合は、投稿を控えて下さい。<br>
また、あなたの投稿する文章はあなた自身によって書かれたものであるか、パブリック・ドメインかそれに類するフリー・リソースからのコピーであることを約束して下さい。
<strong>著作権のある作品を許諾なしに投稿してはいけません！</strong>",


# History pages
#
"revhistory"	=> "改訂履歴",
"nohistory"		=> "このページには改訂履歴がありません。  There is no edit history for this page.",
"revnotfound"	=> "リクエストされたバージョンが見つかりません Revision not found",
"revnotfoundtext" => "リクエストされたこのページの旧バージョンは見つかりませんでした。
URLをもう一度確認して、このページにアクセスしてみて下さい。

The old revision of the page you asked for could not be found.
Please check the URL you used to access this page.\n",
"loadhist"		=> "Loading page history",
"currentrev"	=> "現在のバージョン Current revision",
"revisionasof"	=> "$1の時点でのバージョン Revision as of $1",
"cur"			=> "現行版 cur",
"next"			=> "直後版 next",
"last"			=> "直前版 last",
"orig"			=> "オリジナル版 orig",
"histlegend"	=> "凡例: (cur) = 現行版との違い,
(last) = 直前のバージョンとの違い, M = 細部の変更 minor edit",

# Diffs
#
"difference"	=> "(Difference between revisions)",
"loadingrev"	=> "loading revision for diff",
"lineno"		=> "Line $1:",
"editcurrent"	=> "Edit the current version of this page",

# 検索結果（Search results）
#
"searchresults" => "Search results",
"searchhelppage" => "Wikipedia:Searching",
"searchingwikipedia" => "Searching Wikipedia",
"searchresulttext" => "For more information about searching Wikipedia, see $1.",
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
"titlematches"	=> "Article title matches",
"notitlematches" => "No article title matches",
"textmatches"	=> "Article text matches",
"notextmatches"	=> "No article text matches",
"prevn"			=> "previous $1",
"nextn"			=> "next $1",
"viewprevnext"	=> "View ($1) ($2) ($3).",
"showingresults" => "Showing below <b>$1</b> results starting with #<b>$2</b>.",
"nonefound"		=> "<strong>Note</strong>: unsuccessful searches are
often caused by searching for common words like \"have\" and \"from\",
which are not indexed, or by specifying more than one search term (only pages
containing all of the search terms will appear in the result).",

# Preferences page
#
"preferences"	=> "Preferences",
"prefsnologin" => "Not logged in",
"prefsnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to set user preferences.",
"prefsreset"	=> "Preferences have been reset from storage.",
"qbsettings"	=> "Quickbar settings", 
"changepassword" => "Change password",
"skin"			=> "Skin",
"saveprefs"		=> "Save preferences",
"resetprefs"	=> "Reset preferences",
"oldpassword"	=> "Old password",
"newpassword"	=> "New password",
"retypenew"		=> "Retype new password",
"textboxsize"	=> "Textbox dimensions",
"rows"			=> "Rows",
"columns"		=> "Columns",
"searchresultshead" => "Search result settings",
"resultsperpage" => "Hits to show per page",
"contextlines"	=> "Lines to show per hit",
"contextchars"	=> "Characters of context per line",
"recentchangescount" => "Number of titles in recent changes",
"savedprefs"	=> "Your preferences have been saved.",
"timezonetext"  => "Enter number of hours your local time differs
from server time (UTC).",
"localtime"	=> "Local time",
"timezoneoffset" => "Offset",
"emailflag"		=> "Disable e-mail from other users",

# 最近更新したページ（Recent changes）
#
"recentchanges" => "最近更新したページ",
"recentchangestext" => "最近付け加えられた変更はこのページで確認することができます。
[[Wikipedia:Welcome,_newcomers|新規参加者の方、ようこそ]]！
以下のページも参照して下さい:
[[wikipedia:FAQ|ウィキペディア　よくある質問集]],
[[Wikipedia:Policies and guidelines|ウィキペディア　基本方針]]
(especially [[wikipedia:Naming conventions|記事名のつけ方]],
[[wikipedia:Neutral point of view|中立な観点について]]),
and [[wikipedia:Most common Wikipedia faux pas|ウィキペディアに起こりがちな間違い]].

ウィキペディアが成功するためには、あなたの投稿する内容が他人の著作権などによって束縛されていないことがとても重要です。[[wikipedia:Copyrights|著作権]]
法的責任問題は、プロジェクトに致命傷を与えることもある問題です。他人の著作物などを流用することは絶対に避けてください。また次のページも参照して下さい。[http://meta.wikipedia.org/wiki/Special:Recentchanges recent meta discussion]

Track the most recent changes to Wikipedia on this page.
[[Wikipedia:Welcome,_newcomers|Welcome, newcomers]]!
Please have a look at these pages: [[wikipedia:FAQ|Wikipedia FAQ]],
[[Wikipedia:Policies and guidelines|Wikipedia policy]]
(especially [[wikipedia:Naming conventions|naming conventions]],
[[wikipedia:Neutral point of view|neutral point of view]]),
and [[wikipedia:Most common Wikipedia faux pas|most common Wikipedia faux pas]].

If you want to see Wikipedia succeed, it's very important that you don't add
material restricted by others' [[wikipedia:Copyrights|copyrights]].
The legal liability could really hurt the project, so please don't do it.
See also the [http://meta.wikipedia.org/wiki/Special:Recentchanges recent meta discussion].",
"rcloaderr"		=> "Loading recent changes",
"rcnote"		=> "Below are the last <strong>$1</strong> changes in last <strong>$2</strong> days.",
# "rclinks"		=> "Show last $1 changes in last $2 hours / last $3 days",
"rclinks"		=> "Show last $1 changes in last $2 days.",
"rchide"		=> "in $4 form; $1 minor edits; $2 secondary namespaces; $3 multiple edits.",
"diff"			=> "diff",
"hist"			=> "hist",
"hide"			=> "hide",
"show"			=> "show",
"tableform"		=> "table",
"listform"		=> "list",
"nchanges"		=> "$1 changes",

# Upload
#
"upload"		=> "Upload",
"uploadbtn"		=> "Upload file",
"uploadlink"	=> "Upload images",
"reupload"		=> "Re-upload",
"reuploaddesc"	=> "Return to the upload form.",
"uploadnologin" => "Not logged in",
"uploadnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to upload files.",
"uploadfile"	=> "Upload file",
"uploaderror"	=> "Upload error",
"uploadtext"	=> "<strong>STOP!</strong> Before you upload here,
make sure to read and follow Wikipedia's <a href=\"" .
wfLocalUrlE( "Wikipedia:Image_use_policy" ) . "\">image use policy</a>.
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
"filename"		=> "ファイル名",
"filedesc"		=> "サマリー",
"affirmation"	=> "I affirm that the copyright holder of this file
agrees to license it under the terms of the $1.",
"copyrightpage" => "Wikipedia:Copyrights",

"copyrightpagename" => "Wikipedia copyright",
"uploadedfiles"	=> "Uploaded files",
"noaffirmation" => "You must affirm that your upload does not violate
any copyrights.",
"ignorewarning"	=> "Ignore warning and save file anyway.",
"minlength"		=> "Image names must be at least three letters.",
"badfilename"	=> "Image name has been changed to \"$1\".",
"badfiletype"	=> "\".$1\" is not a recommended image file format.",
"largefile"		=> "It is recommended that images not exceed 100k in size.",
"successfulupload" => "Successful upload",
"fileuploaded"	=> "File \"$1\" uploaded successfully.
Please follow this link: ($2) to the description page and fill
in information about the file, such as where it came from, when it was
created and by whom, and anything else you may know about it.",
"uploadwarning" => "Upload warning",
"savefile"		=> "Save file",
"uploadedimage" => "uploaded \"$1\"",

# Image list
#
"imagelist"		=> "Image list",
"imagelisttext"	=> "Below is a list of $1 images sorted $2.",
"getimagelist"	=> "fetching image list",
"ilshowmatch"	=> "Show all images with names matching",
"ilsubmit"		=> "Search",
"showlast"		=> "Show last $1 images sorted $2.",
"all"			=> "all",
"byname"		=> "by name",
"bydate"		=> "by date",
"bysize"		=> "by size",
"imgdelete"		=> "del",
"imgdesc"		=> "desc",
"imglegend"		=> "Legend: (desc) = show/edit image description.",
"imghistory"	=> "Image history",
"revertimg"		=> "rev",
"deleteimg"		=> "del",
"imghistlegend" => "Legend: (cur) = this is the current image, (del) = delete
this old version, (rev) = revert to this old version.
<br><i>Click on date to see image uploaded on that date</i>.",
"imagelinks"	=> "Image links",
"linkstoimage"	=> "The following pages link to this image:",
"nolinkstoimage" => "There are no pages that link to this image.",

# Statistics
#
"statistics"	=> "アクセス統計", # "Statistics",
"sitestats"		=> "サイト全体の統計", # "Site statistics",
"userstats"		=> "あなたの統計", # "User statistics",
"sitestatstext" => "There are <b>$1</b> total pages in the database.
This includes \"talk\" pages, pages about Wikipedia, minimal \"stub\"
pages, redirects, and others that probably don't qualify as articles.
Excluding those, there are <b>$2</b> pages that are probably legitimate
articles.<p>
There have been a total of <b>$3</b> page views, and <b>$4</b> page edits
since the software was upgraded (July 20, 2002).
That comes to <b>$5</b> average edits per page, and <b>$6</b> views per edit.",
"userstatstext" => "There are <b>$1</b> registered users.
<b>$2</b> of these are administrators (see $3).",

# Miscellaneous special pages
#
"orphans"		=> "孤立しているページ",
"lonelypages"	=> "孤立しているページ",
"unusedimages"	=> "使われていない画像",
"popularpages"	=> "人気のページ",
"nviews"		=> "$1 回表示",
"wantedpages"	=> "投稿が望まれているページ",
"nlinks"		=> "$1 個のリンク",
"allpages"		=> "全ページ",
"randompage"	=> "おまかせ表示",
"shortpages"	=> "短いページ",
"longpages"		=> "長いページ",
"listusers"		=> "User list",
"specialpages"	=> "特別ページ",
"spheading"		=> "特別ページ",
"sysopspheading" => "シスオペ用特別ページ",
"developerspheading" => "開発者用特別ページ",
"protectpage"	=> "Protect page",
"recentchangeslinked" => "リンクを見張る",
"rclsub"		=> "(to pages linked from \"$1\")",
"debug"			=> "デバッグ",
"newpages"		=> "新しいページ",
"movethispage"	=> "このページを移動する",
"unusedimagestext" => "<p>Please note that other web sites
such as the international Wikipedias may link to an image with
a direct URL, and so may still be listed here despite being
in active use.",
"booksources"	=> "Book sources",
"booksourcetext" => "Below is a list of links to other sites that
sell new and used books, and may also have further information
about books you are looking for.
Wikipedia is not affiliated with any of these businesses, and
this list should not be construed as an endorsement.",

# Email this user
#
"mailnologin"	=> "No send address",
"mailnologintext" => "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
and have a valid e-mail address in your <a href=\"" .
  wfLocalUrl( "Special:Preferences" ) . "\">preferences</a>
to send e-mail to other users.",
"emailuser"		=> "E-mail this user",
"emailpage"		=> "E-mail user",
"emailpagetext"	=> "If this user has entered a valid e-mail address in
is user preferences, the form below will send a single message.
The e-mail address you entered in your user preferences will appear
as the \"From\" address of the mail, so the recipient will be able
to reply.",
"noemailtitle"	=> "No e-mail address",
"noemailtext"	=> "This user has not specified a valid e-mail address,
or has chosen not to receive e-mail from other users.",
"emailfrom"		=> "自分のアドレス", # "From",
"emailto"		=> "あて先", # "To",
"emailsubject"	=> "題名", #"Subject",
"emailmessage"	=> "本文", #"Message",
"emailsend"		=> "メール送信", #"Send",
"emailsent"		=> "メールを送りました", #"E-mail sent",
"emailsenttext" => "Your e-mail message has been sent.",

# Watchlist
#
"watchlist"		=> "Watch list",
"watchlistsub"	=> "(for user \"$1\")",
"nowatchlist"	=> "あなたのウォッチリストは空です。", # "You have no items on your watchlist.",
"watchnologin"	=> "ログインしてません", # "Not logged in",
"watchnologintext"	=> "You must be <a href=\"" .
  wfLocalUrl( "Special:Userlogin" ) . "\">logged in</a>
to modify your watchlist.",
"addedwatch"	=> "ウォッチリストに加えました", # "Added to watchlist",
"addedwatchtext" => "The page \"$1\" has been added to your <a href=\"" .
  wfLocalUrl( "Special:Watchlist" ) . "\">watchlist</a>.
Future changes to this page and its associated Talk page will be listed there,
and the page will appear <b>bolded</b> in the <a href=\"" .
  wfLocalUrl( "Special:Recentchanges" ) . "\">list of recent changes</a> to
make it easier to pick out.</p>

<p>If you want to remove the page from your watchlist later, click \"Stop watching\" in the sidebar.",
"removedwatch"	=> "ウォッチリストから削除しました", # "Removed from watchlist",
"removedwatchtext" => "The page \"$1\" has been removed from your watchlist.",
"watchthispage"	=> "ウォッチリストに追加", # "Watch this page",
"unwatchthispage" => "ウォッチリストから削除", # "Stop watching",
"notanarticle"	=> "Not an article",

# Delete/protect/revert
#
"deletepage"	=> "Delete page",
"confirm"		=> "Confirm",
"confirmdelete" => "Confirm delete",
"deletesub"		=> "(Deleting \"$1\")",
"confirmdeletetext" => "指定されたページまたはイメージは、その更新履歴と共に
データベースから永久に削除されようとしています。
あなたが削除を望んでおり、それがもたらす帰結を理解しており、かつあなたの
しようとしていることが[[Wikipedia:Policy|ウィキペディアの基本方針]]に即したものであることを確認して下さい。

You are about to permanently delete a page
or image along with all of its history from the database.
Please confirm that you intend to do this, that you understand the
consequences, and that you are doing this in accordance with
[[Wikipedia:Policy]].",
"confirmcheck"	=> "はい。上記の通りです。　Yes, I really want to delete this.",
"actioncomplete" => "削除を完了しました。　Action complete",
"deletedtext"	=> "\"$1\" は削除されました。　\"$1\" has been deleted.
最近の削除に関しては$2 を参照して下さい。
See $2 for a record of recent deletions.",
"deletedarticle" => "\"$1\"　を削除しました",
"dellogpage"	=> "削除記録　Deletion_log",
"dellogpagetext" => "以下に示すのは最近の削除記録です。時間はサーバーの時間（UTC）によって記録されています。

Below is a list of the most recent deletions.
All times shown are server time (UTC).
<ul>
</ul>
",
"deletionlog"	=> "削除記録　deletion log",
"reverted"		=> "以前のバージョンへの差し戻し。　Reverted to earlier revision",
"deletecomment"	=> "削除の理由　Reason for deletion",
"imagereverted" => "以前のバージョンへの差し戻しに成功しました。　Revert to earlier version was successful.",

# Contributions
#
"contributions"	=> "ユーザーの投稿記録　User contributions",
"contribsub"	=> "ユーザー名：$1　For $1",
"nocontribs"	=> "ユーザーの投稿記録は見つかりませんでした。　

No changes were found matching these criteria.",
"ucnote"		=> "以下に示すのが過去<b>$2</b>日間における、最大<b>$1</b>件の投稿・編集です。

Below are this user's last <b>$1</b> changes in the last <b>$2</b> days.",
"uclinks"		=> "$1 件の投稿・編集を見る。; $2日間分の投稿・編集を見る。

View the last $1 changes; view the last $2 days.",

# What links here
#
"whatlinkshere"	=> "What links here",
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

class LanguageJa extends LanguageUtf8 {

	function getNamespaces() {
		global $wgNamespaceNamesJa;
		return $wgNamespaceNamesJa;
	}

	function getNsText( $index ) {
		global $wgNamespaceNamesJa;
		return $wgNamespaceNamesJa[$index];
	}

	function getNsIndex( $text ) {
		global $wgNamespaceNamesJa;

		foreach ( $wgNamespaceNamesJa as $i => $n ) {
			if ( 0 == strcasecmp( $n, $text ) ) { return $i; }
		}
		return false;
	}

	function getQuickbarSettings() {
		global $wgQuickbarSettingsJa;
		return $wgQuickbarSettingsJa;
	}

	function getSkinNames() {
		global $wgSkinNamesJa;
		return $wgSkinNamesJa;
	}

	function getUserToggles() {
		global $wgUserTogglesJa;
		return $wgUserTogglesJa;
	}

	function getLanguageName( $code ) {
		global $wgLanguageNamesJa;
		if ( ! array_key_exists( $code, $wgLanguageNamesJa ) ) {
			return "";
		}
		return $wgLanguageNamesJa[$code];
	}

	function getMonthName( $key )
	{
		global $wgMonthNamesJa;
		return $wgMonthNamesJa[$key-1];
	}

	function getMonthAbbreviation( $key )
	{
		global $wgMonthAbbreviationsJa;
		return $wgMonthAbbreviationsJa[$key-1];
	}

	function getWeekdayName( $key )
	{
		global $wgWeekdayNamesJa;
		return $wgWeekdayNamesJa[$key-1];
	}

	# Inherit default userAdjust()
	 
	function date( $ts, $adj = false )
	{
		if ( $adj ) { $ts = $this->userAdjust( $ts ); }

		$d = substr( $ts, 0, 4 ) . "年" .
		  $this->getMonthAbbreviation( substr( $ts, 4, 2 ) ) .
		  (0 + substr( $ts, 6, 2 )) . "日";
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

	# Inherit default rfc1123()

	function getValidSpecialPages()
	{
		global $wgValidSpecialPagesJa;
		return $wgValidSpecialPagesJa;
	}

	function getSysopSpecialPages()
	{
		global $wgSysopSpecialPagesJa;
		return $wgSysopSpecialPagesJa;
	}

	function getDeveloperSpecialPages()
	{
		global $wgDeveloperSpecialPagesJa;
		return $wgDeveloperSpecialPagesJa;
	}

	function getMessage( $key )
	{
		global $wgAllMessagesJa;
        if(array_key_exists($key, $wgAllMessagesJa))
			return $wgAllMessagesJa[$key];
		else
			return Language::getMessage($key);
	}

	function stripForSearch( $string ) {
		# MySQL fulltext index doesn't grok utf-8, so we
		# need to fold cases and convert to hex
		global $wikiLowerChars;
		$s = $string;

		# Strip known punctuation ?
		#$s = preg_replace( '/\xe3\x80[\x80-\xbf]/', '', $s ); # U3000-303f

		# Space strings of like hiragana/katakana/kanji
		$hiragana = '(?:\xe3(?:\x81[\x80-\xbf]|\x82[\x80-\x9f]))'; # U3040-309f
		$katakana = '(?:\xe3(?:\x82[\xa0-\xbf]|\x83[\x80-\xbf]))'; # U30a0-30ff
		$kanji = '(?:\xe3[\x88-\xbf][\x80-\xbf]'
			. '|[\xe4-\xe8][\x80-\xbf]{2}'
			. '|\xe9[\x80-\xa5][\x80-\xbf]'
			. '|\xe9\xa6[\x80-\x99])';
			# U3200-9999 = \xe3\x88\x80-\xe9\xa6\x99
		$s = preg_replace( "/({$hiragana}+|{$katakana}+|{$kanji}+)/", ' $1 ', $s );

		# Double-width roman characters: ff00-ff5f ~= 0020-007f
		$s = preg_replace( '/\xef\xbc([\x80-\xbf])/e', 'chr((ord("$1") & 0x3f) + 0x20)', $s );
		$s = preg_replace( '/\xef\xbd([\x80-\x99])/e', 'chr((ord("$1") & 0x3f) + 0x60)', $s );

		return trim( preg_replace(
		  "/([\\xc0-\\xff][\\x80-\\xbf]*)/e",
		  "'U8' . bin2hex( strtr( \"\$1\", \$wikiLowerChars ) )",
		  $s ) );
		return $s;
	}

}

?>