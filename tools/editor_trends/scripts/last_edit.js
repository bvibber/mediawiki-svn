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
PURPOSE: Retrieve last edit from an editor
INPUT: Collection name, array of ids
OUTPUT: Last date of edit by id
*/

editors = db['editors'];
for (i=7861; i < 717535; i++) {
			try 
				{
				authors = db.editors.find({"editor": i.toString()}, {"date":1}).sort({"date":-1}).limit(1);
						
				for (a=0; a < authors.length(); a++)
					{
					print(i.toString(), authors[a]["date"])
					}
				}
			catch(err)
				{
				print(err);
				}
	}