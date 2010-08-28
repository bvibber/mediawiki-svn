package net.psammead.functional;

/** exceptions thrown within Function calculations */
public class Unchecked extends RuntimeException {
    public Unchecked(Throwable cause) {
        super(cause);
        if (cause == null)	throw new IllegalArgumentException("cause may not be null");
    }
}
