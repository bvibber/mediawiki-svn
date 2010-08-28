package net.psammead.mwapi.api.data.list;

import java.util.Date;

import net.psammead.util.ToString;

public final class LogEvents {
	public final LogEvents_logevents logEvents;
	public final Date				continueKey;

	public LogEvents(LogEvents_logevents logEvents, Date continueKey) {
		this.logEvents		= logEvents;
		this.continueKey	= continueKey;
	}
	
	@Override
	public String toString() {
		return new ToString(this)
				.append("logEvents",	logEvents)
				.append("continueKey",	continueKey)
				.toString();
	}
}
