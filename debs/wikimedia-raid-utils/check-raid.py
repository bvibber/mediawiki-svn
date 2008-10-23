#!/usr/bin/python

import sys, os, re, subprocess

def main():
	f = open("/proc/devices", "r")
	regex = re.compile('^\s*\d+\s+(\w+)')
	utility = None
	for line in f:
		m = regex.match(line)
		if m == None:
			continue
		name = m.group(1)
		
		if name == 'aac':
			utility = 'arcconf'
			break
		elif name == 'twe':
			utility = 'tw_cli'
			break
		elif name == 'megadev':
			utility = 'megarc'
			break
		elif name == 'megaraid_sas_ioctl':
			utility = 'MegaCli'

	f.close()

	try:
		if utility == None:
			print 'OK: no RAID installed'
			status = 0
		elif utility == 'arcconf':
			status = checkAdaptec()
		elif utility == 'tw_cli':
			status = check3ware()
		else:
			print 'WARNING: %s is not yet supported by this check script' % (utility)
			status = 1
	except:
		error = sys.exc_info()[1]
		print 'WARNING: check-raid.py encountered exception: ' + str(error)
		status = 1
	
	sys.exit(status)

def checkAdaptec():
	# Need to change directory so that the log file goes to the right place
	oldDir = os.getcwd()
	os.chdir('/var/log')

	# Run the command
	try:
		proc = subprocess.Popen(['/usr/bin/arcconf', 'getconfig', '1'], 
				stdout = subprocess.PIPE)
	except:
		print 'WARNING: Unable to execute arcconf'
		os.chdir(oldDir)
		return 1

	defunctRegex = re.compile('^\s*Defunct disk drive count\s*:\s*(\d+)')
	logicalRegex = re.compile('^\s*Logical devices/Failed/Degraded\s*:\s*(\d+)/(\d+)/(\d+)')
	status = 0
	numLogical = None
	for line in proc.stdout:
		m = defunctRegex.match(line)
		if m != None and m.group(1) != '0':
			print 'CRITICAL: Defunct disk drive count: ' + m.group(1)
			status = 2
			break

		m = logicalRegex.match(line)
		if m != None:
			numLogical = int(m.group(1))
			if m.group(2) != '0' and m.group(3) != '0':
				print 'CRITICAL: logical devices: %s failed and %s defunct' % \
					(m.group(2), m.group(3))
				status = 2
				break
			if m.group(2) != '0':
				print 'CRITICAL: logical devices: %s failed' % \
					(m.group(2))
				status = 2
				break
			if m.group(3) != '0':
				print 'CRITICAL: logical devices: %s defunct' % \
					(m.group(3))
				status = 2
				break

	ret = proc.wait()
	if status == 0 and ret != 0:
		print 'WARNING: arcconf returned exit status %d' % (ret)
		status = 1

	if status == 0 and numLogical == None:
		print 'WARNING: unable to parse output from arcconf'
		status = 1
	
	if status == 0:
		print 'OK: %d logical device(s) checked' % numLogical

	os.chdir(oldDir)
	return status


def check3ware():
	# Get the list of controllers
	try:
		proc = subprocess.Popen(['/usr/bin/tw_cli', 'show'], stdout = subprocess.PIPE)
	except:
		print 'WARNING: error executing tw_cli'
		return 1

	regex = re.compile('^(c\d+)')
	controllers = []
	for line in proc.stdout:
		m = regex.match(line)
		if m != None:
			controllers.push('/' + m.group(1))
	
	ret = proc.wait()
	if ret != 0:
		print 'WARNING: tw_cli returned exit status %d' % (ret)
		return 1

	# Check each controller
	regex = re.compile('^(p\d+)\s+([\w-]+)')
	failedDrives = []
	numDrives = 0
	for controller in controllers:
		proc = subprocess.Popen(['/usr/bin/tw_cli', controller, 'show'],
				stdout = subprocess.PIPE)
		for line in proc.stdout():
			m = regex.match(line)
			if m != None:
				numDrives += 1
				if m.group(2) != 'OK':
					failedDrives.push(controller + '/' + m.group(1))

		proc.wait()
	
	if len(failedDrives) != 0:
		print 'CRITICAL: %d failed drive(s): %s' % \
				(len(failedDrives), ', '.join(failedDrives) )
		return 2

	if numDrives == 0:
		print 'WARNING: no physical drives found, tw_cli parse error?'
		return 1
	else:
		print 'OK: %d drives checked' % numDrives
		return 0

main()
