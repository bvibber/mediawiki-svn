package javax.sound.sampled;

public interface Line {
    public Line.Info getLineInfo();
    public void open() throws LineUnavailableException;
    public void close();
    public static class Info {
	public Info(Class lineClass) {
	}
	public Class getLineClass() {
	    return null;
	}
	public boolean matches(Info info) {
	    return true;
	}
    }
}
