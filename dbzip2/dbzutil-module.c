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

#ifdef DBZ_DEBUG
#define dbz_debug(...) fprintf(stderr, __VA_ARGS__)
#else
#define dbz_debug(...) {}
#endif

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
	dbz_debug("dbzutil.Bitstream: sending to output stream...\n");

	// Write the completed bytes out...
	PyObject *buffer = PyBuffer_FromMemory(self->zbits, self->numZ);
	PyObject *ret = PyEval_CallMethod(self->stream, "write", "(O)", buffer);
	Py_DECREF(buffer);
	
	// And wrap back to the start of the buffer
	self->numZ = 0;
	return ret;
}

/**
 * Read a file stream to its end, breaking it into blocks and passing them
 * to a callback function.
 *
 * To match bzip2's behavior, we have to count run lengths, but we're going
 * to return the raw input so bzip2 can have its way with it again.
 */
static PyObject*
dbzutil_readblock_func(PyObject *self, PyObject *args)
{
	PyObject *stream;
	PyObject *callback;
	int blockSize100k = 9;
	if (!PyArg_ParseTuple(args, "OO|i:readblock", &stream, &callback, &blockSize100k))
		return NULL;
	
	if (!PyCallable_Check(callback)) {
		PyErr_SetString(PyExc_TypeError, "parameter must be callable");
		return NULL;
	}
	
	// Trigger size of RLE-compressed block edge
	int maxBlockSize = 100000 * blockSize100k - 19;
	
	// size of our buffer; may grow because this holds uncompressed data
	int outSize = maxBlockSize + 4;
	char *outBytes = malloc(outSize);
	int outPosition = 0;
	
	if (outBytes == NULL) {
		// shit!
		return NULL;
	}
	
	// Size of current byte run for the RLE encoding.
	int runLength = 0;
	
	// Working position in our hypothetical RLE-compressed output buffer.
	// We don't actually store such a buffer, but we need its size to
	// know where we should split the blocks.
	int runPosition = 0;
	
	int bufferSize = 65536;
	PyObject *buffer = NULL;
	char *readBytes;
	int readSize = 0;
	int readPosition = 0;
	
	char previous;
	char current = 0;
	
	while (1) {
		if (readPosition == readSize) {
			// Grab next chunk of input...
			if (buffer) {
				Py_DECREF(buffer);
			}
			buffer = PyEval_CallMethod(stream, "read", "(i)", bufferSize);
			if (!buffer)
				break;
			if (-1 == PyString_AsStringAndSize(buffer, &readBytes, &readSize))
				break;
			
			if (readSize == 0) {
				// Nothing left in input stream; fire off final block and exit.
				if (outSize > 0) {
					dbz_debug("readblock: final callback, %d bytes\n", outPosition);
					PyObject *args = Py_BuildValue("(s#)", outBytes, outPosition);
					if (!args)
						break;
					PyObject *ret = PyEval_CallObject(callback, args);
					Py_DECREF(args);
					if (!ret)
						break;
				} else {
					dbz_debug("readblock: at end, no output left.\n");
				}
				Py_DECREF(buffer);
				free(outBytes);
				return Py_None;
			}
			
			if (runLength == 0) {
				current = readBytes[readPosition++];
				outBytes[outPosition++] = current;
				runLength = 1;
			} else {
				readPosition = 0;
			}
		}
		previous = current;
		current = readBytes[readPosition++];
		
		if (outPosition == outSize) {
			// A big block, add more memory. :(
			dbz_debug("readblock: reallocing from %d to %d\n", outSize, 2*outSize);
			outSize *= 2;
			char *newBytes = realloc(outBytes, outSize);
			if (newBytes == NULL)
				break;
			else
				outBytes = newBytes;
		}
	
		if (current != previous) {
			if (runLength < 4) {
				// Very short runs have no marker, so they don't expand.
				runPosition += runLength;
			} else if (runLength >= 4) {
				// Runs of 4 throguh 259 bytes are stored as the first four
				// raw bytes plus a one-byte run counter for the remaining
				// length (ie, the count is 0 for a 4-byte run).
				runPosition += 5;
			}
			runLength = 1;
		} else {
			runLength++;
			if (runLength == 255) {
				runPosition += 5;
				runLength = 1;
			}
		}
		if (runPosition >= maxBlockSize) {
			// chunk out the input
			dbz_debug("readblock: callback %d bytes\n", outPosition);
			PyObject *args = Py_BuildValue("(s#)", outBytes, outPosition);
			if (!args)
				break;
			PyObject *ret = PyEval_CallObject(callback, args);
			Py_DECREF(args);
			if (!ret)
				break;
			
			// Reset state for a new output block
			outPosition = 0;
			runPosition = 0;
		}
		
		outBytes[outPosition++] = current;
	}

	// Exception cleanup
	if (buffer) {
		Py_DECREF(buffer);
	}
	free(outBytes);
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
}

static void
dbzutil_bitstream_dealloc(BitstreamObject *self) {
	Py_DECREF(self->stream);
	free(self->zbits);
	self->ob_type->tp_free(self);
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
	
	dbz_debug("dbzutil.Bitstream: writing: ");
	// Go through all the full bytes...
	int i;
	for (i = 0; i < nbytes; i++) {
		dbz_debug("#");
		bsPutUChar(self, inbytes[i]);
		if (self->numZ >= self->bufferSize)
			if (!bsPythonWrite(self))
				return NULL;
	}
	
	// And any leftover bits...
	if (remainder) {
		dbz_debug("+%d bits", remainder);
		bsW(self, remainder, (u_int32_t)inbytes[nbytes] >> (8 - remainder));
		if (self->numZ >= self->bufferSize)
			if (!bsPythonWrite(self))
				return NULL;
	}
	
	dbz_debug("\n");

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
		dbz_debug("dbzutil.Bitstream: flushing %d bits\n", self->numZ);
		if (!bsPythonWrite(self))
			return NULL;
		if (!PyEval_CallMethod(self->stream, "flush", "()"))
			return NULL;
	}
	
	return Py_None;
}

static PyMethodDef dbzutil_methods[] = {
	{"readblock", dbzutil_readblock_func, METH_VARARGS,
	"readblock(stream, callback, blocksize=9)\n"
	"\n"
	"Read through a file stream, breaking it into blocks of the size "
	"bzip2 would use. The chunks are sent to a callback function."},
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
	.tp_init = (initproc)dbzutil_bitstream_init,
	.tp_dealloc = (destructor)dbzutil_bitstream_dealloc
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
