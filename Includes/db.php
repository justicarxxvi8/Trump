<?php
/*
* This file handles the connection to the database and is called in each file that requires a database connection.
* Note that this database connection currently runs on a localhost. 
*/
try{
    $dbh = new PDO("mysql: host=localhost; dbname=Trump", "root", "995o995o");
    }  catch(PDOException $e){
        echo $e->getMessage();
    }
/*
The php code below is the connection handler to the heroku cloud platform. 


$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

$host = $url["host"];
$user = $url["user"];
$pass = $url["pass"];
$dbname = substr($url["path"], 1);

try {
    $dbh = new PDO("mysql:host=".$host."; dbname=".$dbname, $user, $pass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    print "Error!: " . $e->getMessage() . "<br/>";
    die();
}


*/    
    
?>



