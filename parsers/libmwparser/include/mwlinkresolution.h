#ifndef MWLINKRESOLUTION_H_
#define MWLINKRESOLUTION_H_

typedef enum { MWLINKCOLOR_BLUE, MWLINKCOLOR_RED } MWLINKCOLOR;

typedef struct
{
    MWLINKCOLOR color;
    const char *url;
    const char *imageUrl;
    const char *imageWidth;
    const char *imageHeight;
    const char *class;
    const char *alt;

#ifndef SWIG
    void (*free)(void *data);
    void *freeData;
#endif
}
    MWLINKRESOLUTION;

#ifndef SWIG
void MWLinkResolutionFree(void *linkResolution);
#endif

#endif
