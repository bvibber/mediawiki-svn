struct cmd_show_version : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("servmon pre-release");
		return true;
	}
};

struct cmd_enable : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		std::string pass = cd.getpass("% Password: "), rpass;
		try {
			rpass = instance<smcfg::cfg>()->fetchstr("/core/enable_password");
		} catch (smcfg::nokey&) {
			cd.error("Password incorrect.");
			return true;
		}
		if (pass == rpass)
			cd.chgrt(&instance<tmcmds>()->eblrt, "%s# ");
		else
			cd.error("Password incorrect.");
		return true;
	}
};

struct cmd_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("Bye");
		return false;
	}
};

struct cfg_eblpass : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		std::string p1, p2;
		p1 = cd.getpass("% Enter new password: ");
		p2 = cd.getpass("% Confirm new password: ");
		if (p1 != p2) {
			cd.error("Not confirmed.");
			return true;
		}
		instance<smcfg::cfg>()->storestr("/core/enable_password", p1);
		return true;
	}
};

struct chg_parser : handler<tt> {
	chg_parser(handler_node<tt>& newp_, std::string const& prm_)
	: newp(newp_)
	, prm(prm_)
	{}
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&newp, prm);
		return true;
	}
	handler_node<tt>& newp;
	std::string prm;
};

struct cfg_irc_servnick : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		smirc::cfg.newserv_or_chgnick(cd.p(0), cd.p(1));
		return true;
	}
};

struct cfg_irc_servsecnick : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!smirc::cfg.server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		smirc::cfg.server_set_secnick(cd.p(0), cd.p(1));
		return true;
	}
};

struct cfg_irc_noserver : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!smirc::cfg.server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		smirc::cfg.remove_server(cd.p(0));
		return true;
	}
};

struct cfg_irc_showserver : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!smirc::cfg.server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		std::string pnick, snick;
		pnick = instance<smcfg::cfg>()->fetchstr(
				str(format("/irc/servers/%s/nickname") % cd.p(0)));
		try {
			snick = instance<smcfg::cfg>()->fetchstr(
					str(format("/irc/servers/%s/secnickname") % cd.p(0)));
		} catch (smcfg::nokey&) {
			snick = "<none>";
		}
		cd.wrtln(cd.p(0));
		cd.wrtln("  primary nickname:   " + pnick);
		cd.wrtln("  secondary nickname: " + snick);
		return true;
	}
};
