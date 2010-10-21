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
PURPOSE: Retrieve first 10 edits from an editor
INPUT: Collection name, array of ids
OUTPUT: List of dates by editor
*/


editors = db['editors'];
for (i=0; i < 1000000; i++) {
		{	
			authors = db.editors.find({"editor": i.toString()}).sort({"date":1}).limit(10);
				
			try 
				{
				for (a=0; a < authors.length(); a++)
					{
					print(i.toString(), authors[a]["date"]);
					}
				}
			catch(err)
				{
				print(err);
				}
		}
	}
