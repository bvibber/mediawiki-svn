<?php
/**
 * OpenID.i18n.php -- Interface messages for OpenID for MediaWiki
 * Copyright 2006,2007 Internet Brands (http://www.internetbrands.com/)
 * Copyright 2007,2008 Evan Prodromou <evan@prodromou.name>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @author Evan Prodromou <evan@prodromou.name>
 * @addtogroup Extensions
 */

$messages = array();

/** English
 * @author Evan Prodromou <evan@prodromou.name>
 */
$messages['en'] = array(
	'openid-desc' => 'Login to the wiki with an [http://openid.net/ OpenID] and login to other OpenID-aware web sites with a wiki user account',
	'openidlogin' => 'Login with OpenID',
	'openidfinish' => 'Finish OpenID login',
	'openidserver' => 'OpenID server',
	'openidxrds' => 'Yadis file',						
	'openidconvert' => 'OpenID converter',
	'openiderror' => 'Verification error',
	'openiderrortext' => 'An error occured during verification of the OpenID URL.',
	'openidconfigerror' => 'OpenID Configuration Error',
	'openidconfigerrortext' => 'The OpenID storage configuration for this wiki is invalid.
Please consult this site\'s administrator.',
	'openidpermission' => 'OpenID permissions error',
	'openidpermissiontext' => 'The OpenID you provided is not allowed to login to this server.',
	'openidcancel' => 'Verification cancelled',
	'openidcanceltext' => 'Verification of the OpenID URL was cancelled.',
	'openidfailure' => 'Verification failed',
	'openidfailuretext' => 'Verification of the OpenID URL failed. Error message: "$1"',
	'openidsuccess' => 'Verification succeeded',
	'openidsuccesstext' => 'Verification of the OpenID URL succeeded.',
	'openidusernameprefix' => 'OpenIDUser',
	'openidserverlogininstructions' => 'Enter your password below to log in to $3 as user $2 (user page $1).',
	'openidtrustinstructions' => 'Check if you want to share data with $1.',
	'openidallowtrust' => 'Allow $1 to trust this user account.',
	'openidnopolicy' => 'Site has not specified a privacy policy.',
	'openidpolicy' => 'Check the <a target="_new" href="$1">privacy policy</a> for more information.',
	'openidoptional' => 'Optional',
	'openidrequired' => 'Required',
	'openidnickname' => 'Nickname',
	'openidfullname' => 'Fullname',
	'openidemail' => 'Email address',
	'openidlanguage' => 'Language',
	'openidnotavailable' => 'Your preferred nickname ($1) is already in use by another user.',
	'openidnotprovided' => 'Your OpenID server did not provide a nickname (either because it cannot, or because you told it not to).',
	'openidchooseinstructions' => 'All users need a nickname; you can choose one from the options below.',
	'openidchoosefull' => 'Your full name ($1)',
	'openidchooseurl' => 'A name picked from your OpenID ($1)',
	'openidchooseauto' => 'An auto-generated name ($1)',
	'openidchoosemanual' => 'A name of your choice: ',
	'openidconvertinstructions' => 'This form lets you change your user account to use an OpenID URL.',
	'openidconvertsuccess' => 'Successfully converted to OpenID',
	'openidconvertsuccesstext' => 'You have successfully converted your OpenID to $1.',
	'openidconvertyourstext' => 'That is already your OpenID.',
	'openidconvertothertext' => 'That is someone else\'s OpenID.',
	'openidalreadyloggedin' => '<strong>User $1, you are already logged in!</strong>',
	'tog-hideopenid' => 'Hide your <a href="http://openid.net/">OpenID</a> on your user page, if you log in with OpenID.',
	'openidnousername' => 'No username specified.',
	'openidbadusername' => 'Bad username specified.',
	'openidautosubmit' => 'This page includes a form that should be automatically submitted if you have JavaScript enabled.
If not, try the \"Continue\" button.',
	'openidclientonlytext' => 'You cannot use accounts from this wiki as OpenIDs on another site.',
	'openidloginlabel' => 'OpenID URL',
	'openidlogininstructions' => '{{SITENAME}} supports the [http://openid.net/ OpenID] standard for single signon between Web sites.
OpenID lets you log into many different Web sites without using a different password for each.
(See [http://en.wikipedia.org/wiki/OpenID Wikipedia\'s OpenID article] for more information.)

If you already have an account on {{SITENAME}}, you can [[Special:Userlogin|log in]] with your username and password as usual. To use OpenID in the future, you can [[Special:OpenIDConvert|convert your account to OpenID]] after you\'ve logged in normally.

There are many [http://wiki.openid.net/Public_OpenID_providers Public OpenID providers], and you may already have an OpenID-enabled account on another service.

; Other wikis : If you have an account on an OpenID-enabled wiki, like [http://wikitravel.org/ Wikitravel], [http://www.wikihow.com/ wikiHow], [http://vinismo.com/ Vinismo], [http://aboutus.org/ AboutUs] or [http://kei.ki/ Keiki], you can log in to {{SITENAME}} by entering the \'\'\'full URL\'\'\' of your user page on that other wiki in the box above. For example, \'\'<nowiki>http://kei.ki/en/User:Evan</nowiki>\'\'.
; [http://openid.yahoo.com/ Yahoo!] : If you have an account with Yahoo!, you can log in to this site by entering your Yahoo!-provided OpenID in the box above. Yahoo! OpenID URLs have the form \'\'<nowiki>https://me.yahoo.com/yourusername</nowiki>\'\'.
; [http://dev.aol.com/aol-and-63-million-openids AOL] : If you have an account with [http://www.aol.com/ AOL], like an [http://www.aim.com/ AIM] account, you can log in to {{SITENAME}} by entering your AOL-provided OpenID in the box above. AOL OpenID URLs have the form \'\'<nowiki>http://openid.aol.com/yourusername</nowiki>\'\'. Your username should be all lowercase, no spaces.
; [http://bloggerindraft.blogspot.com/2008/01/new-feature-blogger-as-openid-provider.html Blogger], [http://faq.wordpress.com/2007/03/06/what-is-openid/ Wordpress.com], [http://www.livejournal.com/openid/about.bml LiveJournal], [http://bradfitz.vox.com/library/post/openid-for-vox.html Vox] : If you have a blog on any of these services, enter your blog URL in the box above. For example, \'\'<nowiki>http://yourusername.blogspot.com/</nowiki>\'\', \'\'<nowiki>http://yourusername.wordpress.com/</nowiki>\'\', \'\'<nowiki>http://yourusername.livejournal.com/</nowiki>\'\', or \'\'<nowiki>http://yourusername.vox.com/</nowiki>\'\'.',
);

/** Dutch (Nederlands)
 * @author Siebrand
 */
$messages['nl'] = array(
	'openid-desc'            => 'Aanmelden bij de wiki met een [http://openid.net/ OpenID] en aanmelden bij andere websites die OpenID ondersteunen met een wikigebruiker',
	'openidlogin'            => 'Aanmelden met OpenID',
	'openidfinish'           => 'Aanmelden met OpenID afronden',
	'openidserver'           => 'OpenID-server',
	'openidxrds'             => 'Yadis-bestand',
	'openidconvert'          => 'OpenID-convertor',
	'openiderror'            => 'Verificatiefout',
	'openiderrortext'        => 'Er is een fout opgetreden tijdens de verificatie van de OpenID URL.',
	'openidconfigerror'      => 'Fout in de installatie van OpenID',
	'openidconfigerrortext'  => "De instellingen van de opslag van OpenID's voor deze wiki klopt niet.
Raadpleeg alstublieft de beheerder van de site.",
	'openidcancel'           => 'Verificatie geannuleerd',
	'openidcanceltext'       => 'De verificatie van de OpenID URL is geannuleerd.',
	'openidfailure'          => 'Verificatie mislukt',
	'openidusernameprefix'   => 'OpenIDGebruiker',
	'openidoptional'         => 'Optioneel',
	'openidrequired'         => 'Verplicht',
	'openidnickname'         => 'Nickname',
	'openidfullname'         => 'Volledige naam',
	'openidemail'            => 'E-mailadres',
	'openidlanguage'         => 'Taal',
	'openidconvertyourstext' => 'Dat is al uw OpenID.',
	'openidconvertothertext' => 'Iemand anders heeft die OpenID al in gebruik.',
	'openidnousername'       => 'Er is geen gebruikersnaam opgegeven.',
	'openidbadusername'      => 'De opgegeven gebruikersnaam is niet toegestaan.',
	'openidloginlabel'       => 'OpenID URL',
);

/** Vietnamese (Tiếng Việt)
 * @author Minh Nguyen
 */
$messages['vi'] = array(
	'openid-desc'                   => 'Đăng nhập vào wiki dùng [http://openid.net/ OpenID] và đăng nhập vào các website nhận OpenID dùng tài khoản wiki',
	'openidlogin'                   => 'Đăng nhập dùng OpenID',
	'openidfinish'                  => 'Đăng nhập dùng OpenID xong',
	'openidserver'                  => 'Dịch vụ OpenID',
	'openidxrds'                    => 'Tập tin Yadis',
	'openiderror'                   => 'Lỗi thẩm tra',
	'openiderrortext'               => 'Có lỗi khi thẩm tra địa chỉ OpenID.',
	'openidconfigerror'             => 'Lỗi thiết lập OpenID',
	'openidconfigerrortext'         => 'Phần giữ thông tin OpenID cho wiki này không hợp lệ. Xin hãy liên lạc với người quản lý website này.',
	'openidpermission'              => 'Lỗi quyền OpenID',
	'openidpermissiontext'          => 'Địa chỉ OpenID của bạn không được phép đăng nhập vào dịch vụ này.',
	'openidcancel'                  => 'Đã hủy bỏ thẩm tra',
	'openidcanceltext'              => 'Đã hủy bỏ việc thẩm tra địa chỉ OpenID.',
	'openidfailure'                 => 'Không thẩm tra được',
	'openidfailuretext'             => 'Không thể thẩm tra địa chỉ OpenID. Lỗi: “$1”',
	'openidsuccess'                 => 'Đã thẩm tra thành công',
	'openidsuccesstext'             => 'Đã thẩm tra địa chỉ OpenID thành công.',
	'openidserverlogininstructions' => 'Hãy cho vào mật khẩu ở dưới để đăng nhập vào $3 dùng tài khoản $2 (trang thảo luận $1).',
	'openidtrustinstructions'       => 'Hãy kiểm tra hộp này nếu bạn muốn cho $1 biết thông tin cá nhân của bạn.',
	'openidallowtrust'              => 'Để $1 tin cậy vào tài khoản này.',
	'openidnopolicy'                => 'Website chưa xuất bản chính sách về sự riêng tư.',
	'openidpolicy'                  => 'Hãy đọc <a target="_new" href="$1">chính sách về sự riêng tư</a> để biết thêm chi tiết.',
	'openidoptional'                => 'Tùy ý',
	'openidrequired'                => 'Bắt buộc',
	'openidnickname'                => 'Tên hiệu',
	'openidfullname'                => 'Tên đầy đủ',
	'openidemail'                   => 'Địa chỉ thư điện tử',
	'openidlanguage'                => 'Ngôn ngữ',
	'openidnotavailable'            => 'Tên hiệu mà bạn muốn sử dụng, “$1”, đã được sử dụng bởi người khác.',
	'openidnotprovided'             => 'Dịch vụ OpenID của bạn chưa cung cấp tên hiệu, hoặc vì nó không có khả năng này, hoặc bạn đã tắt tính năng tên hiệu.',
	'openidchooseinstructions'      => 'Mọi người dùng cần có tên hiệu; bạn có thể chọn tên hiệu ở dưới.',
	'openidchoosefull'              => 'Tên đầy đủ của bạn ($1)',
	'openidchooseurl'               => 'Tên bắt nguồn từ OpenID của bạn ($1)',
	'openidchooseauto'              => 'Tên tự động ($1)',
	'openidchoosemanual'            => 'Tên khác:',
	'openidloginlabel'              => 'Địa chỉ OpenID',
);

