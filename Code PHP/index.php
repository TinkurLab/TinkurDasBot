<?php
error_reporting (E_ALL ^ E_NOTICE);

require('db.php.inc');

//check for rfid in url. If present, set variable
if(isset($_GET['rfid'])) {
  $rfid = $_GET['rfid'];
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

?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="940.css" />
<title>Das Bot</title>



  <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {packages:['gauge']});
      google.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Label');
        data.addColumn('number', 'Value');
        data.addRows(3);
        data.setValue(0, 0, 'Beer (%)');
        data.setValue(0, 1, <?php echo $kegState; ?>);


        var chart = new google.visualization.Gauge(document.getElementById('gauge_div'));
        var options = {width: 200, height: 200, redFrom: 0, redTo: 10,
            yellowFrom:10, yellowTo: 25, minorTicks: 5};
        chart.draw(data, options);
      }
    </script>

	

</head>
<body>

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

<?php 

if(!isset($_GET['id']))
  $id="";
if(!isset($_GET['name']))
$name="";


//IF ID IS SET, SHOW THE FORM
if (isset($_GET['id']) && ($_GET['action'] == 'new')) {

  $id = $_GET['id'];
  echo "<h2>Registration</h2>";
  echo "<p>Please enter your name</p>";
  
  ?>
  
  <p>
  <form action='register.php' METHOD='GET'>
  
  Name: 
    <input type=text name='name' length=20 maxlength=30 />
    <input type=hidden name='id' value='<?php echo $id; ?>' />
  
  </form>
  
<?php 
}

else if (isset($_GET['name']) && isset($_GET['id'])) { //form submitted, username provided

  $name = mysqli_real_escape_string($GLOBALS["cnx"], $_GET['name']);
  $id = mysqli_real_escape_string($GLOBALS["cnx"], $_GET['id']);
  
  //check to make sure the user really is an orphan
  $orphanVerifyResult = mysqli_query($GLOBALS["cnx"], "SELECT * FROM users WHERE username='orphan' AND id=$id")
    or die(mysqli_error($GLOBALS["cnx"])); 
  if(mysqli_num_rows($orphanVerifyResult) == 1) {
	  $updateQuery = "UPDATE  `dasbot`.`users` SET  `username` =  '$name' WHERE  `users`.`id` =$id";
	  mysqli_query($GLOBALS["cnx"], $updateQuery);
	  
	  echo "<h2>Registration</h2>";
	  echo "<p>Prost, $name! You are now registered!</p>";
  }
}



else { //show the list if ID not set

?>
<h2>Please Register Your Tag Here</h2>

<p>Select your chip ID from the list below (<b>the ID is printed on your receipt</b>). If your ID is not displayed below, please scan your chip at the kegerator</p>

<ul>

<?php

//GET LIST OF "ORPHANS" IN THE USER TABLE
$orphanResult = mysqli_query($GLOBALS["cnx"], "SELECT * FROM users WHERE username='orphan' LIMIT 200")
or die(mysqli_error($GLOBALS["cnx"])); 

//LOOP THROUGH EACH ORPHAN
while($orphanRows = mysqli_fetch_array($orphanResult)){

  $orphanUserID = $orphanRows['id'];
  
  echo "<li><a href='register.php?id=$orphanUserID&action=new'>$orphanUserID</a></li>";

}

?>

</ul>

<?php

} //end else statement



?>

</div>

<div id="sidebar">
<h3>Remaining Beer</h3>
<ul>


<div id="gauge_div"></div>

<!--
<li><a href="#">Proin at</a></li>
<li><a href="#">Class aptent taciti</a></li>
-->
</ul>


</div>

<div style="clear: both;"> </div>

<div id="columns">

</div>

<div id="footer">
<p>&copy; Copyright 2012 </p>
</div>

</div>

</body>
</html>
