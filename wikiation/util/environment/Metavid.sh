#!/bin/sh

revision=48811


./installer.py uninstall mediawiki:MetavidWiki

./installer.py install mediawiki: revision 48941	 as MetavidWiki

./installer.py install extension:Configure in MetavidWiki

./installer.py install extension:SemanticMediaWiki in MetavidWiki
./installer.py install extension:MetavidWiki in MetavidWiki

./installer.py install extension:ParserFunctions in MetavidWiki
./installer.py install extension:ExternalData in MetavidWiki
#./installer.py install extension:Cite in MetavidWiki

./installer.py install naive:ConfirmEdit in MetavidWiki
./installer.py install extension:OggHandler in MetavidWiki

