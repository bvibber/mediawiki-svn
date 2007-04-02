all: $(PROGRAM)

$(PROGRAM): $(OBJS)
	@echo "	(link) $(PROGRAM)"
	@$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ -o $@ $(LIBS)

clean:
	rm -f $(PROGRAM) $(OBJS) 
