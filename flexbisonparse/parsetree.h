
/**
 **  This file is part of the flex/bison-based parser for MediaWiki.
 **       This header file defines all types and functions used
 **                       throughout the parser.
 **
 ** This source file is licensed unter the GNU General Public License
 **                http://www.gnu.org/copyleft/gpl.html
 **                  Originally written 2004 by Timwi
 **/

typedef enum NodeType {
    Article, Paragraph, Heading, TextBlock, TextToken, ExtensionToken,
    Newlines, PreBlock, PreLine, Bold, Italics, Comment,
    LinkEtc, LinkTarget, LinkOption, Template, TemplateVar,
    Table, TableRow, TableCell, TableHead /* 20 */, TableCaption,
    Attribute, AttributeGroup,

    /* After first parse */
    ListBlock, ListLine, ListBullet, ListNumbered,

    /* After processList */
    List, ListItem
} NodeType;

typedef struct NameValueStruct
{
    char *  name;
    char *  value;
}
* NameValue;

/* During the parsing of table cells, we don't know in advance whether what we are currently
 * parsing are attributes for the table cell, or the table cell's textual contents. We parse
 * them as attributes first, but we use AttributeDataStruct to store enough data to allow us
 * to turn it back into text should we later find out they weren't attributes after all. */
typedef struct AttributeDataStruct
{
    char *  name;
    int     type;   /* 0 = just an attribute name; 1 = no quotes; 2 = '; 3 = " */
    int     spacesAfterName;
    int     spacesAfterEquals;
    int     spacesAfterValue;
}
* AttributeData;

typedef union DataType {
    char *          str;
    int             num;
    NameValue       nameval;
    AttributeData   attrdata;
} DataType;

typedef struct NodeStruct
{
    NodeType            type;
    DataType            data;
    struct NodeStruct * nextSibling;
    struct NodeStruct * firstChild;
}
* Node;

Node newNode (NodeType newType);
Node newNodeI (NodeType newType, int data);
Node newNodeS (NodeType newType, char* data);
Node newNodeN (NodeType newType, char* name, char* value, int copyname, int copyvalue); /* see NameValueStruct */

/* Used by the lexer to create a preliminary AttributeData object. */
AttributeData newAttributeDataFromStr (char* str);

/* Completes an AttributeData object created by newAttributeDataFromStr */
Node newNodeA (int t, AttributeData ad, int sae, int sav);

/* Return value of all of these is the first parameter */
Node nodeAddChild (Node node, Node child);
#define nodeAddChild2(a,b,c) nodeAddChild (nodeAddChild (a, b), c)
#define nodeAddChild3(a,b,c,d) nodeAddChild (nodeAddChild (nodeAddChild (a, b), c), d)
Node nodePrependChild (Node node, Node child);
Node nodeAddSibling (Node node, Node sibling);

/* Parameter must be a ListBlock node. Returns a List node. */
Node processListBlock (Node block);

/* Parameter must be a PreBlock node. Returns same */
Node processPreBlock (Node block);

/* Returns a TextToken, or null if n < 1 */
Node processEndHeadingInText (int n);

/* If 'node' is a paragraph node with no siblings, frees it and returns its child.
 * (We do this because if a table cell contains only text, we don't want it to
 * count as a "paragraph".) Otherwise just returns node. */
Node processTableCellContents (Node node);

/* If a is a TextBlock, adds b to it; if b is a TextBlock, prepends a;
 * if both are a TextBlock, adds b's children to a and frees b;
 * otherwise creates new TextBlock with a and b in it.
 * If any parameter is 0, returns the other. */
Node makeTextBlock (Node a, Node b);
#define makeTextBlock2(a,b,c) makeTextBlock (makeTextBlock (a, b), c)
#define makeTextBlock3(a,b,c,d) makeTextBlock (makeTextBlock (makeTextBlock (a, b), c), d)

/* Parameter must be a LinkOption node, optionally with a string of
 * siblings attached. These will all be freed, and a TextBlock returned. */
Node convertPipeSeriesToText (Node node);

/* Parameter must be a AttributeGroup node. It and its children will
 * all be freed, and a TextBlock returned. */
Node convertAttributesToText (Node node);

/* Parameter will be freed, and a TextBlock returned.
 * NOTICE: This will process ONLY the attribute name and the spaces after it. */
Node convertAttributeDataToText (AttributeData data);

/* These all return a TextToken node. */
Node convertTableRowToText (int info);
Node convertTableCellToText (int info);
Node convertTableHeadToText (int info);
Node convertTableCaptionToText (int info);
Node convertHeadingToText (int info);

/* Parameter must be a TextBlock. Turns something like
 * <italics>X<italics>Y</italics>Z</italics> into
 * <italics>X</italics>Y<italics>Z</italics>. Returns node. */
Node processNestedItalics (Node node);

void freeRecursively (Node node);
void freeRecursivelyWithSiblings (Node node);

char* outputXML (Node node, int initialBufferSize);

/* To store the output, outputXML() will use a dynamically-growing character buffer (char*).
 * The following routines manage such a buffer. */
void fb_create_new_buffer (int size);
void fb_write_to_buffer (const char* str);
void fb_write_to_buffer_len (const char* str, int len);
void fb_write_to_buffer_escaped (char* s);
char* fb_get_buffer();

/* More string helper routines ... */

/* e.g. addSpaces ("=", 2) => "=  " */
char* addSpaces (char* src, int spaces);
/* trims only *trailing* whitespace. Returns its parameter; does not create a new string. */
char* strtrim (char* src);
/* like strtrim, but returns the number of spaces removed. */
int strtrimC (char* src);
/* same as strtrim except takes a TextToken node */
Node strtrimN (Node src);
/* like strtrimN, but returns the number of spaces removed. */
int strtrimNC (Node src);
