all: $(LIBRARY)
$(LIBRARY): $(OBJS)
	@echo "	(ar) $(LIBRARY)"
	@ar cr $@ $^
	@ranlib $@

clean:
	rm -f $(LIBRARY) $(OBJS)


depend: $(SRCS)
	@echo "	creating dependencies..."
	@mv Makefile Makefile.depend
	@sed -e '/#DO NOT DELETE/,$$d' <Makefile.depend >Makefile
	@echo "#DO NOT DELETE -- make depend needs it" >>Makefile
	@-$(CXX) $(CPPFLAGS) $(CXXFLAGS) -MM $^ >>Makefile
	@rm -f Makefile.depend
