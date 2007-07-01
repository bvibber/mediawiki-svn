# Copyright (C) 2002-2003 by James Henstridge <james@daa.com.au>
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
# 
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
# 
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software 
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA 02111-1307, US

"""Perform spam detection with SpamAssassin.

Messages are passed to a spamd (SpamAssassin) daemon for spam checking.
Depending on the score returned, messages may be rejected or held for
moderation.
"""

import string
import spamd

from Mailman import mm_cfg
from Mailman import Errors
from Mailman.Logging.Syslog import syslog
from Mailman.Handlers import Hold
from Mailman.Handlers.Moderate import matches_p

SPAMD_HOST    = getattr(mm_cfg, 'SPAMASSASSIN_HOST', '')
DISCARD_SCORE = getattr(mm_cfg, 'SPAMASSASSIN_DISCARD_SCORE', 10)
HOLD_SCORE    = getattr(mm_cfg, 'SPAMASSASSIN_HOLD_SCORE', 5)
MEMBER_BONUS  = getattr(mm_cfg, 'SPAMASSASSIN_MEMBER_BONUS', 2)

class SpamAssassinDiscard(Errors.DiscardMessage):
    '''The message was scored above the discard threshold'''
    reason = 'SpamAssassin identified this message as spam'
    rejection = 'Your message has been discarded as spam'

class SpamAssassinHold(Errors.HoldMessage):
    '''The message was scored above the hold threshold'''
    def __init__(self, score=-1, symbols=''):
        Errors.HoldMessage.__init__(self)
        self.reason = 'SpamAssassin identified this message as possible ' \
                      'spam (score %g)' % score
        self.rejection = 'Your message was held for moderation because ' \
                         'SpamAssassin gave the message a score of %g ' \
                         'for the following reasons:\n\n%s' % \
                         (score, symbols)

def check_message(mlist, message):
    '''Check a message against a SpamAssassin spamd process.
    Returns a tuple of the form (score, symbols)'''
    try:
        connection = spamd.SpamdConnection(SPAMD_HOST)
        # identify as the mailing list, to allow storing per-list
        # AWL and bayes databases.
        connection.addheader('User', mlist.internal_name())
        res = connection.check(spamd.SYMBOLS, message)

        score = connection.getspamstatus()[1]
        symbols = connection.response_message.replace(',', ', ')

        return score, symbols
    except spamd.error, ex:
        syslog('error', 'spamd: %s' % str(ex))
        return -1, ''

def process(mlist, msg, msgdata):
    if msgdata.get('approved'):
        return
    
    score, symbols = check_message(mlist, str(msg))

    if MEMBER_BONUS != 0:
        for sender in msg.get_senders():
            if mlist.isMember(sender) or \
                   matches_p(sender, mlist.accept_these_nonmembers):
                score -= MEMBER_BONUS
                break

    if score > DISCARD_SCORE:
        listname = mlist.real_name
        sender = msg.get_sender()
        syslog('vette', '%s post from %s discarded: '
                        'SpamAssassin score was %g (discard threshold is %g)'
                          % (listname, sender, score, DISCARD_SCORE))
        raise SpamAssassinDiscard
    elif score > HOLD_SCORE:
        Hold.hold_for_approval(mlist, msg, msgdata,
                               SpamAssassinHold(score, symbols))
