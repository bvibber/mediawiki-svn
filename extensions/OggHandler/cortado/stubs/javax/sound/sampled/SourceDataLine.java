package javax.sound.sampled;	

public interface SourceDataLine extends DataLine {
    public void open(AudioFormat format, int bufferSize) throws LineUnavailableException;
    public void open(AudioFormat format) throws LineUnavailableException;
    public int write(byte[] b, int off, int len);
}
