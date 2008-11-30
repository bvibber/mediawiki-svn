/* Copyright (C) <2008> Maik Merten <maikmerten@googlemail.com>
 * Copyright (C) <2004> Wim Taymans <wim@fluendo.com> (HTTPSrc.java parts)
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
package com.fluendo.player;

import com.fluendo.utils.Base64Converter;
import com.fluendo.utils.Debug;
import com.jcraft.jogg.Packet;
import com.jcraft.jogg.Page;
import com.jcraft.jogg.StreamState;
import com.jcraft.jogg.SyncState;
import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.URL;
import java.net.URLConnection;
import java.text.MessageFormat;
import java.util.Hashtable;
import java.util.Locale;

/**
 *
 * @author maik
 */
public class DurationScanner {

    final static int NOTDETECTED = -1;
    final static int UNKNOWN = 0;
    final static int VORBIS = 1;
    final static int THEORA = 2;
    private static long contentLength = -1;

    private InputStream openWithConnection(URL url, String userId, String password, long offset) throws IOException {
        // lifted from HTTPSrc.java
        InputStream dis = null;
        String userAgent = "Cortado";

        URLConnection uc = url.openConnection();

        uc.setRequestProperty("Connection", "Keep-Alive");

        String range;
        if (offset != 0 && contentLength != -1) {
            range = "bytes=" + offset + "-" + (contentLength - 1);
        } else if (offset != 0) {
            range = "bytes=" + offset + "-";
        } else {
            range = null;
        }
        if (range != null) {
            Debug.info("doing range: " + range);
            uc.setRequestProperty("Range", range);
        }

        uc.setRequestProperty("User-Agent", userAgent);
        if (userId != null && password != null) {
            String userPassword = userId + ":" + password;
            String encoding = Base64Converter.encode(userPassword.getBytes());
            uc.setRequestProperty("Authorization", "Basic " + encoding);
        }
        uc.setRequestProperty("Content-Type", "application/octet-stream");

        /* This will send the request. */
        dis = uc.getInputStream();

        String responseRange = uc.getHeaderField("Content-Range");
        long responseOffset;
        if (responseRange == null) {
            Debug.info("Response contained no Content-Range field, assuming offset=0");
            responseOffset = 0;
        } else {
            try {
                MessageFormat format = new MessageFormat("bytes {0,number}-{1,number}", Locale.US);
                java.lang.Object parts[] = format.parse(responseRange);
                responseOffset = ((Number) parts[0]).longValue();
                if (responseOffset < 0) {
                    responseOffset = 0;
                }
                Debug.debug("Stream successfully with offset " + responseOffset);
            } catch (Exception e) {
                Debug.info("Error parsing Content-Range header");
                responseOffset = 0;
            }
        }

        contentLength = uc.getHeaderFieldInt("Content-Length", -1) + responseOffset;

        return dis;
    }

    private void determineType(Packet op, StreamInfo info) {

        // try theora
        com.fluendo.jheora.Comment tc = new com.fluendo.jheora.Comment();
        com.fluendo.jheora.Info ti = new com.fluendo.jheora.Info();

        tc.clear();
        ti.clear();

        int ret = ti.decodeHeader(tc, op);
        if (ret == 0) {
            info.decoder = ti;
            info.type = THEORA;
            info.decodedHeaders++;
            return;
        }

        // try vorbis
        com.jcraft.jorbis.Comment vc = new com.jcraft.jorbis.Comment();
        com.jcraft.jorbis.Info vi = new com.jcraft.jorbis.Info();

        vc.init();
        vi.init();

        ret = vi.synthesis_headerin(vc, op);
        if (ret == 0) {
            info.decoder = vi;
            info.type = VORBIS;
            info.decodedHeaders++;
            return;
        }
        
        info.type = UNKNOWN;
    }

    public float getDurationForInputStream(InputStream is) {
        try {
            float time = -1;

            SyncState oy = new SyncState();
            Page og = new Page();
            Packet op = new Packet();

            Hashtable streaminfo = new Hashtable();

            oy.init();

            boolean eos = false;
            while (!eos) {

                int offset = oy.buffer(4096);
                int read = is.read(oy.data, offset, 4096);
                oy.wrote(read);
                eos = read <= 0;

                while (oy.pageout(og) == 1) {

                    Integer serialno = new Integer(og.serialno());
                    StreamInfo info = (StreamInfo) streaminfo.get(serialno);
                    if (info == null) {
                        info = new StreamInfo();
                        info.streamstate = new StreamState();
                        info.streamstate.init(og.serialno());
                        streaminfo.put(serialno, info);
                        System.out.println("DurationScanner: created StreamState for stream no. " + serialno);
                    }

                    info.streamstate.pagein(og);

                    while (info.streamstate.packetout(op) == 1) {

                        int type = info.type;
                        if (type == NOTDETECTED) {
                            determineType(op, info);
                            info.startgranule = og.granulepos();
                        }

                        switch (type) {
                            case VORBIS:
                                 {
                                    com.jcraft.jorbis.Info i = (com.jcraft.jorbis.Info) info.decoder;
                                    float t = (float) (og.granulepos() - info.startgranule) / i.rate;
                                    if (t > time) {
                                        time = t;
                                    }
                                }
                                break;
                            case THEORA:
                                 {
                                    com.fluendo.jheora.Info i = (com.fluendo.jheora.Info) info.decoder;
                                }
                                break;
                        }
                    }
                }
            }
            return time;
        } catch (IOException e) {
            return -1;
        }
    }

    public float getDurationForURL(URL url, String user, String password) {
        try {
            int headbytes = 16 * 1024;
            int tailbytes = 80 * 1024;

            byte[] buffer = new byte[1024];
            ByteArrayOutputStream bos = new ByteArrayOutputStream();
            InputStream is = openWithConnection(url, user, password, 0);

            int read = 0;
            long totalbytes = 0;
            read = is.read(buffer);
            // read beginning of the stream
            while (totalbytes < headbytes && read > 0) {
                totalbytes += read;
                bos.write(buffer, 0, read);
                read = is.read(buffer);
            }

            is = openWithConnection(url, user, password, contentLength - tailbytes);

            read = is.read(buffer);
            // read tail until eos, also abort if way too many bytes have been read
            while (read > 0 && totalbytes < (headbytes + tailbytes) * 2) {
                totalbytes += read;
                bos.write(buffer, 0, read);
                read = is.read(buffer);
            }

            return getDurationForInputStream(new ByteArrayInputStream(bos.toByteArray()));
        } catch (IOException e) {
            return -1;
        }
    }
    
    private class StreamInfo {
        public Object decoder;
        public int decodedHeaders = 0;
        public int type = NOTDETECTED;
        public long startgranule;
        public StreamState streamstate;
    }

    public static void main(String[] args) throws IOException {

        URL url;
        url = new URL(args[0]);


        System.out.println(new DurationScanner().getDurationForURL(url, null, null));

    }
}
