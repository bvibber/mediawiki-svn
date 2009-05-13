#!/bin/sh
cd ../../installer

revision="48811"
echo $revision

#CentralAuth and GlobalBlocking fail ... global ?

./installer.py uninstall mediawiki:WMF_testsystem

./installer.py install mediawiki: revision $revision as WMF_testsystem

./installer.py install naive:CategoryTree revision $revision in WMF_testsystem
#./installer.py install naive:CentralAuth revision $revision in WMF_testsystem

# needs to run an install script to add table(s) to database
./installer.py install extension:CheckUser revision $revision in WMF_testsystem

#:{{done}} -- this extension shows twice with different numbers.. this is the highest
./installer.py install naive:Cite revision $revision in WMF_testsystem
# SpecialCite shares the Cite directory in SVN. We split them into two directories
./installer.py install extension:SpecialCite revision $revision in WMF_testsystem

./installer.py install naive:Collection revision $revision in WMF_testsystem

# non standard include
./installer.py install extension:CrossNamespaceLinks revision $revision in WMF_testsystem

./installer.py install naive:ExpandTemplates revision $revision in WMF_testsystem

# this extension is marked as obsolete
./installer.py install naive:Makebot revision $revision in WMF_testsystem
# this extension is marked as obsolete
#./installer.py install naive:Makesysop revision $revision in WMF_testsystem

# this is not an extension that you can install from SVN
./installer.py install extension:OAI revision $revision in WMF_testsystem

# needs to be an extension as it requires new tables and stuff
./installer.py install extension:Oversight revision $revision in WMF_testsystem

# needs to be an extension as it requires a non standard entry in LocalSettings
./installer.py install extension:Renameuser revision $revision in WMF_testsystem

./installer.py install naive:SiteMatrix revision $revision in WMF_testsystem
./installer.py install naive:CharInsert revision revision in WMF_testsystem

# requires ploticus, requires settings 
#./installer.py install naive:EasyTimeline revision $revision in WMF_testsystem

./installer.py install extension:ImageMap revision $revision in WMF_testsystem
./installer.py install extension:InputBox revision $revision in WMF_testsystem
./installer.py install extension:ParserFunctions in WMF_testsystem
./installer.py install naive:Poem revision $revision in WMF_testsystem
./installer.py install naive:SyntaxHighlight_GeSHi revision $revision in WMF_testsystem
./installer.py install naive:wikihiero revision $revision in WMF_testsystem
./installer.py install extension:OggHandler revision $revision in WMF_testsystem

# AntiSpoof needs to be installed prior to AbuseFilters
./installer.py install naive:AntiSpoof revision $revision in WMF_testsystem

# needs further configurations.. needs to have a file added
#./installer.py install naive:AbuseFilter revision $revision in WMF_testsystem

./installer.py install naive:AntiBot revision $revision in WMF_testsystem
./installer.py install naive:AssertEdit revision $revision in WMF_testsystem
./installer.py install naive:CentralNotice revision $revision in WMF_testsystem
./installer.py install naive:ConfirmEdit revision $revision in WMF_testsystem
./installer.py install naive:DismissableSiteNotice revision $revision in WMF_testsystem
./installer.py install naive:Gadgets revision $revision in WMF_testsystem

# This expects global pre-exising functionality
#./installer.py install naive:GlobalBlocking revision $revision in WMF_testsystem

./installer.py install naive:MWSearch revision $revision in WMF_testsystem
./installer.py install naive:OpenSearchXml revision $revision in WMF_testsystem
./installer.py install naive:SimpleAntiSpam in WMF_testsystem
./installer.py install naive:SpamBlacklist revision $revision in WMF_testsystem
./installer.py install naive:TitleBlacklist in WMF_testsystem
./installer.py install extension:TitleKey revision $revision in WMF_testsystem
./installer.py install naive:TorBlock revision $revision in WMF_testsystem

# result is an exception error
#./installer.py install naive:TrustedXFF revision $revision in WMF_testsystem

./installer.py install naive:UsernameBlacklist in WMF_testsystem
./installer.py install naive:WikimediaMessages in WMF_testsystem


