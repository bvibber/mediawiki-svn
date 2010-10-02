<?php
$dir = dirname( __FILE__ ) . '/';

$wgExtensionMessagesFiles['LiquidThreads'] = $dir . 'MultilangLqt.i18n.php'; // Added for Multilang Extension

// Classes
$wgAutoloadClasses['HTMLTranslator'] = $dir . 'HTMLTranslator.php';
$wgAutoloadClasses['LanguageGridAccessObject'] = $dir . 'LanguageGridAccessObject.php';
$wgAutoloadClasses['MetaTranslator'] = $dir . 'MetaTranslator.php';
$wgAutoloadClasses['MultilangLqtHooks'] = $dir . 'MultilangLqtHooks.php';
$wgAutoloadClasses['MultilangThreadController'] = $dir . 'MultilangThreadController.php';
$wgAutoloadClasses['SourceView'] = $dir . 'SourceView.php';
$wgAutoloadClasses['ThreadLanguage'] = $dir . 'ThreadLanguage.php';
$wgAutoloadClasses['TranslatedSubject'] = $dir . 'TranslatedSubject.php';
$wgAutoloadClasses['TranslatedThread'] = $dir . 'TranslatedThread.php';

// Language Grid
$wgAutoloadClasses['LangridAccessClient'] = $dir . '../../LanguageGrid/api/class/client/LangridAccessClient.class.php';

// hook
$wgHooks['LiquidThreadsSaveReply'][] = 'MultilangLqtHooks::saveReply';
$wgHooks['LiquidThreadsThreadCommands'][] = 'MultilangLqtHooks::onThreadCommands';
$wgHooks['LiquidThreadsThreadPermalinkView'][] = 'MultilangLqtHooks::onThreadPermalinkView';
$wgHooks['LiquidThreadsEditingFormContent'][] = 'MultilangLqtHooks::translateRoot';
$wgHooks['LiquidThreadsThreadSignature'][] = 'MultilangLqtHooks::onThreadSignature';
$wgHooks['LiquidThreadsShowThreadHeading'][] = 'MultilangLqtHooks::translateSubject';
$wgHooks['LiquidThreadsGetTOC'][] = 'MultilangLqtHooks::translateSubjectforTOC';
$wgHooks['LiquidThreadsShowPostContent'][] = 'MultilangLqtHooks::translateBody';
$wgHooks['LiquidThreadsThreadMajorCommands'][] = 'MultilangLqtHooks::onThreadMajorCommands';
$wgHooks['LiquidThreadsShowPostThreadBody'][] = 'MultilangLqtHooks::onShowPostThreadBody';
$wgHooks['LiquidThreadsDoInlineEditForm'][] = 'MultilangLqtHooks::onDoInlineEditForm';
$wgHooks['LiquidThreadsAfterNewPostMetadataUpdates'][] = 'MultilangLqtHooks::saveSourceLang';
$wgHooks['LiquidThreadsAfterReplyMetadataUpdates'][] = 'MultilangLqtHooks::saveSourceLang';
$wgHooks['LiquidThreadsEndOfShowNewThreadForm'][] = 'MultilangLqtHooks::showLanguageSelector';
$wgHooks['LiquidThreadsShowReplyForm'][] = 'MultilangLqtHooks::showLanguageSelector';

$wgPageProps['use-liquid-threads'] = 'Whether or not the page uses LiquidThreads';


