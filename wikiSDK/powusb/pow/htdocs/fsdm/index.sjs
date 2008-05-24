<?sjs


  if(pow_file_exists("/pow/data/fsdmlist.sqlite")) {
 //  document.write("database list does exist");
  } else {
 //  document.write("database list does not exist");
  
   
// create - open database

var mydb = new pow_DB("fsdmlist");

 //build create table query
ctstring = "CREATE TABLE databaselist (id INTEGER PRIMARY KEY, databasenames)"; 
istring = "INSERT INTO  databaselist (databasenames) VALUES('fsdmlist')";  
mydb.exec(ctstring);
mydb.exec(istring);
  }

 
?>
 


<html>
<head>
<title>Sqlite Database Manager</title>
</head>

<FRAMESET ROWS="10%, 90%" FRAMEBORDER=no  border=0>
<FRAME  SRC="litetop.html"  scrolling="no" marginwidth="0" marginheight="0" name="topnav" >
<FRAME  SRC="litebottom.html" name="main">
</FRAMESET>