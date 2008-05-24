<?sjs

// This gets the variables from link as a get request from url query string 



uprow ="None";

uprow =  pow_server.POST['uprow'];
uprow =  unescape(uprow.replace(/\+/g," "));

 
var colvalue = new Array(); 

colvalue[0]="";

base   =  pow_server.POST['db'];
base =  unescape(base.replace(/\+/g," "));

table   =  pow_server.POST['table'];
table =  unescape(table.replace(/\+/g," "));

numcolnames   =  pow_server.POST['numcolnames'];
numcolnames =  unescape(numcolnames.replace(/\+/g," "));

for (i=1;i<=numcolnames;i++){  
temp = "colvalue"+i;
colvalue[i] = pow_server.POST[temp];
colvalue[i]=  unescape(colvalue[i].replace(/\+/g," "));


// alert(colvalue[i]);
};
 



document.write("<blockquote><br>");
document.write("<h1 align=center>View/Update</h1>");
document.write("Database <b>"+ base +"</b> has been selected");
document.write("<hr>");
document.write("Table <b>"+ table +"</b> has been selected");
document.write("<hr>");
document.write("Row <b>"+ uprow +"</b> has been selected to be updated");
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



 
// UPDATE Person
// SET Address = 'Stien 12', City = 'Stavanger'
// WHERE LastName = 'Rasmussen'


//start update query

qup1 = " UPDATE " +table +" SET ";
qup2  =  "";
for (i=1;i<=numcolnames;i++){
qup2 =  qup2+", "+colnames[i]+" = '"+escape(colvalue[i])+"'"; 
};
qup2 = qup2.slice(1);

qup3 = " WHERE id = '"+uprow+"'";

qupdate = qup1+qup2+qup3;

document.write("<hr>"+qupdate+"<hr>");

upresults =  mydb.exec(qupdate);


//end update query





// Make query string to display the table
qselect =  "SELECT * FROM "+ table;
// execute query string              
var results = mydb.exec(qselect);
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

showup ="<td><a href='update.sjs?db="+base+"&table="+table+"&uprow="+results[i][0]+"'> Update This Row </a> </td>"

document.write(showup);



for (j=0;j<=colcount;j++){

showrow = "<td>" + results[i][j]+"</td>";

document.write(unescape(showrow));

};


document.write("<tr>");

       
}; 


document.write("</table>");


// Make form to add values to the database

document.write("<hr> <h2>Enter A New Row Into Table <u>"+ table + "</u> </h2>");
document.write("<form  method='Post' action='doupdate.sjs'> ");
document.write("<input type='hidden' name='db' value="+base+">");
document.write("<input type='hidden' name='table' value="+table+">");
document.write("<input type='hidden' name='numcolnames' value="+numcolnames+">");
document.write("<input type='hidden' name='uprow' value="+ uprow +">");

document.write("<table border='2' cellpadding='4' cellspaceing='4'>");


//do loop  to find the javascript row for the sql row with id uprow
var q=0
while (q<=rowcount)
{
if(results[q][0] == uprow){jsrow = q; q = rowcount;}
 q=q+1
};
//end of do loop

for (i=1;i<=numcolnames;i++){
showrow = "<tr><td align='right'><b>" + colnames[i]+":</b> &nbsp;&nbsp;</td><td aling='left'>" + "<input size='40' name=colvalue"+i+" value='"+  unescape(results[jsrow][i])+"'></td>";
document.write(showrow);
};
 
 
document.write("</table><br><INPUT TYPE='SUBMIT' Name='submit' VALUE='Update Row "+ uprow +"'></form><hr/>");
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


 