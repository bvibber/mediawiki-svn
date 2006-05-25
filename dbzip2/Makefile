.PHONY: all clean test

all: dbzutil.so

clean:
	rm -f dbzutil.so

test: dbzutil.so
	python dbzutil-test.py
	python BitShifter.py

dbzutil.so: dbzutil-module.c dbzutil-setup.py
	python dbzutil-setup.py build_ext -i
