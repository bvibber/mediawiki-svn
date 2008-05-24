<html>
<head>
<script>
 
//  url from 
//  http://localhost:6670/lite2/check.sjs?base=two   
chref  = location.href;
// alert(chref);
cbase  = chref.split("?");
cbase[1] =  cbase[1].replace(/base=/g,"");
// alert(cbase[1]);

newnav = "<b><a href='"+chref+"' target='main'>View / Manage Tables of Database: "+cbase[1]+"</b></a>";

// alert(newnav);

parent.topnav.document.getElementById('selecteddb').innerHTML = newnav;


</script>

</head>
<body>

<?sjs

// This gets the variables from link as a get request from url query string 
base   =  pow_server.GET['base'];
base =  unescape(base.replace(/\+/g," "));

  
 
// create - open database
var thedb = new pow_DB(base);

document.write("<blockquote><br>");
document.write("Database <b>"+ base +"</b> has been selected");
document.write("<hr>");


// get the table that describes the database
table = 'SQLITE_MASTER';

sstring =  "SELECT * FROM " + table;

results =  thedb.exec(sstring);

resultstr  =  results.join();


tables =  resultstr.split(")")

// alert(tables.length);

numtables =  tables.length;
numtables = numtables -2;


for (i=0;i<=numtables;i++){

  slicenum  = tables[i].indexOf("CREATE");
  tables[i] = tables[i].slice(slicenum);
  tables[i] = tables[i] +")";
// document.write(tables[i]);
// document.write("<hr>");
};

//   first =  unescape(first.replace(/\+/g," "));
 
 var names = new Array();
 var tablename = new Array();
  
for (i=0;i<=numtables;i++){
names[i]  =  tables[i].replace(/CREATE TABLE /g,"");
names[i]  =  names[i].replace(/INTEGER PRIMARY KEY/g,"");
names[i]  =  names[i].replace(/\(/g,"");
names[i]  =  names[i].replace(/\)/g,"");
names[i]  =  names[i].replace(/id/g,",id");
names[i]  =  names[i].replace(/ /g,"");

temp =  names[i].split(",");
tablename[i] =  temp[0];

}; 


// viewlite.php?db=test.db&table=namevalue
// view.sjs?db=thedatabase&table=thetablename

//  base  --> datbase name variable  tablename[i] --> table name variable

//  View/Delete	View/Add	View/Update

for (i=0;i<=numtables;i++){

document.write("Table: <b>"+tablename[i]+"</b>");
 
linkdel = " &nbsp;  &nbsp; &nbsp; &nbsp; <a href='view.sjs?db="+base+"&table="+tablename[i]+"&delrow=None'>View/Delete</a>";
linkadd = " &nbsp;  &nbsp; &nbsp; &nbsp; <a href='add.sjs?db="+base+"&table="+tablename[i]+"'>View/Add</a>"; 
linkup =  " &nbsp;  &nbsp; &nbsp; &nbsp; <a href='update.sjs?db="+base+"&table="+tablename[i]+"&uprow=None'>View/Update</a>";

document.write(linkdel+linkadd+linkup); 
document.write("<br/>"+tables[i]+"<hr>");
document.write("");
document.write("");

}

document.write("</blockquote>");


?>

</body>
</html>


 