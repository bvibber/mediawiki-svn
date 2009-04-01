#!/bin/sh

./installer.py uninstall mediawiki:UW_testsystem

Tag=REL1_13_3

./installer.py install mediawiki:$Tag as UW_testsystem

./installer.py install extension:Configure tag $Tag in UW_testsystem

./installer.py install extension:CssHooks in UW_testsystem
./installer.py install extension:Javascript in UW_testsystem
./installer.py install extension:MooTools12core in UW_testsystem
./installer.py install extension:AutoCreateCategoryPages in UW_testsystem
./installer.py install extension:GenericEditPage in UW_testsystem
./installer.py install extension:CatBoxAtTop in UW_testsystem
./installer.py install extension:CustomToolbar in UW_testsystem
./installer.py install extension:Layouts in UW_testsystem
./installer.py install extension:Authors in UW_testsystem
./installer.py install extension:CreatePage in UW_testsystem

