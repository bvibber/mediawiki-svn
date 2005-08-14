// created on 8/13/2005 at 7:16 PM

namespace MediaWiki.Search {
	using System.Collections;
	
	public class SearchPool {
		static IDictionary _states;
		static object _statesLock;
		
		static SearchPool() {
			_states = new Hashtable();
			_statesLock = new object();
		}
		
		static public SearchReader ForWiki(string dbname) {
			lock (_statesLock) {
				SearchReader found = (SearchReader)_states[dbname];
				if (found == null) {
					found = new SearchReader(dbname);
					_states[dbname] = found;
				}
				return found;
			}
		}
		
		static public void Close() {
			lock (_statesLock) {
				foreach (SearchReader reader in _states.Values) {
					reader.Close();
				}
				_statesLock = new Hashtable();
			}
		}
	}
}
