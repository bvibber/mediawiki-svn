#ifndef MWPARSER_H_
#define MWPARSER_H_

#include <stdbool.h>
#include <wchar.h>
#include <mwtagext.h>

struct MWLISTENER_struct;

/**
 * Character encoding.
 */
typedef enum MWPARSER_ENCODING {
    MWPARSER_8BIT,
    MWPARSER_UTF8,
    MWPARSER_UTF16,
    MWPARSER_UTF16LE,
    MWPARSER_UTF16BE,
    MWPARSER_UTF32,
    MWPARSER_UTF32LE,
    MWPARSER_UTF32BE,
} MWPARSER_ENCODING;

/**
 * A data structure representing a parser instance.
 */
typedef struct MWPARSER_struct MWPARSER;

/**
 * A data structure representing an input stream for the parser.
 */
typedef struct MWPARSER_INPUT_STREAM_struct MWPARSER_INPUT_STREAM;

/**
 * Options that alters the behavior of a particular parser run.  These
 * should be set as the result of preprocessing.
 */
typedef struct MWPARSER_OPTIONS_struct MWPARSER_OPTIONS;

/**
 * Constructor for the parser.
 * @param listener A listener for this parser instance.
 * The contents of the listener structure will be copied verbatim.
 * @return A new instance, or NULL on failure.
 */
MWPARSER *MWParserNew(const struct MWLISTENER_struct *listener, MWPARSER_INPUT_STREAM *inputStream);

/**
 * Reset a parser instance and set the input stream.
 * @param parser pointer to parser instance.
 * @param inputStream pointer to input stream instance.
 */
void MWParserReset(MWPARSER *parser, MWPARSER_INPUT_STREAM *inputStream);
/**
 * Free the resources used by the parser instance.
 * @param parser pointer to parser instance.
 */
void MWParserFree(MWPARSER *parser);

/**
 * Top level entrance point to the parser.  Will treat the input
 * stream as a whole MediaWiki article.
 *
 * Since any input is a valid article, the parser will never fail
 * because of bad input.  It could, however, potentially fail due to
 * lack of memory.  This is not exposed here, though.  The consequence
 * of running out of memory will be a segmentation violation due to
 * dereferencing a NULL pointer.  The caller may catch this by
 * installing the appropriate signal handler.
 * 
 * @param parser pointer to parser instance
 */
void MWParserParseArticle(MWPARSER *parser, MWPARSER_OPTIONS *options);

/**
 * Open an input stream that reads from a character buffer.
 * @param string Character buffer.
 * @param encoding The character encoding of the buffer.
 */
MWPARSER_INPUT_STREAM * MWParserOpenString(char *name, char *string, size_t size, MWPARSER_ENCODING encoding);

/**
 * Open an input stream that reads from a file.
 * @param fileName The name of the file.
 * @param encoding The character encoding of the buffer.
 */
MWPARSER_INPUT_STREAM * MWParserOpenFile(char *fileName, MWPARSER_ENCODING encoding);

/**
 * Close an input stream.
 * @param stream Pointer to the input stream.
 */
void MWParserCloseInputStream(MWPARSER_INPUT_STREAM *stream);

/**
 * Set a regexp that validates a document title.
 * The default is L"^[- %!\"$&'()*,./0-9:;=?@A-Z\\\\^_`a-z~\x80-\xFF+]+$"
 * @param parser Pointer to parser instance.
 * @param perlRegexp A perl regular expression.
 * @return {\code true} on success.
 */
bool MWParserSetLegalTitleRegexp(MWPARSER *parser, const char *perlRegexp);

/**
 * Set a regexp that identifies a link as being a media link.
 * @param parser Pointer to parser instance.
 * @param perlRegexp A perl regular expression.
 * @return {\code true} on success.
 */
bool MWParserSetMediaLinkTitleRegexp(MWPARSER *parser, const char *perlRegexp);

/**
 * Register a tag extension with the parser.
 * @param parser Pointer to parser instance.
 * @param tagExtension Structure that describes the tag extension.
 * @return {\code true} on success.
 */
bool MWParserRegisterTagExtension(MWPARSER *parser, const MWPARSER_TAGEXT *tagExtension);

/**
 * Return a pointer to a resulting output buffer or equivalent.  The
 * contents of this buffer is specific to the listener implementation.
 * The listener implementation does not necessarily provide any such
 * buffer.
 *
 * @param parser
 * @return Pointer to listener specific output buffer or similar.
 */
void * MWParserGetResult(MWPARSER *parser);

#endif
