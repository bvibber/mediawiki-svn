package sun.audio;

public class AudioPlayer extends java.lang.Thread
{
  public static final AudioPlayer player = new AudioPlayer();

  public synchronized void stop(java.io.InputStream as) {}
  public synchronized void start(java.io.InputStream as) {}
}
