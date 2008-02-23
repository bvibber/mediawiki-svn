package javax.sound.sampled;
public interface DataLine extends Line {
    public void drain();
    public void flush();
    public void start();
    public void stop();
    public boolean isRunning();
    public boolean isActive();
    public int getBufferSize();
    public int available();
    public int getFramePosition();
    public long getMicrosecondPosition();
    public static class Info extends Line.Info {
	public Info(Class lineClass, AudioFormat[] formats, int minBufferSize, int maxBufferSize) {
	  super(lineClass);
	}
	public Info(Class lineClass, AudioFormat format, int bufferSize) {
	  super(lineClass);
	}
	public Info(Class lineClass, AudioFormat format) {
	  super(lineClass);
	}
	public int getMinBufferSize() {
	    return 0;
	}
	public int getMaxBufferSize() {
	    return 0;
	}
	public boolean matches(Line.Info info) {
	    return true;
	}
    }
}
