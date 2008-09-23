#include "HostEntry.h"

HostEntry::HostEntry(const char *name)
{
	struct hostent * h = gethostbyname(name);
	InitFromHostent(h);
}

HostEntry::HostEntry(IPAddress & address)
{
	struct hostent * h = gethostbyaddr(
			address.GetBinaryData(), 
			address.GetBinaryLength(),
			address.GetType() );
	InitFromHostent(h);
}

void HostEntry::InitFromHostent(struct hostent *h) {
	if (!h) {
		errno_ = h_errno;
		valid = false;
	} else {
		valid = true;
		errno_ = 0;
		name = h->h_name;
		for (char **p = h->h_aliases; *p; p++) {
			aliases.push_back(*p);
		}

		if (h->h_addrtype == AF_INET) {
			for (char **p = h->h_addr_list; *p; p++) {
				addresses.push_back(IPAddress((struct in_addr*)*p));
			}
		} else if (h->h_addrtype == AF_INET6) {
			for (char **p = h->h_addr_list; *p; p++) {
				addresses.push_back(IPAddress((struct in6_addr*)*p));
			}
		} // else pretend there are no addresses
	}
}

const char* HostEntry::GetError()
{
	switch (GetErrno()) {
		case 0:
			return "No error";
		case HOST_NOT_FOUND:
			return "Host not found";
		case TRY_AGAIN:
			return "Try again later";
		case NO_RECOVERY:
			return "A non-recoverable error occurred";
		case NO_ADDRESS:
			return "The host database contains an entry for the name, but it doesn't have an associated Internet address.";
		default:
			return "Unknown error";
	}
}
			

