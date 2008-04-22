Portable Network Graphics Downsampler is a tool which allows downsizing of PNG 
images without loading the entire file in memory. This makes it possible to 
resize extremely large PNGs.

== Installation ==
# svn co http://svn.wikimedia.org/svnroot/mediawiki/trunk/pngds
# cd pngds && make

== Windows installation ==
pngds can be compiled using mingw as well as with the Microsft Visual C 
compiler. Microsoft Visual C++ 9.0 project files can be downloaded from
<http://toolserver.org/~bryan/pngds/>

== Precompiled binaries ==
Precompiled binaries for Linux and Windows can be downloaded from 
<http://toolserver.org/~bryan/pngds/>

== Usage ==
pngds [--from-stdin] [--to-stdout] [<source>] [<target>]
	[--width <width>] [--height <height>] [--no-filtering] [-n]

	--from-stdin	Read data from stdin instead from <source>
	--to-stdout	Output data to stdout instead to <target>
	
	--width		Resize width
	--height	Resize height
			If only one of width or height is specified, 
			the image is resized keeping aspect ratio.
			
	--no-filtering	Disable Paeth filtering (faster)
	-n		Compression level from 0-9 (-0 .. -9)


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