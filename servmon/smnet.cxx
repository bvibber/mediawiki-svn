#include "smstdinc.hxx"
#include "smnet.hxx"

namespace smnet {

std::map<int, int> bsd::refs;
std::map<int, std::vector<u_char> > bsd::rdbufs;

} // namespace smnet
