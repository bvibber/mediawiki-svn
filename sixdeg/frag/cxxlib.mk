all: $(LIBRARY)
$(LIBRARY): $(OBJS)
	@echo "	(ar) $(LIBRARY)"
	@ar cr $@ $^
	@ranlib $@

clean:
	rm -f $(LIBRARY) $(OBJS)
