###
# Copyright (c) 2007, River Tarnell
# All rights reserved.
#
#
###

"""
Toolserver JIRA plugin
"""

import supybot
import supybot.world as world

__version__ = "1"

__author__ = supybot.Author('River Tarnell', 'flyingparchment', 'river@wikimedia.org')

__contributors__ = {}

__url__ = ''

import config
import plugin
reload(plugin) # In case we're being reloaded.

if world.testing:
    import test

Class = plugin.Class
configure = config.configure


# vim:set shiftwidth=4 tabstop=4 expandtab textwidth=79:
