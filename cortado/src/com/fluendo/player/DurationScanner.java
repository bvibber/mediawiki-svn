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

    final static int UNKNOWN = 0;
    final static int VORBIS = 1;
    final static int THEORA = 2;
    private static Object comment;
    private static Object info;
    private static long contentLength = -1;

    private static int determineType(Packet op) {

        // try theora
        com.fluendo.jheora.Comment tc = new com.fluendo.jheora.Comment();
        com.fluendo.jheora.Info ti = new com.fluendo.jheora.Info();

        tc.clear();
        ti.clear();

        int ret = ti.decodeHeader(tc, op);
        if (ret == 0) {
            comment = tc;
            info = ti;
            return THEORA;
        }

        // try vorbis
        com.jcraft.jorbis.Comment vc = new com.jcraft.jorbis.Comment();
        com.jcraft.jorbis.Info vi = new com.jcraft.jorbis.Info();

        vc.init();
        vi.init();

        ret = vi.synthesis_headerin(vc, op);
        if (ret == 0) {
            comment = vc;
            info = vi;
            return VORBIS;
        }

        return UNKNOWN;
    }

    public static float getDurationForInputStream(InputStream is) throws IOException {
        float time = -1;

        SyncState oy = new SyncState();
        Page og = new Page();
        Packet op = new Packet();

        Hashtable streamstates = new Hashtable();
        Hashtable streamtype = new Hashtable();
        Hashtable streamcomment = new Hashtable();
        Hashtable streaminfo = new Hashtable();
        Hashtable streamstartgranule = new Hashtable();

        oy.init();

        boolean eos = false;
        while (!eos) {

            int offset = oy.buffer(4096);
            int read = is.read(oy.data, offset, 4096);
            oy.wrote(read);
            eos = read <= 0;

            while (oy.pageout(og) == 1) {

                int serialno = og.serialno();
                StreamState os = (StreamState) streamstates.get(serialno);
                if (os == null) {
                    os = new StreamState();
                    os.init(og.serialno());
                    streamstates.put(og.serialno(), os);
                    System.out.println("DurationScanner: created StreamState for stream no. " + serialno);
                }

                os.pagein(og);

                while (os.packetout(op) == 1) {

                    Integer type = (Integer) streamtype.get(serialno);
                    if (type == null) {
                        type = determineType(op);
                        streamtype.put(serialno, type);
                        if (comment != null) {
                            streamcomment.put(serialno, comment);
                        }
                        if (info != null) {
                            streaminfo.put(serialno, info);
                        }
                        streamstartgranule.put(serialno, og.granulepos());
                    }

                    switch (type) {
                        case VORBIS:
                             {
                                com.jcraft.jorbis.Info i = (com.jcraft.jorbis.Info) streaminfo.get(serialno);
                                long startgranule = (Long) streamstartgranule.get(serialno);
                                float t = (float) (og.granulepos() - startgranule) / i.rate;
                                if (t > time) {
                                    time = t;
                                }
                            }
                            break;
                        case THEORA:
                             {
                                com.fluendo.jheora.Info i = (com.fluendo.jheora.Info) streaminfo.get(serialno);
                            }
                            break;
                    }
                }
            }
        }

        return time;
    }

    private static InputStream openWithConnection(URL url, String userId, String password, long offset) throws IOException {
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

    public static float getDurationForURL(URL url, String user, String password) throws IOException {
        int headbytes = 16 * 1024;
        int tailbytes = 80 * 1024;

        byte[] buffer = new byte[1024];
        ByteArrayOutputStream bos = new ByteArrayOutputStream();
        InputStream is = openWithConnection(url, user, password, 0);

        int read = 0;
        long pos = 0;
        read = is.read(buffer);
        while (pos < headbytes && read > 0) {
            pos += read;
            bos.write(buffer, 0, read);
            read = is.read(buffer);
        }

        is = openWithConnection(url, null, null, contentLength - tailbytes);

        read = is.read(buffer);
        while (read > 0) {
            bos.write(buffer, 0, read);
            read = is.read(buffer);
        }

        return getDurationForInputStream(new ByteArrayInputStream(bos.toByteArray()));

    }

    public static void main(String[] args) throws IOException {

        URL url;
        url = new URL(args[0]);


        System.out.println(getDurationForURL(url, null, null));

    }
}
