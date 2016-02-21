
<?php

require('db.php.inc');

//check for rfid in url. If present, set variable
if(isset($_GET['rfid'])) {
  $rfid = $_GET['rfid'];
}

//GET REFERENCE VALUES

//get total ticks per liter
$refTicksPerLiterResult = mysql_query("SELECT value FROM ref_data WHERE name = 'ticks_per_liter' LIMIT 1")
or die(mysql_error());
$refTicksPerLiterRows = mysql_fetch_array( $refTicksPerLiterResult );
$refTicksPerLiter = $refTicksPerLiterRows['value'];


//get current leaders
$leaderQuery = "SELECT users.username, sum(drinks.volume) as total FROM drinks, users WHERE users.id= drinks.userid AND users.username != 'orphan' AND users.username != 'Pitcher' GROUP BY userid ORDER BY total DESC LIMIT 5";

$leadersResult = mysql_query($leaderQuery)
  or die(mysql_error());



//get keg status
$kegConsumedResult = mysql_query("SELECT kegid, percentconsumed FROM keg_stats ORDER BY kegid ASC")
 or die(mysql_error());

//preset values:
$keg1Consumed = 0;
$keg2Consumed = 0;
$keg3Consumed = 0;

$i = 0;
while($kegConsumedRows = mysql_fetch_assoc($kegConsumedResult)){

 if($kegConsumedRows['kegid'] == "1")
    $keg1Consumed = round($kegConsumedRows['percentconsumed']);

  if($kegConsumedRows['kegid'] == "2")
    $keg2Consumed = round($kegConsumedRows['percentconsumed']);

  if($kegConsumedRows['kegid'] == "3")
    $keg3Consumed = round($kegConsumedRows['percentconsumed']);

}



//GET FUN FACTS

//largest vessel
$largestVesselResult = mysql_query("
	SELECT u.username
	FROM users u,
	     drinks d
	WHERE d.userid = u.id
  	  AND u.username != 'Pitcher'
	  AND u.username != 'orphan'
	ORDER BY d.volume DESC
	LIMIT 1")
 or die(mysql_error());
while($largestVesselRows = mysql_fetch_assoc($largestVesselResult)){
  $largestVesselUser = $largestVesselRows['username'];
}

//Most trips
$mostTripsResult = mysql_query("
	SELECT u.username,
	       count(d.volume) as drinks
	FROM users u,
	     drinks d
	WHERE d.userid = u.id
	  AND u.username != 'Pitcher'
	  AND u.username != 'orphan'
	GROUP BY d.userid
	ORDER BY drinks DESC
	LIMIT 1")

 or die(mysql_error());
while($mostTripsRows = mysql_fetch_assoc($mostTripsResult)){
  $mostTripsUser = $mostTripsRows['username'];
  $mostTripsCount = $mostTripsRows['drinks'];
}

//least drank
$leasDrankResult = mysql_query("
	SELECT users.username as username,
	       sum(drinks.volume) as total,
	       count(drinks.volume) as drink_count
	FROM drinks,
	     users
	WHERE users.id= drinks.userid
	  AND users.username != 'orphan'
	  AND users.username != 'Pitcher'
	GROUP BY userid
	ORDER BY total ASC
	LIMIT 1")
 or die(mysql_error());
while($leastDrankRows = mysql_fetch_assoc($leasDrankResult)){
  $leastDrankUser = $leastDrankRows['username'];
  $leastDrankCount = $leastDrankRows['drink_count'];
  $leastDrankVolume = round($leastDrankRows['total'] / $refTicksPerLiter, 1);
}


//DRINK FEED

$recentDrinksFeed = "";

$recentDrinksResult = mysql_query("
	SELECT users.username as username,
	       drinks.volume as volume,
	       drinks.timestamp as time,
	       kegs.BeerName as beerName
	FROM drinks,
	     users,
	     kegs
	WHERE users.id= drinks.userid
	  AND drinks.kegid = kegs.kegid
	  AND users.username != 'orphan'
	  AND users.username != 'Pitcher'
	ORDER BY drinks.id DESC
	LIMIT 5")
 or die(mysql_error());
while($recentDrinksRows = mysql_fetch_assoc($recentDrinksResult)){
  $recentDrinksUser = $recentDrinksRows['username'];
  $recentDrinksTime= date("g:i a", $recentDrinksRows['time']+(3600*3)); //fix the time zone thing
  $recentDrinksBeer= $recentDrinksRows['beerName'];
  $recentDrinksVolume = round($recentDrinksRows['volume'] / $refTicksPerLiter, 1);
  $recentDrinksFeed = $recentDrinksFeed . "<li>$recentDrinksUser drank $recentDrinksVolume of $recentDrinksBeer at $recentDrinksTime </li>";
}







// GET BADGES

//Default to Inactive version
//TODO: pull these from the database. For now, they're hardcoded...
$badge1Icon = "badges/Inactive/1_liter.png";
$badge2Icon = "badges/Inactive/2_liter.png";
$badge3Icon = "badges/Inactive/mystery.png";
$badge4Icon = "badges/Inactive/mystery.png";
$badge5Icon = "badges/Inactive/mystery.png";
$badge6Icon = "badges/Inactive/mystery.png";
$badge7Icon = "badges/Inactive/5_badges.png";
$badge8Icon = "badges/Inactive/welcoming.png";
$badge9Icon = "badges/Inactive/mystery.png";
$badge10Icon = "badges/Inactive/mystery.png";
$badge11Icon = "badges/Inactive/mystery.png";
$badge12Icon = "badges/Inactive/back2back.png";
$badge13Icon = "badges/Inactive/mystery.png";
$badge14Icon = "badges/Inactive/10_badges.png";

$badge1Winners = "";
$badge2Winners = "";
$badge3Winners = "";
$badge4Winners = "";
$badge5Winners = "";
$badge6Winners = "";
$badge7Winners = "";
$badge8Winners = "";
$badge9Winners = "";
$badge10Winners = "";
$badge11Winners = "";
$badge12Winners = "";
$badge13Winners = "";
$badge14Winners = "";


//badge 1

$badge1Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 1
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge1_num_winners = mysql_num_rows($badge1Result);
$i=0;
if($badge1_num_winners != 0) {
	$badge1Winners = "";
	while($badge1Rows = mysql_fetch_assoc($badge1Result)){
	  if($i<3)
	    $badge1Winners = $badge1Winners.$badge1Rows['username']."<br>";
	  $badge1Title = $badge1Rows['title'];
	  $badge1Desc = $badge1Rows['description'];
	  $badge1Icon = "badges/Active/".$badge1Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge1Winners = $badge1Winners."+$i others";
	}
} //end Badge 1


//badge 2

$badge2Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 18
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge2_num_winners = mysql_num_rows($badge2Result);
$i=0;
if($badge2_num_winners != 0) {
	$badge2Winners = "";
	while($badge2Rows = mysql_fetch_assoc($badge2Result)){
	  if($i<3)
	    $badge2Winners = $badge2Winners.$badge2Rows['username']."<br>";
	  $badge2Title = $badge2Rows['title'];
	  $badge2Desc = $badge2Rows['description'];
	  $badge2Icon = "badges/Active/".$badge2Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge2Winners = $badge2Winners."+$i others";
	}
} //end Badge 2

//badge 3
$badge3Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 5
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge3_num_winners = mysql_num_rows($badge3Result);
$i=0;
if($badge3_num_winners != 0) {
	$badge3Winners = "";
	while($badge3Rows = mysql_fetch_assoc($badge3Result)){
	  if($i<3)
	    $badge3Winners = $badge3Winners.$badge3Rows['username']."<br>";
	  $badge3Title = $badge3Rows['title'];
	  $badge3Desc = $badge3Rows['description'];
	  $badge3Icon = "badges/Active/".$badge3Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge3Winners = $badge3Winners."+$i others";
	}
} //end Badge 3


//badge 4
$badge4Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 4
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge4_num_winners = mysql_num_rows($badge4Result);
$i=0;
if($badge4_num_winners != 0) {
	$badge4Winners = "";
	while($badge4Rows = mysql_fetch_assoc($badge4Result)){
	  if($i<3)
	    $badge4Winners = $badge4Winners.$badge4Rows['username']."<br>";
	  $badge4Title = $badge4Rows['title'];
	  $badge4Desc = $badge4Rows['description'];
	  $badge4Icon = "badges/Active/".$badge4Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge4Winners = $badge4Winners."+$i others";
	}
} //end Badge 4

//badge 5
$badge5Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 3
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge5_num_winners = mysql_num_rows($badge5Result);
$i=0;
if($badge5_num_winners != 0) {
	$badge5Winners = "";
	while($badge5Rows = mysql_fetch_assoc($badge5Result)){
	  if($i<3)
	    $badge5Winners = $badge5Winners.$badge5Rows['username']."<br>";
	  $badge5Title = $badge5Rows['title'];
	  $badge5Desc = $badge5Rows['description'];
	  $badge5Icon = "badges/Active/".$badge5Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge5Winners = $badge5Winners."+$i others";
	}
} //end Badge 5

//badge 6
$badge6Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 16
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge6_num_winners = mysql_num_rows($badge6Result);
$i=0;
if($badge6_num_winners != 0) {
	$badge6Winners = "";
	while($badge6Rows = mysql_fetch_assoc($badge6Result)){
	  if($i<3)
	    $badge6Winners = $badge6Winners.$badge6Rows['username']."<br>";
	  $badge6Title = $badge6Rows['title'];
	  $badge6Desc = $badge6Rows['description'];
	  $badge6Icon = "badges/Active/".$badge6Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge6Winners = $badge6Winners."+$i others";
	}
} //end Badge 6

//badge7
$badge7Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 7
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge7_num_winners = mysql_num_rows($badge7Result);
$i=0;
if($badge7_num_winners != 0) {
	$badge7Winners = "";
	while($badge7Rows = mysql_fetch_assoc($badge7Result)){
	  if($i<3)
	    $badge7Winners = $badge7Winners.$badge7Rows['username']."<br>";
	  $badge7Title = $badge7Rows['title'];
	  $badge7Desc = $badge7Rows['description'];
	  $badge7Icon = "badges/Active/".$badge7Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge7Winners = $badge7Winners."+$i others";
	}
} //end badge7

//badge8
$badge8Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 17
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge8_num_winners = mysql_num_rows($badge8Result);
$i=0;
if($badge8_num_winners != 0) {
	$badge8Winners = "";
	while($badge8Rows = mysql_fetch_assoc($badge8Result)){
	  if($i<3)
	    $badge8Winners = $badge8Winners.$badge8Rows['username']."<br>";
	  $badge8Title = $badge8Rows['title'];
	  $badge8Desc = $badge8Rows['description'];
	  $badge8Icon = "badges/Active/".$badge8Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge8Winners = $badge8Winners."+$i others";
	}
} //end badge8

//badge9
$badge9Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 12
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge9_num_winners = mysql_num_rows($badge9Result);
$i=0;
if($badge9_num_winners != 0) {
	$badge9Winners = "";
	while($badge9Rows = mysql_fetch_assoc($badge9Result)){
	  if($i<3)
	    $badge9Winners = $badge9Winners.$badge9Rows['username']."<br>";
	  $badge9Title = $badge9Rows['title'];
	  $badge9Desc = $badge9Rows['description'];
	  $badge9Icon = "badges/Active/".$badge9Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge9Winners = $badge9Winners."+$i others";
	}
} //end badge9

//badge10
$badge10Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 13
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge10_num_winners = mysql_num_rows($badge10Result);
$i=0;
if($badge10_num_winners != 0) {
	$badge10Winners = "";
	while($badge10Rows = mysql_fetch_assoc($badge10Result)){
	  if($i<3)
	    $badge10Winners = $badge10Winners.$badge10Rows['username']."<br>";
	  $badge10Title = $badge10Rows['title'];
	  $badge10Desc = $badge10Rows['description'];
	  $badge10Icon = "badges/Active/".$badge10Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge10Winners = $badge10Winners."+$i others";
	}
} //end badge10

//badge11
$badge11Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 14
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge11_num_winners = mysql_num_rows($badge11Result);
$i=0;
if($badge11_num_winners != 0) {
	$badge11Winners = "";
	while($badge11Rows = mysql_fetch_assoc($badge11Result)){
	  if($i<3)
	    $badge11Winners = $badge11Winners.$badge11Rows['username']."<br>";
	  $badge11Title = $badge11Rows['title'];
	  $badge11Desc = $badge11Rows['description'];
	  $badge11Icon = "badges/Active/".$badge11Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge11Winners = $badge11Winners."+$i others";
	}
} //end badge11

//badge12
$badge12Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 9
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge12_num_winners = mysql_num_rows($badge12Result);
$i=0;
if($badge12_num_winners != 0) {
	$badge12Winners = "";
	while($badge12Rows = mysql_fetch_assoc($badge12Result)){
	  if($i<3)
	    $badge12Winners = $badge12Winners.$badge12Rows['username']."<br>";
	  $badge12Title = $badge12Rows['title'];
	  $badge12Desc = $badge12Rows['description'];
	  $badge12Icon = "badges/Active/".$badge12Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge12Winners = $badge12Winners."+$i others";
	}
} //end badge12

//badge13
$badge13Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 15
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge13_num_winners = mysql_num_rows($badge13Result);
$i=0;
if($badge13_num_winners != 0) {
	$badge13Winners = "";
	while($badge13Rows = mysql_fetch_assoc($badge13Result)){
	  if($i<3)
	    $badge13Winners = $badge13Winners.$badge13Rows['username']."<br>";
	  $badge13Title = $badge13Rows['title'];
	  $badge13Desc = $badge13Rows['description'];
	  $badge13Icon = "badges/Active/".$badge13Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge13Winners = $badge13Winners."+$i others";
	}
} //end badge13

//badge14
$badge14Result = mysql_query("
	SELECT  u.username as username,
		b.badgeimg as active,
	        b.badgeimg_inactive as inactive,
	        b.badgedescription as description,
	        b.badgetitle as title
	FROM badges b,
	     badgesawarded ba,
	     users u
	WHERE u.id= ba.userid
	  AND ba.badgeid = b.badgeid
	  AND b.badgeid = 8
	  AND u.username != 'orphan'")
 or die(mysql_error());

$badge14_num_winners = mysql_num_rows($badge14Result);
$i=0;
if($badge14_num_winners != 0) {
	$badge14Winners = "";
	while($badge14Rows = mysql_fetch_assoc($badge14Result)){
	  if($i<3)
	    $badge14Winners = $badge14Winners.$badge14Rows['username']."<br>";
	  $badge14Title = $badge14Rows['title'];
	  $badge14Desc = $badge14Rows['description'];
	  $badge14Icon = "badges/Active/".$badge14Rows['active'];
	  $i++;
	}
	if($i > 3){
	  $i = $i -3;
	  $badge14Winners = $badge14Winners."+$i others";
	}
} //end badge14

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="940.css" />
<title>Das Bot</title>

<!-- AUTO REFRESH SCRIPT -->

<script type="text/JavaScript">
<!--
function timedRefresh(timeoutPeriod) {
	setTimeout("location.reload(true);",timeoutPeriod);
}
//   -->
</script>

<!-- JustGage Scripts -->
<script src="justGage/resources/js/raphael.2.1.0.min.js"></script>
<script src="justGage/resources/js/justgage.1.0.1.min.js"></script>
<!-- end JustGage -->





<!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
    <script type="text/javascript">

      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawChart);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawChart() {

      // Create the data table.
      var data = new google.visualization.DataTable();
      data.addColumn('string', 'User');
      data.addColumn('number', 'Liters');
      data.addRows([
        <?php
        //LOOP THROUGH LEADER
	      $i=1;
	      while($rowLeaders = mysql_fetch_array($leadersResult)){
	        $user = $rowLeaders['username'];
	        $totalConsumed = $rowLeaders['total'];
	        $totalConsumedLiters = round($totalConsumed / $refTicksPerLiter, 1);

	        echo "['$user', $totalConsumedLiters],";
	        $i++;
               }
        ?>

      ]);

      // Set chart options
      var options = {'width':600,
                     'height':200,
                     'is3D':true};

      // Instantiate and draw our chart, passing in some options.
      var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
      chart.draw(data, options);
    }
    </script>





</head>
<body  onload="JavaScript:timedRefresh(30000);">

<div id="wrap">


<div id="header">
<h1><a href="index.php">Fest 2012 - Das Bot 2.0</a></h1>
</div>


<div id="menu">
<ul>
<li><a href="dash.php">Dashboard</a></li>
<li><a href="index.php">Register</a></li>
</ul>
</div>


<div id="content">

  <h2>Leaderboard</h2>



  <!--Div that will hold the pie chart-->

  <div id="chart_div"></div>


  <h2>Badges Achieved</h2>

  <br/>
  <table border='0' width='100%'>
      <tr valign="top">
        <td>
          <div id="noti_Container">
            <img src="<?php echo $badge1Icon; ?>" alt="<?php echo $badge1Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge1_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge1Winners; ?></div>
	</td>
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge2Icon; ?>" alt="<?php echo $badge2Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge2_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge2Winners; ?></div>
	</td>
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge3Icon; ?>" alt="<?php echo $badge3Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge3_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge3Winners; ?></div>
	</td>
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge4Icon; ?>" alt="<?php echo $badge4Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge4_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge4Winners; ?></div>
	</td>
        <td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge5Icon; ?>" alt="<?php echo $badge5Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge5_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge5Winners; ?></div>
	</td>
	<td>
          <div id="noti_Container">
            <img src="<?php echo $badge6Icon; ?>" alt="<?php echo $badge6Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge6_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge6Winners; ?></div>
	</td>
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge7Icon; ?>" alt="<?php echo $badge7Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge7_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge7Winners; ?></div>
	</td>
      </tr>
      <tr valign="top">
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge8Icon; ?>" alt="<?php echo $badge8Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge8_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge8Winners; ?></div>
	</td>
	<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge9Icon; ?>" alt="<?php echo $badge9Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge9_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge9Winners; ?></div>
	</td>
		<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge10Icon; ?>" alt="<?php echo $badge10Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge10_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge10Winners; ?></div>
	</td>
		<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge11Icon; ?>" alt="<?php echo $badge11Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge11_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge11Winners; ?></div>
	</td>
		<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge12Icon; ?>" alt="<?php echo $badge12Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge12_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge12Winners; ?></div>
	</td>
		<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge13Icon; ?>" alt="<?php echo $badge13Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge13_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge13Winners; ?></div>
	</td>
		<td>
	  <div id="noti_Container">
	    <img src="<?php echo $badge14Icon; ?>" alt="<?php echo $badge14Title; ?>" />
	    <div class="noti_bubble"><?php echo $badge14_num_winners; ?></div>
	  </div>
	  <div class='winnerlist'><?php echo $badge14Winners; ?></div>
	</td>
      </tr>
  </table>




</div>

<div id="sidebar">
<h3>Beer Consumed </h3>



<center>
<div class="guage_box">
  <div id="keg1" style="width:200px; height:160px"></div>
</div>
<div class="guage_box">
  <div id="keg2" style="width:200px; height:160px"></div>
</div>
<div class="guage_box">
  <div id="keg3" style="width:200px; height:160px"></div>
</div>
</center>


<!--
<li><a href="#">Proin at</a></li>
<li><a href="#">Class aptent taciti</a></li>
-->



</div>

<div style="clear: both;"> </div>

<div id="columns">


<div id="column1">
<h3>Fun Facts</h3>
<ul>
<li><b><?php echo $largestVesselUser; ?></b> has the largest drinking vessel.</li>
<li><b><?php echo $mostTripsUser; ?></b> has been to the keg <b><?php echo $mostTripsCount; ?></b> times.</li>
<li><b><?php echo $leastDrankUser; ?></b> has been to the keg <b><?php echo $leastDrankCount; ?></b> times, but only drank <b><?php echo $leastDrankVolume; ?>L</b>.</li>
</ul>
</div>

<div id="column2_double">
<h3>Recent Pours</h3>
  <ul><?php echo $recentDrinksFeed; ?></ul>
</div>
<!--
<div id="column3">
<h3>Friends</h3>
<ul>
<li><a href="http://www.oldwisdom.info">Old Wisdom</a></li>
<li><a href="http://www.supplies4pets.info">Supplies for Pets</a></li>
<li><a href="http://www.viennasights.info">Vienna Sightseeing</a></li>
<li><a href="http://www.barcelonasightseeing.info">Barcelona, Spain</a></li>
<li><a href="http://www.amsterdamsightseeing.info">Amsterdam</a></li>
<li><a href="http://www.francesightseeing.info">French Cities</a></li>
</ul>

</div>
-->

<div style="clear: both;"> </div>

</div>


<div id="footer">
<p>&copy; 2012 Tinkurlab</p>
</div>

</div>

</body>
</html>



<!-- Gauges -->

<!-- keg1 gauge -->
	<script>
	  var keg1 = new JustGage({
		id: "keg1",
		value: <?php echo $keg1Consumed; ?>,
		min: 0,
		max: 100,
		title: "Hefeweizen",
		titleFontColor: "#000000",
		showMinMax: false,
		label: "% Consumed",
		levelColorsGradient: true,
		levelColors: ["#027d25", "#e6d925", "#c41010"]
	  });


	</script>

<!-- keg2 gauge -->
	<script>
	  var keg2 = new JustGage({
		id: "keg2",
		value: <?php echo $keg2Consumed; ?>,
		min: 0,
		max: 100,
		title: "Hofbr\344uhaus",
		titleFontColor: "#000000",
		showMinMax: false,
		label: "% Consumed",
		levelColorsGradient: true,
		levelColors: ["#027d25", "#e6d925", "#c41010"]
	  });
	</script>

<!-- keg3 gauge -->
	<script>
	  var keg3 = new JustGage({
		id: "keg3",
		value: <?php echo $keg3Consumed; ?>,
		min: 0,
		max: 100,
		title: "Dunkelweizen",
		titleFontColor: "#000000",
		showMinMax: false,
		label: "% Consumed",
		levelColorsGradient: true,
		levelColors: ["#027d25", "#e6d925", "#c41010"]
	  });
	</script>