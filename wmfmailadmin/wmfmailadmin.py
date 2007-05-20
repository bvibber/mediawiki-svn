#!/usr/bin/python
"""
WMF mailadmin
Simple mail account maintenance script
Written by Mark Bergsma <mark@wikimedia.org>
"""

import sys

dbname = 'user.db'
conn = None

actions = {
	# Option: ( action, description )
	'l': ('list',	"List accounts"),
	'c': ('create',	"Create account"),
	'd': ('delete',	"Delete account"),
	'u': ('update',	"Update account")
}
longactions = {}

fieldmappings = {
	# Option: ( fieldname, description, default )
	'e': ('email',		"E-mail address",	None),
	'p': ('password',	"Password",			None),
	'r': ('realname',	"Real name",		None),
	'i': ('id',			"Id",				None),
	'q': ('quota',		"Quota",			2**30/1024),
	'a': ('active',		"Active",			True),
	'f': ('filter',		"Filter",			None)
}
longmappings = {}

updateables = ('password', 'quota', 'realname', 'active')

def list_accounts(fields):
	"""
	List accounts in the database
	"""
	
	global conn, longmappings
	
	defaultheaders = ('id', 'email', 'realname', 'password', 'active', 'quota', 'filter')
	
	where_clause = " AND ".join(
		[f + ' = :'+f
		 for f in ('id', 'localpart', 'domain', 'password', 'realname', 'quota', 'active')
		 if f in fields])
	if where_clause != "": where_clause = " WHERE " + where_clause
	
	cur = conn.cursor()
	cur.execute("SELECT id, localpart, domain, realname, password, active, quota, filter NOTNULL AS filter "
			    "FROM account" + where_clause, fields)
	
	header = [longmappings[m][1] for m in defaultheaders]
	columnlengths = map(len, header)
	
	# Prepare formatted fields, determine maximum column lengths
	displaylist = []
	for row in cur:
		columns = ("%d\t%s@%s\t%s\t%s\t%s\t%d KiB\t%d" % row).split('\t')
		columnlengths = map(max, zip(columnlengths, map(len, columns)))
		displaylist.append(columns)

	# Print header and data
	header = [header, ["-" * l for l in columnlengths]]
	for row in header+displaylist:
		print "  ".join(["%*s" % field for field in zip(columnlengths, row)])

def create_account(fields):
	"""
	Create an account
	"""
	
	global conn, longmappings, updateables
	
	required_fields = ('email', 'password', 'realname')
	require_fields( (required_fields, ), fields)
	
	# Set default values for fields not given
	value_fields = ['localpart', 'domain', 'password', 'realname']
	for fieldname in updateables:
		default = longmappings[fieldname][2]
		if fieldname not in fields and default is not None:
			fields[fieldname] = default
			value_fields.append(fieldname)

	# Construct list of fields that are either given, or should get default values
	values_list = "(" + ", ".join(value_fields) + ") VALUES (:" + ", :".join(value_fields) + ")"
	
	conn.cursor().execute("INSERT INTO account " + values_list, fields)
	conn.commit()

def delete_account(fields):
	"""
	Remove an account
	"""
	
	global conn
	
	require_fields( (('id', ), ('email', )), fields)

	conn.cursor().execute("DELETE FROM account WHERE " + (fields.has_key('id')
				and "id=:id"
				or "localpart=:localpart AND domain=:domain"),
				fields)
	conn.commit()
	
def update_account(fields):
	"""
	Update an existing account
	"""
	
	global conn
	
	require_fields( (('id', ), ('email', )), fields)
	require_fields( (('password', ), ('realname', ), ('quota', ), ('active', ), ), fields)
	
	# Build UPDATE clause from update arguments
	update_clause = " AND ".join(
		[f+'=:'+f
		 for f in ('password', 'realname', 'quota', 'active')
		 if f in fields])
	
	# Build WHERE clause from selectables
	where_clause = (fields.has_key('id')
				and "id=:id"
				or "localpart=:localpart AND domain=:domain")
	
	conn.cursor().execute("UPDATE account SET %s WHERE %s" % (update_clause, where_clause), fields)
	conn.commit()

def require_fields(required_fields, fields):
	"""
	Checks whether all required fields given in the nested tuple
	required_fields are present in dict fields
	Format: ((a1 AND a2 AND a2) OR (b1 AND b2)) ...
	"""

	if not reduce(lambda a,b: a or set(b) <= set(fields.keys()), required_fields, False):
		raise Exception("Fields " + 
					    " or ".join([", ".join(s) for s in required_fields])
					    + " are required.")	

def split_email(fields):
	"""
	Split e-mail address into localpart and domain fields
	"""
	
	fields['localpart'], fields['domain'] = fields['email'].rsplit('@')
	# TODO: syntax checking
	return fields

def add_index(dct, fieldindex):
	"""
	Expects: a dict containing tuples
	Creates a new dict using a tuple value (fieldindex) as the index
	"""
	
	newDict = {}
	for k, v in dct.items():
		newDict[v[fieldindex]] = dct[k]
	return newDict

def connect_db():
	"""
	Creates a connection to the database
	"""
	
	import sqlite3
	global conn
	
	conn = sqlite3.connect(dbname)
	return conn

def print_usage():
	"""
	Print help screen
	"""
	
	global actions, fieldmappings
	
	print "Usage:", sys.argv[0], "[ACTION] [FIELDS]"

	print "\nActions:"
	for a, action in actions.iteritems():
		print "  -%s   --%-10s\t%s" % (a, action[0], action[1])

	print "\nFields:"
	for f, field in fieldmappings.iteritems():
		print "  -%s <...>   --%-10s\t%s" % (f, field[0], field[1])

def parse_arguments():
	"""
	Parse command line arguments
	"""
	
	import getopt
	global actions, fieldmappings
	
	# Build option list
	options = "".join(actions.keys() + [c+':' for c in fieldmappings.keys()]) + "h"
	long_options = [o[0] for o in actions.values()] + \
		[o[0]+'=' for o in fieldmappings.values()] + \
		["--help"]

	try:
		opts, args = getopt.gnu_getopt(sys.argv[1:], options, long_options)
	except getopt.GetoptError:
		# Print help information and exit
		print_usage()
		sys.exit(2)
	
	# Parse options	
	action, fields = None, {}
	for o, a in opts:
		if o in ('-h', '--help'):
			print_usage()
			sys.exit(0)
		if len(o) == 2:
			# Short option
			optionc = o[1]
			if optionc in actions and action is None:
				action = actions[optionc][0]
			elif optionc in fieldmappings:
				fields[fieldmappings[optionc][0]] = a
			else:
				print_usage()
				sys.exit(2)
		else:
			# Long option
			loption = o[2:]
			if loption in actions and action is None:
				action = loption
			elif loption in fieldmappings:
				fields[loption] = a
			else:
				print_usage()
				sys.exit(2)
	
	return action, fields

def main():
	"""
	Main function
	"""
	
	global longactions, longmappings

	longactions, longmappings = add_index(actions, 0), add_index(fieldmappings, 0)
	action, fields = parse_arguments()
	
	if action is not None:
		connect_db()

	# Split e-mail address into localpart and domain fields
	if 'email' in fields: split_email(fields)
	
	if action == 'list':
		list_accounts(fields)
	elif action == 'create':
		create_account(fields)
		print "Account added:"
		list_accounts(fields)
	elif action == 'delete':
		delete_account(fields)
	elif action == 'update':
		update_account(fields)
		print "Account updated:"
		list_accounts(fields)

if __name__ == "__main__":
	main()