#
# Makefile to compile MWDaemon/MWSearch with the GCJ native compiler.
#
# You want a recent GCJ version (from CVS probably!) and may need to
# recompile the Cleaner class in je.jar with a bit removed.
#

#GCJ=gcj
#GCJ=gcj-3.4
GCJ=gcj-4.0

#OPTS=
OPTS=-O2

.PHONY : all clean

all : MWSearch MWDaemon

clean :
	rm -f MWDaemon MWSearch *.o

lucene.o : lucene-1.4.3.jar
	$(GCJ) -c $(OPTS) -o lucene.o lucene-1.4.3.jar 

#je.o : je.jar
#	$(GCJ) -c $(OPTS) -o je.o je.jar

mysql-connector.o : mysql-connector-java-3.0.16-ga-bin.jar
	$(GCJ) -c $(OPTS) -o mysql-connector.o mysql-connector-java-3.0.16-ga-bin.jar

MWSearch.o : org/wikimedia/lsearch/Article.java \
        org/wikimedia/lsearch/ArticleIterator.java \
        org/wikimedia/lsearch/ArticleList.java \
        org/wikimedia/lsearch/Configuration.java \
        org/wikimedia/lsearch/DatabaseConnection.java \
        org/wikimedia/lsearch/EnglishAnalyzer.java \
        org/wikimedia/lsearch/MWDaemon.java \
        org/wikimedia/lsearch/MWSearch.java \
        org/wikimedia/lsearch/SearchClientReader.java \
        org/wikimedia/lsearch/SearchDatabase.java \
        org/wikimedia/lsearch/SearchIndexUpdater.java \
        org/wikimedia/lsearch/SearchState.java \
        org/wikimedia/lsearch/Title.java \
        org/wikimedia/lsearch/TitlePrefixMatcher.java \
        org/wikimedia/lsearch/UserInteraction.java
	$(GCJ) --classpath=lucene-1.4.3.jar \
	    -c $(OPTS) -o MWSearch.o \
	    org/wikimedia/lsearch/*.java

MWSearch : MWSearch.o lucene.o mysql-connector.o
	$(GCJ) -o MWSearch \
	--main=org.wikimedia.lsearch.MWSearch \
	-Djdbc.drivers=com.mysql.jdbc.Driver \
	MWSearch.o lucene.o mysql-connector.o

MWDaemon : MWSearch.o lucene.o mysql-connector.o
	$(GCJ) -o MWDaemon \
	--main=org.wikimedia.lsearch.MWDaemon \
	-Djdbc.drivers=com.mysql.jdbc.Driver \
	MWSearch.o lucene.o mysql-connector.o
