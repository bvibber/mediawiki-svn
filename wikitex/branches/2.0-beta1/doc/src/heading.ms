.ig
WikiTeX: expansible LaTeX module for MediaWiki
Copyright (C) 2004-7 Peter Danenberg
See doc/COPYING for details.
..
.\" ms-macro to produce numbered heading and insert the same into the
.\" table of contents; also: indent accordingly.
.
.\" Document will be followed by `.XHE' to reset indentation
.\" and include TOC.
.
.\" Art schon included, baby? (`.if r': groff extension.)
.if r XH*I .nx
.nr XH*I 1
.de EI                  \" Equalize indent
.nr ei*ti \\$1          \" Target indent
.\" Define current indent if not defined (not usually defined until the
.\" first `.RS'.
.if !r \\n[.ev]:il \{\
.       if r DB .tm \\n[.ev]:il not defined
.       nr \\n[.ev]:il 0
.\}
.\" If the target indent and current indent are equal, there is
.\" no work to be done.
.if (\\n[ei*ti]=\\n[\\n[.ev]:il]) \{\
.       if r DB .tm ei*d and .ev:il are identical
.       return
.\}
.\" If the target indent is greater than the current indent,
.\" we need to `.RS' up to it.
.if (\\n[ei*ti]>\\n[\\n[.ev]:il]) \{\
.       if r DB .tm ei*d is @RS
.       als ei*d @RS    \" RS causes stack overflow
.\}
.\" If the target indent is less than the current indent,
.\" we need to `.RE' down to it.
.if (\\n[ei*ti]<\\n[\\n[.ev]:il]) \{\
.       if r DB .tm ei*d is @RE
.       als ei*d @RE
.\}
.while !(\\n[ei*ti]=\\n[\\n[.ev]:il]) \{\
.       if r DB .tm applying ei*d
.       ei*d
.\}
..
.\" Argument thus supplied should be: 1) depth; 2) the symbolic heading
.\" and TOC-entry.  Includes an optional `S xx [xx [...]] a la `.NH'
.\" to micromanage the section-number.
.de XH
.nr xh*i \\$1
.ds xh*h \\$2
.EI \\n[xh*i]-1
.ie '\\$3'S' \{\
.       shift 3
.       NH S \\$@
.\}
.el \{\
.       NH \\n[xh*i]
.\}
\\*[xh*h]
.\" Preliminary tabs before each entry in XS, corresponding to level.
.ds xh*tabs
.nr xh*tablevel 1
.while \\n[nh*hl]>\\n[xh*tablevel] \{\
.       nr xh*tablevel +1
.       as xh*tabs \t
.\}
.XS
\\*[xh*tabs]\\*[SN]  \\*[xh*h]
.XE
.EI \\n[xh*i]
..
.\" Reduces the current ident to nil and displays TOC.
.de XHE
.EI 0
.TC
..
.\" End-of-input trap to show the TOC (doesn't work: gets cut off,
.\" I presume after the `.bp').
.em XHE
