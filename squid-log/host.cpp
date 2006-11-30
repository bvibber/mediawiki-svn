#include "HostEntry.h"
#include <iostream>

// Simple test program for IP address stuff

int main(int argc, char** argv)
{
	using namespace std;

	if (argc != 2) {
		cerr << "Usage: " << argv[0] << " <hostname>\n";
		return 1;
	}

	HostEntry entry(argv[1]);
	cout << "Name: " << entry.GetName();
	cout << "\nAliases:";
	for (int i=0; i<entry.NumAliases(); i++) {
		cout << " " << entry.GetAlias(i);
	}
	cout << "\nAddresses:";
	for (int i = 0; i < entry.NumAddresses(); i++) {
		cout << " " << entry.GetAddress(i).ToString();
	}
	if (entry.GetErrno()) {
		cout << "\nError: " << entry.GetError();
	}
	cout << endl;
	return 0;
}
