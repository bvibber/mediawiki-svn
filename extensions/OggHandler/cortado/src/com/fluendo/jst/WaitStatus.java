package com.fluendo.jst;

public class WaitStatus {
  int status;
  long jitter;

  public static final int OK          =  0;
  public static final int LATE        =  1;
  public static final int UNSCHEDULED =  2;
  public static final int BUSY        =  3;
  public static final int BADTIME     =  4;
  public static final int ERROR       =  5;
  public static final int UNSUPPORTED =  6;

  WaitStatus(int status_, long jitter_) {
    status = status_;
    jitter = jitter_;
  }

  WaitStatus() {
    status = ERROR;
    jitter = 0;
  }

  public static WaitStatus newOK() {
    return new WaitStatus(OK, 0);
  }
}
