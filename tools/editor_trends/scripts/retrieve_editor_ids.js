/*
Copyright (C) 2010 by Diederik van Liere (dvanliere@gmail.com)
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License version 2
as published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
See the GNU General Public License for more details, at
http://www.fsf.org/licenses/gpl.html
*/

/*
PURPOSE: Retrieve all editor ids.
INPUT: Collection name
OUTPUT: Array of editor ids

FUTURE USE: array can be used in subsequent queries. 
*/

editors = db['editors'];
c = db.editors.distinct('editor');
print(c);