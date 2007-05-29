include ../mk/rules.mk

all: $(PROG)
$(PROG): $(OBJS)
	$(CC) $(CFLAGS) $(LDFLAGS) $(OBJS) -o $@ $(LIBS)
lint:
	$(LINT) $(LINTFLAGS) $(INCLUDES) $(SRCS) $(LIBS)
clean:
	rm -f $(PROG) $(OBJS)

.PHONY: clean
