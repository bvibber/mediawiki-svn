package sun.audio;

import java.io.*;

public class AudioStream extends java.io.FilterInputStream
{
  public AudioStream(InputStream is) throws IOException {
    super(is);
  }

  
}
