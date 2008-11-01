/* Cortado - a video player java applet
 * Copyright (C) 2004 Fluendo S.L.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Street #330, Boston, MA 02111-1307, USA.
 */

package com.fluendo.jst;

public class Message {

  /* types */
  public static final int EOS               = (1 << 0);
  public static final int ERROR             = (1 << 1);
  public static final int WARNING           = (1 << 2);
  public static final int INFO              = (1 << 3);
  public static final int TAG               = (1 << 4);
  public static final int BUFFERING         = (1 << 5);
  public static final int STATE_CHANGED     = (1 << 6);
  public static final int STATE_DIRTY       = (1 << 7);
  public static final int STEP_DONE         = (1 << 8);
  public static final int CLOCK_PROVIDE     = (1 << 9);
  public static final int CLOCK_LOST        = (1 << 10);
  public static final int NEW_CLOCK         = (1 << 11);
  public static final int STRUCTURE_CHANGE  = (1 << 12);
  public static final int STREAM_STATUS     = (1 << 13);
  public static final int APPLICATION       = (1 << 14);
  public static final int ELEMENT           = (1 << 15);
  public static final int SEGMENT_START     = (1 << 16);
  public static final int SEGMENT_DONE      = (1 << 17);
  public static final int DURATION          = (1 << 18);
  public static final int RESOURCE          = (1 << 19);
  public static final int BYTEPOSITION      = (1 << 20);

  private com.fluendo.jst.Object src;
  private int type;

  private boolean boolVal;
  private int intVal;
  private long longVal;
  private String stringVal;
  private int old, next, pending;

  private Message(com.fluendo.jst.Object src, int type) {
    this.src = src;
    this.type = type;
  }

  public com.fluendo.jst.Object getSrc () {
    return src;
  }
  public int getType () {
    return type;
  }
  public String toString ()
  {
    switch (type) {
      case EOS:
        return "[Message]: "+src+" type: EOS";
      case BUFFERING:
        return "[Message]: "+src+" type: BUFFERING, busy:"+boolVal+", percent:"+intVal;
      case STATE_CHANGED:
        return "[Message]: "+src+" type: STATE_CHANGED, old: " + Element.getStateName(old) +
	  ", next: " + Element.getStateName(next)+
	  ", pending: " + Element.getStateName(pending);
      case STATE_DIRTY:
        return "[Message]: "+src+" type: STATE_DIRTY";
      case STREAM_STATUS:
        return "[Message]: "+src+" type: STREAM_STATUS, "+(boolVal?"start":"stop")+", reason: "+
			Pad.getFlowName(intVal)+", "+stringVal;
      case ERROR:
        return "[Message]: "+src+" type: ERROR, "+stringVal;
      default:
        return "[Message]: "+src+" type: "+type;
    }
  }

  public static Message newEOS(com.fluendo.jst.Object src) {
    return new Message(src, EOS);
  }
  public static Message newError(com.fluendo.jst.Object src, String str) {
    Message msg;

    msg = new Message(src, ERROR);
    msg.stringVal = str;

    return msg;
  }
  public static Message newWarning(com.fluendo.jst.Object src, String str) {
    Message msg;

    msg = new Message(src, WARNING);
    msg.stringVal = str;

    return msg;
  }
  public String parseErrorString() {
    return stringVal;
  }

  public static Message newBuffering(com.fluendo.jst.Object src, boolean busy, int percent) {
    Message msg;
    
    msg = new Message(src, BUFFERING);
    msg.boolVal = busy;
    msg.intVal = percent;

    return msg;
  }
  public boolean parseBufferingBusy() {
    return boolVal;
  }
  public int parseBufferingPercent() {
    return intVal;
  }

  public static Message newStateChanged(com.fluendo.jst.Object src, int old, int next, int pending) {
    Message msg = new Message(src, STATE_CHANGED);
    msg.old = old;
    msg.next = next;
    msg.pending = pending;
    return msg;
  }
  public int parseStateChangedOld() {
    return old;
  }
  public int parseStateChangedNext() {
    return next;
  }
  public int parseStateChangedPending() {
    return pending;
  }

  public static Message newStateDirty(com.fluendo.jst.Object src) {
    return new Message(src, STATE_DIRTY);
  }

  public static Message newStreamStatus(com.fluendo.jst.Object src, boolean start, int reason, String aString) {
    Message msg = new Message(src, STREAM_STATUS);
    msg.stringVal = aString;
    msg.boolVal = start;
    msg.intVal = reason;
    return msg;
  }
  public String parseStreamStatusString() {
    return stringVal;
  }
  public boolean parseStreamStatusStart() {
    return boolVal;
  }
  public int parseStreamStatusReason() {
    return intVal;
  }

  public static Message newResource(com.fluendo.jst.Object src, String aString) {
    Message msg = new Message(src, RESOURCE);
    msg.stringVal = aString;
    return msg;
  }
  public String parseResourceString() {
    return stringVal;
  }
  public static Message newDuration(com.fluendo.jst.Object src, int aFmt, long aDur) {
    Message msg = new Message(src, DURATION);
    msg.intVal = aFmt;
    msg.longVal = aDur;
    return msg;
  }
  public int parseDurationFormat() {
    return intVal;
  }
  public long parseDurationValue() {
    return longVal;
  }
  public static Message newBytePosition(com.fluendo.jst.Object src, long aPos) {
    Message msg = new Message(src, BYTEPOSITION);
    msg.longVal = aPos;
    return msg;
  }
  public long parseBytePosition() {
      return longVal;
  }

}
