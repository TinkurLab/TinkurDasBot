
<?php

require('db.inc.php');

//check for rfid in url. If present, set variable
if(isset($_GET['rfid'])) {
  $rfid = $_GET['rfid'];
}

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

//IF ID IS SET, SHOW THE FORM
if (isset($_GET['id']) && ($_GET['action'] == 'new')) {

  echo "<h2>Registration</h2>";
  echo "<p>Please enter your name</p>";
  
  ?>
  
  <p>
  <form action='register.php' METHOD='GET'>
  
  Name: 
    <input type=text name='name' length=20 maxlength=30 />
    <input name="id" type="hidden" value="<?php echo $_GET['id'] ?>" />
    <input name="action" type="hidden" value="assign" />
  
  
  </form>
  
<?php 
}

if (isset($_GET['name']) && ($_GET['action'] == 'assign') && isset($_GET['id'])) { 

$name = $_GET['name'];
$id = $_GET['id'];

mysqli_query($GLOBALS["cnx"], "UPDATE users SET username = '$name' WHERE id = $id");


?>
<h2>Thank you for registering!</h2>

<p>View the <a href="dash.php">Dashboard</a>!</p>
<?php

}

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