#include <antlr3.h>
#include <mwattributes.h>
#include <stdlib.h>
#include <glib.h>
#include <mwkeyvalue.h>
#include <stdbool.h>

#define UTF8_REPLACEMENT "\xef\xbf\xbd"

#define COMMON        "id", "class", "lang", "dir", "title", "style"
#define RDF          "about", "property", "resource", "datatype", "typeof"
#define MICRODATA    "itemid", "itemprop", "itemref", "itemscope", "itemtype"
#define BLOCK        "align", COMMON
#define TABLE_ALIGN  "align", "char", "charoff", "valign"
#define TABLE_CELL   "abbr",                    \
		     "axis",                    \
		     "headers",                 \
		     "scope",                   \
		     "rowspan",                 \
		     "colspan",                 \
                     "nowrap", /* deprecated */ \
                     "width",  /* deprecated */ \
		     "height", /* deprecated */ \
                     "bgcolor" /* deprecated */


static const char * const block[]       = { BLOCK };
static const char * const common[]      = { COMMON };
static const char * const block_quote[] = { BLOCK, "cite" };
static const char * const br[]          = { "id", "class", "title", "style", "clear" };
static const char * const pre[]         = { BLOCK, "width" };
static const char * const ins_del[]     = { BLOCK, "cite", "datetime" };
static const char * const ul[]          = { COMMON, "type" };
static const char * const ol[]          = { COMMON, "start" };
static const char * const li[]          = { COMMON, "value" };
static const char * const table[]       = { COMMON, "summary", "width", "border", "frame",
                                            "rules", "cellspacing", "cellpadding",
                                            "align", "bgcolor", };
static const char * const table_align[] = { COMMON, TABLE_ALIGN };
static const char * const col[]         = { COMMON, TABLE_ALIGN, "span", "width" };
static const char * const tr[]          = { COMMON, TABLE_ALIGN, "bgcolor" };
static const char * const cell[]        = { COMMON, TABLE_CELL, TABLE_ALIGN };
static const char * const a[]           = { COMMON, "href", "rel", "ref" };
static const char * const img[]         = { COMMON, "alt", "src", "width", "height" };
static const char * const font[]        = { COMMON, "size", "color", "face" };
static const char * const hr[]          = { COMMON, "noshade", "size", "width" };
static const char * const rt[]          = { COMMON, "rbspan" };
static const char * const math[]        = { "class", "style", "id", "title" };

typedef struct ELEMENT_ATTR ELEMENT_ATTR;

static struct ELEMENT_ATTR {
    const char * const name;
    size_t numAttributes;
    const char *const*const attributes;
} whitelist[] = 
    {
#define ATTR_LIST(name) sizeof(name)/sizeof(name[0]), name
        /* This must be kept in lexiographic order on the name */
        { "a",          ATTR_LIST(a)           },
        { "abbr",       ATTR_LIST(common)      },
        { "b",          ATTR_LIST(common)      },
        { "big",        ATTR_LIST(common)      },
        { "blockquote", ATTR_LIST(block_quote) },
        { "br",         ATTR_LIST(br)          },
        { "caption",    ATTR_LIST(block)       },
        { "center",     ATTR_LIST(common)      }, /* deprecated */
        { "cite",       ATTR_LIST(common)      },
        { "code",       ATTR_LIST(common)      },
        { "col",        ATTR_LIST(col)         },
        { "colgroup",   ATTR_LIST(col)         },
        { "del",        ATTR_LIST(ins_del)     },
        { "div",        ATTR_LIST(block)       },
        { "em",         ATTR_LIST(common)      },
        { "font",       ATTR_LIST(font)        },
        { "h1",         ATTR_LIST(block)       },
        { "h2",         ATTR_LIST(block)       },
        { "h3",         ATTR_LIST(block)       },
        { "h4",         ATTR_LIST(block)       },
        { "h5",         ATTR_LIST(block)       },
        { "h6",         ATTR_LIST(block)       },
        { "hr",         ATTR_LIST(hr)          },
        { "i",          ATTR_LIST(common)      },
        { "img",        ATTR_LIST(img)         },
        { "ins",        ATTR_LIST(ins_del)     },
        { "li",         ATTR_LIST(li)          },
        { "math",       ATTR_LIST(math)        },
        { "ol",         ATTR_LIST(ol)          },
        { "p",          ATTR_LIST(block)       },
        { "pre",        ATTR_LIST(pre)         },
        { "s",          ATTR_LIST(common)      },
        { "small",      ATTR_LIST(common)      },
        { "span",       ATTR_LIST(block)       }, /* ? */
        { "strike",     ATTR_LIST(common)      },
        { "strong",     ATTR_LIST(common)      },
        { "sub",        ATTR_LIST(common)      },
        { "sup",        ATTR_LIST(common)      },
        { "ruby",       ATTR_LIST(common)      },
        { "rb",         ATTR_LIST(common)      },
        { "rp",         ATTR_LIST(common)      },
        { "rt",         ATTR_LIST(rt)          },
        { "table",      ATTR_LIST(table)       },
        { "td",         ATTR_LIST(cell)        },
        { "th",         ATTR_LIST(cell)        },
        { "tr",         ATTR_LIST(tr)          },
        { "tt",         ATTR_LIST(common)      },
        { "tbody",      ATTR_LIST(table_align) },
        { "tfoot",      ATTR_LIST(table_align) },
        { "thead",      ATTR_LIST(table_align) },
        { "u",          ATTR_LIST(common)      },
        { "ul",         ATTR_LIST(ul)          },
        { "var",        ATTR_LIST(common)      },
    };


static int
cmpElementAttr(const void *a, const void *b)
{
    const struct ELEMENT_ATTR *e1 = a;
    const struct ELEMENT_ATTR *e2 = b;
    return strcmp(e1->name, e2->name);
}

static struct ELEMENT_ATTR *
lookupElement(const char * const name)
{
    const struct ELEMENT_ATTR key = { name, 0, NULL };
    return bsearch(&key, whitelist, sizeof(whitelist)/sizeof(struct ELEMENT_ATTR), sizeof(struct ELEMENT_ATTR), cmpElementAttr);
}

static gchar *
antlr3StringToGchar(pANTLR3_STRING s)
{
    return s->toUTF8(s)->chars;
}

static char *
antlr3StringToChar(pANTLR3_STRING s)
{
    return s->toUTF8(s)->chars;
}

static bool
findElement(ELEMENT_ATTR *ea, const char *attrName)
{
    int i;
    for (i = 0; i < ea->numAttributes; i++) {
        if (strcmp(ea->attributes[i], attrName) == 0) {
            return true;
        }
    }
    return false;
}

static void
validateElementAttr(GHashTable *table, ELEMENT_ATTR *ea, MWKEYVALUE *kv)
{
    char * key = antlr3StringToChar(kv->key);
    char * value = strdup(antlr3StringToChar(kv->value));
    if (!findElement(ea, key)) {
        return;
    }
    g_hash_table_insert(table, key, value);
}

static void
serializeAttributes(GHashTable *attributes, char **buf, size_t *len)
{
    GHashTableIter iter;
    GString *result = g_string_new("");
    g_hash_table_iter_init(&iter, attributes);
    gpointer key;
    gpointer value;
    while (g_hash_table_iter_next(&iter, &key, &value)) {
        g_string_append_c(result, ' ');
        g_string_append(result, key);
        g_string_append(result, "=\"");
        g_string_append(result, value);
        g_string_append_c(result, '"');
    }
    *len = result->len;
    *buf = g_string_free(result, FALSE);
}

/**
 * Output a string representation of all valid attributes to the buffer.
 *
 * @param buf pointer to storage space for pointer to returned buffer.  The caller must free the buffer with ANTLR3_FREE.
 * @param len pointer to storage space for returned buffer length.
 * @param attr The raw attributes.
 */
void
MWAttributesValidate(char **buf, size_t *len, const char *element, pANTLR3_VECTOR attr)
{
    GHashTable *validAttributes = g_hash_table_new_full(g_str_hash, g_str_equal, NULL, g_free);
    ELEMENT_ATTR *ea = lookupElement(element);
    if (ea != NULL) {
        int i;
        for (i = 0; i < attr->count; i++) {
            validateElementAttr(validAttributes, ea, attr->get(attr, i));
        }
    }
    serializeAttributes(validAttributes, buf, len);
    g_hash_table_unref(validAttributes);
}
