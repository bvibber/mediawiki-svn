.ig
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004-7 Peter Danenberg
See doc/COPYING for details.
..
.so heading.ms
.so title.ms
.\" Footnotes-numbers trailed by `.'.
.nr FF 1
.\" Redefine footnote-numberer to have enclosing braces.
.ds * [\\*[par@sup-start]\En+[fn*text-num]\\*[par@sup-end]]
