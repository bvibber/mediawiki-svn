#include <iostream>
#include <boost/tr1/unordered_map.hpp>
#include <map>
#include <stdint.h>
#include <boost/lexical_cast.hpp>
#include <cstring>
#include <time.h>
#include <stdlib.h>
#include <math.h>

using std::strtok;

typedef std::tr1::unordered_map<std::string, uint64_t>::iterator SeqIterator;

struct HostData {
	HostData() : received(1), sent(0) {}

	// Number of packets received (sampled)
	int64_t received;

	// Number of packets sent, estimated from sequence numbers (unsampled)
	int64_t sent;
};
typedef std::tr1::unordered_map<std::string, HostData>::iterator HostIterator;

struct SampleData {
	SampleData() : total(0), outOfOrder(0), invalid(0) {}

	// Number of sampled lines received
	int64_t total;

	// Number of sampled lines out of order
	int64_t outOfOrder;

	// Number of sampled lines which were invalid
	int64_t invalid;

	std::tr1::unordered_map<std::string, HostData> hosts;

	void Report(const struct tm * timeStruct, int sampleRate);
	void PrintRatio(int64_t numerator, int64_t denominator, double numeratorError);

};

const time_t reportInterval = 600;


int main(int argc, char** argv)
{
	using namespace std;
	const size_t bufSize = 65536;
	std::tr1::unordered_map<string, uint64_t> seqs;
	char buffer[bufSize];
	int sampleRate;

	SampleData initialData;
	SampleData currentData;
	time_t lastReportTime = 0;
	time_t currentTime;
	struct tm timeStruct;

	if (argc < 2) {
		cerr << "usage: packet-loss <sample-rate>\n";
		exit(1);
	}

	try {
		sampleRate = boost::lexical_cast<int>(argv[1]);
	} catch (...) {
		cerr << "packet-loss: invalid sample rate\n";
		exit(1);
	}
	if (sampleRate < 1) {
		cerr << "packet-loss: invalid sample rate\n";
		exit(1);
	}

	// Don't take all day due to http://gcc.gnu.org/bugzilla/show_bug.cgi?id=45574
	ios::sync_with_stdio(false);

	while (cin.good()) {
		cin.getline(buffer, bufSize);
		if (!cin.gcount()) {
			continue;
		}
		currentData.total++;
		if (cin.gcount() > (streamsize)(bufSize - 2)) {
			// Oversize
			currentData.invalid++;
			continue;
		}
		char * strConn = strtok(buffer, " ");
		char * strSeq = strtok(NULL, " ");
		char * strTime = strtok(NULL, " ");
		char * restOfLine = strtok(NULL, "\n");
		if (!strConn || !strSeq || !strTime || !restOfLine) {
			// Invalid line
			currentData.invalid++;
			continue;
		}
		uint64_t seq;
		try {
			seq = boost::lexical_cast<uint64_t>(strSeq);
		} catch (...) {
			// Non-numeric sequence number
			currentData.invalid++;
			continue;
		}

		if (!strptime(strTime, "%Y-%m-%dT%H:%M:%S", &timeStruct)) {
			// Invalid date/time
			currentData.invalid++;
			continue;
		}
		currentTime = timegm(&timeStruct);

		SeqIterator seqIter = seqs.find(strConn);
		HostIterator hostIter = currentData.hosts.find(strConn);
		if (hostIter == currentData.hosts.end()) {
			// First instance of this host since the last report, or first instance overall
			initialData.hosts[strConn] = HostData();
			currentData.hosts[strConn] = HostData();
			if (seqIter != seqs.end()) {
				std::cerr << "packet-loss: unexpected host entry\n";
				abort();
			}
			seqs[strConn] = seq;
		} else {
			if (seqIter == seqs.end()) {
				std::cerr << "packet-loss: unexpected lack of host entry\n";
				abort();
			} else if (seqIter->second == (uint64_t)-1) {
				// First instance of this host since out-of-order data
				seqs[strConn] = seq;
			} else if (seqIter->second > seq) {
				// Out-of-order, suspend counting until we resync
				currentData.outOfOrder++;
				seqIter->second = (uint64_t)-1;
			} else {
				// Normal case: sequence number increased
				int64_t delta = (int64_t)(seq - seqIter->second);
				hostIter->second.sent += delta;
				hostIter->second.received ++;
				seqIter->second = seq;
			}
		}

		if (currentTime - lastReportTime >= reportInterval) {
			lastReportTime = currentTime;
			currentData.Report(&timeStruct, sampleRate);
			currentData = initialData;
		}
	}
	// Final report
	currentTime = time(NULL);
	currentData.Report(&timeStruct, sampleRate);
	return 0;
}

void SampleData::Report(const struct tm * timeStruct, int sampleRate) {
	using namespace std;
	char timebuf[256];

	strftime(timebuf, sizeof(timebuf), "[%Y-%m-%dT%H:%M:%S] ", timeStruct);

	cout << timebuf << "invalid: ";
	PrintRatio(invalid, total, sqrt(total));
	cout << "\n";

	cout << timebuf << "out of order: ";
	PrintRatio(outOfOrder, total, sqrt(total));
	cout << "\n";

	map<string, HostData> sortedHosts;
	HostIterator iter;
	int64_t totalSent = 0, totalReceived = 0;
	for (iter = hosts.begin(); iter != hosts.end(); iter++) {
		if (iter->second.received < 10) {
			// Sample size too small
			continue;
		}
		sortedHosts[iter->first] = iter->second;
		totalSent += iter->second.sent;
		totalReceived += iter->second.received;
	}

	cout << timebuf << "total lost: ";
	PrintRatio(
		totalSent - totalReceived * sampleRate, 
		totalSent,
		sqrt(totalReceived) * sampleRate
	);
	cout << "\n";

	map<string, HostData>::iterator sortedIter;
	for (sortedIter = sortedHosts.begin(); sortedIter != sortedHosts.end(); sortedIter++) {
		cout << timebuf << sortedIter->first << " lost: ";
		PrintRatio(
			sortedIter->second.sent - sortedIter->second.received * sampleRate, 
			sortedIter->second.sent,
			sqrt(sortedIter->second.received) * sampleRate
		);
		cout << "\n";
	}
}

void SampleData::PrintRatio(int64_t numerator, int64_t denominator, double numeratorError) {
	using namespace std;
	if (denominator == 0) {
		cout << "(0 +/- 0)%";
	} else {
		double percent = (double)numerator / denominator * 100;
		double percentError = numeratorError / denominator * 100;
		cout.precision(5);
		cout.flags(ios::fixed);
		cout << "(" << percent << " +/- ";
		cout.precision(5);
		cout.flags(ios::fixed);
		cout <<  percentError << ")%";
	}
}

// vim: ts=4 sw=4:

