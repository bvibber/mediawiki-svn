#!/bin/sh

./installer.py uninstall mediawiki:SMW_testsystem

Tag=REL1_14_0

./installer.py install mediawiki:$Tag as SMW_testsystem

./installer.py install extension:Configure in SMW_testsystem

./installer.py install extension:SemanticMediaWiki in SMW_testsystem
./installer.py install extension:SemanticForms in SMW_testsystem
./installer.py install extension:SemanticDrilldown in SMW_testsystem
./installer.py install extension:SemanticCompoundQueries in SMW_testsystem
./installer.py install extension:SemanticGoogleMaps in SMW_testsystem
./installer.py install extension:SemanticResultFormats in SMW_testsystem

./installer.py install extension:ExternalData in SMW_testsystem
./installer.py install extension:DataTransfer in SMW_testsystem
./installer.py install extension:ParserFunctions in SMW_testsystem
./installer.py install extension:Renameuser revision 37407 in SMW_testsystem
./installer.py install naive:ReplaceText in SMW_testsystem
./installer.py install naive:Cite revision 37577 in SMW_testsystem
./installer.py install extension:HeaderTabs in SMW_testsystem
./installer.py install naive:StringFunctions in SMW_testsystem
./installer.py install naive:ConfirmEdit in SMW_testsystem
./installer.py install extension:GoogleGeocoder in SMW_testsystem
./installer.py install naive:Widgets in SMW_testsystem

./installer.py install naive:DeleteBatch tag $Tag in SMW_testsystem
