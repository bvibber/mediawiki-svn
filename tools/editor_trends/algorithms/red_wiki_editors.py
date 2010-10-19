import re
import settings

try:
    import psyco
    psyco.full()
except ImportError:
    pass


RE_ID = re.compile('\d*')
RE_IP = re.compile('(?:\d{1,3}\.){2,3}\d{1,3}') #Some of the addresses have the last 3 digits blocked as xxx



def determine_contributor_type(id):
    if len(re.findall(RE_ID, id)) == 1:
        return 'id'
    elif len(re.findall(RE_IP, id)) == 1:
        return 'ip'
    else:
        return 'name'

def open_file_handles():
    fh1, fh2,fh3 = None, None, None
    handles = {'id.txt': fh1, 
               'ip.txt': fh2, 
               'name.txt': fh3
               }
    for handle, var in handles.iteritems():
        var = codecs.open(handle, 'w', encoding=settings.ENCODING)
    
    return handles

def close_file_handles(handles):
    for handle, var in handles.iteritems():
        var.close()

def write_data(vars):
    pass