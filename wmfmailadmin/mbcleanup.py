#!/usr/bin/python
"""
WMF mailadmin mbcleanup
Cleans up mailboxes for no longer existing accounts
Written by Mark Bergsma <mark@wikimedia.org>
"""

import sys, os, sqlite3, time

def get_accounts(dbname):
    """
    Retrieves a list of current accounts
    """
    
    conn = sqlite3.connect(dbname)
    cur = conn.cursor()
    cur.execute("SELECT domain, localpart FROM account")
    
    return set(cur.fetchall())
    
def get_mailboxes(mbroot):
    """
    Retrieves a list of mail boxes
    """
    
    # 2 levels: domain/localpart
    mailboxes = set()
    for domain in os.listdir(mbroot):
        domaindir = os.path.join(mbroot, domain)
        if not domain.startswith('.') and os.path.isdir(domaindir):
            try:
                for localpart in os.listdir(domaindir):
                    if not localpart.startswith('.') and localpart.find('@') < 0 and \
                        os.path.isdir(os.path.join(domaindir, localpart)):
                        mailboxes.add((unicode(domain), unicode(localpart)))
            except OSError, e:
                if e.errno != 13: raise

    return mailboxes

def move_mailboxes(mbroot, mbbackuproot, mailboxes):
    """
    Moves a set of mailboxes to a backup location
    """
    
    for mb in mailboxes:
        date = time.strftime('%Y%m%d%H%M')
        oldpath = os.path.join(mbroot, mb[0], mb[1])
        newpath = os.path.join(mbbackuproot, mb[0], mb[1]) + '@' + date
        print 'Moving', oldpath, '=>', newpath
        os.makedirs(newpath)
        os.rename(oldpath, newpath)

def main():
    """
    Main function
    """
    
    try:
        dbname, mbroot, mbbackuproot = sys.argv[1:4]
    except ValueError:
        print >> sys.stderr, "Usage:", sys.argv[0], "{user.db file} {mail boxes root path} {backup root path}"
        sys.exit(1)
        
    accounts, mailboxes = get_accounts(dbname), get_mailboxes(mbroot)
    
    # Move mailboxes without a corresponding account
    move_mailboxes(mbroot, mbbackuproot, mailboxes - accounts)

if __name__ == '__main__':
    main()