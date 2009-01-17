#-*- coding: utf-8 -*-

## This file will compress the revision history of a database dump or
## export file into a more compact "edit syntax".
##
## Usage: ConvertToEditSyntax input_file output_file
##
## An option flag "-v" is defined that generates a "verbose mode" with
## rolling feedback on the scripts' progress.
##
## This process takes approximately 1 minute for every 250 MB of input.

import os, string, re, time, sys;
import codecs;
import EditSyntax

blockdata = "";
blockpos = 0;
blocksize = 500000;

chars = 0;
compressed = 0;
cnt = 0;
revcnt = 0;

argv = sys.argv;
input_file = "";
output_file = "";

def readRevision(f):
    global blockdata, blockpos, blocksize;
    p2 = blockdata.find("</revision>",blockpos);
    if p2 > -1:
        st = blockdata[blockpos:p2+11];
        blockpos = p2+11;
        return st;
    else:
        while p2 == -1:
            new_block = fin.read(blocksize)
            blockdata = blockdata[blockpos:] + new_block;
            blockpos = 0;
            p2 = blockdata.find("</revision>",blockpos);
            if new_block == "":
                return "";
        st = blockdata[blockpos:p2+11];
        blockpos = p2+11;
        return st;


if '-v' in argv:
    verbose = True;
    argv.remove('-v');
else:
    verbose = False;

##verbose = True;
##fname = "cowiki-20081203";
##input_file = "J:\\Wikidata\\" + fname + "-pages-meta-history.xml";
##output_file = "J:\\Wikidata\\"+ fname + "-pages-shrunk.xml";

if len(argv) >= 3:
    input_file = argv[1];
    output_file = argv[2];

if input_file == "" or output_file == "":
    verbose = True;
    input_file = raw_input("File to compress? ");
    output_file = raw_input("Destination file? ");
    print "\n";

filesize = os.path.getsize(input_file);
fin = codecs.open(input_file,'r','utf-8');
fout = codecs.open(output_file,"w", 'utf-8');

textre = re.compile("<revision>\\s*<id>([0-9]*)</id>.*<text[^>]*>([^<]*)</text>",re.I+re.S);
revidre = re.compile("<revision>\\s*<id>([0-9]*)</id>",re.I+re.S);

ct = time.clock();

A=readRevision(fin);

if verbose:
    print " Pages  Revisions  File Read    Compression     Time     Rev/s";

while A != "":

    if A.find("</page>") > -1 or cnt == 0:
        rev1 = A;
        text1 = textre.findall(A);
        if len(text1) > 0:
            revid = int(text1[0][0]);
            text1 = text1[0][1];
        else:
            revid = revidre.findall(A);
            revid = int(revid[0]);
            text1 = "";
        EditSyntax.newArticle(text1,revid);            
        fout.write(rev1);

        revcnt += 1;
        cnt += 1;
    else:
        rev2 = A;
        text2 = textre.findall(A);
        if len(text2) > 0:
            revid = int(text2[0][0]);
            text2 = text2[0][1];
        elif A.find("<text ") > -1:
            revid = revidre.findall(A);
            revid = int(revid[0]);
            text2 = "";

        EditSyntax.newRevision(text2,revid);
        revcnt += 1;

        if text2 != "":
            output = EditSyntax.getXMLDifference();

            if verbose:
                chars += len(text2) + 43;
                compressed += len(output);

            p1 = rev2.find("<text")-6;
            p2 = rev2.find("</text>")+8;
            rev2 = rev2[:p1] + output + rev2[p2:];
        
        fout.write(rev2);
        
        rev1 = rev2;
        text1 = text2;

    if revcnt % 1000 == 0 and verbose:
        remaining = (filesize - fin.tell()) * (time.clock()-ct) / fin.tell();
        hours = remaining / 3600;
        minutes = (remaining % 3600) / 60;
        seconds = remaining % 60;
        print "%(1)6i %(2)8i %(3)5iM" % \
              {'1': cnt, '2': revcnt, '3': fin.tell() / 2**20}, \
              "(%(1)4.1f%%)" % {'1': fin.tell() / float(filesize) * 100}, \
              " %(1)2.2f%%" % {'1': fout.tell() / float(fin.tell())*100}, \
              "(%(1)2.2f%%)" % {'1': compressed / float(chars)*100}, \
              " %(hour)i:%(min)02i:%(sec)02i" % \
              {'hour' : hours, 'min' : minutes, 'sec': seconds}, \
              " %(1)6.1f" % {'1': revcnt / (time.clock()-ct)};
                
    A = readRevision(fin);

fout.write(blockdata);

if verbose:
    fout.flush();
    elapsed = time.clock()-ct
    hours = elapsed / 3600;
    minutes = (elapsed % 3600) / 60;
    seconds = elapsed % 60;
    print "\n     ", cnt, "Pages, ", revcnt, "Revisions, ", \
          "%(hour)i:%(min)02i:%(sec)02i Processing Time\n" % \
          {'hour' : hours, 'min' : minutes, 'sec': seconds};
    print "      Total File Size:         ", fin.tell(), "-->", fout.tell(), \
          "(%(1)2.2f%%)" % {'1': fout.tell() / float(fin.tell())*100};  
    print "      Compressible Characters: ", chars, "-->", compressed, \
          "(%(1)2.2f%%)" % {'1': compressed / float(chars)*100};  
        

fin.close();
fout.close();

