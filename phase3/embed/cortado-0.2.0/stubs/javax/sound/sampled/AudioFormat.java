package javax.sound.sampled;

public class AudioFormat {
    public AudioFormat(Encoding encoding, float sampleRate, int sampleSizeInBits,
		       int channels, int frameSize, float frameRate, boolean bigEndian) {
    }
    public AudioFormat(float sampleRate, int sampleSizeInBits,
		       int channels, boolean signed, boolean bigEndian) {
    }
    public static class Encoding {
	public static final Encoding PCM_SIGNED = new Encoding("PCM_SIGNED");
	public static final Encoding PCM_UNSIGNED = new Encoding("PCM_UNSIGNED");
	public static final Encoding ULAW = new Encoding("ULAW");
	public static final Encoding ALAW = new Encoding("ALAW");
	protected Encoding(String name) {
	}
    }
}

