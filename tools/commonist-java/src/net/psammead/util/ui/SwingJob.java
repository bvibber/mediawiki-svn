package net.psammead.util.ui;

public interface SwingJob<V> {
    V construct() throws Exception;
    void finished(V v);
    void failed(Exception e);
}
