package net.psammead.commonist.data;


/** Data edited in a CommonUI */
public final class CommonData {
	public final String			wiki;
	public final String			user;
	public final String			password;
	public final String			description;
	public final String			source;
	public final String			date;
	public final String			author;
	public final LicenseData	license;
	public final String			categories;
	
	public CommonData(
			String			wiki,
			String			user,
			String			password,
			String			description,
			String			source,
			String			date,
			String			author,
			LicenseData		license,
			String			categories) {
		this.wiki			= wiki;
		this.user			= user;
		this.password		= password;
		this.description	= description;
		this.source			= source;
		this.date			= date;
		this.author			= author;
		this.license		= license;
		this.categories		= categories;
	}
}
