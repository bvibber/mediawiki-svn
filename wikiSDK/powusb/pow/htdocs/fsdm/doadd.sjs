<?sjs
 

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
colvalue[i] =  unescape(colvalue[i].replace(/\+/g," "));

// alert(colvalue[i]);
};
 

document.write("<blockquote><br>");
document.write("<h1 align=center>View/Adding</h1>");
document.write("Database <b>"+ base +"</b> has been selected");
document.write("<hr>");
document.write("Table <b>"+ table +"</b> has been selected");
document.write("<hr>");


// create - open database
 
var mydb = new pow_DB(base);
 
 // get the table that describes the database
sstring =  "SELECT * FROM  SQLITE_MASTER";
masterresults =  mydb.exec(sstring);
totaltable =  masterresults.length; tt = totaltable -1;
for (i=0;i<=tt;i++){ 
if(masterresults[i][4].indexOf(table) != -1){mastertablenum = i;}; 
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

 allnames =""; allvalues =""; 
document.write("Values to be Added to database<br>");
  for (i=1;i<=numcolnames;i++){
 document.write("Column:<b>"+colnames[i]+"</b>Value:<b>"+colvalue[i] +"</b> <br>");
 
 allnames = allnames + ',' + colnames[i];
 allvalues = allvalues + ',' +'"'+escape(colvalue[i])+'"';
  };
 document.write("<hr>");
// end of getting column names
// Make query string  to insert new table row values
// mydb.exec("INSERT INTO namevalue (name, value) VALUES ( 'age', '39')");

allnames = allnames.slice(1);
allvalues = allvalues.slice(1);

qinsert =  "INSERT INTO "+ table +" ("+ allnames+") VALUES ("+allvalues+")";

 document.write(qinsert);

mydb.exec(qinsert);
 



// Make query string  to display table values
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

for (i=0;i<=numcolnames;i++){
showrow = "<td><b>" + colnames[i]+"</b></td>";
document.write(showrow);
};


for (i=0;i<=rowcount;i++){
document.write("<tr>");
for (j=0;j<=colcount;j++){
showrow = "<td>" + results[i][j]+"</td>";
document.write(unescape(showrow));
};
document.write("<tr>");
}; 
document.write("</table>");

// Make form to add values to the database

document.write("<hr> <h2>Enter A New Row Into Table <u>"+ table + "</u> </h2>");
document.write("<form  method='Post' action='doadd.sjs'> ");
document.write("<input type='hidden' name='db' value="+base+">");
document.write("<input type='hidden' name='table' value="+table+">");
document.write("<input type='hidden' name='numcolnames' value="+numcolnames+">");

document.write("<table border='2' cellpadding='4' cellspaceing='4'>");

for (i=1;i<=numcolnames;i++){
showrow = "<tr><td align='right'><b>" + colnames[i]+":</b> &nbsp;&nbsp;</td><td aling='left'>" + "<input size='40' name=colvalue"+i+"></td>";
document.write(showrow);
};

 
document.write("</table><br><INPUT TYPE='SUBMIT' Name='submit' VALUE='Add Row to Table'></form><hr/>");
document.write("");
 




 

//  end of the entire document
document.write("</blockquote>");
?>


 