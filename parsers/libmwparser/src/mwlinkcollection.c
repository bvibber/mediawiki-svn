/*
 * Copyright 2010  Andreas Jonsson
 *
 * This file is part of libmwparser.
 *
 * Libmwparser is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

#include <glib.h>
#include <antlr3.h>
#include <mwlinkcollection.h>
#include <assert.h>

struct MWLINKCOLLECTION_struct
{
    GTree *tree;
    GTree *inverseTree;
    GList *tokens;
};

typedef struct LCKEY_struct {
    MWLINKTYPE type;
    pANTLR3_STRING linkTitle;
} LCKEY;

static LCKEY *
lckeyNew(MWLINKTYPE type, pANTLR3_STRING linkTitle)
{
    LCKEY *key = ANTLR3_MALLOC(sizeof(*key));
    if (key == NULL) {
        return NULL;
    }
    key->type = type;
    key->linkTitle = linkTitle;
    return key;
}

static void
lckeyFree(void *lckey)
{
    ANTLR3_FREE(lckey);
}

static gint
lckeyCmp(gconstpointer a, gconstpointer b, gpointer unused)
{
    const LCKEY *k1 = a;
    const LCKEY *k2 = b;

    if (k1->type != k2->type) {
        return (gint)k1->type - (gint)k2->type;
    }

    assert(k1->linkTitle->encoding == k2->linkTitle->encoding);

    if (k1->linkTitle->len != k2->linkTitle->len) {
        return (gint)k2->linkTitle->len - (gint)k1->linkTitle->len;
    }

    return strncmp((char *)k1->linkTitle->chars, (char *)k2->linkTitle->chars, k1->linkTitle->len);
}

typedef struct LCVAL_struct {
    GList *tokenList;
} LCVAL;

static LCVAL *
lcvalNew()
{
    LCVAL *val = ANTLR3_MALLOC(sizeof(*val));
    if (val == NULL) {
        return NULL;
    }
    val->tokenList = NULL;
    return val;
}

static gint lcvalCmp(gconstpointer a, gconstpointer b)
{
    return b - a;
}

static void
lcvalFree(void *lcval)
{
    LCVAL *val = lcval;
    if (val->tokenList != NULL) {
        g_list_free(val->tokenList);
    }
    ANTLR3_FREE(val);
}

MWLINKCOLLECTION *MWLinkCollectionNew()
{
    MWLINKCOLLECTION *collection = ANTLR3_MALLOC(sizeof(*collection));
    if (collection == NULL) {
        return NULL;
    }
    collection->tree = g_tree_new_full(lckeyCmp, NULL, lckeyFree, lcvalFree);
    if (collection->tree == NULL) {
        ANTLR3_FREE(collection);
        return NULL;
    }

    collection->inverseTree = g_tree_new(lcvalCmp);
    if (collection->tree == NULL) {
        g_tree_unref(collection->tree);
        ANTLR3_FREE(collection);
        return NULL;
    }

    collection->tokens = NULL;

    return collection;
}

void MWLinkCollectionFree(void *linkCollection)
{
    MWLINKCOLLECTION *collection = linkCollection;

    g_tree_unref(collection->tree);
    g_tree_unref(collection->inverseTree);
    if (collection->tokens != NULL) {
        g_list_free(collection->tokens);
    }

    ANTLR3_FREE(collection);
}

void
MWLinkCollectionAdd(MWLINKCOLLECTION *collection, MWLINKTYPE type, pANTLR3_STRING link, pANTLR3_COMMON_TOKEN token)
{
    LCKEY *key = lckeyNew(type, link);
    gpointer origKey;
    gpointer pVal;
    gboolean found = g_tree_lookup_extended(collection->tree, key, &origKey, &pVal);
    LCVAL *val = pVal;
    if (!found) {
        val = lcvalNew();
        g_tree_insert(collection->tree, key, val);
    } else {
        lckeyFree(key);
        key = origKey;
    }
    val->tokenList = g_list_prepend(val->tokenList, token);
    collection->tokens = g_list_prepend(collection->tokens, val->tokenList);
    g_tree_insert(collection->inverseTree, val->tokenList, key);
}

MWLINKCOLLECTION_MARK
MWLinkCollectionMark(MWLINKCOLLECTION *collection)
{
    return collection->tokens;
}

void
MWLinkCollectionRewind(MWLINKCOLLECTION *collection, MWLINKCOLLECTION_MARK mark)
{
    gboolean last;
    do {
        last = collection->tokens == mark;
        if (collection->tokens != NULL) {
            GList *node = collection->tokens->data;
            collection->tokens = g_list_delete_link(collection->tokens, collection->tokens);
            LCKEY *key = g_tree_lookup(collection->inverseTree, node);
            g_tree_remove(collection->inverseTree, node);
            assert(key != NULL);
            LCVAL *val = g_tree_lookup(collection->tree, key);
            val->tokenList = g_list_remove(val->tokenList, node);
            if (val->tokenList == NULL) {
                g_tree_remove(collection->tree, key);
            }
        }
    } while(!last);
}

struct CALLBACKDATA {
    int (*callback)(MWLCKEY *key, void *data);
    void *data;
};

static gboolean
callCallback(gpointer key, gpointer value, gpointer data)
{
    struct CALLBACKDATA *cb = data;
    return cb->callback(key, cb->data);
}

void
MWLinkCollectionTraverse(MWLINKCOLLECTION *linkCollection,
                         int (*callback)(MWLCKEY *key, void *data),
                         void *callbackData)
{
    struct CALLBACKDATA cb = { callback, callbackData };
    g_tree_traverse(linkCollection->tree, callCallback, G_IN_ORDER, &cb);
}

static void setMediaLinkResolution(gpointer data, gpointer userdata)
{
    pANTLR3_COMMON_TOKEN t = data;
    MWLINKRESOLUTION *resolution = userdata;
    pANTLR3_VECTOR attr = t->custom;
    attr->set(attr, attr->count - 2, resolution, MWLinkResolutionFree, true);
}

static void setLinkResolution(gpointer data, gpointer userdata)
{
    pANTLR3_COMMON_TOKEN t = data;
    MWLINKRESOLUTION *resolution = userdata;
    pANTLR3_VECTOR attr = t->custom;
    attr->set(attr, attr->count - 1, resolution, MWLinkResolutionFree, true);
}

void
MWLinkCollectionResolve(MWLINKCOLLECTION *linkCollection, MWLCKEY *key, MWLINKRESOLUTION *resolution)
{
    LCVAL *val = g_tree_lookup(linkCollection->tree, key);
    if (val != NULL) {
        if (key->type == MWLT_MEDIA) {
            g_list_foreach(val->tokenList, setMediaLinkResolution, resolution);
        } else {
            g_list_foreach(val->tokenList, setLinkResolution, resolution);
        }
    }
}

const char *
MWLCKeyGetLinkTitle(MWLCKEY *lckey)
{
    return (char*)lckey->linkTitle->chars;
}

MWLINKTYPE
MWLCKeyGetLinkType(MWLCKEY *lckey)
{
    return lckey->type;
}

int
MWLinkCollectionNumLinks(MWLINKCOLLECTION *collection)
{
    return g_tree_nnodes(collection->tree);
}
