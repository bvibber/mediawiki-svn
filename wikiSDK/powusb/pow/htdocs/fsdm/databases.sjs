<?sjs
dblist =" out put the database list from the central database";

var mydb = new pow_DB("fsdmlist");

table =  "databaselist";
 
// Make query string to display table results
qselect =  "SELECT * FROM "+ table;
// execute query string              
var results = mydb.exec(qselect);
// array returned in results  rows is number of rows returned
// two dimensional array  results[i][i]  returned
rows = results.length;
//zero based numbering for arrarys
rowcount = rows - 1;
//get number of colums in the result set
// cols = results[0].length;
//zero based numbering for arrarys
colcount  =  1; 

document.write("<html><body><blockquote><h3>Pick a Database to manage</h3><table border=1 cellpadding=4 cellspacing=4>");
 
 
showrow = "<tr><td> </td><td><b> #</b></td><td><b> Database Name</b></td>";
document.write(showrow);
 
document.write("</tr>");

for (i=0;i<=rowcount;i++){


document.write("<tr>");

showdel ="<td><a href='check.sjs?base="+results[i][1]+"'>Select this database </a> </td>"

document.write(showdel);


for (j=0;j<=colcount;j++){

showrow = "<td>" + results[i][j]+"</td>";

document.write(showrow);

};


document.write("<tr>");

       
}; 


document.write("</table><blockquote></body></html>");


?>

 