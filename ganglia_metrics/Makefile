SOURCES=__init__.py gmetricd.py GangliaMetrics.py MySQLStats.py SelectServer.py DiskStats.py
OBJECTS=gmetricd.pyo GangliaMetrics.pyo MySQLStats.pyo SelectServer.pyo DiskStats.pyo

all: $(OBJECTS)

clean: 
	rm -f $(OBJECTS)

%.pyo : %.py
	python -O -m py_compile $<

install:
	install -d $(DESTDIR)/usr/lib/ganglia-metrics
	install -m644 $(OBJECTS) $(SOURCES) $(DESTDIR)/usr/lib/ganglia-metrics
	install -d $(DESTDIR)/usr/sbin
	install -m755 gmetricd $(DESTDIR)/usr/sbin/gmetricd

