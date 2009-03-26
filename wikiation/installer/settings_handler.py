# This software, copyright (C) 2008-2009 by Wikiation. 
# This software is developed by Kim Bruning.
#
# Distributed under the terms of the MIT license.
#
# =============================================================
# Default settings file. DO NOT EDIT (edit settings.py instead)
# =============================================================

# Provides sane defauls and backwards compatibility for
# settings.py.

import os

# "You Are Here"
installerdir=os.path.dirname(os.path.abspath(__file__))

# where to find .install directories and the files contained therein
installfiles=os.path.join(installerdir,'installfiles')

# where do we store the _tagcache file
tagcache=os.path.join(installerdir,"_tagcache")

# where to find mediawiki tags and trunk on svn
tagsdir="http://svn.wikimedia.org/svnroot/mediawiki/tags"
trunkdir="http://svn.wikimedia.org/svnroot/mediawiki/trunk"
# we could alternately/additionally take a tag version for extensions. (future)
extensionssubdir="extensions"
extensionsdir=trunkdir+"/"+extensionssubdir

# where to install diverse revisions
instancesdir='/var/www/revisions'


# base scriptpath for every installation (ie, where to reach the above over the web)
base_scriptpath="/revisions/"

# where to install the toolkit
toolkit_dir='/usr/local/wikiation'


#where wikiation_check_isolation can be found
isolation_create=toolkit_dir+'/wikiation_check_isolation/create_and_ul.sh'
isolation_test=toolkit_dir+'/wikiation_check_isolation/dl_and_check.sh'
# run automated tests during installation
# this is useful if you are in a testing environment.
# If you are running production, you might want to leave
# this set to False.
run_automated_tests=False

debug=False

# initial user
adminuser_name="admin"
adminuser_password="admin1234"

#mysql info
mysql_user="root"
mysql_pass=""

# what mysql commands should be used. (Who us? Use horrible hacks?)

userpart=""
passpart=""
if mysql_user:
	userpart="-u"+mysql_user
if mysql_pass:
	passpart="-p"+mysql_pass

mysql_arguments=" "+userpart+" "+passpart

if not 'mysql_command' in globals():
	mysql_command="mysql "+mysql_arguments

if not 'mysqldump_command' in globals():
	mysqldump_command="mysqldump "+mysql_arguments

if os.path.exists(os.path.join(installerdir, 'settings.py')):
        from settings import *

#legacy support (rename old variables, etc)
if 'revisionsdir' in globals():
	instancesdir=revisionsdir
	
