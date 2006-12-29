all: SelectServer.pyo GangliaMetrics.pyo gmetricd.pyo

clean: 
	rm -f SelectServer.pyo GangliaMetrics.pyo gmetricd.pyo

%.pyo : %.py
	python -O -m py_compile $<

install:
	install -d $(DESTDIR)/usr/lib/ganglia-metrics
	install -m644 gmetricd.py gmetricd.pyo __init__.py SelectServer.py SelectServer.pyo GangliaMetrics.py GangliaMetrics.pyo $(DESTDIR)/usr/lib/ganglia-metrics
	install -d $(DESTDIR)/usr/sbin
	install -m755 gmetricd $(DESTDIR)/usr/sbin/gmetricd

