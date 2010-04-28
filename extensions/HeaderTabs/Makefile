all:

rel:	release
release:
ifndef v
	# Must specify version as 'v' param
	#
	#   make rel v=1.1.1
	#
else
	#
	# Tagging it with release tag
	#
	svn copy . svn+ssh://sergeychernyshev@svn.wikimedia.org/svnroot/mediawiki/tags/extensions/HeaderTabs/REL_${subst .,_,${v}}/
	#
	# Creating release tarball and zip
	#
	svn export http://svn.wikimedia.org/svnroot/mediawiki/tags/extensions/HeaderTabs/REL_${subst .,_,${v}}/ HeaderTabs
	# Not including Makefile into the package since it's not doing anything but release packaging
	rm HeaderTabs/Makefile
	tar -c HeaderTabs |gzip > HeaderTabs_${v}.tgz
	zip -r HeaderTabs_${v}.zip HeaderTabs
	rm -rf HeaderTabs 

	# upload to Google Code repository (need account with enough permissions)
	googlecode/googlecode_upload.py -s "MediaWiki HeaderTabs Extension v${v} (tarball)" -p mediawiki-header-tabs -l "Featured,Type-Archive,OpSys-All" HeaderTabs_${v}.tgz
	googlecode/googlecode_upload.py -s "MediaWiki HeaderTabs Extension v${v} (zip)" -p mediawiki-header-tabs -l "Featured,Type-Archive,OpSys-All" HeaderTabs_${v}.zip
	rm HeaderTabs_${v}.tgz HeaderTabs_${v}.zip
endif
