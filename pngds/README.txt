Portable Network Graphics Downsampler is a tool which allows downsizing of PNG 
images without loading the entire file in memory. This makes it possible to 
resize extremely large PNGs.

The implementation is Python works and uses indeed only few memory, but is much
too slow for use. This implementation also only outputs raw data and does not 
recompress to PNG.

The C version is supposed to be faster and even less memory using, but not yet
working.

It currently decompresses any PNG to raw RGB data.