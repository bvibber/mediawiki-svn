/* $Header$ */
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
		
HDL(cfg_monit_server_mysql_master) {
	EX {
		try {
			std::string curmaster = SMI(smcfg::cfg)->fetchstr("/monit/mysql/master");
			cd.inform("Removing MySQL master status from " + curmaster);
		} catch (smcfg::nokey&) {}
		SMI(smcfg::cfg)->storestr("/monit/mysql/master", cd.p(0));
		return true;
	}
};

HDL(cfg_monit_mysql_username) {
	EX {
		SMI(smcfg::cfg)->storestr("/monit/mysql/username", cd.p(0));
		return true;
	}
};

HDL(cfg_monit_mysql_password) {
	EX {
		SMI(smcfg::cfg)->storestr("/monit/mysql/password", cd.p(0));
		return true;
	}
};

HDL(cfg_monit_monitor_interval) {
	EX {
		try {
			SMI(smcfg::cfg)->storeint("/monit/interval", b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_monit_ircinterval) {
	EX {
		try {
			SMI(smcfg::cfg)->storeint("/monit/ircinterval", b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_monit_showintervals) {
	EX {
		try {
			cd.inform("Monitor interval         : " + b::lexical_cast<std::string>(SMI(smcfg::cfg)->fetchint("/monit/interval")));
		} catch (smcfg::nokey&) {
			cd.inform("Monitor interval         : <default>");
		}
		try {
			cd.inform("IRC notification interval: " + b::lexical_cast<std::string>(SMI(smcfg::cfg)->fetchint("/monit/ircinterval")));
		} catch (smcfg::nokey&) {
			cd.inform("IRC notification interval: <default>");
		}
		return true;
	}
};

HDL(cfg_qb_rule) {
	EX {
		cd.setdata(cd.p(0));
		cd.chgrt(&SMI(tmcmds)->qbrrt, "%s(conf-qb-rule)# ");
		if (!SMI(smqb::cfg)->rule_exists(cd.p(0))) {
			SMI(smqb::cfg)->create_rule(cd.p(0));
			cd.inform("Creating new rule.");
		}
		return true;
	}
};

HDL(cfg_qb_norule) {
	EX {
		if (!SMI(smqb::cfg)->rule_exists(cd.p(0))) {
			cd.error("No such rule.");
			return true;
		}
		SMI(smqb::cfg)->delete_rule(cd.p(0));
		return true;
	}
};

HDL(cfg_qbr_description) {
	EX {
		std::string const& r = cd.getdata();
		SMI(smqb::cfg)->rule_description(r, cd.p(0));
		return true;
	}
};

HDL(cfg_qb_show_rule) {
	EX {
		std::vector<smqb::rule> rules;
		if (cd.num_params() == 0) {
			rules = SMI(smqb::cfg)->getrules();
		} else {
			try {
				rules.push_back(SMI(smqb::cfg)->getrule(cd.p(0)));
			} catch (smqb::norule&) {
				cd.error("No such rule.");
				return true;
			}
		}
		FE_TC_AS(std::vector<smqb::rule>, rules, i) {
			cd.wrtln("Rule " + i->name);
			cd.wrtln("    Description: " + i->description);
			if (i->enabled)
				cd.wrtln("    Enabled:     Yes");
			else
				cd.wrtln("    Enabled:     No");
			cd.wrtln("    Match conditions:");
			cd.wrtln("      Minimum threads:      " + b::lexical_cast<std::string>(i->minthreads));
			cd.wrtln("      Minimum last threads: " + b::lexical_cast<std::string>(i->minlastthreads));
			cd.wrtln("      Lowest position:      " + b::lexical_cast<std::string>(i->lowestpos));
			cd.wrtln("      Minimum run time:     " + b::lexical_cast<std::string>(i->minruntime));
			std::string userstr;
			FE_TC_AS(std::set<std::string>, i->users, j) userstr += *j + " ";
			cd.wrtln("      Users:                " + userstr);
			cd.wrtln("      Command type:         " + i->cmdtype);
			cd.wrtln("      Query:                " + i->query);
		}
		return true;
	}
};

HDL(cfg_qbr_matchif_minthreads) {
	EX {
		try {
			SMI(smqb::cfg)->set_minthreads(cd.getdata(), b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_qbr_matchif_minlastthreads) {
	EX {
		try {
			SMI(smqb::cfg)->set_minlastthreads(cd.getdata(), b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_qbr_matchif_lowestpos) {
	EX {
		try {
			SMI(smqb::cfg)->set_lowestpos(cd.getdata(), b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_qbr_matchif_minruntime) {
	EX {
		try {
			SMI(smqb::cfg)->set_minruntime(cd.getdata(), b::lexical_cast<int>(cd.p(0)));
		} catch (b::bad_lexical_cast&) {
			cd.error("Bad number.");
		}
		return true;
	}
};

HDL(cfg_qbr_matchif_user) {
	EX {
		SMI(smqb::cfg)->set_user(cd.getdata(), cd.p(0));
		return true;
	}
};

HDL(cfg_qbr_matchif_command) {
	EX {
		SMI(smqb::cfg)->set_command(cd.getdata(), cd.p(0));
		return true;
	}
};

HDL(cfg_qbr_matchif_querystring) {
	EX {
		SMI(smqb::cfg)->set_querystring(cd.getdata(), cd.p(0));
		return true;
	}
};

HDL(cfg_qbr_enable) {
	EX {
		SMI(smqb::cfg)->set_enabled(cd.getdata());
		return true;
	}
};

HDL(cfg_qbr_noenable) {
	EX {
		SMI(smqb::cfg)->set_disabled(cd.getdata());
		return true;
	}
};
