/*
   And XML parser using libxml2 that checks whether or not a given xml file is
   valid returns EXIT_SUCCESS on success and EXIT_FAILURE on failure

   Usage: xml_parse file.xml

   Author: Ævar Arnfjörð Bjarmason <avarab@gmail.com>
   License: Public domain

   To compile use: gcc -o xml_parse `xml2-config --cflags` xml_parse.c `xml2-config --libs`
*/
#include <stdio.h>
#include <stdlib.h>

#include <libxml/xmlreader.h>

static void usage(const char *);
static int streamFile(const char *);

int main(const int argc, const char **argv)
{
	int ret;
	
	if (argc != 2)
		usage(argv[0]);

	LIBXML_TEST_VERSION

	ret = streamFile(argv[1]);

	xmlCleanupParser();

	xmlMemoryDump();
		
	return ret;
}

static void usage(const char *program)
{
	fprintf(stderr, "Usage: %s file\n", program);
	
	exit(EXIT_FAILURE);
}

static int streamFile(const char *filename)
{
	xmlTextReaderPtr reader;
	int ret;

	reader = xmlReaderForFile(filename, NULL, 0);

	if (reader != NULL) {
		do {
			ret = xmlTextReaderRead(reader);
		} while (ret == 1);

		xmlFreeTextReader(reader);

		if (ret != 0) {
			fprintf(stderr, "%s: failed to parse\n", filename);
			return EXIT_FAILURE;
		} else {
			fprintf(stderr, "%s: all OK\n", filename);
			return EXIT_SUCCESS;
		}
	} else {
		fprintf(stderr, "Unable to open %s\n", filename);
		return EXIT_FAILURE;
	}
}
