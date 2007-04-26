#!/bin/sh
java -Djava.rmi.server.codebase=file://$PWD/MWSearch.jar -Djava.rmi.server.hostname=$1 -jar MWSearch.jar
