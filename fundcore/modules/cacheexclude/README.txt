ABOUT

This module provides a simple way to exclude certain pages from being cached.  Sometimes
you want all pages to be cached for anonymous users except for one or two pages that have 
dynamic or random or rotating content.  If those pages are cached, the dynamic parts 
cease to be dynamic.  This module allows an administrator to selectively exclude certain 
paths from being cached so that dynamic content is actually dynamic.

This module was originally written by <a href="http://drupal.org/user/86524">joepublicster</a> in this thread: http://drupal.org/node/23797.  I just cleaned it up and committed it.  

Currently, paths are specified literally, not with the usual regex magic.  That would be
a nice addition in the future.

REQUIREMENTS

- Drupal 5.0

INSTALLATION

- Copy the cacheexclude directory to your modules directory.
- Go to admin/build/modules and enable it.
- Go to admin/settings/cacheexclude and configure paths you want excluded from caching.

AUTHOR AND CREDIT

Larry Garfield, larry@garfieldtech.com - Maintainer
joepubliciser - Original Author

