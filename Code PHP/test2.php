
<?php

require('db.inc.php');

$userid = 27;

$usernamelookupResult = mysqli_query($GLOBALS["cnx"], "SELECT kegid, percentconsumed FROM dasbot.keg_stats ORDER BY kegid ASC")
	or die(mysqli_error($GLOBALS["cnx"])); 

while($usernamelookupRows = mysqli_fetch_array($usernamelookupResult)){
	
	//echo "Keg ID: ";
	echo $usernamelookupRows['kegid'];
	//echo "<br>";
	//echo "Percent Consumed: ";
	echo $usernamelookupRows['percentconsumed'];
	//echo "<br>";
	//echo "<br>";
	
}



?>


