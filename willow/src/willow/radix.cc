/* 
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
	This product includes software developed by the University of Michigan,
	Merit Network, Inc., and their contributors. 
 * 
 * See the radix.h header for more information.
 * Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>
 */
/* From: Id: radix.c,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* $Id$ */

#include <cstdio>
#include <cstring>
#include <cstdlib>
using std::sprintf;

#include "willow.h"
#include "radix.h"

char *
prefix::tostring (void)
{
static char	ret[100];
char		ipstr[100];
	inet_ntop(family, (void*)&add, ipstr, sizeof(ipstr));
	snprintf(ret, 100, "%s/%d", ipstr, prefixlen);
	return ret;
}

static int 
comp_with_mask (void *addr, void *dest, uint32_t mask)
{
	if ( /* mask/8 == 0 || */ memcmp (addr, dest, mask / 8) == 0) {
		int n = mask / 8;
		int m = ((-1) << (8 - (mask % 8)));
		if (mask % 8 == 0 || (((uint8_t *)addr)[n] & m) == (((uint8_t *)dest)[n] & m))
			return (1);
	}
	return (0);
}

static uint8_t *
prefix_tochar (const struct prefix * prefix)
{
	if (prefix == NULL)
		return (NULL);

	return ((uint8_t *) & prefix->add.sin4);
}

extern struct prefix *
prefix_new (int family, void *dest, int prefixlen)
{
	struct prefix *prefix;

	prefix = new ::prefix;

	if (family == AF_INET6) {
		memcpy (&prefix->add.sin6, dest, 16);
	} else if (family == AF_INET) {
		memcpy (&prefix->add.sin4, dest, 4);
	} else {
		return NULL;
	}
	prefix->prefixlen = prefixlen;
	prefix->family = family;
	prefix->ref_count = 0;

	return prefix;
}

prefix::prefix(void)
	: family(0)
	, prefixlen(0)
	, ref_count(0)
{
	memset(&add, 0, sizeof(add));
}

static void
prefix_destroy (struct prefix *prefix)
{
	if (!prefix)
		return;
	delete prefix;
	return;
}


extern struct prefix *
prefix_fromstring (const char *string, struct prefix *prefix)
{
	char *cp;
	char prefixstr[64];

	if (!string)
		return NULL;

	if (!prefix)
		return NULL;
			
	if (strchr (string, ':')) {
		prefix->prefixlen = 128;
		prefix->family = AF_INET6;
	} else if (strchr (string, '.')) {
		prefix->family = AF_INET;
		prefix->prefixlen = 32;
	} else {
		return NULL;
	}

	if ((cp = strchr (string, '/')) != NULL) {
		prefix->prefixlen = atol (cp+1);
		memcpy (prefixstr, string, cp-string);
		prefixstr[cp-string] = '\0';
	} else {
		strlcpy (prefixstr, string, sizeof (prefixstr));
	}

	if (inet_pton (prefix->family, prefixstr, &prefix->add) != 1)
		return NULL;
	return prefix;
}


static void
prefix_deref (struct prefix *prefix)
{
	if (!prefix)
		return;
/*	printf ("prefix_deref: ref_count=%d\n", prefix->ref_count); */
	assert (prefix->ref_count > 0);
	prefix->ref_count--;
	if (prefix->ref_count == 0)
		prefix_destroy (prefix);
	return;
}

static struct prefix *
prefix_ref (struct prefix *prefix)
{
	if (!prefix)
		return NULL;

/*	printf ("prefix_ref: ref_count=%d\n", prefix->ref_count); */
	prefix->ref_count++;
	return prefix;
}


radix::radix(void)
	: head(NULL)
	, maxbits(128)
	, num_active_node(0)
{
}

extern struct radix_node *
radix_search (const struct radix *radix, const char *prefixstr)
{
	int inclusive = 1;
	struct radix_node *node;
	struct radix_node *stack[RADIX_MAXBITS + 1];
	struct prefix prefix;
	uint8_t *addr;
	int cnt = 0;

	if (!radix)
		return NULL;
	if (!prefixstr)
		return NULL;

	if (!prefix_fromstring(prefixstr, &prefix))
		return NULL;

	if (prefix.prefixlen > radix->maxbits) {
		return NULL;
	}

	if (radix->head == NULL) {
		return (NULL);
	}

	node = radix->head;
	addr = prefix_tochar (&prefix);

	while (node->bit < prefix.prefixlen) {
		if (node->prefix) {
			stack[cnt++] = node;
		}

		if (BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			break;
	}

	if (inclusive && node && node->prefix)
		stack[cnt++] = node;

	if (cnt <= 0) {
		return (NULL);
	}

	while (--cnt >= 0) {
		node = stack[cnt];
        if (comp_with_mask (prefix_tochar (node->prefix), prefix_tochar (&prefix), node->prefix->prefixlen)) { 
			return node;
		}
	}
	return NULL;
}

static void
radix_clear (struct radix *radix, void_fn_t func)
{
	assert (radix);

	if (radix->head) {
		struct radix_node *Xstack[RADIX_MAXBITS+1];
        struct radix_node **Xsp = Xstack;
        struct radix_node *Xrn = radix->head;

		while (Xrn) {
			struct radix_node *l = Xrn->l;
			struct radix_node *r = Xrn->r;

			if (Xrn->prefix) {
				prefix_deref (Xrn->prefix);
				if (Xrn->data && func)
					((void (*)(void *))func) (Xrn->data);
			} else {
				assert (NULL == Xrn->data);
			}
			delete Xrn;
			radix->num_active_node--;
			if (l) {
				if (r) {
					*Xsp++ = r;
				}
				Xrn = l;
			} else if (r) {
				Xrn = r;
			} else if (Xsp != Xstack) {
				Xrn = *(--Xsp);
			} else {
				Xrn = (struct radix_node *) 0;
			}
		}
	}
	assert (radix->num_active_node == 0);
	return;
}

extern void 
radix_destroy (struct radix **radix, void_fn_t func)
{
	radix_clear (*radix, func);
	delete *radix;
	*radix = NULL;
/*	printf ("radix_destroy\n"); */
	return;
}

extern void
radix_doall (struct radix *radix, void_fn_t func)
{
	struct radix_node *node;

	if (!func)
		return;
	if (!radix)
		return;

	RADIX_WALK (radix->head, node) {
		((void (*)(prefix *, void *)) func) (node->prefix, node->data);
	} RADIX_WALK_END;
}


struct radix_node *
radix_search_exact (const struct radix *radix, const char *prefixstr)
{
	struct radix_node *node;
	uint8_t *addr;
	struct prefix prefix;

	if (!radix)
		return NULL;

	if (!prefix_fromstring (prefixstr, &prefix))
		return NULL;

	assert (prefix.prefixlen <= radix->maxbits);

	node = radix->head;
	addr = prefix_tochar (&prefix);

	if (!radix->head)
		return NULL;

	while (node->bit < prefix.prefixlen) {
		if (BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			return NULL;
	}
    if (node->bit > prefix.prefixlen || node->prefix == NULL)
        return (NULL);
    assert (node->bit == prefix.prefixlen);
    assert (node->bit == node->prefix->prefixlen);
    if (comp_with_mask (prefix_tochar (node->prefix), prefix_tochar (&prefix), prefix.prefixlen))
		return (node);
	
	return NULL;
}

struct radix_node *
radix_add (struct radix *radix, const char *prefixstr)
{
	struct radix_node *node, *new_node, *parent, *glue;
	uint8_t *addr, *test_addr;
	uint32_t prefixlen, check_bit, differ_bit;
	int i, j, r;
	struct prefix *prefix;

	if (!radix)
		return NULL;

	node = radix_search_exact (radix, prefixstr);
	if (node) {
		return node;
	}
	prefix = new ::prefix;
	if (!prefix)
		return NULL;

	if (!prefix_fromstring (prefixstr, prefix)) {
		prefix_destroy (prefix);
		return NULL;
	}

	if (radix->head == NULL) {
		node = new radix_node;
		node->bit = prefix->prefixlen;
		node->prefix = prefix_ref (prefix);
		node->parent = NULL;
		node->l = node->r = NULL;
		node->data = NULL;
		radix->head = node;
		radix->num_active_node++;
		return node;
	}

	addr = prefix_tochar (prefix);
	prefixlen = prefix->prefixlen;
	node = radix->head;

	while (node->bit < prefixlen || node->prefix == NULL) {
		if (node->bit < radix->maxbits && BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			if (node->r == NULL)
				break;
			node = node->r;
		} else {
			if (node->l == NULL)
				break;
			node = node->l;
		}
		assert (node);
	}
	assert (node->prefix);


	test_addr = prefix_tochar (node->prefix);
	/* find the first bit different */
	check_bit = (node->bit < prefixlen)? node->bit: prefixlen;
	differ_bit = 0;
	for (i = 0; i*8 < (int) check_bit; i++) {
		if ((r = (addr[i] ^ test_addr[i])) == 0) {
			differ_bit = (i + 1) * 8;
			continue;
		}
		/* I know the better way, but for now */
		for (j = 0; j < 8; j++) {
			if (BIT_TEST (r, (0x80 >> j)))
				break;
		}
		/* must be found */
		assert (j < 8);
		differ_bit = i * 8 + j;
		break;
	}

	if (differ_bit > check_bit)
		differ_bit = check_bit;

	parent = node->parent;
	while (parent && parent->bit >= differ_bit) {
		node = parent;
		parent = node->parent;
	}
	if (differ_bit == prefixlen && node->bit == prefixlen) {
		if (node->prefix) {
			return node;
		}
        node->prefix = prefix_ref (prefix);
		assert (node->data == NULL);
		return node;
	}

	new_node = new radix_node;
	new_node->bit = prefix->prefixlen;
	new_node->prefix = prefix_ref (prefix);
	new_node->parent = NULL;
	new_node->l = new_node->r = NULL;
	new_node->data = NULL;
	radix->num_active_node++;

	if (node->bit == differ_bit) {
		new_node->parent = node;
		if (node->bit < radix->maxbits && BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			assert (node->r == NULL);
			node->r = new_node;
		} else {
			assert (node->l == NULL);
			node->l = new_node;
		}
		return (new_node);
	}

	if (prefixlen == differ_bit) {
		if (prefixlen < radix->maxbits && BIT_TEST (test_addr[prefixlen >> 3], 0x80 >> (prefixlen & 0x07))) {
			new_node->r = node;
		} else {
			new_node->l = node;
		}
		new_node->parent = node->parent;
		if (node->parent == NULL) {
			assert (radix->head == node);
			radix->head = new_node;
		} else if (node->parent->r == node) {
			node->parent->r = new_node;
		} else {
			node->parent->l = new_node;
		}
		node->parent = new_node;
	} else {
		glue = new radix_node;
		glue->bit = differ_bit;
		glue->prefix = NULL;
		glue->parent = node->parent;
		glue->data = NULL;
		radix->num_active_node++;
		if (differ_bit < radix->maxbits && BIT_TEST (addr[differ_bit >> 3], 0x80 >> (differ_bit & 0x07))) {
			glue->r = new_node;
			glue->l = node;
		} else {
			glue->r = node;
			glue->l = new_node;
		}
		new_node->parent = glue;
		if (node->parent == NULL) {
			assert (radix->head == node);
			radix->head = glue;
		} else if (node->parent->r == node) { 
			node->parent->r = glue;
		} else { 
			node->parent->l = glue;
		}
		node->parent = glue; 
	}
	return new_node;
}

struct radix_node *
radix_lookup (struct radix *radix, struct prefix *prefix)
{
	struct radix_node *node, *new_node, *parent, *glue;
	uint8_t *addr, *test_addr;
	uint32_t prefixlen, check_bit, differ_bit;
	int i, j, r;

	assert (radix);
	assert (prefix);
	assert (prefix->prefixlen <= radix->maxbits);
	if (radix->head == NULL) {
		node = new radix_node;
		node->bit = prefix->prefixlen;
		node->prefix = prefix_ref (prefix);
		node->parent = NULL;
		node->l = node->r = NULL;
		node->data = NULL;
		radix->head = node;
		radix->num_active_node++;
		return node;
	}

    addr = prefix_tochar (prefix);
	prefixlen = prefix->prefixlen;
	node = radix->head;

	while (node->bit < prefixlen || node->prefix == NULL) {
		if (node->bit < radix->maxbits && BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			if (node->r == NULL)
				break;
			node = node->r;
		} else {
			if (node->l == NULL)
				break;
			node = node->l;
		}
		assert (node);
	}
	assert (node->prefix);


	test_addr = prefix_tochar (node->prefix);
	/* find the first bit different */
	check_bit = (node->bit < prefixlen)? node->bit: prefixlen;
	differ_bit = 0;
	for (i = 0; i*8 < (int) check_bit; i++) {
		if ((r = (addr[i] ^ test_addr[i])) == 0) {
			differ_bit = (i + 1) * 8;
			continue;
		}
		/* I know the better way, but for now */
		for (j = 0; j < 8; j++) {
			if (BIT_TEST (r, (0x80 >> j)))
				break;
		}
		/* must be found */
		assert (j < 8);
		differ_bit = i * 8 + j;
		break;
	}

    if (differ_bit > check_bit)
		differ_bit = check_bit;

	parent = node->parent;
	while (parent && parent->bit >= differ_bit) {
		node = parent;
		parent = node->parent;
	}
    if (differ_bit == prefixlen && node->bit == prefixlen) {
		if (node->prefix) {
			return node;
		}
        node->prefix = prefix_ref (prefix);
		assert (node->data == NULL);
		return node;
	}

	new_node = new radix_node;
	new_node->bit = prefix->prefixlen;
	new_node->prefix = prefix_ref (prefix);
	new_node->parent = NULL;
	new_node->l = new_node->r = NULL;
	new_node->data = NULL;
	radix->num_active_node++;

    if (node->bit == differ_bit) {
		new_node->parent = node;
		if (node->bit < radix->maxbits && BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			assert (node->r == NULL);
			node->r = new_node;
		} else {
			assert (node->l == NULL);
			node->l = new_node;
		}
		return (new_node);
	}

	if (prefixlen == differ_bit) {
		if (prefixlen < radix->maxbits && BIT_TEST (test_addr[prefixlen >> 3], 0x80 >> (prefixlen & 0x07))) {
			new_node->r = node;
		} else {
			new_node->l = node;
		}
		new_node->parent = node->parent;
		if (node->parent == NULL) {
			assert (radix->head == node);
			radix->head = new_node;
		} else if (node->parent->r == node) {
			node->parent->r = new_node;
		} else {
			node->parent->l = new_node;
		}
		node->parent = new_node;
	} else {
		glue = new radix_node;
		glue->bit = differ_bit;
		glue->prefix = NULL;
		glue->parent = node->parent;
		glue->data = NULL;
		radix->num_active_node++;
		if (differ_bit < radix->maxbits && BIT_TEST (addr[differ_bit >> 3], 0x80 >> (differ_bit & 0x07))) {
			glue->r = new_node;
			glue->l = node;
		} else {
			glue->r = node;
			glue->l = new_node;
		}
		new_node->parent = glue;
		if (node->parent == NULL) {
			assert (radix->head == node);
			radix->head = glue;
		} else if (node->parent->r == node) { 
			node->parent->r = glue;
		} else { 
			node->parent->l = glue;
		}
		node->parent = glue; 
	}
	return new_node;
}



extern void
radix_remove (struct radix *radix, struct radix_node *node)
{
	struct radix_node *parent, *child;

	assert (radix);
	assert (node);

	if (node->r && node->l) {
		if (node->prefix != NULL) 
			prefix_deref (node->prefix);
		node->prefix = NULL;
		node->data = NULL;
		return;
	}

	if (node->r == NULL && node->l == NULL) {
		parent = node->parent;
		prefix_deref (node->prefix);
		delete node;
		radix->num_active_node--;
		if (parent == NULL) {
			assert (radix->head == node);
			radix->head = NULL;
			return;
		}

        if (parent->r == node) {
			parent->r = NULL;
			child = parent->l;
		} else {
			assert (parent->l == node);
			parent->l = NULL;
			child = parent->r;
		}

		if (parent->prefix)
			return;

		/* we need to remove parent too */

		if (parent->parent == NULL) {
			assert (radix->head == parent);
			radix->head = child;
		} else if (parent->parent->r == parent) {
			parent->parent->r = child;
		} else {
			assert (parent->parent->l == parent);
			parent->parent->l = child;
		}
		child->parent = parent->parent;
		delete node;
		radix->num_active_node--;
		return;
	}
    if (node->r) {
		child = node->r;
	} else {
		assert (node->l);
		child = node->l;
	}
	parent = node->parent;
	child->parent = parent;

	prefix_deref (node->prefix);
	delete node;
	radix->num_active_node--;

	if (parent == NULL) {
		assert (radix->head == node);
		radix->head = child;
		return;
	}

	if (parent->r == node) {
		parent->r = child;
	} else {
		assert (parent->l == node);
		parent->l = child;
	}

	return;
}

extern int
radix_del (struct radix *radix, const char *prefixstr)
{
	struct radix_node *node;

	node = radix_search_exact (radix, prefixstr);
	if (!node) {
/*		printf ("radix_del: Cannot find '%s'\n", prefixstr); */
		return -1;
	}
	radix_remove (radix, node);
	return 0;
}
