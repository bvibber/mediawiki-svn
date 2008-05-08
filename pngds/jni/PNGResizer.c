#include	<stdio.h>
#include	<stdlib.h>
#include	<jni.h>

#include	"pngresize.h"
#include	"pngwriter.h"

JNIEXPORT jint JNICALL
Java_pngds_PNGResizer_resize(JNIEnv *env, jclass class, jstring jinfile, jstring joutfile, jint width, jint height)
{
	char const *infile = (*env)->GetStringUTFChars(env, jinfile, NULL);
	char const *outfile = (*env)->GetStringUTFChars(env, joutfile, NULL);
	int ret = 0;

	FILE *inf = NULL, *outf = NULL;
	if ((inf = fopen(infile, "r")) == NULL) {
		ret = -1;
		goto cleanup;
	}

	if ((outf = fopen(outfile, "w")) == NULL) {
		ret = -1;
		goto cleanup;
	}

        pngcallbacks callbacks;
        pngwriter *winfo;

        callbacks.completed_scanline = &png_write_scanline;
        callbacks.read_header = &png_write_header;
        callbacks.done = &png_write_end;
        
        winfo = calloc(sizeof(pngwriter), 1);
#if 0
        winfo->deflate_level = *(char *)opts[PNGOPT_DEFLATE_LEVEL];
        winfo->filter_method = *(char *)opts[PNGOPT_NO_FILTERING] ? FILTER_NONE : FILTER_PAETH;
#endif
    
	png_resize(inf, outf, width, height, &callbacks, winfo);

cleanup:
	if (inf)
		fclose(inf);
	if (outf)
		fclose(outf);

	(*env)->ReleaseStringUTFChars(env, jinfile, infile);
	(*env)->ReleaseStringUTFChars(env, joutfile, outfile);

	return ret;
}
