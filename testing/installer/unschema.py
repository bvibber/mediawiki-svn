# (C) 2009 Kim Bruning, Distributed under the terms of the MIT license (see LICENSE file for details)
#
# pseudo-parse schema.sql files, figure out what tables we want to drop, and drop them
import re
import os
import tempfile
import installer_util

class SchemaException(Exception):
	pass

def file2statements(filename):
	"""read file specified by filename, strip comments,
	and return an array of what is hopefully statements.
	(Known error: escaped or quoted ';' will cause this
	code to break. This may not be a big deal, since we're
	only seeking particular statements)"""

	if not os.path.exists(filename):
		raise SchemaException("file '"+filename+"' not found.")
	data=file(filename).read()

	#strip comments
	
	lines=data.split("\n")# convenience transform to array of lines
	i=0
	while (i<len(lines)):
		if lines[i].startswith("--"):
			lines.pop(i)
		i+=1
	
	# transform to array of statements
	data=" ".join(lines)
	statements=data.split(";")
	
	return statements


def create2drop(statements):
	"""take a list of SQL statements, find the CREATE statements, and generate the corresponding DROP statements."""	
	drops=""
	createre=re.compile(".*CREATE TABLE(.*?)\(.*")
	for statement in statements:
		if "CREATE TABLE" in statement:
			matches=createre.match(statement)
			table_name=matches.group(1).strip()
			drops+="DROP TABLE IF EXISTS "+table_name+";\n"
	return drops

def exec_drops(drops,instancedir):
	"""Execute the SQL code to drop tables, using sql.php"""
	# python 2.3-2.5
	dropfilename=tempfile.mktemp(suffix=".sql")
	dropfile=file(dropfilename,"w")
	# dropfile=tempfile.NamedTemporaryFile(delete=False) python 2.6 or later
	dropfile.write(drops)
	dropfile.close()
	try:
		installer_util.sqldotphp(instancedir,dropfile.name)	
	except Exception,e:
		raise SchemaException("exec_drops: issue occurred while dropping table",e)
	finally: #Get rid of the file if it's still there
		if os.path.exists(dropfile.name):
			os.unlink(dropfile.name)


def unschema(instancedir,filename):
	statements=file2statements(filename)
	drops=create2drop(statements)
	exec_drops(drops,instancedir)
	
if __name__=="__main__":
	statements=file2statements("./schema.sql")
	print statements
	drops=create2drop(statements)
	print drops
