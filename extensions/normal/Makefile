INSTALL_TARGET = `php-config --extension-dir`/php_utfnormal.so
PRODUCT=utfnormal

# For Linux
SHARED = -shared

php_$(PRODUCT).so : $(PRODUCT).cpp $(PRODUCT)_wrap.cpp
	g++ -O2 `php-config --includes` -licuuc $(SHARED) -o php_$(PRODUCT).so $(PRODUCT).cpp $(PRODUCT)_wrap.cpp

$(PRODUCT)_wrap.cpp : $(PRODUCT).i
	swig -php4 -c++ $(PRODUCT).i

install : php_$(PRODUCT).so
	cp php_$(PRODUCT).so "$(INSTALL_TARGET)"

uninstall :
	rm -f "$(INSTALL_TARGET)"

clean :
	rm -f php_$(PRODUCT).so
	rm -f $(PRODUCT)_wrap.cpp
	rm -f $(PRODUCT).php
	rm -f php_$(PRODUCT).h

test : php_$(PRODUCT).so
	php test.php
