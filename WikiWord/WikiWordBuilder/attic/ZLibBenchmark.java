package de.brightbyte.wikiword.builder;

import java.io.BufferedReader;
import java.io.File;
import java.io.IOException;
import java.io.InputStreamReader;
import java.io.UnsupportedEncodingException;

import com.jcraft.jzlib.JZlib;
import com.jcraft.jzlib.ZStream;

import de.brightbyte.data.ByteString;
import de.brightbyte.io.ConsoleIO;
import de.brightbyte.io.IOUtil;
import de.brightbyte.util.StringUtils;

public class ZLibBenchmark {
	protected byte[] dictionary;

	public ZLibBenchmark() {
	}

	public void setDictionary(byte[] data) {
		this.dictionary = data;
	}

	protected int level = JZlib.Z_BEST_COMPRESSION;
	protected int windowbits = 15;
	protected int strategy = JZlib.Z_DEFAULT_STRATEGY;
	protected int bufferSize = 32 * 1024;
	
	public ByteString compress(byte[] data) {
		int err;

		int comprLen = bufferSize; //FIXME: data.length;

		byte[] compr = new byte[comprLen];

		ZStream c_stream = new ZStream();

		err = c_stream.deflateInit(level, 15);
		CHECK_ERR(c_stream, err, "deflateInit");

		err = c_stream.deflateParams(level, strategy);
		CHECK_ERR(c_stream, err, "deflateInit");

		err = c_stream.deflateSetDictionary(dictionary, dictionary.length);
		CHECK_ERR(c_stream, err, "deflateSetDictionary");

		long dictId = c_stream.adler;

		c_stream.next_out = compr;
		c_stream.next_out_index = 0;
		c_stream.avail_out = comprLen;

		c_stream.next_in = data;
		c_stream.next_in_index = 0;
		c_stream.avail_in = data.length;

		err = c_stream.deflate(JZlib.Z_FINISH);
		//FIXME: JZlib.Z_STREAM_END expected, getting JZlib.Z_OK
		
		if (err != JZlib.Z_STREAM_END && err != JZlib.Z_OK) {
			throw new RuntimeException("deflate should report Z_STREAM_END, found "+err);
		}
		err = c_stream.deflateEnd();
		CHECK_ERR(c_stream, err, "deflateEnd");

		return new ByteString(compr, 0, c_stream.next_out_index);
	}

	public ByteString uncompress(byte[] data, boolean ignoreChecksumm) {
		int uncomprLen = bufferSize; //FIXME: data.length * 10;
		byte[] uncompr = new byte[uncomprLen];
		ZStream d_stream = new ZStream();

		d_stream.next_in = data;
		d_stream.next_in_index = 0;
		d_stream.avail_in = data.length;

		int err = d_stream.inflateInit(windowbits);
		CHECK_ERR(d_stream, err, "inflateInit");
		d_stream.next_out = uncompr;
		d_stream.next_out_index = 0;
		d_stream.avail_out = uncomprLen;

		while (true) {
			err = d_stream.inflate(JZlib.Z_NO_FLUSH);
			if (err == JZlib.Z_STREAM_END) {
				break;
			}
			if (err == JZlib.Z_NEED_DICT) {
				/*if ((int) d_stream.adler != (int) dictId) {
					System.out.println("unexpected dictionary");
					System.exit(1);
				} */
				err = d_stream.inflateSetDictionary(dictionary,
						dictionary.length);
			}
			
			if (ignoreChecksumm && err==JZlib.Z_DATA_ERROR) break;
			else CHECK_ERR(d_stream, err, "inflate with dict");
		}

		err = d_stream.inflateEnd();
		CHECK_ERR(d_stream, err, "inflateEnd");

		int j = 0;
		for (; j < uncompr.length; j++)
			if (uncompr[j] == 0)
				break;

		return new ByteString(uncompr, 0, d_stream.next_out_index);
	}
	
	public ByteString getPrefix(ByteString b) {
		return b.subString(0, 6);
	}
	
	public ByteString strip(ByteString b) {
		return b.subString(6, b.length()-5);
	}

	public ByteString pad(ByteString prefix, ByteString b, ByteString suffix) {
		return ByteString.concat(prefix, b, suffix);
	}

	public static void main(String[] args) throws IOException {

		String d = args[0];
		String denc = "UTF-8";
		String enc = "UTF-8";
		
		String dict = IOUtil.slurp(new File(d), denc);

		ZLibBenchmark app = new ZLibBenchmark();
		app.setDictionary(dict.getBytes(enc));
		
		ByteString b = app.compress("dummy".getBytes());
		ByteString prefix = app.getPrefix(b);
		ByteString suffix = new ByteString( new byte[] {0, 0, 0, 0, 0} );
		
		BufferedReader r = new BufferedReader( new InputStreamReader( System.in ));
		String s;
		while ((s = r.readLine()) != null) {
			s = s.trim();
			if (s.length()==0) continue;
			
			byte[] data = s.getBytes(enc);
			System.out.println("UTF-16: "+s.length()*2+" bytes");
			System.out.println(enc+": "+data.length+" bytes: "+StringUtils.hex(data));
			b = app.compress(data);
			System.out.println("compressed: "+b.length()+" bytes: "+b.toString());
			b = app.strip(b);
			System.out.println("stripped: "+b.length()+" bytes: "+b.toString());
			b = app.pad(prefix, b, suffix);
			b = app.uncompress(b.getBytes(), true);
			System.out.println("uncompressed: "+b.length()+" bytes, "+new String(b.getBytes(), enc));
		}
	}

	static void CHECK_ERR(ZStream z, int err, String msg) {
		if (err != JZlib.Z_OK)
			throw new RuntimeException(z.msg + "; code: " + err);
	}

}
