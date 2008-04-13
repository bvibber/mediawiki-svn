Portable Network Graphics Downsampler is a tool which allows downsizing of PNG 
images without loading the entire file in memory. This makes it possible to 
resize extremely large PNGs.

The implementation is Python works and uses indeed only few memory, but is much
too slow for use. This implementation also only outputs raw data and does not 
recompress to PNG.

The C version is supposed to be faster and even less memory using.

It currently decompresses any PNG into raw RGB data.

== License ==
Copyright (C) 2008 Bryan Tong Minh

This library is free software; you can redistribute it and/or
modify it under the terms of the GNU Lesser General Public
License as published by the Free Software Foundation; either
version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public
License along with this library; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA