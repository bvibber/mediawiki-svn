#ifndef SM_SMAUTH_HXX_INCLUDED_
#define SM_SMAUTH_HXX_INCLUDED_

namespace smauth {

	bool login_usr(std::string const& usr, std::string const& pass);
	bool authebl(std::string const& pass);

	bool usr_exists(std::string const& usr);
	void add_usr(std::string const& usr, std::string const& pass);
	void del_usr(std::string const& usr);
	
} // namespace smauth

#endif
