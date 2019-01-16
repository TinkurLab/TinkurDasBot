<?php


//connect to Database

$link = ($GLOBALS["cnx"] = mysqli_connect('www.yourdbserver.com',  'DBUserName',  'DBUserPwd'));
if (!$link) {
    die('Could not connect: ' . mysqli_error($GLOBALS["cnx"]));
}
else{
    mysqli_select_db($GLOBALS["cnx"], "dasbot");
}


?>

