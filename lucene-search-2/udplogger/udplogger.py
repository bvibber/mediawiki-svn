import socket, traceback

file = 'searchqueries'

host = ''
port = 51234

s = socket.socket(socket.AF_INET, socket.SOCK_DGRAM)
s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
s.bind((host, port))

out = open(file, 'a')

print('Logging search queries on UDP port %d into file %s' % (port,file))

while 1:
    try:
        # receive a message and write it to log file
        message, address = s.recvfrom(8192)
        out.write(message)
        out.flush()
    except (KeyboardInterrupt, SystemExit):
        out.close()
        raise
    except:
        traceback.print_exc()



