<?sjs

// get database and table name

dname   =  pow_server.POST['dname'];
tname   =  pow_server.POST['tname']; 
dname =  unescape(dname.replace(/\+/g," "));
tname =  unescape(tname.replace(/\+/g," ")); 

// from php version of sdm
// $cstring = "CREATE TABLE $table (id INTEGER PRIMARY KEY, $colnames)";
// $istring = "INSERT INTO $table ($colnames) VALUES($colvalues)";

//write database name to cvs database list should be in same location as db's
//dnamecsv =  dname+",";
//pow_file_put_contents("/pow/htdocs/lite2/databases.txt",dnamecsv, "wa" );
 
// put dbname into dblist list 
var mydb = new pow_DB("fsdmlist");
 
qinsert =  "INSERT INTO databaselist (databasenames) VALUES ('"+dname+"')";

 document.write(qinsert);

mydb.exec(qinsert); 

// 
 
 
// create - open database
var mydb = new pow_DB(dname);


// get the column names
colnames = "";  colvalues = "";
for (i=1;i<=100;i++){
dex = "text"+ i; dex   =  pow_server.POST[dex]; 
dex = dex+""; dex =  unescape(dex.replace(/\+/g," "));
if(dex=="undefined"){break}else {
// use this line to build column part of the query
colnames = colnames+", "+dex;  
colvalues = colvalues + ',"'+dex+'"';
}
}

//build create table query
ctstring = "CREATE TABLE "+ tname +" (id INTEGER PRIMARY KEY"+ colnames+")";
//build insert table  query
istring = "INSERT INTO  "+ tname +" (" + colnames.slice(1) +") VALUES("+colvalues.slice(1)+")";
//build select all query
sstring =  "SELECT * FROM " + tname;


mydb.exec(ctstring);
mydb.exec(istring);
results =  mydb.exec(sstring);

out = "Database Name: <a href='check.sjs?base="+dname+"'>"+ dname +"</a><br>" + ctstring +"<hr>"+istring +"<hr>"+sstring+"<hr>"+results;

?>
<html>
<body> 
<?sjs
 document.write(out); 
?>
</body>
</html>
