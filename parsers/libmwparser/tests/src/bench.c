#include <antlr3.h>
#include <sys/time.h>
#include <mwparser.h>
#include <mwlistener.h>

extern MWLISTENER mwParserTracingListener;

int main(int argc, const char **argv)
{
    MWPARSER *parser;
    MWPARSER_INPUT_STREAM *inputStream;
    inputStream = MWParserOpenString("input", "hello", strlen("hello"), MWPARSER_UTF8);
    parser = MWParserNew(&mwParserTracingListener, inputStream);
        
    struct timeval start;
    gettimeofday(&start, NULL);

    int n = 100000;

    int i;
    for (i = 0; i < n; i++) {
        inputStream = MWParserOpenString("input", "hello", strlen("hello"), MWPARSER_UTF8);
        MWParserReset(parser, inputStream);
        MWParserParseArticle(parser, NULL);
        MWParserCloseInputStream(inputStream);
    }

    struct timeval stop;
    gettimeofday(&stop, NULL);
    MWParserFree(parser);

    int diff_s = stop.tv_sec - start.tv_sec;
    int diff_us = stop.tv_usec - start.tv_usec;

    diff_us += diff_s * 1000000;
    double ms = (double)diff_us / 1000;
    fprintf(stderr, "Time: %lf milli seconds\n", ms/n);
}
