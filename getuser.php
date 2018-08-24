<?php

print_r($_REQUEST);
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);

if (!$conn) {
    die('Could not connect: ' . mysql_error());
}
$sql = "SELECT * FROM clients where id=".$_REQUEST[q];
echo $sql; 
mysql_select_db('colleges');
$retval = mysql_query($sql, $conn);

if (!$retval) {
    die('Could not get data: ' . mysql_error());
}

while ($row = mysql_fetch_array($retval, MYSQL_ASSOC)) {
    echo "Tutorial ID :{$row['id']}  <br> " .
    "Title: {$row['name']} <br> " .
    "Author: {$row['email']} <br> " .
    "--------------------------------<br>";
}
echo "Fetched data successfully\n";
mysql_close($conn);
