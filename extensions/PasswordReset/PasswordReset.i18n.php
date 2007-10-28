<?php
/** \file
* \brief Internationalization file for the Password Reset Extension.
*/

$allMessages = array(
	'en' => array( 
		'passwordreset' => 'Password Reset',
		'passwordreset-invalidusername' => 'Invalid Username',
		'passwordreset-emptyusername' => 'Empty Username',
		'passwordreset-nopassmatch' => 'Passwords do not match',
		'passwordreset-badtoken' => 'Invalid Edit Token',
		'passwordreset-username' => 'Username',
		'passwordreset-newpass' => 'New Password',
		'passwordreset-confirmpass' => 'Confirm Password',
		'passwordreset-submit' => 'Reset Password',
		'passwordreset-success' => 'Password has been reset for user_id: $1'
	),

	'ar' => array(
		'passwordreset' => 'تمت إعادة ضبط كلمة السر',
		'passwordreset-invalidusername' => 'اسم مستخدم غير صحيح',
		'passwordreset-emptyusername' => 'اسم مستخدم فارغ',
		'passwordreset-nopassmatch' => 'كلمات السر لا تتطابق',
		'passwordreset-badtoken' => 'نص تعديل غير صحيح',
		'passwordreset-username' => 'اسم مستخدم',
		'passwordreset-newpass' => 'كلمة سر جديدة',
		'passwordreset-confirmpass' => 'أكد كلمة السر',
		'passwordreset-submit' => 'أعد ضبط كلمة السر',
		'passwordreset-success' => 'كلمة السر تم ضبطها ل user_id: $1',
	),

	'de' => array( 
		'passwordreset' => 'Passwort zurücksetzen',
	),

	'hsb' => array(
		'passwordreset' => 'Hesło wróćo stajić',
		'passwordreset-invalidusername' => 'Njepłaćiwe wužiwarske mjeno',
		'passwordreset-emptyusername' => 'Žane wužiwarske mjeno',
		'passwordreset-nopassmatch' => 'Hesle njerunatej so',
		'passwordreset-badtoken' => 'Njepłaćiwe wobdźěłanske znamjo',
		'passwordreset-username' => 'Wužiwarske mjeno',
		'passwordreset-newpass' => 'Nowe hesło',
		'passwordreset-confirmpass' => 'Hesło wobkrućić',
		'passwordreset-submit' => 'Hesło wróćo stajić',
		'passwordreset-success' => 'Hesło bu za wužiwarski ID $1 wróćo stajene.',
	),

	'nl' => array(
		'passwordreset' => 'Wachtwoord opnieuw instellen',
		'passwordreset-invalidusername' => 'Onjuiste gebruiker',
		'passwordreset-emptyusername' => 'Gebruiker niet ingegeven',
		'passwordreset-nopassmatch' => 'De wachtwoorden komen niet overeen',
		'passwordreset-badtoken' => 'Ongeldig bewerkingstoken',
		'passwordreset-username' => 'Gebruiker',
		'passwordreset-newpass' => 'Nieuw wachtwoord',
		'passwordreset-confirmpass' => 'Bevestig wachtwoord',
		'passwordreset-submit' => 'Wachtwoord opnieuw instellen',
		'passwordreset-success' => 'Wachtwoord voor gebruikers-ID $1 is opnieuw ingesteld',
	),

	'pms' => array(
		'passwordreset' => 'Cambi ëd ciav',
		'passwordreset-invalidusername' => 'Stranòm nen giust',
		'passwordreset-emptyusername' => 'Stranòm veujd',
		'passwordreset-nopassmatch' => 'Le doe ciav a son pa mideme',
		'passwordreset-badtoken' => 'Còdes ëd modìfica nen bon',
		'passwordreset-username' => 'Stranòm',
		'passwordreset-newpass' => 'Ciav neuva',
		'passwordreset-confirmpass' => 'Confermè la ciav',
		'passwordreset-submit' => 'Cambié la ciav',
		'passwordreset-success' => 'La ciav ëd l\'utent $1 a l\'é staita cambià',
	),

	'yue' => array( 
		'passwordreset' => '密碼重設',
		'passwordreset-invalidusername' => '無效嘅用戶名',
		'passwordreset-emptyusername' => '空白嘅用戶名',
		'passwordreset-nopassmatch' => '密碼唔對',
		'passwordreset-badtoken' => '無效嘅編輯幣',
		'passwordreset-username' => '用戶名',
		'passwordreset-newpass' => '新密碼',
		'passwordreset-confirmpass' => '確認新密碼',
		'passwordreset-submit' => '重設密碼',
		'passwordreset-success' => 'User_id: $1 嘅密碼已經重設咗'
	),

	'zh-hans' => array( 
		'passwordreset' => '密码重设',
		'passwordreset-invalidusername' => '无效的用户名',
		'passwordreset-emptyusername' => '空白的用户名',
		'passwordreset-nopassmatch' => '密码不匹配',
		'passwordreset-badtoken' => '无效的编辑币',
		'passwordreset-username' => '用户名',
		'passwordreset-newpass' => '新密码',
		'passwordreset-confirmpass' => '确认新密码',
		'passwordreset-submit' => '重设密码',
		'passwordreset-success' => 'User_id: $1 的密码已经重设'
	),

	'zh-hant' => array( 
		'passwordreset' => '密碼重設',
		'passwordreset-invalidusername' => '無效的用戶名',
		'passwordreset-emptyusername' => '空白的用戶名',
		'passwordreset-nopassmatch' => '密碼不匹配',
		'passwordreset-badtoken' => '無效的編輯幣',
		'passwordreset-username' => '用戶名',
		'passwordreset-newpass' => '新密碼',
		'passwordreset-confirmpass' => '確認新密碼',
		'passwordreset-submit' => '重設密碼',
		'passwordreset-success' => 'User_id: $1 的密碼已經重設'
	),
);

$allMessages['zh'] = $allMessages['zh-hans'];
$allMessages['zh-cn'] = $allMessages['zh-hans'];
$allMessages['zh-hk'] = $allMessages['zh-hant'];
$allMessages['zh-sg'] = $allMessages['zh-hans'];
$allMessages['zh-tw'] = $allMessages['zh-hant'];
$allMessages['zh-yue'] = $allMessages['yue'];
