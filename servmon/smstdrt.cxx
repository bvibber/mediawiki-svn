#define HDL(x) struct x : handler<tt>
#define EX bool execute(comdat<tt> const& cd)

HDL(cmd_show_version) {
	EX {
		cd.inform("servmon pre-release");
		return true;
	}
};

HDL(cmd_enable) {
	EX {
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

HDL(cmd_exit) {
	EX {
		cd.inform("Bye");
		return false;
	}
};

HDL(cfg_eblpass) {
	std::string p1;
	EX {
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

HDL(chg_parser) {
	chg_parser(handler_node<tt>& newp_, std::string const& prm_)
	: newp(newp_)
	, prm(prm_)
	{
	}
	EX {
		cd.chgrt(&newp, prm);
		return true;
	}
	handler_node<tt>& newp;
	std::string prm;
};

HDL(cfg_userpass) {
	std::string usr;
	EX {
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

HDL(cfg_no_user) {
	EX {
		if (!smauth::usr_exists(cd.p(0))) {
			cd.error("No such user.");
			return true;
		}
		smauth::del_usr(cd.p(0));
		return true;
	}
};

HDL(cfg_irc_servnick) {
	EX {
		SMI(smirc::cfg)->newserv_or_chgnick(cd.p(0), cd.p(1));
		return true;
	}
};

HDL(cfg_irc_servsecnick) {
	EX {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->server_set_secnick(cd.p(0), cd.p(1));
		return true;
	}
};

HDL(cfg_irc_channel) {
	EX {
		SMI(smirc::cfg)->channel(cd.p(0));
		return true;
	}
};

HDL(cfg_irc_nochannel) {
	EX {
		if (!SMI(smirc::cfg)->nochannel(cd.p(0)))
			cd.error("No such channel.");
		return true;
	}
};

HDL(cfg_irc_noserver) {
	EX {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->remove_server(cd.p(0));
		return true;
	}
};

HDL(cfg_irc_showchannels) {
	EX {
		try {
			std::set<std::string> channels = SMI(smcfg::cfg)->fetchlist("/irc/channels");
			cd.inform("Currently configured channels:");
			FE_TC_AS(std::set<std::string>, channels, i) {
				cd.wrtln("    " + *i);
			}
		} catch (smcfg::nokey&) {
			cd.inform("No channels configured.");
		}
		return true;
	}
};

HDL(cfg_irc_showserver) {
	EX {
		if (cd.num_params() == 0) {
			try {
				std::set<std::string> servers = SMI(smcfg::cfg)->fetchlist("/irc/servers");
				FE_TC_AS(std::set<std::string>, servers, i) {
					comdat<tt> cd2(cd);
					cd2.add_p(*i);
					execute(cd2);
				}
			} catch (smcfg::nokey&) {
				cd.inform("No servers configured");
			}
			return true;
		}
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		std::string pnick, snick;
		try {
			pnick = SMI(smcfg::cfg)->fetchstr(
					b::str(format("/irc/server/%s/nickname") % cd.p(0)));
		} catch (smcfg::nokey&) {
			pnick = "<not set>";
		}
		try {
			snick = SMI(smcfg::cfg)->fetchstr(
					b::str(format("/irc/server/%s/secnickname") % cd.p(0)));
		} catch (smcfg::nokey&) {
			snick = "<not set>";
		}
		cd.wrtln(cd.p(0));
		cd.wrtln("  primary nickname:   " + pnick);
		cd.wrtln("  secondary nickname: " + snick);
		return true;
	}
};

HDL(cfg_irc_enableserver) {
	EX {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->enable_server(cd.p(0), true);
		return true;
	}
};

HDL(cfg_irc_noenableserver) {
	EX {
		if (!SMI(smirc::cfg)->server_exists(cd.p(0))) {
			cd.error("No such server.");
			return true;
		}
		SMI(smirc::cfg)->enable_server(cd.p(0), false);
		return true;
	}
};

HDL(cfg_monit_showservers) {
	EX {
		std::map<std::string, smmon::cfg::serverp> servers;
		if (cd.num_params() == 0) {
			servers = SMI(smmon::cfg)->servers();
		} else {
			try {
				servers[cd.p(0)] = SMI(smmon::cfg)->serv(cd.p(0));
			} catch (smmon::noserv&) {
				cd.error("Server does not exist.");
				return true;
			}
		}
		for(std::map<std::string, smmon::cfg::serverp>::const_iterator i
			    = servers.begin(), end = servers.end(); i != end; ++i) {
			cd.wrtln(i->first + ":");
			cd.wrtln("  Type:  " + i->second->type());
		}
		return true;
	}
};

HDL(cfg_monit_server_type) {
	EX {
		if (!SMI(smmon::cfg)->knowntype(cd.p(1))) {
			cd.error(b::io::str(b::format("Unknown monitor type %s.") % cd.p(1)));
			return true;
		}
		if (SMI(smmon::cfg)->server_exists(cd.p(0))) {
			cd.error("Server already exists.");
			return true;
		}
		SMI(smmon::cfg)->create_server(cd.p(0), cd.p(1));
		return true;
	}
};
		
		
