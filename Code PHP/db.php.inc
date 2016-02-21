<?php


//connect to Database

$link = mysql_connect('www.yourdbserver.com', 'DBUserName', 'DBUserPwd');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
else{
    mysql_select_db('dasbot');
}


?>

