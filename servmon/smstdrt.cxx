template<class tt>
struct cmd_show_version : handler<tt> {
	void execute(comdat<tt> const& cd) {
		cd.wrtln("servmon pre-release");
	}
};
