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

struct ebl_config : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&instance<tmcmds>()->cfgrt, "%s(conf)# ");
		return true;
	}
};

struct cmd_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("Bye");
		return false;
	}
};

struct ebl_disable : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&instance<tmcmds>()->stdrt, "%s> ");
		return true;
	}
};

struct cfg_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&instance<tmcmds>()->eblrt, "%s# ");
		return true;
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
