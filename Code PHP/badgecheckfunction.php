<?php

//ensure "require('db.php.inc');" is included in the main file from which this is included

//usage "awardbadges(1);" 

function awardbadges($userid)
{

$usernamelookupResult = mysql_query("SELECT username FROM dasbot.users WHERE id = $userid")
	or die(mysql_error()); 

while($usernamelookupRows = mysql_fetch_array($usernamelookupResult)){
	$username = $usernamelookupRows['username'];	
}
	
if ($username == "Pitcher")
{
	
	echo "Pitcher gets no badges! ";
		
} else {
	
	//get available badges 
	$availablebadgesResult = mysql_query("SELECT bb.badgeid, bb.badgetitle, bb.badgeactive, bb.badgerules, bb.badgeqty, ba.badgeawardedcount
	FROM (dasbot.badges bb LEFT JOIN (SELECT badgeid, count(*) AS badgeawardedcount FROM dasbot.badgesawarded GROUP BY badgeid) ba 
	ON bb.badgeid = ba.badgeid) LEFT JOIN (SELECT badgeid FROM dasbot.badgesawarded WHERE userid = $userid) bc ON bb.badgeid = bc.badgeid
	WHERE (bb.badgeqty = 0 OR bb.badgeqty > ba.badgeawardedcount OR ba.badgeawardedcount IS NULL) AND bc.badgeid IS NULL AND bb.badgeactive = 1")
	or die(mysql_error()); 
	
	//establish counter for array of awarded badges
	$b = 0;
	
	//loop through available badges
	while($availablebadgesRows = mysql_fetch_array($availablebadgesResult)){
	
	  $availablebadgesID = $availablebadgesRows['badgeid'];
	  $availablebadgesTitle = $availablebadgesRows['badgetitle'];
	  $availablebadgesRules = $availablebadgesRows['badgerules'];
	  
	  //update SQL rule from badge w/ userid
	  $availablebadgesRules = str_replace("replaceWithUserID",$userid,$availablebadgesRules);
	 
	  //output for debugging	
	  //echo $availablebadgesID;
	  //echo $availablebadgesTitle;
	  //echo $availablebadgesRules;
	  //echo "<br>";
	  
		//check if badge should be awarded
		$awardbadgeResult = mysql_query("$availablebadgesRules")
		or die(mysql_error()); 
		echo mysql_error();
		
		while($awardbadgeRows = mysql_fetch_array($awardbadgeResult)){
		
		  $awardbadgeBool = $awardbadgeRows['awardbadge'];
		  
		  //output for debugging
		  //echo "award bool: "; 
		  //echo $awardbadgeBool;
		  //echo "<br>";
		  
		  //award badge if qualifies
		  if($awardbadgeBool == 1){
			  $awardBadge = "INSERT INTO dasbot.badgesawarded (userid, badgeid) VALUES ($userid, $availablebadgesID)";      
			  mysql_query($awardBadge);
			  
			  //store awarded badges in array
			  $awardedBadges[$b] = $availablebadgesID;
			  
			  //increment array index
			  $b++;
		  }
	
		}
	
	
	}
	
	//output for debugging
	for ($i=0; $i<$b; $i++)
	  {
	  	//echo "Badge awarded: " . $awardedBadges[$i] . " "; 
	    
	  	$badgelookupResult = mysql_query("SELECT badgetitle, badgedescription FROM dasbot.badges WHERE badgeid = $awardedBadges[$i]")
	  	or die(mysql_error()); 

		while($badgelookupRows = mysql_fetch_array($badgelookupResult)){
			$badgetitle = $badgelookupRows['badgetitle'];	
			$badgedescription = $badgelookupRows['badgedescription'];
			
			echo ("You won a badge: " . $badgetitle . "\n");
			
		}

	  }
	
	}
}
?>