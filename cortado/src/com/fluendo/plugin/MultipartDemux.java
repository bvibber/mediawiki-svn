/* Copyright (C) <2004> Wim Taymans <wim@fluendo.com>
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Library General Public
 * License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Library General Public License for more details.
 *
 * You should have received a copy of the GNU Library General Public
 * License along with this library; if not, write to the
 * Free Software Foundation, Inc., 59 Temple Place - Suite 330,
 * Boston, MA 02111-1307, USA.
 */

package com.fluendo.plugin;

import java.util.*;
import com.fluendo.jst.*;
import com.fluendo.utils.*;

public class MultipartDemux extends Element {
    private static final String MIME = "multipart/x-mixed-replace";

    private static final String DEFAULT_BOUNDARY = "--ThisRandomString";

    private Vector streams;

    private byte[] accum;

    private int accumSize;

    private int accumPos;

    private int dataEnd;

    private static final int STATE_FIND_BOUNDARY = 1;

    private static final int STATE_PARSE_HEADERS = 2;

    private static final int STATE_FIND_DATA_END = 3;

    private int state = STATE_FIND_BOUNDARY;

    private String boundaryString = DEFAULT_BOUNDARY;

    private byte[] boundary = boundaryString.getBytes();

    private int boundaryLen = boundary.length;

    private static final byte[] headerEnd = "\n".getBytes();

    private static final int headerEndLen = headerEnd.length;

    private static final String contentType = "content-type: ";

    private static final int contentTypeLen = contentType.length();

    private MultipartStream currentStream = null;

    class MultipartStream extends Pad {
        private String mimeType;

        public MultipartStream(String mime) {
            super(Pad.SRC, "src_" + mime);

            mimeType = mime;
            caps = new Caps(mime);
        }

        protected boolean eventFunc(com.fluendo.jst.Event event) {
            return sinkpad.pushEvent(event);
        }
    }

    private Pad sinkpad = new Pad(Pad.SINK, "sink") {

        protected boolean setCapsFunc(Caps caps) {
            String mime = caps.getMime();
            String capsBoundary;

            if (!mime.equals(MIME)) {
                postMessage(Message.newError(this, "expected \"" + mime
                        + "\", got \"" + mime + "\""));
                return false;
            }

            capsBoundary = caps.getFieldString("boundary", DEFAULT_BOUNDARY);

            Debug.log(Debug.INFO, this + " boundary string: \"" + capsBoundary
                    + "\"");

            boundaryString = capsBoundary + "\n";
            boundary = boundaryString.getBytes();
            boundaryLen = boundary.length;

            return true;
        }

        private MultipartStream findStream(String mime) {
            MultipartStream stream = null;
            for (int i = 0; i < streams.size(); i++) {
                stream = (MultipartStream) streams.elementAt(i);
                if (stream.mimeType.equals(mime))
                    break;
                stream = null;
            }
            return stream;
        }

        private boolean forwardEvent(com.fluendo.jst.Event event) {
            for (int i = 0; i < streams.size(); i++) {
                MultipartStream stream = (MultipartStream) streams.elementAt(i);
                stream.pushEvent(event);
            }
            return true;
        }

        protected boolean eventFunc(com.fluendo.jst.Event event) {
            switch (event.getType()) {
            case Event.FLUSH_START:
                forwardEvent(event);
                synchronized (streamLock) {
                    Debug.log(Debug.INFO, "synced " + this);
                }
                break;
            case Event.NEWSEGMENT:
            case Event.FLUSH_STOP:
            case Event.EOS:
                synchronized (streamLock) {
                    forwardEvent(event);
                }
                break;
            default:
                forwardEvent(event);
                break;
            }
            return true;
        }

        /*
         * copy the buffer data into our buffer. If we need to enlarge the
         * buffer, we can flush out any skipped bytes
         */
        private void accumulateBuffer(com.fluendo.jst.Buffer buf) {
            int lastPos = accumSize + accumPos;

            /* make room */
            if (accum.length < lastPos + buf.length) {
                byte[] newAcum;

                /*
                 * FIXME, check if we need a bigger buffer or if we can just
                 * shrink the current one
                 */
                newAcum = new byte[accum.length + buf.length];
                System.arraycopy(accum, accumPos, newAcum, 0, accumSize);
                accum = newAcum;
                accumPos = 0;
                lastPos = accumSize;
            }
            System.arraycopy(buf.data, buf.offset, accum, lastPos, buf.length);
            accumSize += buf.length;
            // System.out.println("added "+buf.length+" pos "+accumPos+" size
            // "+accumSize);
        }

        private void flushBytes(int bytes) {
            accumPos += bytes;
            accumSize -= bytes;
            // System.out.println("flushing "+bytes+" pos "+accumPos+" size
            // "+accumSize);
        }

        /*
         * find bytes of consecutive bytes in the buffer. This function returns
         * the position in the buffer where the bytes were found or -1 if the
         * bytes were not found.
         */
        private int findBytes(int startPos, byte[] bytes, int bytesLen) {
            /*
             * startPos is the first byte in the buffer where we should start
             * scanning.
             */
            int scanPos = startPos;
            /* we always start comparing the first byte of the bytes */
            int pos = 0;
            int size = accumSize;

            // System.out.println("findBytes: size:"+size+" "+bytesLen);
            /* while we have enough data to compare */
            while (size > bytesLen) {
                /* check if we have a match */
                if (accum[scanPos] == bytes[pos]) {
                    // System.out.println("match: scanPos:"+scanPos+"
                    // "+accum[scanPos]+" pos:"+pos+" "+bytes[pos]);
                    /* position to compare the next byte */
                    pos++;
                    if (pos == bytesLen) {
                        /*
                         * we found all consecutive bytes, we have a complete
                         * match
                         */
                        return startPos;
                    }
                } else {
                    /*
                     * we have no match, flush our buffer to next byte and start
                     * scanning for the first byte in the bytes string again.
                     * The data size decrements.
                     */
                    // System.out.println("fail: scanPos:"+scanPos+"
                    // "+accum[scanPos]+" pos:"+pos+" "+bytes[pos]);
                    scanPos -= pos;
                    size += pos;
                    startPos = scanPos + 1;
                    pos = 0;
                }
                /* move to next byte */
                scanPos++;
                size--;
            }
            return -1;
        }

        /*
         * find boundary bytes of consecutive bytes in the buffer. This function
         * returns true if the bytes where found with the accumPos position
         * pointing to the byte in the buffer.
         */
        private boolean findBoundary() {
            int pos = findBytes(accumPos, boundary, boundaryLen);
            if (pos != -1) {
                flushBytes(pos - accumPos);
                // System.out.println("buffer now: sync at "+accumPos);
            }
            return pos != -1;
        }

        /*
         * read the headers up to the first \n\n sequence. we store the
         * Content-Type: header in lastContentType
         */
        private boolean parseHeaders() {
            int headerStart = accumPos;
            int prevHdr;

            while (true) {
                prevHdr = headerStart;

                int pos = findBytes(headerStart, headerEnd, headerEndLen);
                if (pos == -1)
                    return false;

                if (pos == prevHdr) {
                    /* all headers parsed */
                    flushBytes(pos + 1 - accumPos);
                    return true;
                }
                String header = new String(accum, headerStart, pos
                        - headerStart);
                header = header.toLowerCase();

                if (header.startsWith(contentType)) {
                    String mime = header.substring(contentTypeLen).trim();

                    currentStream = findStream(mime);
                    if (currentStream == null) {
                        currentStream = new MultipartStream(mime);
                        streams.addElement(currentStream);
                        addPad(currentStream);
                    }
                }

                /* go to next header */
                headerStart = pos + 1;
            }
        }

        private boolean findDataEnd() {
            int pos = findBytes(accumPos, boundary, boundaryLen);
            // System.out.println("find data end "+accumPos+" size "+accumSize+"
            // pos "+pos);
            if (pos != -1) {
                dataEnd = pos - 1;
            }
            return pos != -1;
        }

        protected int chainFunc(com.fluendo.jst.Buffer buf) {
            int flowRet = OK;

            // System.out.println("input");
            // MemUtils.dump (buf.data, buf.offset, buf.length);

            accumulateBuffer(buf);
            buf.free();

            switch (state) {
            case STATE_FIND_BOUNDARY:
                if (!findBoundary())
                    break;
                /* skip boundary */
                flushBytes(boundary.length);
                state = STATE_PARSE_HEADERS;
            /* fallthrough */
            case STATE_PARSE_HEADERS:
                if (!parseHeaders())
                    break;
                state = STATE_FIND_DATA_END;
            /* fallthrough */
            case STATE_FIND_DATA_END:
                if (!findDataEnd())
                    break;

                com.fluendo.jst.Buffer data = com.fluendo.jst.Buffer.create();
                int dataSize = dataEnd - accumPos;

                // System.out.println("dataSize: "+dataSize);
                // MemUtils.dump (accum, accumPos, dataSize);

                data.copyData(accum, accumPos, dataSize);
                data.time_offset = -1;
                data.timestamp = -1;

                /* skip data */
                flushBytes(dataSize);

                /* and push */
                flowRet = currentStream.push(data);
                state = STATE_FIND_BOUNDARY;
                break;
            default:
                flowRet = ERROR;
                break;
            }
            // System.out.println("return "+flowRet);
            return flowRet;
        }
    };

    public String getFactoryName() {
        return "multipartdemux";
    }

    public String getMime() {
        return MIME;
    }

    public int typeFind(byte[] data, int offset, int length) {
        return -1;
    }

    public MultipartDemux() {
        super();

        accum = new byte[8192];
        accumSize = 0;
        accumPos = 0;

        streams = new Vector();

        addPad(sinkpad);
    }
}
