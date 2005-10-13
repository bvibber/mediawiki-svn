#!/usr/bin/python
#
# udpcast.py
# application level udp multicaster/multiplexer
# Written on 2005/04/03 by Mark Bergsma <mark@nedworks.org>
#
# $Id$

import socket, getopt, sys, os, signal

debugging = False

def createDaemon():
   """
   Detach a process from the controlling terminal and run it in the
   background as a daemon.
   """

   try:
      # Fork a child process so the parent can exit.  This will return control
      # to the command line or shell.  This is required so that the new process
      # is guaranteed not to be a process group leader.  We have this guarantee
      # because the process GID of the parent is inherited by the child, but
      # the child gets a new PID, making it impossible for its PID to equal its
      # PGID.
      pid = os.fork()
   except OSError, e:
      return((e.errno, e.strerror))     # ERROR (return a tuple)

   if (pid == 0):       # The first child.

      # Next we call os.setsid() to become the session leader of this new
      # session.  The process also becomes the process group leader of the
      # new process group.  Since a controlling terminal is associated with a
      # session, and this new session has not yet acquired a controlling
      # terminal our process now has no controlling terminal.  This shouldn't
      # fail, since we're guaranteed that the child is not a process group
      # leader.
      os.setsid()

      # When the first child terminates, all processes in the second child
      # are sent a SIGHUP, so it's ignored.
      signal.signal(signal.SIGHUP, signal.SIG_IGN)

      try:
         # Fork a second child to prevent zombies.  Since the first child is
         # a session leader without a controlling terminal, it's possible for
         # it to acquire one by opening a terminal in the future.  This second
         # fork guarantees that the child is no longer a session leader, thus
         # preventing the daemon from ever acquiring a controlling terminal.
         pid = os.fork()        # Fork a second child.
      except OSError, e:
         return((e.errno, e.strerror))  # ERROR (return a tuple)

      if (pid == 0):      # The second child.
         # Ensure that the daemon doesn't keep any directory in use.  Failure
         # to do this could make a filesystem unmountable.
         os.chdir("/")
         # Give the child complete control over permissions.
         os.umask(0)
      else:
         os._exit(0)      # Exit parent (the first child) of the second child.
   else:
      os._exit(0)         # Exit parent of the first child.

   # Close all open files.  Try the system configuration variable, SC_OPEN_MAX,
   # for the maximum number of open files to close.  If it doesn't exist, use
   # the default value (configurable).
   try:
      maxfd = os.sysconf("SC_OPEN_MAX")
   except (AttributeError, ValueError):
      maxfd = 256       # default maximum

   for fd in range(0, maxfd):
      try:
         os.close(fd)
      except OSError:   # ERROR (ignore)
         pass

   # Redirect the standard file descriptors to /dev/null.
   os.open("/dev/null", os.O_RDONLY)    # standard input (0)
   os.open("/dev/null", os.O_RDWR)       # standard output (1)
   os.open("/dev/null", os.O_RDWR)       # standard error (2)

   return(0)

def debug(msg):
    global debugging
    if debugging:
        print msg;

def multicast_diagrams(sock, addrrules):
    portnr = sock.getsockname()[1];

    while 1:
        diagram, srcaddr = sock.recvfrom(2**14)
        if not diagram: break

        try:
            addresses = addrrules[srcaddr[0]]
        except KeyError:
            addresses = addrrules[0]

        for addr in addresses:
            try:
                sock.sendto(diagram, 0, (addr, portnr))
                debug('Sent diagram to '+addr+' port '+str(portnr))
            except socket.error:
                debug('Error while sending diagram to '+addr)
                pass

def join_multicast_group(sock, multicast_group):
    import struct

    ip_mreq = struct.pack('!4sl', socket.inet_aton(multicast_group),
        socket.INADDR_ANY)
    sock.setsockopt(socket.IPPROTO_IP,
                    socket.IP_ADD_MEMBERSHIP,
                    ip_mreq)

def print_help():
    print 'Usage:\n\tudpmcast [ options ] { addresses | forward rules }\n'
    print 'Options:'
    print '\t-d\tFork into the background (become a daemon)'
    print '\t-p {portnr}\tUDP port number to listen on (default is 4827)'
    print '\t-j {multicast address}\tMulticast group to join on startup'
    print '\t-v\tBe more verbose'

if __name__ == '__main__':
    host = ''
    portnr = 4827
    multicast_group = None
    daemon = False
    opts = 'dhj:p:v'

    # Parse options
    options, arguments = getopt.getopt(sys.argv[1:], opts)
    if len(arguments) == 0:
        print_help()
        sys.exit()
    else:
        for option, value in options:
            if option == '-j':
                multicast_group = value
            elif option == '-p':
                portnr = int(value)
            elif option == '-h':
                print_help()
                sys.exit()
            elif option == '-d':
                daemon = True
            elif option == '-v':
                debugging = True

    try:
        # Become a daemon
        if daemon:
            createDaemon()

        # Open the UDP socket
        sock = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
        sock.bind((host, portnr))

        # Join a multicast group if requested
        if multicast_group != None:
            debug('Joining multicast group ' + multicast_group)
            join_multicast_group(sock, multicast_group)

        # Parse the argument list
        addrrules = { 0: [] }
        for argument in arguments:
            if argument[0] == '{':
                # Forward rule
                addrrules.update(eval(argument))
            else:
                # Default forward
                addrrules[0].append(argument)

        debug('Forward rules: ' + str(addrrules))

        # Multiplex everything that comes in
        multicast_diagrams(sock, addrrules)
    except socket.error, msg:
        print msg[1];
    except KeyboardInterrupt:
        pass

