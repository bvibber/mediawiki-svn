#
# Makefile to compile MWDaemon/MWSearch with the GCJ native compiler.
#


# Currently GCC 4.0 is required; older versions had an incomplete
# class library and will die in various places.
# Pull a snapshot or CVS: http://gcc.gnu.org/cvs.html
#GCJ=gcj
GCJ=gcj-4.0

# Optimization is faster!
OPTS=-O2

# Get Apache Lucene from http://lucene.apache.org/
LUCENE=lucene-1.4.3.jar

# oh woe is me
BERKELEYDB=je.jar

# MySQL Connector/J 3.1 doesn't compile right; it wants log4j or such
# Get 3.0: http://dev.mysql.com/downloads/connector/j/3.0.html
MYSQL=mysql-connector-java-3.0.16-ga-bin.jar

#OBJECTS=MWSearch.o lucene.o mysql-connector.o je.o
OBJECTS=MWSearch.o lucene.o mysql-connector.o

RUNTIMEOPTS=-Djdbc.drivers=com.mysql.jdbc.Driver

SOURCES=org/wikimedia/lsearch/Article.java \
        org/wikimedia/lsearch/ArticleList.java \
        org/wikimedia/lsearch/Configuration.java \
        org/wikimedia/lsearch/DatabaseConnection.java \
        org/wikimedia/lsearch/EnglishAnalyzer.java \
        org/wikimedia/lsearch/EsperantoAnalyzer.java \
        org/wikimedia/lsearch/EsperantoStemFilter.java \
        org/wikimedia/lsearch/MWDaemon.java \
        org/wikimedia/lsearch/MWSearch.java \
        org/wikimedia/lsearch/NamespaceFilter.java \
        org/wikimedia/lsearch/QueryStringMap.java \
        org/wikimedia/lsearch/SearchClientReader.java \
        org/wikimedia/lsearch/SearchDbException.java \
        org/wikimedia/lsearch/SearchState.java \
        org/wikimedia/lsearch/Title.java \
        org/wikimedia/lsearch/TitlePrefixMatcher.java

.PHONY : all clean

all : MWSearch MWDaemon

clean :
	rm -f MWDaemon MWSearch *.o

lucene.o : $(LUCENE)
	$(GCJ) -c $(OPTS) -o lucene.o $(LUCENE)

mysql-connector.o : $(MYSQL)
	$(GCJ) -c $(OPTS) -o mysql-connector.o $(MYSQL)

MWSearch.o : $(SOURCES)
	$(GCJ) --classpath=$(LUCENE):$(BERKELEYDB) \
	    -c $(OPTS) -o MWSearch.o \
	    $(SOURCES)

MWSearch : $(OBJECTS)
	$(GCJ) -o MWSearch \
	--main=org.wikimedia.lsearch.MWSearch \
	$(RUNTIMEOPTS) \
	$(OBJECTS)

MWDaemon : $(OBJECTS)
	$(GCJ) -o MWDaemon \
	--main=org.wikimedia.lsearch.MWDaemon \
	$(RUNTIMEOPTS) \
	$(OBJECTS)
