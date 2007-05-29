include ../config.mk

ifeq ($(LIBTSUTILS),YES)
INCLUDES	+= -I../libtsutils
LIBS		+= -L../libtsutils -ltsutils
endif

.c.o:
	$(CC) $(CPPFLAGS) $(INCLUDES) $(CFLAGS) -c $<
