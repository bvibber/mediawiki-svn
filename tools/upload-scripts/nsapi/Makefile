CC		= cc
CFLAGS		= -O -g -fPIC -DPIC -D_REENTRANT -W -Wall
CPPFLAGS	= -I /opt/webserver7/include
LDFLAGS		= -shared
LIBS		= -lpcre
GLIB_CFLAGS	= `pkg-config glib-2.0 --cflags`
GLIB_LIBS	= `pkg-config glib-2.0 --libs`
CURL_CFLAGS	= `curl-config --cflags`
CURL_LIBS	= `curl-config --libs`

all: thumb-handler.so

thumb-handler.o: thumb-handler.c
	$(CC) $(CPPFLAGS) $(CFLAGS) $(GLIB_CFLAGS) $(CURL_CFLAGS) -c $<

thumb-handler.so: thumb-handler.o
	$(CC) $(CFLAGS) $(LDFLAGS) $< -o $@ $(LIBS) $(GLIB_LIBS) $(CURL_LIBS)

clean:
	rm -f thumb-handler.o thumb-handler.so
