include ../mk/rules.mk

all: lib$(LIB).a
lib$(LIB).a: $(OBJS)
	rm -f $@
	ar crv $@ $(OBJS)
lint:
	$(LINT) -o$(LIB) $(LINTFLAGS) $(SRCS)
clean:
	rm -f lib$(LIB).a $(OBJS)
.PHONY: clean
