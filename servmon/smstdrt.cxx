struct cmd_show_version : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("servmon pre-release");
		return true;
	}
};

struct cmd_enable : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.wrt("Password: ");
		cd.term.echo(false);
		cd.term.readline(boost::bind(&cmd_enable::vfypass, this, _1, _2));
		return true;
	}
	void vfypass(tt& trm, std::string const& pass) {
		trm.echo(true);
		if (smauth::authebl(pass))
			trm.chgrt(&SMI(tmcmds)->eblrt, "%s# ");
		else
			trm.error("Authentication failure.");
	}
};

struct cmd_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("Bye");
		return false;
	}
};

struct cfg_eblpass : handler<tt> {
	std::string p1;
	bool execute(comdat<tt> const& cd) {
		cd.term.echo(false);
		cd.wrt("Enter new password: ");
		cd.term.readline(boost::bind(&cfg_eblpass::gotp1, this, _1, _2));
		return true;
	}
	void gotp1(tt& trm, std::string const& pass) {
		p1 = pass;
		trm.wrt("Confirm new password: ");
		trm.readline(boost::bind(&cfg_eblpass::gotp2, this, _1, _2));
		return;
	}
	void gotp2(tt& trm, std::string const& p2) {
		trm.echo(true);
		if (p1 != p2) {
			trm.error("Not confirmed.");
			return;
		}
		SMI(smcfg::cfg)->storestr("/core/enable_password", p1);
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

struct cfg_userpass : handler<tt> {
	std::string usr;
	bool execute(comdat<tt> const& cd) {
		if (smauth::usr_exists(cd.p(0))) {
			cd.error("User already exists.");
			return true;
		}
		usr = cd.p(0);
		cd.term.echo(false);
		cd.term.wrt("Enter password: ");
		cd.term.readline(boost::bind(&cfg_userpass::gotpass, this, _1, _2));
		return true;
	}
	void gotpass(tt& trm, std::string const& pass) {
		trm.echo(true);
		smauth::add_usr(usr, pass);
	}
};

struct cfg_no_user : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!smauth::usr_exists(cd.p(0))) {
			cd.error("No such user.");
			return true;
		}
		smauth::del_usr(cd.p(0));
		return true;
	}
};

struct cfg_irc_servnick : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		SMI(smirc::cfg)->newserv_or_chgnick(cd.p(0), cd.p(1));
		return true;
	}
};

struct cfg_irc_servsecnick : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->server_set_secnick(cd.p(0), cd.p(1));
		return true;
	}
};

struct cfg_irc_noserver : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->remove_server(cd.p(0));
		return true;
	}
};

struct cfg_irc_showserver : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		std::string pnick, snick;
		pnick = SMI(smcfg::cfg)->fetchstr(
				str(format("/irc/servers/%s/nickname") % cd.p(0)));
		try {
			snick = SMI(smcfg::cfg)->fetchstr(
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
