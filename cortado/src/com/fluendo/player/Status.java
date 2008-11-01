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

package com.fluendo.player;

import java.awt.*;
import java.awt.image.*;
import java.awt.event.*;
import java.util.*;

public class Status extends Component implements MouseListener,
        MouseMotionListener {
    private static final long serialVersionUID = 1L;

    private int bufferPercent;
    private boolean buffering;

    private String message;
    private String error;

    private Rectangle r;
    private Component component;

    private Font font = new Font("SansSerif", Font.PLAIN, 10);

    private boolean haveAudio;
    private boolean havePercent;
    private boolean seekable;
    private boolean live;
    private boolean showSpeaker;

    private static final int NONE = 0;
    private static final int BUTTON1 = 1;
    private static final int BUTTON2 = 2;
    private static final int SEEKER = 3;
    private static final int SEEKBAR = 4;
    private int clicked = NONE;

    private Color button1Color;
    private Color button2Color;
    private Color seekColor;

    private static final int SPEAKER_WIDTH = 12;
    private static final int SPEAKER_HEIGHT = 10;
    private static final int TIME_WIDTH = 38;
    private static final int SEEK_TIME_GAP = 10;
    private static final int THUMB_WIDTH = 9;

    public static final int STATE_STOPPED = 0;
    public static final int STATE_PAUSED = 1;
    public static final int STATE_PLAYING = 2;

    private int state = STATE_STOPPED;

    private double position = 0;
    private long time;
    private double duration;
    private long byteDuration;
    private long bytePosition;

    private String speaker = "\0\0\0\0\0\357\0\0\357U\27"
            + "\36\0\0\0\0\357\357\0\0" + "\0\357U\30\0\0\0\357\0\357"
            + "\0\357\0\0\357\23\357" + "\357\357\0\34\357\0Z\357\0"
            + "\357\\\357\0)+F\357\0\0\357" + "\0\357r\357Ibz\221\357"
            + "\0\0\357\0\357r\357\357\357" + "\276\323\357\0Z\357\0\357"
            + "\\\0\0\0\357\357\357\0" + "\357\0\0\357\0\0\0\0\0\357"
            + "\357\0\0\0\357\\\0\0\0" + "\0\0\0\357\0\0\357\\\0\0";

    private Image speakerImg;
    private int speakerWidth; // width of the speaker icon or zero if hidden


    private Vector listeners = new Vector();

    public Status(Component comp) {
        int[] pixels = new int[SPEAKER_WIDTH * SPEAKER_HEIGHT];
        component = comp;

        for (int i = 0; i < SPEAKER_WIDTH * SPEAKER_HEIGHT; i++) {
            pixels[i] = 0xff000000 | (speaker.charAt(i) << 16)
                    | (speaker.charAt(i) << 8) | (speaker.charAt(i));
        }
        speakerImg = comp.getToolkit().createImage(
                new MemoryImageSource(SPEAKER_WIDTH, SPEAKER_HEIGHT, pixels, 0, SPEAKER_WIDTH));
        button1Color = Color.black;
        button2Color = Color.black;
        seekColor = Color.black;
    }

    public void addStatusListener(StatusListener l) {
        listeners.addElement(l);
    }

    public void removeStatusListener(StatusListener l) {
        listeners.remove(l);
    }

    public void notifyNewState(int newState) {
        for (Enumeration e = listeners.elements(); e.hasMoreElements();) {
            ((StatusListener) e.nextElement()).newState(newState);
        }
    }

    public void notifySeek(double position) {
        for (Enumeration e = listeners.elements(); e.hasMoreElements();) {
            ((StatusListener) e.nextElement()).newSeek(position);
        }
    }

    public void update(Graphics g) {
        paint(g);
    }

    private void paintBox(Graphics g) {
        g.setColor(Color.darkGray);
        g.drawRect(0, 0, r.width - 1, r.height - 1);
        g.setColor(Color.black);
        g.fillRect(1, 1, r.width - 2, r.height - 2);
    }

    private void paintPercent(Graphics g) {
        if (havePercent) {
            g.setColor(Color.white);
            g.drawString("" + bufferPercent + "%", r.width - 26 - speakerWidth, r.height - 2);
        }
    }

    private void paintButton1(Graphics g) {
	int x,y,w,h;

	x = 1;
	y = 1;
	w = r.height-2;
	h = r.height-2;
        g.setColor(Color.darkGray);
        g.drawRect(x, y, w, h);
        g.setColor(button1Color);
        g.fillRect(x+1, y+1, w-1, h-1);

        if (state == STATE_PLAYING) {
            g.setColor(Color.white);
            if (live) {
	      /* STOP */
              g.fillRect((int)(w * .4), (int)(w * .4), (int)(w * .5), (int)(w * .5));
	    }
	    else {
	      /* PAUSE */
              g.fillRect((int)(w * .4), (int)(h * .4), (int)(w * .2), (int)(h * .5));
              g.fillRect((int)(w * .7), (int)(h * .4), (int)(w * .2), (int)(h * .5));
	    }
        } else {
            int triangleX[] = { (int)(w*.4), (int)(w*.4), (int)(w*.9) };
            int triangleY[] = { (int)(w*.3), (int)(w*.9), (int)(w*.6) };
            g.setColor(Color.white);
            g.fillPolygon(triangleX, triangleY, 3);
        }
    }

    private void paintButton2(Graphics g) {
	int x,y,w,h;

	x = r.height + 1;
	y = 1;
	w = r.height - 2;
	h = r.height - 2;

        g.setColor(Color.darkGray);
        g.drawRect(x, y, w, h);
        g.setColor(button2Color);
        g.fillRect(x+1, y+1, w-1, h-1);
        g.setColor(Color.white);
        g.fillRect(r.height + (int)(w * .4), (int)(w * .4), (int)(w * .5), (int)(w * .5));
    }

    private void paintMessage(Graphics g, int pos) {
        if (message != null) {
            g.setColor(Color.white);
            g.drawString(message, pos, r.height - 2);
        }
    }

    private void paintBuffering(Graphics g, int pos) {
        g.setColor(Color.white);
        g.drawString("Buffering", pos, r.height - 2);
    }

    /*
     * Get the inclusive bounding rectangle of the seek bar
     */
    private Rectangle getSeekBarRect() {
      return new Rectangle(r.height*2 + 1, 2,
	  r.width - SEEK_TIME_GAP - TIME_WIDTH - speakerWidth - (r.height * 2), 
	  r.height - 4);
    }

    /*
     * Get the inclusive bounding rectangle of the seek bar thumb
     */
    private Rectangle getThumbRect() {
      Rectangle seekRect = getSeekBarRect();
      int availableWidth = seekRect.width - THUMB_WIDTH;
      int pos = (int)(availableWidth * position);
      return new Rectangle(pos + seekRect.x, 1, THUMB_WIDTH, r.height - 2);
    }

    private void paintSeekBar(Graphics g) {
        Rectangle sr = getSeekBarRect();
        Rectangle tr = getThumbRect();

        // Bounding rectangle
        g.setColor(Color.darkGray);
        g.drawRect(sr.x, sr.y, sr.width, sr.height);

        // Progress bar
        g.setColor(Color.gray);
        g.fillRect(sr.x + 2, sr.y + 3, tr.x - (sr.x + 2), sr.height - 6);
        
        // Thumb
        g.setColor(Color.white);
        g.drawLine(tr.x + 1,        tr.y,             tr.x + tr.width - 1, tr.y);                 // Top
        g.drawLine(tr.x + 1,        tr.y + tr.height, tr.x + tr.width - 1, tr.y + tr.height);     // Bottom
        g.drawLine(tr.x,            tr.y + 1,         tr.x,                tr.y + tr.height - 1); // Left
        g.drawLine(tr.x + tr.width, tr.y + 1,         tr.x + tr.width,     tr.y + tr.height - 1); // Right

        // Thumb interior
        g.setColor(seekColor);
        g.fillRect(tr.x + 1, tr.y + 1, tr.width - 1, tr.height - 1);
    }

    private void paintTime(Graphics g) {
        long hour, min, sec;
        int end;

        if (time < 0)
            return;

        sec = time % 60;
        min = time / 60;
        hour = min / 60;
        min %= 60;

        r = getBounds();

        end = r.width - speakerWidth - TIME_WIDTH;

        g.setColor(Color.white);
        g.drawString("" + hour + ":" + (min < 10 ? "0" + min : "" + min) + ":"
                + (sec < 10 ? "0" + sec : "" + sec), end, r.height - 2);
    }

    private void paintSpeaker(Graphics g) {
        if (haveAudio) {
            g.drawImage(speakerImg, r.width - SPEAKER_WIDTH, r.height - SPEAKER_HEIGHT - 1, null);
        }
    }

    public void paint(Graphics g) {
	int pos = 0;

        if (!isVisible())
            return;

        r = getBounds();

        Image img = component.createImage(r.width, r.height);
	if (img == null)
          return;
        Graphics g2 = img.getGraphics();
	if (g2 == null)
          return;
        g2.setFont(font);

        paintBox(g2);
	if (!buffering) {
            paintButton1(g2);
	}
	if (!live) {
          paintButton2(g2);
	  pos = r.height*2;
	}
	else {
	  pos = r.height;
	}
        if (buffering) {
            paintPercent(g2);
            paintBuffering(g2, pos + 3);
	}
        else if (state == STATE_STOPPED || !seekable) {
            paintMessage(g2, pos + 3);
            paintTime(g2);
	}
	else if (seekable) {
            paintSeekBar(g2);
            paintTime(g2);
	}
        if (showSpeaker) {
            paintSpeaker(g2);
        }

        g.drawImage(img, r.x, r.y, null);
        img.flush();
    }

    public void setBufferPercent(boolean buffering, int bp) {
	boolean changed;

	changed = this.buffering != buffering;
	changed |= this.bufferPercent != bp;

	if (changed) {
          this.buffering = buffering;
          this.bufferPercent = bp;
          component.repaint();
	}
    }

    public void setTime(double seconds) {
        if (clicked == NONE) {
            double newPosition;
	    
            if (seconds < duration || seekable)
                time = (long) seconds;
            else
                time = (long) duration;

            if(duration > -1) {
                newPosition = ((double) time) / duration;
                if (newPosition != position) {
                    position = newPosition;
                    component.repaint();
                }
            } else {
                newPosition = ((double)bytePosition) / (double)byteDuration;
                position = newPosition;
                component.repaint();
            }
        }
    }

    public void setDuration(double seconds) {
        duration = seconds;
        component.repaint();
    }
    
    public void setByteDuration(long bytes) {
        this.byteDuration = bytes;
        if(duration == -1) {
            position = ((double)bytePosition) / (double)byteDuration;
            component.repaint();
        }
    }
    
    public void setBytePosition(long bytes) {
        this.bytePosition = bytes;
        if(duration == -1) {
            position = ((double)bytePosition) / (double)byteDuration;
            component.repaint();
        }
    }

    public void setMessage(String m) {
        message = m;
        component.repaint();
    }

    public void setHaveAudio(boolean a) {
        haveAudio = a;
        component.repaint();
    }

    public void setHavePercent(boolean p) {
        havePercent = p;
        component.repaint();
    }

    public void setSeekable(boolean s) {
        seekable = s;
        component.repaint();
    }

    public void setLive(boolean l) {
        live = l;
        component.repaint();
    }

    public void setShowSpeaker(boolean s) {
        showSpeaker = s;
        speakerWidth = s ? SPEAKER_WIDTH : 0;
        component.repaint();
    }

    public void setState(int aState) {
	if (state != aState) {
          state = aState;
          component.repaint();
	}
    }

    private boolean intersectButton1(MouseEvent e) {
        if (r == null)
            return false;

        return (e.getX() >= 0 && e.getX() <= r.height-2 && e.getY() > 0 && e.getY() <= r.height-2);
    }

    private boolean intersectButton2(MouseEvent e) {
        if (r == null)
            return false;

        return (e.getX() >= r.height && e.getX() <= r.height + r.height-2 && e.getY() > 0 && e.getY() <= r.height-2);
    }

    private boolean intersectSeeker(MouseEvent e) {
        int end;

        r = getBounds();
	Rectangle tr = getThumbRect();
	return tr.contains(e.getPoint());
    }

    private boolean intersectSeekbar(MouseEvent e) {
        int end;

        r = getBounds();
	Rectangle sr = getSeekBarRect();
	return sr.contains(e.getPoint());
    }

    private int findComponent(MouseEvent e) {
        if (!buffering && intersectButton1(e))
            return BUTTON1;
        else if (intersectButton2(e))
            return BUTTON2;
        else if (seekable && intersectSeeker(e))
            return SEEKER;
        else if (seekable && intersectSeekbar(e))
            return SEEKBAR;
        else
            return NONE;
    }

    public void cancelMouseOperation() {
      button1Color = Color.black;
      button2Color = Color.black;
      seekColor = Color.black;
      clicked = NONE;
    }

    public void mouseClicked(MouseEvent e) {
    }

    public void mouseEntered(MouseEvent e) {
    }

    public void mouseExited(MouseEvent e) {
    }

    public void mousePressed(MouseEvent e) {
        e.translatePoint(-1, -1);
        clicked = findComponent(e);
        if (clicked == SEEKBAR && state != STATE_STOPPED) {
          clicked = SEEKER;
          seekColor = Color.gray;
	  mouseDragged (e);
        }
    }

    public void mouseReleased(MouseEvent e) {
        int comp;

        e.translatePoint(-1, -1);

        comp = findComponent(e);
        if (clicked != comp) {
            if (clicked == SEEKER)
                comp = clicked;
            else
                return;
        }

        switch (comp) {
        case BUTTON1:
            if (state == STATE_PLAYING) {
		if (live)
                  state = STATE_STOPPED;
		else
                  state = STATE_PAUSED;
                notifyNewState(state);
            } else {
                state = STATE_PLAYING;
                notifyNewState(state);
            }
            break;
        case BUTTON2:
            state = STATE_STOPPED;
            notifyNewState(state);
            break;
        case SEEKER:
            if (state != STATE_STOPPED)
              notifySeek(position);
            break;
        case SEEKBAR:
            break;
        case NONE:
            break;
        }
        clicked = NONE;
        component.repaint();
    }

    public void mouseDragged(MouseEvent e) {
        if (seekable) {
            e.translatePoint(-1, -1);
            if (clicked == SEEKER) {
	        Rectangle sr = getSeekBarRect();
		int availableWidth = sr.width - THUMB_WIDTH;
		// If the midpoint of the thumb is at the cursor, where would the left of the thumb be?
		// (relative to sr.x)
		int thumbLeft = e.getX() - sr.x - THUMB_WIDTH / 2;
		double newPosition = thumbLeft / (double)availableWidth;

                if (newPosition < 0.0)
                    newPosition = 0.0;
                else if (newPosition > 1.0)
                    newPosition = 1.0;

		if (newPosition != position) {
	          position = newPosition;
                  time = (long) (duration * position);
                  component.repaint();
		}
            }
        }
    }

    public void mouseMoved(MouseEvent e) {
        boolean needRepaint = false;

        e.translatePoint(-1, -1);

	if (!buffering) {
            if (intersectButton1(e)) {
	        if (button1Color != Color.gray) {
                    button1Color = Color.gray;
	            needRepaint = true;
                }
            } else {
	        if (button1Color != Color.black) {
                    button1Color = Color.black;
	            needRepaint = true;
	        }
	    }
	}
        if (intersectButton2(e)) {
            if (button2Color != Color.gray) {
                button2Color = Color.gray;
                needRepaint = true;
            }
        } else {
	    if (button2Color != Color.black) {
                button2Color = Color.black;
	        needRepaint = true;
	    }
	}

        if (seekable) {
            if (intersectSeeker(e)) {
                if (seekColor != Color.gray) {
                    seekColor = Color.gray;
                    needRepaint = true;
		}
            } else {
                if (seekColor != Color.black) {
                    seekColor = Color.black;
                    needRepaint = true;
                }
            }
        }
	if (needRepaint)
          component.repaint();
    }
}
