##
# WikiTeX: expansible LaTeX module for MediaWiki
# Copyright (C) 2004-7 Peter Danenberg
# See doc/COPYING for details.
from signal import SIGTERM, SIGKILL
from resource import RLIMIT_CORE, RLIMIT_CPU, RLIMIT_FSIZE, RLIMIT_DATA, \
     RLIMIT_STACK, RLIMIT_RSS, RLIMIT_NPROC, RLIMIT_NOFILE, RLIMIT_OFILE, \
     RLIMIT_MEMLOCK, RLIMIT_AS
from os.path import join, dirname

from wikitex.facility import Facility
from wikitex.constants import Constants

class Config(object):
    # Where groupeth the sundry radices
    root = '/usr/local/var/wikitex'
    # Where rooteth the exposed web dir
    webroot = '/usr/local/apache2/htdocs'
    # Where will lie the Apache handler
    web = join(webroot, 'wikitex')
    # Where lieth MediaWiki's root
    mediawiki = join(webroot, 'mediawiki/LocalSettings.php')
    # Where will lie MediaWiki-client
    extension = join(dirname(mediawiki), 'extensions/wikitex')
    # Where will lie MediaWiki-client-cache
    cache = join(extension, 'cache')
    # Dir to store executable scripts
    scripts = '/usr/local/bin'
    # Dir to store documentation
    docs = '/usr/local/share/wikitex'
    # Dir that storeth process-identification
    run = '/var/run'
    # Dir that containeth our play-dbs
    db_root = join(root, 'db')
    # File that databaseth
    db = join(db_root, 'wikitex')
    # File that locketh writing
    db_lock = join(db_root, 'wikitex.lock')
    # User that runneth
    user = 'wikitex'
    # Group that o'er-runneth
    group = 'wikitex'
    # Apache-user
    http_user = 'nobody'
    # Apache-group
    http_group = 'nobody'
    # Haven where bindeth socket
    default_port = 8000
    # Guest's correlative where bindeth socket
    default_host = '127.0.0.1'
    # When and how to signal runaway children (independent of resource limits)
    default_wait = {45: SIGTERM,
                    50: SIGKILL}
    # Specifica (values preset to None are typically ignored);
    # env is currently superfluous.
    # Base values, replicated in the subclasses
    facilities = {Constants.LATEX: Facility(root='latex',
                                            host=default_host,
                                            port=default_port + 0,
                                            path='/usr/local/teTeX/bin/latex',
                                            wait=default_wait,
                                            args=['--interaction=nonstopmode']),
                  Constants.DVIPNG: Facility(root=None,
                                             host=None,
                                             port=None,
                                             path='/usr/local/bin/dvipng',
                                             wait=default_wait,
                                             args=['--strict', '-l', '=1', '-bg', 'Transparent', '-T', 'tight'])}
    # Selectively map public interfaces to private facilities
    publicae = {Constants.MATH: facilities[Constants.LATEX]}
    # Process-ceiling for ForkingMixIn (independent of resource limits)
    max_children = 40
    # Resource limits (see Constants for defaults).
    limits = {
        # Core file (don't produce them)
        RLIMIT_CORE: (0, 0),           
        # CPU time
        RLIMIT_CPU: (0, 0),
        # Maximum file size
        RLIMIT_FSIZE: (Constants.MiB, Constants.MiB),
        # (Un)initialized data plus heap
        RLIMIT_DATA: (0, 0),
        # Stack
        RLIMIT_STACK: (0, 0),
        # Resident set size (low memory conditions)
        RLIMIT_RSS: (0, 0),
        # Child processes                                        
        RLIMIT_NPROC: (2**3, 2**3),
        # Open files
        RLIMIT_NOFILE: (2**3, 2**3),
        # Memory lock
        RLIMIT_MEMLOCK: (0, 0),
        # Total available memory
        RLIMIT_AS: (2**6 * Constants.MiB, 2**6 * Constants.MiB),
        }
    # Unit of processor time
    ctime_unit = 60.0
    # Maximum processor time per unit processor time
    # that a client can usurp.
    max_ctime_per_unit = ctime_unit * 0.5
