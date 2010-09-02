#include <sys/types.h>
#include <sys/stat.h>
#include <fcntl.h>
#include <unistd.h>
#include <stdlib.h>
#include <stdio.h>
#include <antlr3.h>
#include <tracingcontext.h>
#include <sys/time.h>
#include <mwparser.h>

extern MWLISTENER mwParserTracingListener;

int main(int argc, char *const* argv)
{
    struct timeval start;
    gettimeofday(&start, NULL);

    MWPARSER *parser;
    MWPARSER_INPUT_STREAM *inputStream;

    int j;
    for (j = 0; j < 1; j++) {

    int i;
    for (i = 1; i < argc; i++) {
        inputStream = MWParserOpenFile(argv[i], MWPARSER_UTF8);
        if (inputStream == NULL) {
            return 1;
        }

        if (i == 1) {
            parser = MWParserNew(&mwParserTracingListener, inputStream);
            if (parser == NULL) {
                MWParserCloseInputStream(inputStream);
                return 1;
            }
        } else {
            MWParserReset(parser, inputStream);
        }

        MWParserParseArticle(parser);
        MWParserCloseInputStream(inputStream);
    }
    MWParserFree(parser);
    }
    struct timeval stop;
    gettimeofday(&stop, NULL);

    int diff_s = stop.tv_sec - start.tv_sec;
    int diff_us = stop.tv_usec - start.tv_usec;

    diff_us += diff_s * 1000000;
    //            fprintf(stderr, "Time: %d micro seconds\n", diff_us);

    return 0;
}
