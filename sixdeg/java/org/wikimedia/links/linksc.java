/* $Id$ */
/*
 * Six degrees of Wikipedia: Java client.
 * This source code is released into the public domain.
 */
package org.wikimedia.links;

import java.util.Map;
import java.util.HashMap;
import java.net.Socket;
import java.net.URLEncoder;
import java.net.URL;
import java.net.HttpURLConnection;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStream;
import java.io.IOException;
import java.io.BufferedReader;
import java.io.BufferedInputStream;
import java.io.BufferedReader;

public class linksc {
	public static class PathEntry {
		PathEntry() {
			context = "";
		}

		public String getArticle() {
			return article;
		}

		public int getId() {
			return text_id;
		}

		private static final String wikiproxy = "http://127.0.0.1/~daniel/WikiSense/WikiProxy.php?go=Fetch&wiki=enwiki";
		public void setContext(PathEntry nextlink) {
			try {
				String urltxt = wikiproxy + "&title=" + article + "&rev=" + text_id;
				URL url = new URL(urltxt);
				HttpURLConnection conn = (HttpURLConnection) url.openConnection();
				conn.setRequestProperty("User-Agent", "Six-Degrees-Of-Wikipedia; (contact: river)");
				conn.connect();
				if (conn.getResponseCode() != 200) {
					context = "<cannot retrieve text [" + article + "]:" + text_id + " (" + urltxt + ")>";
					return;
				}

				BufferedReader dis = new BufferedReader(new InputStreamReader(
							new BufferedInputStream(conn.getInputStream())));
				char[] data = new char[1024];
				int i;
				StringBuilder sb = new StringBuilder();
				while ((i = dis.read(data, 0, 1024)) > -1) {
					sb.append(data, 0, i);
				}
				String text = sb.toString();
				String pfx = "[[" + nextlink.article.replaceAll("_", " ").toLowerCase();
				String link1 = pfx + "|";
				String link2 = pfx + "]]";
				int found = text.toLowerCase().indexOf(link1);
				if (found == -1)
					found = text.toLowerCase().indexOf(link2);
				if (found == -1) {
					context = "<no match>";
					return;
				}

				found -= 50;
				if (found < 0)
					found = 0;
				int end = found + 100;
				if (end > text.length() - 1)
					end = text.length() - 1;

				context = text.substring(found, end);
			} catch (IOException e) {
				context = "<cannot retrieve text (" + e.getMessage() + ")";
			}
		}

		public String getContext() {
			return context;
		}

		public String article;
		public int text_id;
		public String context;
	};

	public PathEntry[] findPath(String from, String to, boolean ignoreDates) throws ErrorException {
		try {
			Socket s;
			s = new Socket("127.0.0.1", 6534);
			OutputStream out = s.getOutputStream();
			InputStream in = s.getInputStream();

			RequestEncoder enc = new RequestEncoder();
			Map<String, String> args = new HashMap<String, String>();
			args.put("from", from);
			args.put("to", to);
			if (ignoreDates)
				args.put("ignore_dates", "1");

			byte[] request = enc.encodeRequest(args);

			out.write(request);

			byte[] resp = new byte[4096];
			in.read(resp);

			RequestDecoder dec = new RequestDecoder();
			Map<String, String> reply = dec.decodeRequest(resp);
			String x;

			if ((x = reply.get("error")) != null) {
				if (x.equals("no_to"))
					throw new ErrorException("Target article does not exist");
				else if (x.equals("no_from"))
					throw new ErrorException("Source article does not exist");
				else if (x.equals("illegal_request"))
					throw new ErrorException("Server rejected path request");
				else
					throw new ErrorException("Unknown server error " + x);
			}

			if ((x = reply.get("path")) == null)
				throw new ErrorException("Server response contains neither path nor error");

			String[] rawpath = x.split("\\|");
			PathEntry[] path = new PathEntry[rawpath.length];

			for (int i = 0; i < rawpath.length; ++i) {
				String[] q = rawpath[i].split("#");

				PathEntry e = new PathEntry();
				e.article = q[0];
				e.text_id = Integer.parseInt(q[1]);
				path[i] = e;
			}

			for (int i = 0; i < path.length - 1; ++i)
				path[i].setContext(path[i + 1]);

			return path;
		} catch (IOException e) {
			throw new ErrorException(e.getMessage());
		}
	}

	public static void main(String[] args) {
		linksc c = new linksc();
		PathEntry[] result = null;
		try {
			result = c.findPath(args[0], args[1], false);
		} catch (ErrorException e) {
			System.out.printf("Error: %s\n", e.geterror());
			return;
		}
		for (PathEntry e: result)
			System.out.printf("%s (id %d)\n", e.article, e.text_id);
	}
}
