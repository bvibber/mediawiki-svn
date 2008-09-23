#ifndef HOST_ENTRY_H
#define HOST_ENTRY_H

#include <netdb.h>
#include <string>
#include <vector>
#include <cerrno>

#include "IPAddress.h"

// C++ wrapper for the hostent structure
class HostEntry {
public:
	// Create a host entry by name
	HostEntry(const char *name);

	// Create a host entry by address
	HostEntry(IPAddress & address);

	// Create a host entry from a hostent*
	inline HostEntry(struct hostent* h);

	int GetErrno() { return errno_; }
	const char* GetError();
	bool IsValid() { return valid; }
	std::string & GetName() { return name; }
	std::string & GetAlias(int i) { return aliases.at(i); }
	int NumAliases() { return aliases.size(); }
	IPAddress & GetAddress(int i) { return addresses.at(i); }
	int NumAddresses() { return addresses.size(); }
protected:
	void InitFromHostent(struct hostent* h);

	int errno_;
	bool valid;
	std::string name;
	std::vector<std::string> aliases;
	std::vector<IPAddress> addresses;
};

HostEntry::HostEntry(struct hostent *h)
{
	void InitFromHostent(struct hostent* h);
}

#endif
