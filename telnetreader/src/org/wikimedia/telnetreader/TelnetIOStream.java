package org.wikimedia.telnetreader;

import java.io.IOException;
import java.io.InputStream;
import java.io.OutputStream;
import java.util.ArrayList;

/**
 * @author Kate Turner
 *
 */
public class TelnetIOStream {
	InputStream in;
	OutputStream out;
	
	private static final int
		_S_NORMAL	= 1,
		_S_IAC		= 2,
		_S_SBIAC	= 3, /* Got IAC during subnegotiation */
		_S_SUBNEG	= 4,
		_S_DO		= 5,
		_S_DONT		= 6,
		_S_WILL		= 7,
		_S_WONT		= 8
		;
	
	private static final int
		/* Control data */
		_C_IAC		= 255,	/* Begin control statement */
		_C_DONT		= 254,	/* You shouldn't */
		_C_DO		= 253,	/* You should */
		_C_WILL		= 251,	/* I will */
		_C_WONT		= 252,	/* I won't */
		_C_SB		= 250,	/* Begin subnegotiation */
		_C_SE		= 240,	/* End subnegotiation */
		/* Request types */
		_C_ECHO		= 1,	/* Echo */
		_C_SGA		= 3,	/* Suppress go-ahead */
		_C_TTYPE	= 24,	/* Terminal type */
		_C_NAWS		= 31,	/* Negotiate about window size */
		_C_LINEMODE = 34	/* Linemode */
		;
	
	private int state = _S_NORMAL;
	
	private ArrayList subnegdata;	/* Data from subnegotiation */
	
	private int termcols, termrows;
	
	public TelnetIOStream(InputStream in, OutputStream out) throws IOException {
		this.in = in;
		this.out = out;
		this.termcols = this.termcols = -1;
		this.state = _S_NORMAL;
		sendDo(_C_SGA);
		sendWill(_C_SGA);
		sendDont(_C_LINEMODE);
		sendDo(_C_NAWS);
		sendDo(_C_TTYPE);
		sendDont(_C_ECHO);
		sendWill(_C_ECHO);
	}
	
	public void stateChange(int newstate) {
		int oldstate = state;
		state = newstate;
		TelnetReader.logMsg("State change: " + oldstate + " -> " + newstate);
	}
	
	public int read() throws IOException {
		return read(false);
	}
	public int read(boolean negonly) throws IOException {
		/*
		 * TELNET is a byte-stream protocol oriented around the IAC
		 * character, 255.  We start in the normal state; any data
		 * other than IAC read in this state is passed back as characters.
		 * The IAC character introduces several possible commands;
		 * 
		 * 		IAC (WILL/WONT/DO/DONT) <option>
		 * 				- option negotiation
		 * 		IAC SB <data...> IAC SE
		 * 				- subnegotiation - arbitrary length data for a previously
		 * 					negotiated option
		 * 		IAC IAC
		 * 				- a literal IAC character		
		 */
		do {
			int i = in.read();
			if (i == -1)
				return i;
			TelnetReader.logMsg("Read raw char: " + i + ", state = " + state);
			switch (state) { 						/* Current state?				*/
			case _S_NORMAL: { 						/*   Normal state				*/
				switch (i) {						/*     Char?					*/
				case _C_IAC: {						/*       IAC: change state		*/
					stateChange(_S_IAC);
					break;
				}
				default: {							/*       Anything else: return	*/
					TelnetReader.logMsg("Returning");
					return i;
				}
				}
				break;
			}
			case _S_DO: {							/*   IAC DO state				*/
				handleDo(i);
				break;
			}
			case _S_DONT: {							/*   IAC DONT state				*/
				handleDont(i);
				break;
			}
			case _S_WILL: {							/*   IAC WILL state				*/
				handleWill(i);
				break;
			}
			case _S_WONT: {							/*   IAC WONT state				*/
				handleWont(i);
				break;
			}
			case _S_SUBNEG: {						/*   IAC SB ... state			*/
				switch(i) {							/*     Char?					*/
				case _C_IAC: {						/*       IAC, may be IAC SE		*/
					stateChange(_S_SBIAC);
					break;
				}
				default: {							/*     Subnegotiation data		*/
					subnegdata.add(new Integer(i));
					break;
				}
				}
				break;
			}
			case _S_SBIAC: {						/*   Data after IAC SB...IAC	*/
				switch (i) {						/*     Char?					*/
				case _C_SE: {						/*       End subneg state		*/
					stateChange(_S_NORMAL);
					processSubnegData();
					break;
				}
				default: {							/*       Just more data			*/
					subnegdata.add(new Integer(i));
					break;
				}
				}
				break;
			}
			case _S_IAC: {							/*   IAC state					*/
				switch(i) {							/*     Char?					*/
				case _C_IAC: {						/*       IAC IAC: return IAC	*/
					return _C_IAC;
				}
				case _C_SB: {						/*       IAC SB: Begin subneg	*/
					subnegdata = new ArrayList();
					stateChange(_S_SUBNEG); break;
				}
				case _C_WILL: {						/*       IAC WILL				*/
					stateChange(_S_WILL); break;
				}
				case _C_WONT: {						/*       IAC WONT				*/
					stateChange(_S_WONT); break;
				}
				case _C_DO: {						/*       IAC DO					*/
					stateChange(_S_DO); break;
				}
				case _C_DONT: {						/*       IAC DONT				*/
					stateChange(_S_DONT); break;
				}
				default: {
					break;							/*       IAC ??? ignore			*/
				}
				}
				break;
			}
			}
		} while (!negonly);
		return -2;
	}
	
	private void handleWill(int what) throws IOException {
		switch (what) {
		case _C_ECHO: {
			sendDont(_C_ECHO);
			break;
		}
		}
		stateChange(_S_NORMAL);
	}
	private void handleWont(int what) {
		stateChange(_S_NORMAL);
	}
	private void handleDo(int what) throws IOException {
		stateChange(_S_NORMAL);
	}
	private void handleDont(int what) {
		stateChange(_S_NORMAL);
	}
	
	private void sendSomething(int opinion, int what) throws IOException {
		byte[] data = {
				(byte) _C_IAC, (byte) opinion, (byte) what
		};
		out.write(data);
	}
	private void sendDo(int what) throws IOException {
		sendSomething(_C_DO, what);
	}
	private void sendDont(int what) throws IOException {
		sendSomething(_C_DONT, what);
	}
	private void sendWill(int what) throws IOException {
		sendSomething(_C_WILL, what);
	}
	private void sendWont(int what) throws IOException {
		sendSomething(_C_WONT, what);
	}
	private void processSubnegData() {
		int what = ((Integer)subnegdata.get(0)).intValue();
		switch (what) {
		case _C_NAWS: {		/* Negotiate about window size */
			/* NAWS data has four bytes:
			 * 		1 - cols (hi byte)
			 * 		2 - cols (lo byte)
			 * 		3 - rows (hi byte)
			 * 		4 - rows (lo byte)
			 */
			if (subnegdata.size() < 5)
				return;
			int chi, clo, rhi, rlo;
			chi = ((Integer)subnegdata.get(1)).intValue();
			clo = ((Integer)subnegdata.get(2)).intValue();
			rhi = ((Integer)subnegdata.get(3)).intValue();
			rlo = ((Integer)subnegdata.get(4)).intValue();
			this.termcols = (chi << 8) + clo;
			this.termrows = (rhi << 8) + rlo;
			return;
		}
		default: {			/* Don't recognise it, ignore... */
			return;
		}
		}
	}
	public int getTermcols() {
		return termcols;
	}
	public void setTermcols(int termcols) {
		this.termcols = termcols;
	}
	public int getTermrows() {
		return termrows;
	}
	public void setTermrows(int termrows) {
		this.termrows = termrows;
	}
	public void waitForTermSetup() throws IOException {
		while (termrows < 0 || termcols < 0)
			read(true);
		in.skip(in.available());
	}
	public void write(String str) throws IOException {
		out.write(str.getBytes());
	}
}
