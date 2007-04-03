/* Copyright (c) 2005, IPng, Pim van Pelt <pim@ipng.nl>	*/
/* Copyright (c) 2006 River Tarnell. */
/*
 * This file implements a 128 bit deep Patricia Trie, to facilitate very
 * fast best-match route lookup. Parts from this (and its accompanying C
 * file) were taken from the Merit MRTd project:
 *
 *   This product includes software developed by the University of Michigan,
 *   Merit Network, Inc., and their contributors. 
 */
 
/* From: Id: radix.h,v 1.1.1.1 2005/11/07 20:17:44 pim Exp */
/* From: (Lorely) Id: radix.h 18305 2006-12-13 08:20:58Z river */
/* $Id$ */

#ifndef _RADIX_H
#define _RADIX_H

#include <cassert>
#include <utility>

#include "prefix.h"

#define RADIX_MAXBITS 128
#define BIT_TEST(f, b)  ((f) & (b))

namespace radix_detail {

int comp_with_mask (void const *, void const *, uint32_t);

}

/**
 * A node in the radix tree.  The radix tree contains many of these; end nodes
 * have a non-null data provided by the user.
 */
template<typename T>
struct radix_node {
	/**
	 * Copy an existing radix node.  This does *not* do a deep copy of any
	 * children.
	 * \param other node to copy from
	 */
	radix_node(radix_node<T> const &other);

	/**
	 * Replace this node with another.
	 */
	radix_node& operator= (radix_node<T> const &other);

private:
	template<typename> friend struct radix;
	template<typename, typename> friend struct radix_iterator;

	/**
	 * Construct a new, empty radix node.
	 */
	radix_node() 
		: bit(0)
		, pfx(0)
		, l(0)
		, r(0)
		, parent(0)
	{}

	uint32_t	 bit;
	prefix 		*pfx;		/**< Prefix stored in this node. */
	radix_node 	*l, *r;		/**< Left/right nodes */
	radix_node 	*parent;	/**< Parent node */
	T		*value;		/**< User data */

};

template<typename T, typename R>
struct radix_iterator {
	radix_iterator () : _node(NULL) {}

	radix_iterator (radix_iterator<T,R> const &o)
		: _node(o._node) {}

	radix_iterator &operator= (radix_iterator<T,R> const &o) {
		_node = o._node;
		return *this;
	}

	T &
	operator* (void) const {
		assert(_node->value);
		return *_node->value;
	}

	T *
	operator->(void) const {
		assert(_node->value);
		return _node->value;
	}

	bool operator==(radix_iterator<T,R> const &other) {
		return _node == other._node;
	}

	bool operator!=(radix_iterator<T,R> const &other) {
		return !(*this == other);
	}

private:
	template<typename> friend class radix;

	radix_iterator (R *node)
		: _node(node) {}

	R *_node;
};

/**
 * A radix tree.  Radix trees (also called patricria tries) are a data
 * structure allowing very fast matching of bitstring prefixes.  This
 * implementation is designed to store IP addresses, for use in access
 * control.
 *
 * A radix tree is initially empty.  It should be populated with prefixes
 * using add().  The radix_node returned by add() has two fields that can
 * be filled in by the user: radix_node#data and radix_node#flags.  This
 * allows you to associate information with a prefix for later retrieval.
 *
 * After prefixes have been added to the tree, use search() or search_exact()
 * to query the list.  Both functions return the matching node, or NULL
 * if the node was not found.  The difference between the two is that
 * search_exact required the prefix length to match, but search() will return
 * the longest matching prefix shorter than or equal to the argument.
 *
 * For example, if the tree contains "10.0.1.0/24", search("10.0.1.10/32")
 * will return that entry; but search_exact() for the same IP will return
 * NULL.  To find the entry for 10.0.1.0/24 using search_exact, 
 * search_exact("10.0.1.0/24") would be required.
 */
template<typename T>
struct radix {
	typedef radix_iterator<T, radix_node<T> > iterator;
	typedef radix_iterator<T, radix_node<T> const> const_iterator;
	typedef std::pair<prefix, T> value_type;

	/**
	 * Construct a new, empty radix tree.
	 */
	radix();

	/**
	 * Dtor; frees the tree and all its nodes.
	 */
	virtual ~radix();

	/**
	 * Create a radix tree from an existing tree.
	 * \param other tree to copy from
	 */
	radix(radix<T> const &other);

	/**
	 * Replace the contents of this tree with another.
	 * \param other tree to copy from
	 */
	radix &operator= (radix<T> const &other);

	/**
	 * Equivalent to insert(prefix(pfx), value).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa insert(prefix const &, T const&)
	 */
	std::pair<iterator, bool>	insert		(char const *pfx, T const &value);

	/**
	 * Add a new prefix to the tree, and return the node.  If the node
	 * already exists, the existing node is returned.
	 *
	 * \param pfx prefix to add
	 * \returns a pair P.  P.first is the iterator to the inserted prefix.
	 * P.second is true if the prefix was actually inserted, or false if it
	 * already existed.
	 */
	std::pair<iterator, bool>	insert		(prefix const &pfx, T const &value);

	/**
	 * Equivalent to insert(prefix(pfx), value).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa insert(prefix const &, T const&)
	 */
	std::pair<iterator, bool>	insert		(std::string const &pfx, T const &value);

	/**
	 * Delete the specified node from the tree.
	 *
	 * \param pfx prefix to delete
	 * \returns 0 if the node was removed, or -1 if the node was not found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	int		 del		(char const *pfx);

	/**
	 * Delete the specified node from the tree.
	 *
	 * \param pfx prefix to delete
	 * \returns 0 if the node was removed, or -1 if the node was not found.
	 * \throws invalid_prefix if the prefix could not be parsed.
	 */
	int		 del		(std::string const &pfx);

	/**
	 * Remove all nodes from this tree, leaving it empty.
	 */
	void		 clear		(void);

	/**
	 * Remove the given node from the tree.
	 * \param n node to remove
	 */
	void		 remove		(radix_node<T> *n);

	/**
	 * Equivalent to search(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search(prefix const &) const
	 */
	const_iterator	 search		(char const *pfx) const {
		return _search<const_iterator>(pfx);
	}
	iterator	 search		(char const *pfx) {
		return _search<iterator>(pfx);
	}

	/**
	 * Search the tree for the specified prefix.  The longest prefix matching
	 * the given prefix will be returned.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or end() if no match was found.
	 */
	const_iterator	search		(prefix const &pfx) const {
		return _search<const_iterator>(pfx);
	}

	/**
	 * Search the tree for the specified prefix.  The longest prefix matching
	 * the given prefix will be returned.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or end() if no match was found.
	 */
	iterator	search		(prefix const &pfx) {
		return _search<iterator>(pfx);
	}

	/**
	 * Equivalent to search(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search(prefix const &) const
	 */
	const_iterator	search		(std::string const &pfx) const {
		return _search<const_iterator>(pfx);
	}

	/**
	 * Equivalent to search(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search(prefix const &) const
	 */
	iterator	search		(std::string const &pfx) {
		return _search<iterator>(pfx);
	}

	/**
	 * Equivalent to search(prefix(addr)).
	 * \sa search(prefix const &) const
	 */
	const_iterator	search		(sockaddr const *addr) const {
		return _search<const_iterator>(addr);
	}

	/**
	 * Equivalent to search(prefix(addr)).
	 * \sa search(prefix const &) const
	 */
	iterator	search		(sockaddr const *addr) {
		return _search<iterator>(addr);
	}

	/**
	 * Equivalent to search_exact(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search_exact(prefix const &) const
	 */
	const_iterator	search_exact	(char const *pfx) const {
		return _search_exact<const_iterator>(pfx);
	}

	/**
	 * Equivalent to search_exact(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search_exact(prefix const &) const
	 */
	iterator	search_exact	(char const *pfx) {
		return _search_exact<iterator>(pfx);
	}

	/**
	 * Search the tree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or end() if no match was found.
	 */
	const_iterator	search_exact	(prefix const &pfx) const {
		return _search_exact<const_iterator>(pfx);
	}

	/**
	 * Search the tree for the specified address.  A matching prefix will
	 * only be returned if it is the same length as the prefix searched for.
	 *
	 * \param pfx prefix to search for
	 * \returns matching node, or end() if no match was found.
	 */
	iterator	search_exact	(prefix const &pfx) {
		return _search_exact<iterator>(pfx);
	}

	/**
	 * Equivalent to search_exact(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search_exact(prefix const &) const
	 */
	const_iterator	search_exact	(std::string const &pfx) const {
		return _search_exact<const_iterator>(pfx);
	}

	/**
	 * Equivalent to search_exact(prefix(pfx)).
	 * \throws invalid_prefix if the prefix could not be parsed.
	 * \sa search_exact(prefix const &) const
	 */
	iterator	search_exact	(std::string const &pfx) {
		return _search_exact<iterator>(pfx);
	}

	/**
	 * Equivalent to search_exact(prefix(addr)).
	 * \sa search_exact(prefix const &) const
	 */
	const_iterator search_exact	(sockaddr const *addr) const {
		return _search_exact<const_iterator>(addr);
	}

	/**
	 * Equivalent to search_exact(prefix(addr)).
	 * \sa search_exact(prefix const &) const
	 */
	iterator search_exact	(sockaddr const *addr) {
		return _search_exact<iterator>(addr);
	}

	/**
	 * Returns one past the last prefix in the tree.
	 */
	const_iterator end (void) const {
		return const_iterator();
	}

	/**
	 * Returns one past the last prefix in the tree.
	 */
	iterator end (void) {
		return iterator();
	}

	/**
	 * Test if this tree has any elements.
	 * \returns true if the tree is empty, else false
	 */
	bool		 empty		(void) const {
		return !head;
	}

private:
	template<typename iterT> iterT _search(prefix const &) const;
	template<typename iterT> iterT _search(char const *) const;
	template<typename iterT> iterT _search(std::string const &) const;
	template<typename iterT> iterT _search(sockaddr const *) const;

	template<typename iterT> iterT _search_exact(prefix const &) const;
	template<typename iterT> iterT _search_exact(char const *) const;
	template<typename iterT> iterT _search_exact(std::string const &) const;
	template<typename iterT> iterT _search_exact(sockaddr const *) const;

	mutable radix_node<T> 	*head;	/**< Head of the tree */
	uint32_t	 maxbits; /**< Longest node in this tree */
	uint32_t	 num_active_node; /**< Number of nodes in this tree */
};

template<typename T>
radix<T>::radix(radix const &o)
	: head(NULL)
	, maxbits(o.maxbits)
	, num_active_node(o.num_active_node)
{
	head = new radix_node<T>(*o.head);
}

template<typename T>
radix<T>::radix(void)
	: head(NULL)
	, maxbits(128)
	, num_active_node(0)
{
}

template<typename T>
radix<T> &
radix<T>::operator=(radix<T> const &other)
{
	head = new radix_node<T>(*other.head);
	maxbits = other.maxbits;
	num_active_node = other.num_active_node;
	return *this;
}

template<typename T>
radix<T>::~radix(void)
{
	clear();
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search(std::string const &s) const
{
	return _search<iterT>(prefix(s.c_str()));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search(const char *prefixstr) const
{
	return _search<iterT>(prefix(prefixstr));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search(const sockaddr *addr) const
{
	return _search<iterT>(prefix(addr));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search(prefix const &prefix) const
{
int		 inclusive = 1;
radix_node<T>	*node;
radix_node<T>	*stack[RADIX_MAXBITS + 1];
uint8_t	const	*addr;
int		 cnt = 0;

	if (prefix.prefixlen > maxbits)
		return iterT();

	if (head == NULL)
		return iterT();

	node = head;
	addr = prefix.tochar();

	while (node->bit < prefix.prefixlen) {
		if (node->pfx) {
			stack[cnt++] = node;
		}

		if (BIT_TEST(addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			break;
	}

	if (inclusive && node && node->pfx)
		stack[cnt++] = node;

	if (cnt <= 0) {
		return end();
	}

	while (--cnt >= 0) {
		node = stack[cnt];
		if (radix_detail::comp_with_mask(node->pfx->tochar(), 
		    prefix.tochar(), node->pfx->prefixlen)) { 
			return iterT(node);
		}
	}
	return iterT();
}

template<typename T>
void
radix<T>::clear (void)
{
	if (head) {
	radix_node<T> *Xstack[RADIX_MAXBITS+1];
	radix_node<T> **Xsp = Xstack;
	radix_node<T> *Xrn = head;

		while (Xrn) {
		radix_node<T> *l = Xrn->l;
		radix_node<T> *r = Xrn->r;

			if (Xrn->pfx) {
				delete Xrn->pfx;
			}
			Xrn->pfx = NULL;
			delete Xrn;
			num_active_node--;
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
				Xrn = NULL;
			}
		}
	}
	assert(num_active_node == 0);
	return;
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search_exact(std::string const &s) const
{
	return _search_exact(prefix(s.c_str()));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search_exact(const char *prefixstr) const
{
	return _search_exact(prefix(prefixstr));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search_exact(const sockaddr *addr) const
{
	return _search_exact(prefix(addr));
}

template<typename T>
template<typename iterT>
iterT
radix<T>::_search_exact(prefix const &pfx) const
{
radix_node<T>	*node;
uint8_t const	*addr;

	assert(pfx.prefixlen <= maxbits);

	if (!head)
		return iterT();

	node = head;
	addr = pfx.tochar();

	while (node->bit < pfx.prefixlen) {
		if (BIT_TEST (addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			node = node->r;
		} else {
			node = node->l;
		}

		if (node == NULL)
			return iterT();
	}
	if (node->bit > pfx.prefixlen || node->pfx == NULL)
		return iterT();
	assert(node->bit == pfx.prefixlen);
	assert(node->bit == node->pfx->prefixlen);
	if (radix_detail::comp_with_mask(node->pfx->tochar(), pfx.tochar(), 
	    pfx.prefixlen))
		return iterT(node);
	
	return iterT();
}

template<typename T>
std::pair<typename radix<T>::iterator, bool>
radix<T>::insert (std::string const &s, T const &value)
{
	return insert(prefix(s.c_str()), value);
}

template<typename T>
std::pair<typename radix<T>::iterator, bool>
radix<T>::insert(const char *prefixstr, T const &value)
{
	return insert(prefix(prefixstr), value);
}

template<typename T>
std::pair<typename radix<T>::iterator, bool>
radix<T>::insert(prefix const &pfx, T const &value)
{
radix_node<T>	*node, *new_node, *parent, *glue;
uint8_t	const	*addr, *test_addr;
uint32_t	 prefixlen, check_bit, differ_bit;
int		 i, j, r;
iterator	 it;
	if ((it = search_exact(pfx)) != iterator()) {
		return std::make_pair(it, false);
	}

	if (head == NULL) {
		node = new radix_node<T>;
		node->bit = pfx.prefixlen;
		node->pfx = new prefix(pfx);
		node->parent = NULL;
		node->l = node->r = NULL;
		node->value = new T(value);
		head = node;
		num_active_node++;
		return std::make_pair(iterator(node), true);
	}

	addr = pfx.tochar();
	prefixlen = pfx.prefixlen;
	node = head;

	while (node->bit < prefixlen || node->pfx == NULL) {
		if (node->bit < maxbits && BIT_TEST(addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
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
	assert(node->pfx);

	test_addr = node->pfx->tochar();
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
			if (BIT_TEST(r, (0x80 >> j)))
				break;
		}
		/* must be found */
		assert(j < 8);
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
		assert(!node->value);
		node->value = new T(value);
		if (node->pfx) {
			return std::make_pair(iterator(node), true);
		}
		node->pfx = new prefix(pfx);
		return std::make_pair(iterator(node), true);
	}

	new_node = new radix_node<T>;
	new_node->bit = pfx.prefixlen;
	new_node->pfx = new prefix(pfx);
	new_node->parent = NULL;
	new_node->l = new_node->r = NULL;
	new_node->value = new T(value);
	num_active_node++;

	if (node->bit == differ_bit) {
		new_node->parent = node;
		if (node->bit < maxbits && BIT_TEST(addr[node->bit >> 3], 0x80 >> (node->bit & 0x07))) {
			assert(node->r == NULL);
			node->r = new_node;
		} else {
			assert(node->l == NULL);
			node->l = new_node;
		}
		return std::make_pair(iterator(new_node), true);
	}

	if (prefixlen == differ_bit) {
		if (prefixlen < maxbits && BIT_TEST (test_addr[prefixlen >> 3], 0x80 >> (prefixlen & 0x07))) {
			new_node->r = node;
		} else {
			new_node->l = node;
		}
		new_node->parent = node->parent;
		if (node->parent == NULL) {
			assert(head == node);
			head = new_node;
		} else if (node->parent->r == node) {
			node->parent->r = new_node;
		} else {
			node->parent->l = new_node;
		}
		node->parent = new_node;
	} else {
		glue = new radix_node<T>;
		glue->bit = differ_bit;
		glue->pfx = NULL;
		glue->parent = node->parent;
		num_active_node++;
		if (differ_bit < maxbits && BIT_TEST(addr[differ_bit >> 3], 0x80 >> (differ_bit & 0x07))) {
			glue->r = new_node;
			glue->l = node;
		} else {
			glue->r = node;
			glue->l = new_node;
		}
		new_node->parent = glue;
		if (node->parent == NULL) {
			assert(head == node);
			head = glue;
		} else if (node->parent->r == node) { 
			node->parent->r = glue;
		} else { 
			node->parent->l = glue;
		}
		node->parent = glue; 
	}
	return std::make_pair(iterator(new_node), true);
}

template<typename T>
void
radix<T>::remove (radix_node<T> *node)
{
radix_node<T> *parent, *child;

	assert (node);

	if (node->r && node->l) {
		delete node->pfx;
		node->pfx = NULL;
		delete node->value;
		return;
	}

	if (node->r == NULL && node->l == NULL) {
		parent = node->parent;
		delete node->pfx;
		node->pfx = NULL;
		delete node;
		num_active_node--;
		if (parent == NULL) {
			assert(head == node);
			head = NULL;
			return;
		}

		if (parent->r == node) {
			parent->r = NULL;
			child = parent->l;
		} else {
			assert(parent->l == node);
			parent->l = NULL;
			child = parent->r;
		}

		if (parent->pfx)
			return;

		/* we need to remove parent too */

		if (parent->parent == NULL) {
			assert(head == parent);
			head = child;
		} else if (parent->parent->r == parent) {
			parent->parent->r = child;
		} else {
			assert(parent->parent->l == parent);
			parent->parent->l = child;
		}
		child->parent = parent->parent;
		delete node;
		num_active_node--;
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

	delete node->pfx;
	node->pfx = NULL;
	delete node;
	num_active_node--;

	if (parent == NULL) {
		assert(head == node);
		head = child;
		return;
	}

	if (parent->r == node) {
		parent->r = child;
	} else {
		assert(parent->l == node);
		parent->l = child;
	}

	return;
}

template<typename T>
int
radix<T>::del (std::string const &s)
{
	return del(s.c_str());
}

template<typename T>
int
radix<T>::del (const char *prefixstr)
{
radix_node<T> *node;

	node = search_exact(prefixstr);
	if (!node)
		return -1;
	remove(node);
	return 0;
}

template<typename T>
radix_node<T>::radix_node(radix_node<T> const &o)
	: bit(o.bit)
	, pfx(o.pfx)
	, l(o.l)
	, r(o.r)
	, parent(o.parent)
	, value(NULL)
{
	pfx->ref_count++;

	if (o.value)
		value = new T(*o.value);
}

template<typename T>
radix_node<T> &
radix_node<T>::operator=(radix_node<T> const &o)
{
	pfx->ref_count--;
	bit = o.bit;
	pfx = o.pfx;
	l = o.l;
	r = o.r;
	parent = o.parent;
	pfx->ref_count++;
	delete value;
	if (o.value)
		value = new T(*o.value);
	else
		value = NULL;
}

#endif /* RADIX_H */
