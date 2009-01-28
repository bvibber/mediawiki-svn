#-*- coding: utf-8 -*-

## Originally written by Robert A. Rohde (rohde@robertrohde.com)
##
## The file provides a library of functions used to re-express
## Mediawiki full history dump files into a much more compact "edit syntax"
## or to make them grow large again.
##
## For practical implementations of use, see the companion files:
## ConvertToEditSyntax.py and ConvertFromEditSyntax.py

import string, re, copy, hashlib

## Global variables used by this script
max_buffer_size = 15000000;  ##Maximum number of characters to keep in
                             ##revision buffer.

revision_buffer = dict();
revision_order = []
hash_table = dict();
current_buffer_size = 0;
current_revision = -1;
previous_revision = -1;


## This function should be called with the first revision of each
## article.  It clears the internal buffers and reinitializes the state.
def newArticle(text, revision_id):
    global revision_buffer, hash_table
    global current_revision, previous_revision;
    global current_buffer_size, revision_order;

    revision_buffer = dict();
    revision_buffer[revision_id] = text;

    hash_table = dict();
    hash_table[hash(text)] = [revision_id];

    current_revision = revision_id;
    previous_revision = -1;
    revision_order = [revision_id];

    current_buffer_size = len(text);


## When reading from a full history dump, this function should be called
## with each subsequent revision of the article started with newArticle.
def newRevision(text, revision_id):
    global revision_buffer, hash_table;
    global current_revision, previous_revision;
    global current_buffer_size, max_buffer_size, revision_order;

    previous_revision = current_revision;
    current_revision = revision_id;
    revision_order.append(current_revision);
    
    h = hash(text);
    if h not in hash_table:
        hash_table[h] = [current_revision];
    else:
        hash_table[h].append(current_revision);

    ##Prevent Memory Overflow
    pos = 0;
    while current_buffer_size + len(text) > max_buffer_size \
          and pos < len(revision_order) - 1:
        key = revision_order[pos];
        if revision_buffer[key] != False:
            current_buffer_size -= len(revision_buffer[key]);
            revision_buffer[key] = False;
        pos += 1;

    revision_buffer[revision_id] = text;
    current_buffer_size += len(text);


## When reading from dump generated using editSyntax, this function should
## be called with each new <changes> block passed as a text string.
def newChanges(xml, revision_id):
    global revision_buffer, current_revision;
    changes = readXMLToChanges(xml);

    start = -1;
    for k in range(len(changes)):
        if changes[k]['node'] == 'revert':
            text = revision_buffer[changes[0]['revision']];
            start = k;
            
    if start >= 0:
        if len(changes) > start+1:
            text = differenceRestorer(text,changes[start+1:]);
    else:
        text = differenceRestorer(revision_buffer[current_revision],changes);        
        
    newRevision(text, revision_id);


## The full-text of the current revision.
def getCurrentText():
    global revision_buffer, current_revision;
    return revision_buffer[current_revision];


## Returns an XML formatted <changes> block comparing the current revision
## to the previous one.
def getXMLDifference(indent = 3, indentstr = "  "):
    global revision_buffer, hash_table, current_revision, previous_revision;

    xml_out = "";
    if previous_revision > -1:
        h = hash(revision_buffer[current_revision]);
        revert_id = -1;
        if len(hash_table[h]) > 1:
            choices = hash_table[h];
            for c in choices[:-1]:
                if revision_buffer[c] == revision_buffer[current_revision]:
                    revert_id = c;
                    break;
        if revert_id > -1:
            if revert_id != previous_revision:
                changes = [dict({'node':'revert','revision':revert_id})];
            else:
                changes = [];
        else:
            changes = differenceGenerator(revision_buffer[previous_revision],
                                           revision_buffer[current_revision]);
            
        xml_out = XMLOutput(changes,indent,indentstr);        
        if len(xml_out) > len(revision_buffer[current_revision]) + 40:
            changes = [dict({'node':'new','value':revision_buffer[current_revision]})];
            xml_out = XMLOutput(changes,indent,indentstr);
    else:
        changes = [dict({'node':'new','value':revision_buffer[current_revision]})];
        xml_out = XMLOutput(changes,indent,indentstr);

    return xml_out;


## Takes a changes structure and produces a string with XML formatted output
def XMLOutput(changes, indent = 3, indentstr = "  "):
    in1 = "";
    for k in range(indent):
        in1 += indentstr;
    in2 = in1 + indentstr;

    if len(changes) == 1 and changes[0]['node'] == 'new':
        st = in1 + '<text xml:space="preserve"';
        if changes[0]['value'] != "":
            st += ">" + changes[0]['value'] + "</text>\n";
        else:
            st += " />\n";
        return st;

    need_breaks = False;
    for ch in changes:
        if 'value' in ch and "\n" in ch['value']:
            need_breaks = True;
            break;
    if need_breaks:
        st = in1 + "<changes xml:space=\"preserve\">\n";
    else:
        st = in1 + "<changes>\n";

    for ch in changes:
        if ch['node'] == 'replace' or ch['node'] == 'delete':
            if ch['line1'] == ch['line2']:
                ch['line'] = ch['line1'];
            else:
                ch['lines'] = unicode(ch['line1']) + "-" + \
                              unicode(ch['line2']);
            del ch['line1'];
            del ch['line2'];
        elif ch['node'] == 'text_replace':
            ch['pos'] = unicode(ch['pos_start']) + "-" + \
                        unicode(ch['pos_end']);
            del ch['pos_start'];
            del ch['pos_end'];
        
        st += in2 + "<" + ch['node'];
        for key in ch:
            if key != 'node' and key != 'value':
                st += " " + key + '="' + unicode(ch[key]) + '"';
        if 'value' in ch and ch['value'] != '':
            st2 = "";
            if ch['node'] == 'permute':
                keylist = ch['value'].keys();
                keylist.sort();
                for k in keylist:
                    st2 += unicode(k) + ": " + unicode(ch['value'][k]) + ", ";
                st2 = "{" + st2[:-2] + "}";
            else:
                st2 = unicode(ch['value']);
            st += ">" + st2 + "</" + ch['node'] + ">\n";
        else:
            st += " />\n";

    st += in1 + "</changes>\n";
    return st;


## Creates a changes structure denoting the transformation from
## old_line to new_line.
def textBlocking(old_line, new_line, line_number = False):

    min_char_block = 40;    ## Number of consecutive matching characters
                            ## within a block of changed text to justify
                            ## spliting one replacement into two.
    
    matches = [];
    k1 = 0;
    min_k2 = 0;
    
    while k1 < len(old_line)-min_char_block:
        k2 = string.find(new_line,old_line[k1:k1+min_char_block],min_k2);
        if k2 >= 0:
            if k1 > 0 and k2 > 0 and old_line[k1-1] == new_line[k2-1]:
                k1 += 1;
                continue;
            s = min_char_block;
            while k1 + s < len(old_line) \
                  and k2 + s < len(new_line) \
                  and old_line[k1:k1+s+1] == new_line[k2:k2+s+1]:
                s += 1;
            matches.append([k1,k2,s]);
            k1 += s;
            min_k2 = k2 + s;
            continue;
        k1 += 1;

    old = [];
    new = [];
    if len(matches) > 0:
        mlast_o = 0;
        mlast_n = 0;
        for m in matches:
            old.append(old_line[mlast_o:m[0]]);
            old.append(old_line[m[0]:m[0]+m[2]]);
            new.append(new_line[mlast_n:m[1]]);
            new.append(new_line[m[1]:m[1]+m[2]]);
            mlast_o = m[0]+m[2];
            mlast_n = m[1]+m[2];
        old.append(old_line[mlast_o:]);
        new.append(new_line[mlast_n:]);
    else:
        changes = [dict({'node':'replace','line1':line_number, 
                         'line2':line_number,'value':new_line})];
        return changes;

    changes = [];
    pos = 0;
    for k in range(len(old)):
        if old[k] != new[k]:
            p1 = 0;
            p2 = -1;
            while p1 < len(old[k]) \
                  and p1 < len(new[k]) \
                  and old[k][p1] == new[k][p1]:
                p1 += 1;

            while -p2-1 < len(old[k]) \
                  and -p2-1 < len(new[k]) \
                  and old[k][p2] == new[k][p2]:
                p2 -= 1;

            if p2 == -1:
                changes.append(dict({'node':'text_replace', 
                                     'line':line_number, 
                                     'pos_start':pos+p1,
                                     'pos_end':pos+len(old[k]),
                                     'value':new[k][p1:]}));
            else:
                changes.append(dict({'node':'text_replace',
                                     'line':line_number,
                                     'pos_start':pos+p1,
                                     'pos_end':pos+len(old[k])+p2+1,
                                     'value':new[k][p1:p2+1]}));
            pos += len(new[k]);
        else:
            pos += len(old[k]);

    return changes;
    

## Private ##
##
## Takes a changes structure and combines consecutive entries if
## the resulting code would be more compact.
##
## Must be called prior to calling normalizeLineNumbers
def consolidateChanges(changes):
    k = 0;

    while k < len(changes) - 1:
        if changes[k]['node'] == 'replace':
            if changes[k+1]['node'] == 'replace' \
               and changes[k+1]['line1'] == changes[k]['line2'] + 1:
                changes[k]['value'] += "\n" + changes[k+1]['value'];
                changes[k]['line2'] = changes[k+1]['line2'];
                del changes[k+1];
            elif changes[k+1]['node'] == 'insert' \
                 and changes[k+1]['line'] == changes[k]['line2'] + 1:
                changes[k]['value'] += "\n" + changes[k+1]['value'];
                del changes[k+1];
            elif changes[k+1]['node'] == 'delete' \
                 and changes[k+1]['line1'] == changes[k]['line2'] + 1:
                changes[k]['line2'] = changes[k+1]['line2'];
                del changes[k+1];
            else:
                k += 1;
        elif changes[k]['node'] == 'insert':
            if changes[k+1]['node'] == 'replace' and \
               changes[k+1]['line1'] == changes[k]['line']:
                ch = copy.copy(changes[k+1]);
                ch['value'] += changes[k]['value'] + "\n" + ch['value'];
                changes[k] = ch;
                del changes[k+1];
            elif changes[k+1]['node'] == 'insert' and \
                 changes[k+1]['line'] == changes[k]['line']:
                changes[k]['value'] += "\n" + changes[k+1]['value'];
                del changes[k+1];
            elif changes[k+1]['node'] == 'delete' and \
                 changes[k+1]['line1'] == changes[k]['line']:
                ch = dict({'node':'replace'});
                ch['line1'] = changes[k+1]['line1'];
                ch['line2'] = changes[k+1]['line2'];                
                ch['value'] = changes[k]['value'];
                changes[k] = ch;
                del changes[k+1];
            else:
                k += 1;

## Current code never generates these cases
## may want to uncomment this block if that changes.
##
##        elif changes[k]['node'] == 'delete':
##            if changes[k+1]['node'] == 'replace' and \
##               changes[k+1]['line1'] == changes[k]['line2']+1:
##                ch = copy.copy(changes[k+1]);
##                ch['line1'] = changes[k]['line1'];
##                changes[k] = ch;
##                del changes[k+1];
##            elif changes[k+1]['node'] == 'insert' and changes[k+1]['line']+1 == changes[k]['line2']:
##                ch = dict({'node':'replace'});
##                ch['line1'] = changes[k]['line1'];
##                ch['line2'] = changes[k]['line2'];                
##                ch['value'] = changes[k+1]['value'];
##                changes[k] = ch;
##                del changes[k+1];                
##            elif changes[k+1]['node'] == 'delete' and \
##                 changes[k+1]['line1'] == changes[k]['line2']+1:
##                changes[k]['line2'] = changes[k+1]['line2'];
##                del changes[k+1];
##            else:
##                k += 1;
            
        else:
            k += 1;

    return changes;

## Private ##
## Cleanup function addressing changes with embedded newlines.
def normalizeLineNumbers(changes):
    lshift = 0;
    for ch in changes:
        if 'line' in ch:
            ch['line'] += lshift;
        if 'line1' in ch:
            ch['line1'] += lshift;
            ch['line2'] += lshift;
        if ch['node'] == 'delete':
            lshift -= ch['line2']-ch['line1']+1;
        if ch['node'] == 'insert':
            lshift += 1;
        if ch['node'] == 'replace':
            lshift -= ch['line2']-ch['line1'];
        if 'value' in ch and ch['node'] != 'permute':
            lshift += ch['value'].count("\n");

    return changes;

## Private ##
## Changes zero indexed values to one indexed for easier human readability.
def addOne(changes):
    for ch in changes:
        if 'line' in ch:
            ch['line'] += 1;
        if 'line1' in ch:
            ch['line1'] += 1;
            ch['line2'] += 1;
        if 'pos_start' in ch:
            ch['pos_start'] += 1;
            ch['pos_end'] += 1;
        if ch['node'] == 'permute':
            per = dict();
            for key in ch['value']:
                per[key+1] = ch['value'][key] + 1;
            ch['value'] = per;

    return changes;
    
## Private ##
## Changes one indexed values back to zero indexed values.
def subtractOne(changes):
    for ch in changes:
        if 'line' in ch:
            ch['line'] -= 1;
        if 'line1' in ch:
            ch['line1'] -= 1;
            ch['line2'] -= 1;
        if 'pos_start' in ch:
            ch['pos_start'] -= 1;
            ch['pos_end'] -= 1;
        if ch['node'] == 'permute':
            per = dict();
            for key in ch['value']:
                per[key-1] = ch['value'][key] - 1;
            ch['value'] = per;

    return changes;


## Private ##
## Called by differenceGenerator for cases requiring permutations.
def permutedDifferenceGenerator(lines1, lines2, line_map):
    changes = [];

    rlnm = dict();
    rlnm[0] = 0;
    last_k = 0;
    for k in range(len(lines2)):
        if line_map[k] != rlnm[last_k] + k-last_k and line_map[k] >= 0:
            rlnm[k] = line_map[k];
            last_k = k;
    if rlnm[0] == 0:
        del rlnm[0];

    changes.append(dict({'node':'permute','length':len(lines2),'value':rlnm}));
    line_map2 = regeneratePermuteMap(len(lines2),rlnm);

    for k in range(len(lines2)):

        if line_map[k] == -1:
            if line_map2[k] < len(lines1) and \
               lines1[line_map2[k]] == lines2[k]:
                line_map[k] = line_map2[k];
                continue;
            if line_map2[k] < len(lines1) \
               and k < len(lines2) and line_map2[k] >= 0:
                changes.extend(textBlocking(lines1[line_map2[k]],lines2[k],k));
                continue;
                
            changes.append(dict({'node':"replace",
                                 'line1':k,
                                 'line2':k,
                                 'value':lines2[k]}));
        if line_map[k] == -10:
            if line_map2[k] < len(lines1) and \
               lines1[line_map2[k]] == lines2[k]:
                line_map[k] = line_map2[k];
                continue;
            if line_map2[k] >= len(lines1):
                continue;
            changes.append(dict({'node':"replace",
                                 'line1':k,
                                 'line2':k,
                                 'value':""}));

    changes = consolidateChanges(changes);
    changes = normalizeLineNumbers(changes);
    changes = addOne(changes);
    
    return changes;

    
## Creates a change structure showing the differences between old_text
## and new_text.
def differenceGenerator(old_text,new_text):
    lines1 = old_text.split("\n");
    lines2 = new_text.split("\n");

    line_map = dict();
    for k in range(len(lines2)):
        ln = lines2[k];
        if ln == "":
            k2 = k - 1;
            while k2 >= 0 and line_map[k2] < 0:
                k2 = k2 - 1;
            df = k - k2;
            if k2 >= 0:
                if line_map[k2] + df < len(lines1) and lines1[line_map[k2]+df] == "":
                    line_map[k] = line_map[k2] + df;
                else:
                    line_map[k] = -10;
            else:
                line_map[k] = -10;
            continue;
        if ln in lines1:
            if k > 0:
                if line_map[k-1]+1 < len(lines1) and line_map[k-1] >= 0 \
                   and lines1[line_map[k-1]+1] == ln:
                    line_map[k] = line_map[k-1]+1;
                    continue;
            line_map[k] = lines1.index(ln);
        else:
            line_map[k] = -1;
            if k >= 2 and line_map[k-2] == -1:
                if line_map[k-1] >= 0 and len(lines1[line_map[k-1]]) < 10:
                    line_map[k-1] = -1;

    k_last = 0;
    for k in range(1,len(line_map)):
        if line_map[k] <= line_map[k_last] and line_map[k] >= 0:
            return permutedDifferenceGenerator(lines1,lines2,line_map);
        if line_map[k] >= 0:
            k_last = k;
    
    changes = [];
    k2 = 0;
    k = 0;
    while k < len(line_map):
        if line_map[k] == k2:
            k2 = k2 + 1;
            k = k + 1;
            continue;
        if line_map[k] == -1:
            ks = k + 1;
            while (ks < len(line_map) and line_map[ks] < k2) or \
                  (ks+1 < len(line_map) and lines2[ks] == ""):
                ks += 1;
            if ks >= len(line_map):
                if k2 > 0:
                    changes.append(dict({'node':"truncate",'line':k2}));
                    changes.append(dict({'node':"append",
                                         'value':string.join(lines2[k:],"\n")}));
                else:
                    changes.append(dict({'node':"new",
                                         'value':string.join(lines2,"\n")}));
                break;
            else:
                if line_map[ks] == k2:
                    changes.append(dict({'node':"insert",
                                         'line':k2,
                                         'value':string.join(lines2[k:ks],"\n")}));
                    k = ks;
                    continue;
                else:
                    ch2 = [];
                    for j in range(k,ks):
                        if k2 + j - k  < line_map[ks]:
                            if lines2[j] != "":
                                ch2.extend(textBlocking(lines1[k2+j-k],lines2[j],k2+j-k));
                            else:
                                if len(ch2) > 0 and ch2[-1]['node'] == 'replace':
                                    ch2[-1]['line2'] += 1;
                                    ch2[-1]['value'] += "\n";
                                else:
                                    ch2.append(dict({'node':"replace",
                                                     'line1':k2+j-k,
                                                     'line2':k2+j-k,
                                                     'value':""}));
                        else:
                            ch2.append(dict({'node':"insert",
                                             'line':line_map[ks],
                                             'value':lines2[j]}));

                    if ks-k < line_map[ks]-k2:
                        ch2.append(dict({'node':"delete",
                                        'line1':k2+ks-k,
                                        'line2':line_map[ks]-1}));
                                            
                    changes.extend(ch2);

                    k2 = line_map[ks];
                    k = ks;
                    continue;
        if line_map[k] == -10:
            ks = k + 1;
            while ks < len(line_map) and line_map[ks] == -10:
                ks += 1;
            if ks < len(line_map) and k2 == line_map[ks]:
                temp = [];
                for j in range(k,ks):
                    temp.append("");
                changes.append(dict({'node':"insert",
                                     'line':k2,
                                     'value':string.join(temp,"\n")}));
                k = ks;
                continue;
            else:
                changes.append(dict({'node':"replace",
                                     'line1':k2,
                                     'line2':k2,
                                     'value':""}));
                k2 += 1;
                k += 1;
                continue;
        if line_map[k] > k2:
            changes.append(dict({'node':'delete',
                                 'line1':k2,
                                 'line2':line_map[k]-1}));
            k2 = line_map[k]+1;
            k = k + 1;
            continue;
        if line_map[k] < k2 and line_map[k] > 0:
            k2 = k2 -1;

        print k;
        k = k + 1;
        k2 = k2 + 1;        

    if k2 < len(lines1) and len(changes) > 0:
        if changes[-1]['node'] != 'append' and changes[-1]['node'] != 'truncate' \
           and changes[-1]['node'] != 'new':
            changes.append(dict({'node':"truncate",'line':k2}));
    if k2 < len(lines1) and len(changes) == 0:
        changes.append(dict({'node':"truncate",'line':k2}));

    changes = consolidateChanges(changes);
    changes = normalizeLineNumbers(changes);
    changes = addOne(changes);

    return changes;


## Reconstruct a full line map from the condensed <permute> data.
def regeneratePermuteMap(length,permute_data):
    line_map = dict();
    last_k = 0;
    line_map[0] = 0;
    for k in range(length):
        if k in permute_data:
            line_map[k] = permute_data[k];
            last_k = k;
        else:
            line_map[k] = line_map[last_k] + k-last_k;

    return line_map;


## Applies "changes" to "old_text" and returns new text 
def differenceRestorer(old_text,changes):
    changes = subtractOne(changes);

    lines1 = old_text.split("\n");
    lines2 = copy.copy(lines1);

    lshift = 0;  ##Keep track of newline insertions, faster than
                 ##restructuing each time a newline is inserted.
    
    last_line = -1;

    for k in range(len(changes)):
        ch = changes[k];
        
        ##Detect out of order operations
        current_line = -1;
        if 'line' in ch:
            current_line = ch['line'];
        elif 'line2' in ch:
            current_line = ch['line2'];
        if current_line >= 0 and current_line < last_line:
            lines2 = string.join(lines2,"\n");
            lines2 = lines2.split("\n");
            lshift = 0;
            
        if ch['node'] == 'permute':
            lines1 = lines2;
            lines2 = [];
            
            line_map = regeneratePermuteMap(ch['length'],
                                         ch['value']);

            for k in range(ch['length']):
                lines2.append("");
            for k in line_map:
                if line_map[k] >= 0 and line_map[k] < len(lines1):
                    lines2[k] = copy.copy(lines1[line_map[k]]);  
        
        elif ch['node'] == 'insert':
            lines2.insert(ch['line']-lshift,ch['value']);
        elif ch['node'] == 'replace':
            del lines2[ch['line1']-lshift:ch['line2']-lshift+1];
            lines2.insert(ch['line1']-lshift,ch['value']);            
        elif ch['node'] == 'text_replace':
            ln = lines2[ch['line']-lshift];
            ln = ln[:ch['pos_start']] + ch['value'] + ln[ch['pos_end']:];
            lines2[ch['line']-lshift] = ln;
        elif ch['node'] == 'truncate':
            del lines2[ch['line']-lshift:];
        elif ch['node'] == 'append':
            lines2.append(ch['value']);
        elif ch['node'] == 'delete':
            del lines2[ch['line1']-lshift:ch['line2']-lshift+1];
        elif ch['node'] == 'new':
            lines2 = ch['value'].split("\n");
            lshift = 0;
            last_line = -1;

        if 'value' in ch and ch['node'] != 'permute':
            new_lines = ch['value'].count("\n");
            lshift += new_lines;

            if new_lines > 0:
                if 'line' in ch:
                    last_line = ch['line'];
                elif 'line2' in ch:
                    last_line = ch['line2'];
                else:
                    last_line = len(lines2) + lshift;

    return string.join(lines2,"\n");


## A simplified XML parser that translates a <changes> block, given as a
## string, into the the changes structure used internally.
def readXMLToChanges(xml):
    tagre = re.compile("<([^>]*)(?:>([^<]*)</[^>]*>| />)",re.I+re.S);
    kwre = re.compile("([^ ]*)=\"([^\"]*)\"",re.I+re.S);
    permutere = re.compile("([0-9]*): ([0-9]*)");

    changes = []
    tags = tagre.findall(xml);
    for tg in tags:
        tag = dict();
        f1 = tg[0].find(" ");
        if f1 > -1:
            tag['node'] = tg[0][:f1];
        else:
            tag['node'] = tg[0];
        kws = kwre.findall(tg[0]);
        for kw in kws:
            if kw[1].isdigit():
                tag[kw[0]] = int(kw[1]);
            else:
                tag[kw[0]] = kw[1];                
        tag['value'] = tg[1];
        changes.append(tag);

    for ch in changes:
        if 'line' in ch:
            if ch['node'] == 'replace' or ch['node'] == 'delete':
                ch['line1'] = ch['line'];
                ch['line2'] = ch['line'];
                del ch['line'];
        if 'lines' in ch:
            f = ch['lines'].find('-');
            ch['line1'] = int(ch['lines'][:f]);
            ch['line2'] = int(ch['lines'][f+1:]);
            del ch['lines'];
        if 'pos' in ch:
            f = ch['pos'].find('-');
            ch['pos_start'] = int(ch['pos'][:f]);
            ch['pos_end'] = int(ch['pos'][f+1:]);
            del ch['pos'];
        if ch['node'] == 'permute':
            vals = permutere.findall(ch['value']);
            map_values = dict();
            for v in vals:
                map_values[int(v[0])] = int(v[1]);
            ch['value'] = map_values;
            
    return changes;
    
