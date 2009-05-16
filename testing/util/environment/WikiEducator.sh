#!/bin/sh

cd /usr/local/wikiation/installer

./installer.py uninstall mediawiki:WE_testsystem

revision=48811
echo $revision

./installer.py install mediawiki: revision $revision as WE_testsystem

./installer.py install extension:Collection in WE_testsystem
./installer.py install extension:ParserFunctions revision $revision in WE_testsystem
./installer.py install extension:Cite revision $revision in WE_testsystem
./installer.py install extension:InputBox revision $revision in WE_testsystem
./installer.py install extension:ExpandTemplates revision $revision in WE_testsystem
./installer.py install extension:Quiz revision $revision in WE_testsystem
./installer.py install extension:LiquidThreads in WE_testsystem
./installer.py install extension:googleAnalytics revision $revision in WE_testsystem
./installer.py install extension:WikiArticleFeeds in WE_testsystem

cd /usr/local/wikiation/util/environment
