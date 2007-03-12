SRCS	= conn.cc constants.cc error.cc field.cc log.cc query.cc row.cc 
OBJS	 =$(SRCS:.cc=.o)

all: liborapp.a

liborapp.a: $(OBJS)
	@rm -f $@
	ar rscv liborapp.a $(OBJS)

depend:
	@rm -f .depends
	@echo making dependencies...
	@$(CXX) -M -MG $(CXXFLAGS) $(INCLUDES) $(SRCS) > .depends

clean:
	rm -f *.o liborapp.a test

distclean: clean
	rm -f *~ core* .depends

include ../rules.mk
-include .depends
