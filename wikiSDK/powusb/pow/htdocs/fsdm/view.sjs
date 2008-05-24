<?sjs

// This gets the variables from link as a get request from url query string 

delrow ="None";

delrow   =  pow_server.GET['delrow'];
delrow =  unescape(delrow.replace(/\+/g," "));


base   =  pow_server.GET['db'];
base =  unescape(base.replace(/\+/g," "));

table   =  pow_server.GET['table'];
table =  unescape(table.replace(/\+/g," "));
document.write("<blockquote>");

document.write("<h1 align=center>View/Delete</h1>");
document.write("Database <b>"+ base +"</b> has been selected");
document.write("<hr>");
document.write("Table <b>"+ table +"</b> has been selected");
document.write("<hr>");
document.write("Row <b>"+ delrow +"</b> has been selected and deleted");
document.write("<hr>");



 
// create - open database
 
var mydb = new pow_DB(base);
// mydb.exec("CREATE TABLE namevalue (id INTEGER PRIMARY KEY, name,value)");
// mydb.exec("INSERT INTO namevalue (name, value) VALUES ( 'age', '39')");



// get the table that describes the database
sstring =  "SELECT * FROM  SQLITE_MASTER";
masterresults =  mydb.exec(sstring);
// writes the creation string of the table
totaltable =  masterresults.length;
tt = totaltable -1;
// loop through the tables in the database
for (i=0;i<=tt;i++){ 
//  masterresults[i][4] is the value with the table creation string
// next line test if table creation string contains the table name
if(masterresults[i][4].indexOf(table) != -1){mastertablenum = i;
// document.write(masterresults[i][4]);document.write("<hr>");
}; 
// end of loop thought table
};

// get the column names
creation = masterresults[mastertablenum][4];
creation = creation.replace(/ INTEGER PRIMARY KEY/g,"");
creation = creation.replace(/CREATE TABLE/g,"");
creation = creation.replace(table,"");
creation = creation.replace(/\(/g,"");
creation = creation.replace(/\)/g,"");
colnames = creation.split(",");

//get number of colums in the result set
numcolnames = colnames.length;
//zero based numbering for arrarys
numcolnames  =  numcolnames -1; 
//for (i=0;i<=numcolnames;i++){
//document.write("Column:<b>"+colnames[i]+"</b> ");};
//document.write("<hr>");

// end of getting column names

//make query and do the delete

//   $delQuery =  "DELETE FROM $table WHERE id = \"$pickrow\"";


qdelete  =   'DELETE FROM '+ table + ' WHERE id = "'+delrow+'"';

// document.write(qdelete);

 var results = mydb.exec(qdelete);

// delete has been accomplished


// Make query string to display table results
qselect =  "SELECT * FROM "+ table;
// execute query string              
var results = mydb.exec(qselect);

document.write("<hr>");
document.write(typeof results);
document.write("<hr>");

// array returned in results  rows is number of rows returned
// two dimensional array  results[i][i]  returned
rows = results.length;
//zero based numbering for arrarys
rowcount = rows - 1;
//get number of colums in the result set
cols = results[0].length;
//zero based numbering for arrarys
colcount  =  cols -1; 

document.write("<table border=1 cellpadding=4 cellspacing=4>");
document.write("<tr><td> </td>");
for (i=0;i<=numcolnames;i++){
showrow = "<td><b>" + colnames[i]+"</b></td>";
document.write(showrow);
};
document.write("</tr>");

for (i=0;i<=rowcount;i++){


document.write("<tr>");

showdel ="<td><a href='view.sjs?db="+base+"&table="+table+"&delrow="+results[i][0]+"'> Delete This Row </a> </td>"

document.write(showdel);


for (j=0;j<=colcount;j++){

showrow = "<td>" + results[i][j]+"</td>";

document.write(unescape(showrow));

};


document.write("<tr>");

       
}; 


document.write("</table>");


// Make form to add values to the database

document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");
document.write("");







//  check for variable crash
//for (i=0;i<=numcolnames;i++){
//document.write("Column:<b>"+colnames[i]+"</b> ");};
//document.write("<hr>");

 

//  end of the entire document
document.write("</blockquote>");
?>


 