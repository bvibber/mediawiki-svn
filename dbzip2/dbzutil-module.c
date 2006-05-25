/*
  Low-level utility modules for dbzip2 Python version
  Copyright 2006 by Brion Vibber <brion@pobox.com>
  
  
  Includes portions modified from libbzip2, a library for lossless,
  block-sorting data compression.

  Copyright (C) 1996-2005 Julian R Seward.  All rights reserved.

  Redistribution and use in source and binary forms, with or without
  modification, are permitted provided that the following conditions
  are met:

  1. Redistributions of source code must retain the above copyright
     notice, this list of conditions and the following disclaimer.

  2. The origin of this software must not be misrepresented; you must 
     not claim that you wrote the original software.  If you use this 
     software in a product, an acknowledgment in the product 
     documentation would be appreciated but is not required.

  3. Altered source versions must be plainly marked as such, and must
     not be misrepresented as being the original software.

  4. The name of the author may not be used to endorse or promote 
     products derived from this software without specific prior written 
     permission.

  THIS SOFTWARE IS PROVIDED BY THE AUTHOR ``AS IS'' AND ANY EXPRESS
  OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
  WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
  ARE DISCLAIMED.  IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY
  DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL
  DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE
  GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
  INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
  WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
  NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
  SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

  Julian Seward, Cambridge, UK.
  jseward@bzip.org
  bzip2/libbzip2 version 1.0 of 21 March 2000

  This program is based on (at least) the work of:
     Mike Burrows
     David Wheeler
     Peter Fenwick
     Alistair Moffat
     Radford Neal
     Ian H. Witten
     Robert Sedgewick
     Jon L. Bentley

  For more information on these sources, see the manual.
--*/

#include <sys/types.h>

#include <Python.h>

//#define DBZ_DEBUG 1


/*---------------------------------------------------*/
/*--- Bit stream I/O                              ---*/
/*---------------------------------------------------*/

typedef struct {
	PyObject_HEAD
	
	/* the buffer for bit stream creation */
	u_int32_t      bsBuff; // word-size work buffer
	int            bsLive; // count of bits output so far
	
	unsigned char *zbits;  // output buffer
	int            numZ;   // count of bytes output so far
	
	int            bufferSize;
	PyObject      *stream; // file object for output
} BitstreamObject;


/*---------------------------------------------------*/
static
void bsInitWrite ( BitstreamObject* s )
{
   s->bsLive = 0;
   s->bsBuff = 0;
}


/*---------------------------------------------------*/
static
void bsFinishWrite ( BitstreamObject* s )
{
   while (s->bsLive > 0) {
      s->zbits[s->numZ] = (unsigned char)(s->bsBuff >> 24);
      s->numZ++;
      s->bsBuff <<= 8;
      s->bsLive -= 8;
   }
}


/*---------------------------------------------------*/
#define bsNEEDW(nz)                           \
{                                             \
   while (s->bsLive >= 8) {                   \
      s->zbits[s->numZ]                       \
         = (unsigned char)(s->bsBuff >> 24);          \
      s->numZ++;                              \
      s->bsBuff <<= 8;                        \
      s->bsLive -= 8;                         \
   }                                          \
}


/*---------------------------------------------------*/
static
__inline__
void bsW ( BitstreamObject* s, int n, u_int32_t v )
{
   bsNEEDW ( n );
   s->bsBuff |= (v << (32 - s->bsLive - n));
   s->bsLive += n;
}


/*---------------------------------------------------*/
static
void bsPutUInt32 ( BitstreamObject* s, u_int32_t u )
{
   bsW ( s, 8, (u >> 24) & 0xffL );
   bsW ( s, 8, (u >> 16) & 0xffL );
   bsW ( s, 8, (u >>  8) & 0xffL );
   bsW ( s, 8,  u        & 0xffL );
}


/*---------------------------------------------------*/
static
void bsPutUChar ( BitstreamObject* s, unsigned char c )
{
   bsW( s, 8, (u_int32_t)c );
}

/**
 * Flush the completed bytes in the buffer out to the output stream.
 */
static PyObject*
bsPythonWrite(BitstreamObject *self) {
#ifdef DBZ_DEBUG
	fprintf(stderr, "dbzutil.Bitstream: sending to output stream...\n");
#endif

	// Write the completed bytes out...
	PyObject *buffer = PyBuffer_FromMemory(self->zbits, self->numZ);
	PyObject *ret = PyEval_CallMethod(self->stream, "write", "(O)", buffer);
	Py_DECREF(buffer);
	
	// And wrap back to the start of the buffer
	self->numZ = 0;
	return ret;
}

static PyObject*
dbzutil_readblock_func(PyObject *self, PyObject *args)
{
	return NULL;
}

static int
dbzutil_bitstream_init(BitstreamObject *self, PyObject *args, PyObject *kwds) {
	PyObject *stream;
	int bufferSize = 32768;
	
	if (!PyArg_ParseTuple(args, "O|i:__init__", &stream, &bufferSize))
		return -1;
	
	Py_INCREF(stream);
	self->stream = stream;
	
	self->bufferSize = bufferSize;
	self->zbits = malloc(bufferSize);
	self->numZ = 0;
	
	bsInitWrite(self);
	
	return 0;
	// fixme: need a destructor function
}

static PyObject*
dbzutil_bitstream_write(BitstreamObject *self, PyObject *args) {
	unsigned char *inbytes;
	int inlength;
	int nbits = -1;
	
	if (!PyArg_ParseTuple(args, "s#|i:write", &inbytes, &inlength, &nbits))
		return NULL;
	
	if (nbits < 0)
		nbits = inlength * 8;
	
	int nbytes = nbits / 8;
	int remainder = nbits % 8;
	
	if (inlength < nbytes + (remainder ? 1 : 0)) {
		// Input string too short!
		return NULL;
	}
	
#ifdef DBZ_DEBUG
	fprintf(stderr, "dbzutil.Bitstream: writing: ");
#endif
	// Go through all the full bytes...
	int i;
	for (i = 0; i < nbytes; i++) {
#ifdef DBZ_DEBUG
		fprintf(stderr, "#");
#endif
		bsPutUChar(self, inbytes[i]);
		if (self->numZ >= self->bufferSize)
			if (!bsPythonWrite(self))
				return NULL;
	}
	
	// And any leftover bits...
	if (remainder) {
#ifdef DBZ_DEBUG
		fprintf(stderr, "+%d bits", remainder);
#endif
		bsW(self, remainder, (u_int32_t)inbytes[nbytes] >> (8 - remainder));
		if (self->numZ >= self->bufferSize)
			if (!bsPythonWrite(self))
				return NULL;
	}
	
#ifdef DBZ_DEBUG
	fprintf(stderr, "\n");
#endif

	// Flush buffer to output
	if (!bsPythonWrite(self))
		return NULL;
	
	return Py_None;
}

static PyObject*
dbzutil_bitstream_flush(BitstreamObject *self, PyObject *args) {
	// Flush out any remaining unwritten bits
	bsFinishWrite(self);
	
	if (self->numZ > 0) {
#ifdef DBZ_DEBUG
		fprintf(stderr, "dbzutil.Bitstream: flushing %d bits\n", self->numZ);
#endif
		if (!bsPythonWrite(self))
			return NULL;
		if (!PyEval_CallMethod(self->stream, "flush", "()"))
			return NULL;
	}
	
	return Py_None;
}

static PyMethodDef dbzutil_methods[] = {
	{"readblock", dbzutil_readblock_func, METH_VARARGS,
	"readblock(stream, blocksize)\n"
	"\n"
	"Return a tuple of an RLE-compressed data block and its CRC."},
	{NULL}
};

static PyMethodDef dbzutil_bitstream_methods[] = {
	{"write", (PyCFunction)dbzutil_bitstream_write, METH_VARARGS,
	 "write(data, [nbits])\n"
	 "Write a sequence of bits to the stream. "
	 "If no length given, writes all bytes in the given data."
	},
	{"flush", (PyCFunction)dbzutil_bitstream_flush, METH_VARARGS,
	 "flush()\n"
	 "Write out any buffered bits to the output stream, zero-padded "
	 "to the next byte boundary."
	},
	{NULL, NULL}
};

static PyTypeObject dbzutil_bitstream_type = {
	PyObject_HEAD_INIT(NULL)
	.tp_name = "dbzutil.Bitstream",
	.tp_basicsize = sizeof(BitstreamObject),
	.tp_flags = Py_TPFLAGS_DEFAULT,
	.tp_doc = "Bitstream writer.",
	.tp_methods = dbzutil_bitstream_methods,
	.tp_new = PyType_GenericNew,
	.tp_init = (initproc)dbzutil_bitstream_init
};

// python c module tutorial reference so i don't forget:
// http://starship.python.net/crew/mwh/toext/your-first-extension.html

static char dbzutil_doc[] =
"Low-level utility functions for dbzip2.";

PyMODINIT_FUNC
initdbzutil(void) 
{
	PyObject *module;
	
	if (PyType_Ready(&dbzutil_bitstream_type) < 0) return;
	
	module = Py_InitModule3("dbzutil", dbzutil_methods, dbzutil_doc);
	if (module == NULL) return;
	
	Py_INCREF(&dbzutil_bitstream_type);
	PyModule_AddObject(module, "Bitstream", (PyObject *)&dbzutil_bitstream_type);
}
