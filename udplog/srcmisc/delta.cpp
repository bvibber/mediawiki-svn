#include <iostream>
#include <map>
#include <stdint.h>
#include <boost/lexical_cast.hpp>
#include <cstring>

using std::strtok;

int main(int argc, char** argv)
{
	using namespace std;
	const size_t bufSize = 65536;
	map<string, uint64_t> seqs;
	typedef map<string, uint64_t>::iterator MapIterator;
	char buffer[bufSize];
	
	while (cin.good()) {
		cin.getline(buffer, bufSize);
		if (!cin.gcount()) {
			continue;
		}
		if (cin.gcount() > (streamsize)(bufSize - 2)) {
			cerr << "delta: oversized line detected" << endl;
			continue;
		}
		char * strConn = strtok(buffer, " ");
		char * strSeq = strtok(NULL, " ");
		char * restOfLine = strtok(NULL, "\n");
		if (!strConn || !strSeq || !restOfLine) {
			cerr << "delta: invalid line detected" << endl;
			continue;
		}
		uint64_t seq;
		try {
			seq = boost::lexical_cast<uint64_t>(strSeq);
		} catch (...) {
			cerr << "delta: invalid sequence number" << endl;
			continue;
		}
		MapIterator iter = seqs.find(strConn);
		if (iter == seqs.end()) {
			seqs[strConn] = seq;
		} else {
			if (iter->second > seq) {
				cerr << "delta: out-of-order packet detected" << endl;
				seqs.erase(iter);
			} else {
				uint64_t delta = seq - iter->second;
				cout << delta << " " << restOfLine << endl;
				iter->second = seq;
			}
		}
	}
}

// vim: ts=4 sw=4:

