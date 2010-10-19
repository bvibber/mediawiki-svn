import os
import sys

WORKING_DIRECTORY = os.getcwd()#[:-9]
IGNORE_DIRS = ['wikistats', 'zips']

dirs = [name for name in os.listdir(WORKING_DIRECTORY) if
        os.path.isdir(os.path.join(WORKING_DIRECTORY, name))]


for subdirname in dirs:
    if not subdirname.startswith('.') and subdirname not in IGNORE_DIRS:
        sys.path.append(os.path.join(WORKING_DIRECTORY, subdirname))
        #print os.path.join(WORKING_DIRECTORY, subdirname)
