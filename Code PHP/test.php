
<?php

require('db.php.inc');

$userid = 27;

$usernamelookupResult = mysql_query("SELECT kegid, percentconsumed FROM dasbot.keg_stats ORDER BY kegid ASC")
	or die(mysql_error()); 

while($usernamelookupRows = mysql_fetch_array($usernamelookupResult)){
	
	echo "Keg ID: ";
	echo $usernamelookupRows['kegid'];
	echo "<br>";
	echo "Percent Consumed: ";
	echo $usernamelookupRows['percentconsumed'];
	echo "<br>";
	echo "<br>";
	
}



?>


