#!/usr/bin/python
"""
WMF mailadmin
Simple mail account maintenance script
Written by Mark Bergsma <mark@wikimedia.org>
"""

import sys, os, sqlite3

dbname = '/var/vmaildb/user.db'
conn = None

actions = {
	# Option: ( action, description, argument required )
	'l': ('list',	"List accounts",	False),
	'c': ('create',	"Create account",	False),
	'd': ('delete',	"Delete account",	False),
	'u': ('update',	"Update account",	False),
	's': ('show',	"Show field",		True)
}
longactions = {}

fieldmappings = {
	# Option: ( fieldname, description, default, explanation )
	'e': ('email',		"E-mail address",	None,		None),
	'p': ('password',	"Password",			'-',		"Password hash or '-' for prompting"),
	'r': ('realname',	"Real name",		None,		None),
	'i': ('id',			"Id",				None,		None),
	'q': ('quota',		"Quota",			2**30/1024, None),
	'a': ('active',		"Active",			True,		None),
	'f': ('filter',		"Filter",			None,		"Filter file or '-' for stdin, or 'None'")
}
longmappings = {}

updateables = ('password', 'quota', 'realname', 'active', 'filter')
table_fields = ('id', 'localpart', 'domain', 'password', 'realname', 'active', 'quota', 'filter')

supported_hash_algorithms = ('{SHA1}')

max_filter_size = 4096

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
	
	required_fields = ('email', 'realname')		# password will be prompted for if needed
	require_fields( (required_fields, ), fields)
	
	# Set default values for fields not given
	value_fields = ['localpart', 'domain', 'realname', 'password', 'active', 'filter']
	for fieldname in updateables:
		default = longmappings[fieldname][2]
		if fieldname not in fields:
			if default is not None:
				# Use the default value
				fields[fieldname] = default
				value_fields.append(fieldname)
			else:
				# Field is not given on the command line, and apparently not required
				value_fields.remove(fieldname)
				
	# Input password if needed
	input_password(fields)
	# Read filter file if needed
	input_filter(fields)

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
	
	global conn, updateables
	
	require_fields( (('id', ), ('email', )), fields)
	require_fields( (('password', ), ('realname', ), ('quota', ), ('active', ), ('filter', ), ), fields)

	# Input password if needed
	input_password(fields)
	# Read filter file if needed
	input_filter(fields)
	
	# Build UPDATE clause from update arguments
	update_clause = " AND ".join(
		[f+'=:'+f
		 for f in updateables
		 if f in fields])
	
	# Build WHERE clause from selectables
	where_clause = (fields.has_key('id')
				and "id=:id"
				or "localpart=:localpart AND domain=:domain")
	
	conn.cursor().execute("UPDATE account SET %s WHERE %s" % (update_clause, where_clause), fields)
	conn.commit()

def show_field(fields):
	"""
	Shows a single field of an account - useful for larger text fields such as 'filter'
	"""
	
	global conn, table_fields
	
	require_fields( (('id', ), ('email', )), fields)
	
	# Determine the field to display
	field = fields['show']
	if not field in table_fields:
		raise Exception("Invalid selected field " + field)
	
	# Build WHERE clause from selectables
	where_clause = (fields.has_key('id')
				and "id=:id"
				or "localpart=:localpart AND domain=:domain")
	
	cur = conn.cursor()
	cur.execute("SELECT " + field + " FROM account WHERE " + where_clause, fields)
	
	try:
		value = cur.fetchone()[0]
		if value is not None:
			print value
		else:
			print >> sys.stderr, "(NULL)"
	except:
		print >> sys.stderr, "(No rows returned)"

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

def input_password(fields):
	"""
	Checks if the password argument on the commandline was "-" or empty,
	and prompts for a password if that is the case
	"""
	
	global supported_hash_algorithms
	
	if not fields.has_key('password') or fields['password'] not in ('', '-'): return
	
	# Simply outsource to dovecotpw
	pipe = os.popen('dovecotpw -s sha1', 'r')
	password = pipe.readline().rstrip('\n')
	rval = pipe.close()
	if rval is None and password.startswith(supported_hash_algorithms):
		fields['password'] = password
	else:
		raise Exception("Problem invoking dovecotpw")

def input_filter(fields):
	"""
	Reads a filter from a file into fields['filter'] (overwriting)
	"""
	
	global max_filter_size
	
	if not fields.has_key('filter') or fields['filter'] == "": return
	
	if fields['filter'].lower() == 'none':
		# Set to NULL in the db
		fields['filter'] = None
		return
	elif fields['filter'] == '-':
		filterfile = sys.stdin
	else:
		try:
			filterfile = open(fields['filter'])
		except IOError, e:
			raise Exception("Could not open filter file %s: %e" % (fields['filter'], e.message))
	
	fields['filter'] = filterfile.read(max_filter_size)
	
	if len(fields['filter']) == max_filter_size and filterfile.read(1):
		print >> sys.stderr, "Warning: filter truncated at %d bytes!" % max_filter_size

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
	
	global conn
	
	conn = sqlite3.connect(dbname)
	return conn

def print_usage():
	"""
	Print help screen
	"""
	
	global actions, fieldmappings
	
	print "Usage:", sys.argv[0], "[ACTION] [FIELDS] [dbfile]"

	print "\nActions:"
	for a, action in actions.iteritems():
		print "  -%s   --%-10s\t%s" % (a, action[0], action[1])

	print "\nFields:"
	for f, field in fieldmappings.iteritems():
		print "  -%s <...>   --%-10s\t%s" % (f, field[0], field[3] or field[1])

def parse_arguments():
	"""
	Parse command line arguments
	"""
	
	import getopt
	global actions, fieldmappings, dbname
	
	# Build option lists for actions
	options, long_options = "", []
	for action, attributes in actions.iteritems():
		if attributes[2]:	# Argument required
			options += action + ':'
			long_options.append(attributes[0] + '=')
		else:
			options += action
			long_options.append(attributes[0])
	
	# Build option lists for fields
	options += "".join([c+':' for c in fieldmappings.keys()]) + "h"
	long_options += [o[0]+'=' for o in fieldmappings.values()] + \
		["--help"]

	try:
		opts, args = getopt.gnu_getopt(sys.argv[1:], options, long_options)
	except getopt.GetoptError:
		# Print help information and exit
		print_usage()
		sys.exit(2)

	# (First, optional) argument should be dbfile
	if len(args) > 0 and args[0] != "":
		dbname = args[0]
	
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
				if actions[optionc][2]:	# Action with parameter, store in fields
					fields[action] = a
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
				if actions[action][2]:	# Action with parameter, store in fields
					fields[action] = a
			elif loption in fieldmappings:
				fields[loption] = a
			else:
				print_usage()
				sys.exit(2)
	
	if action is None:
		print_usage()
		sys.exit(2)
	else:
		return action, fields

def main():
	"""
	Main function
	"""
	
	global longactions, longmappings, dbname

	longactions, longmappings = add_index(actions, 0), add_index(fieldmappings, 0)
	action, fields = parse_arguments()
	
	if action is not None:
		try:
			connect_db()
		except sqlite3.OperationalError, e:
			print >> sys.stderr, "Can't open database file %s: %s" % (dbname, e.message)
			sys.exit(2)

	# Split e-mail address into localpart and domain fields
	if 'email' in fields: split_email(fields)
	
	try:
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
		elif action == 'show':
			show_field(fields)
	except sqlite3.IntegrityError, e:
		print >> sys.stderr, "SQL integrity error. Maybe the account does already exist? (%s)" % e.message
		sys.exit(2)
	except Exception, e:
		print >> sys.stderr, "Error:", e.message
		sys.exit(2)

if __name__ == "__main__":
	main()