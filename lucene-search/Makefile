#
# Makefile to compile MWDaemon/MWSearch with the GCJ native compiler.
#
# You want a recent GCJ version (from CVS probably!) and may need to
# recompile the Cleaner class in je.jar with a bit removed.
#

.PHONY : all clean

all : MWSearch MWDaemon

clean :
	rm -f MWDaemon MWSearch *.o

lucene.o : lucene-1.4.3.jar
	gcj -c -o lucene.o lucene-1.4.3.jar 

je.o : je.jar
	gcj -c -o je.o je.jar

mysql-connector.o : mysql-connector-java-3.0.16-ga-bin.jar
	gcj -c -o mysql-connector.o mysql-connector-java-3.0.16-ga-bin.jar

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
	gcj --classpath=je.jar:lucene-1.4.3.jar \
	    -c -o MWSearch.o \
	    org/wikimedia/lsearch/*.java

MWSearch : MWSearch.o lucene.o je.o mysql-connector.o
	gcj -o MWSearch \
	--main=org.wikimedia.lsearch.MWSearch \
	-Djdbc.drivers=com.mysql.jdbc.Driver \
	MWSearch.o lucene.o je.o mysql-connector.o

MWDaemon : MWSearch.o lucene.o je.o mysql-connector.o
	gcj -o MWDaemon \
	--main=org.wikimedia.lsearch.MWDaemon \
	-Djdbc.drivers=com.mysql.jdbc.Driver \
	MWSearch.o lucene.o je.o mysql-connector.o
