#include "include.h"
#include "ttree.h"
#include "zhdaemon.h"
#include "segment.h"

/* a very simple max matching segmentor */
unsigned char *doSegment(int tid, const unsigned char *input, int len) {
  int ii, ri, rlen, c;
  unsigned char *r;
  rlen = len*2;
  r = (unsigned char*)malloc(sizeof(unsigned char) * rlen);
  if(!r) {
    if(optWarning)
      fprintf(stderr, "%d: doSegment() out of memory.\n", tid);
    return NULL;
  }

  ri = ii = 0;
  while( ii < len ) {
    int k, m;
    k = (int)searchMax(dictSeg, input+ii, &m);
    if(m==0) { // not found. copy the content up to the start of
               // the next UTF-8 byte.
      for(m=1;(input[ii+m]&0xc0)==0x80;m++);
    }
    if(ri+m+2>=rlen) {
      rlen=rlen*2;
      r = (unsigned char*)realloc(r, sizeof(unsigned char) * rlen);
      if(!r) {
	if(optWarning)
	  fprintf(stderr, "%d: doSegment() out of memory.\n", tid);
	return NULL;
      }
    }
    for(c=0;c<m;c++)
      r[ri++] = input[ii+c];
    r[ri++]=' ';
    ii+=m;
  }
  r[ri++]='\0';
  return r;
}
