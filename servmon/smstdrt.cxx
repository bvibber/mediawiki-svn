struct cmd_show_version : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("servmon pre-release");
		return true;
	}
};

struct cmd_config : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&instance<tmcmds>()->cfgrt, "%s(conf)");
		return true;
	}
};

struct cmd_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.inform("Bye");
		return false;
	}
};

struct cfg_exit : handler<tt> {
	bool execute(comdat<tt> const& cd) {
		cd.chgrt(&instance<tmcmds>()->stdrt, "%s");
		return true;
	}
};
