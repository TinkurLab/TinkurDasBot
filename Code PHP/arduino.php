<?php

//Test URL
//http://www.tinkurlab.com/projects/dasbot/arduino.php?rfid=12345678&consumed1=750&consumed2=0&consumed3=0

require('db.php.inc');

include 'badgecheckfunction.php';

//First, check for RFID values
if(isset($_GET['rfid'])) {  
  $rfid = $_GET['rfid'];  
}

else { // No RFID value sent - end communication
  exit("ERROR: No RFID value sent. Please retry");
}


//GET REFERENCE VALUES

//get total ticks per liter
$refTicksPerLiterResult = mysqli_query($GLOBALS["cnx"], "SELECT value FROM ref_data WHERE name = 'ticks_per_liter' LIMIT 1")
or die(mysqli_error($GLOBALS["cnx"]));
$refTicksPerLiterRows = mysqli_fetch_array( $refTicksPerLiterResult );
$refTicksPerLiter = $refTicksPerLiterRows['value'];

//get keg status 
 $kegStateResult = mysqli_query($GLOBALS["cnx"], "SELECT AVG(percentconsumed) AS avgpercentconsumed FROM keg_stats")
 or die(mysqli_error($GLOBALS["cnx"]));
 $kegStateRows = mysqli_fetch_array( $kegStateResult );
 $kegState = (100 - round($kegStateRows['avgpercentconsumed'],0));
 

//Now that we know the RFID value has been passed, check to see if the RFID is a current user

$userResult = mysqli_query($GLOBALS["cnx"], "SELECT * FROM users WHERE rfid = '$rfid' LIMIT 1")
or die(mysqli_error($GLOBALS["cnx"]));

if(mysqli_num_rows($userResult) == 1) {
  //echo "Thank you for using Das Bot";
  
  $userRows = mysqli_fetch_array( $userResult );
  $user = $userRows['username'];
  $userID = $userRows['id'];
  
  //Check to see if user has registered
  
  //UNREGISTERED USER RETURNS
  if($user == 'orphan' && !isset($_GET['consumed1'])) { // rfid tag not registered yet
   
     die("Welcome back ID $userID!\n\nI Uou must register at http://is.gd/kickthekeg before I can give you more beer.\n\nNo beer for you!");
    
  }
  
  //EXISTING, REGISTERED USER RETURNS
  else { 

    //debug
    //echo "<p>total ticks per keg = $refTotalTicks</p>";
    //echo "<p>total ticks per Liter = $refTicksPerLiter</p>";
    
    //GET CURRENT TOTAL CONSUMPTION
    $totalDrinkResult = mysqli_query($GLOBALS["cnx"], "SELECT sum(volume) as total FROM drinks WHERE userid = '$userID' LIMIT 1")
	or die(mysqli_error($GLOBALS["cnx"]));
    $totalDrinksRows = mysqli_fetch_array( $totalDrinkResult );
    $totalDrinks = $totalDrinksRows['total'];
    $litersConsumed = round($totalDrinks / $refTicksPerLiter, 1);
    
    
    ///  - 2nd service call - ///
    
    //check to see if this is the second call with pour data
    if(isset($_GET['consumed1']) && isset($_GET['rfid'])) {
     
      //add drink record(s)
      $curTime = time();
      
	  if($_GET['consumed1'] > 5) {
		  $drink1 = mysqli_real_escape_string($GLOBALS["cnx"], $_GET['consumed1']);
		  $insertDrinkQuery = "INSERT INTO  `dasbot`.`drinks` (id,timestamp, userid, volume, kegid)
					VALUES (NULL ,'$curTime', '$userID',  '$drink1', 1)";      
		  mysqli_query($GLOBALS["cnx"], $insertDrinkQuery);
	  }
	  
	  if($_GET['consumed2'] > 5) {
		  $drink2 = mysqli_real_escape_string($GLOBALS["cnx"], $_GET['consumed2']);
		  $insertDrinkQuery = "INSERT INTO  `dasbot`.`drinks` (id,timestamp, userid, volume, kegid)
					VALUES (NULL ,'$curTime', '$userID',  '$drink2', 2)";     
		  mysqli_query($GLOBALS["cnx"], $insertDrinkQuery);
	  }
	  
	  if($_GET['consumed3'] > 5) {
		  $drink3 = mysqli_real_escape_string($GLOBALS["cnx"], $_GET['consumed3']);
		  $insertDrinkQuery = "INSERT INTO  `dasbot`.`drinks` (id,timestamp, userid, volume, kegid)
					VALUES (NULL ,'$curTime', '$userID',  '$drink3', 3)";       
		  mysqli_query($GLOBALS["cnx"], $insertDrinkQuery);
	  }     
	  
	  //award badges via function
	  awardbadges($userID);
      
      //get current leaders
      $leaderQuery = "SELECT users.username, sum(drinks.volume) as total FROM drinks, users WHERE users.id= drinks.userid GROUP BY userid ORDER BY total DESC LIMIT 3";
      
      $leadersResult = mysqli_query($GLOBALS["cnx"], $leaderQuery)
        or die(mysqli_error($GLOBALS["cnx"]));
      
      //output message
      $curDrinkLiters = round(($drink1 + $drink2 + $drink3)/$refTicksPerLiter, 1);
      echo "You poured $curDrinkLiters L.  The kegs are now $kegState% full.  Come back soon!";
      
      /*
      //LOOP THROUGH LEADER
      $i=1;
      while($rowLeaders = mysql_fetch_array($leadersResult)){
        $user = $rowLeaders['username'];
        $totalConsumed = $rowLeaders['total'];
        $totalConsumedLiters = round($totalConsumed / $refTicksPerLiter, 1);
        
        echo "$i. $user - $totalConsumedLiters L\n";
        $i++;
      }
      */
      
      
      die; // if this is the 2nd call, end here
    }
    
    
    /// - 1st service call - ///
        
    echo "Welcome back $user!  You have currently consumed $litersConsumed L of beer.  The kegs are $kegState% full.";
    
  }
}

//FIRST TIME USING THE RFID CHIP
else {
  //echo "User not found";
  
  mysqli_query($GLOBALS["cnx"], "INSERT INTO users (rfid) 
	VALUES ('$rfid')");
  $userID = ((is_null($___mysqli_res = mysqli_insert_id($GLOBALS["cnx"]))) ? false : $___mysqli_res);
  
  echo "Hello! Das Bot v2.0 welcomes youID $userID!\n\n You must register at http://is.gd/kickthekeg before I can give you more beer.";
}

?>