<?php # UPDATE DATABASE COUNT
//This script checks for a vote sent, and performs the Database update if so
require_once ('includes/config.inc.php'); 
$page_title = 'Pollistics.com';
ob_start();
session_start();
require_once (MYSQL);

// THIS BLOCK UPDATES THE OPEN POLL QUESTION IF THE WEB USER HAS HIT THE SUBMIT BUTTON
if (isset($_POST['pollquestion'])) 
{
		$poll_id = $_POST['pollquestion'];

		$qUpdateOpenPoll = "update poll_open set poll_id = $poll_id where poll_open_id = 1";
		$rUpdateOpenPoll = mysqli_query($dbc, $qUpdateOpenPoll) or trigger_error("Query: $qUpdateOpenPoll\n<br />MySQL Error: " . mysqli_error($dbc));
		// echo "You selected to open question". $poll_id;
}

// THIS BLOCK GETS THE OPEN POLL QUESTION 
$qGetOpenPoll = "select poll_id from poll_open where  poll_open_id = 1";
$rGetOpenPoll = mysqli_query($dbc, $qGetOpenPoll) or trigger_error("Query: $qGetOpenPoll\n<br />MySQL Error: " . mysqli_error($dbc));
$resultrow = mysqli_fetch_array($rGetOpenPoll);
// echo "Poll Open : ". $resultrow['poll_id'];
$poll_id = $resultrow['poll_id'];





// THIS BLOCK UPDATES DATABASE COUNTS IF THE URL CONTAINS DATA
$x = FALSE;

if (isset($_GET['x'])) {   // then we were passed data.
	// Update the database...
	$x = $_GET['x'];
	$error = false;
	if 		($x=='a') { $update_column = 'count_a';}
	else if ($x=='b') { $update_column = 'count_b' ;}
	else if ($x=='c') { $update_column = 'count_c'; }
	else if ($x=='d') { $update_column = 'count_d' ;}
	else { $error = true; }
	if (!$error) {
			$q = "UPDATE poll_data SET $update_column = $update_column+1 WHERE poll_id=(select poll_id from poll_open where poll_open_id = 1)";
			//echo $q;				
			$r = mysqli_query ($dbc, $q) or trigger_error("Query: $q\n<br />MySQL Error: " . mysqli_error($dbc));
	}
	if (mysqli_affected_rows($dbc) == 1) {
		//echo "<h3>Row Update Success</h3>";
	} else {
		//echo '<p class="error">No Rows Updated</font></p>'; 
	}
}
?>




<!doctype html>
<html>
<head>
<meta charset="UTF-8">
<?PHP #THIS BLOCK UPDATES THE GRAPHICAL CHART BY PERFORMING A SELECT QUERY ON THE DB
// This script retrieves the a row of poll data for use in the chart javascript function
			$qcount1 = "SELECT count_a, count_b, count_c, count_d, 
							text_a, text_b, text_c, text_d, question_text from poll_data WHERE poll_id=$poll_id";
			$result1 = mysqli_query ($dbc, $qcount1)
					 or trigger_error("Query: $qcount1\n<br />MySQL Error: " . mysqli_error($dbc));
			$row = mysqli_fetch_array($result1);
?>

<!--UPDATE CHART GOOGLE API -->
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
        data.addColumn('string', 'Answers');
        data.addColumn('number', 'Count');
        data.addRows([
          ["<?PHP echo $row['text_a'];?>", <?PHP echo $row['count_a'];?>],
          ["<?PHP echo $row['text_b'];?>", <?PHP echo $row['count_b'];?>],
          ["<?PHP echo $row['text_c'];?>", <?PHP echo $row['count_c'];?>],
          ["<?PHP echo $row['text_d'];?>", <?PHP echo $row['count_d'];?>],
        ]);

        // Set chart options
        var options = {'title':"Result:  <?PHP echo $row['question_text'].'?' ?> ",
                       'width':400,
                       'height':330};

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.ColumnChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>

<title>POLLISTICS.com</title>
<link href="css/pollisticsBen2.css" rel="stylesheet" type="text/css">
</head>

<body>
<div id="wrapper_div">
<div id="header_div">

</div><!-- header_div end -->

<div id="nav_div">

</div><!-- nav_div end -->

<img src="graphics/Polistics-Logo-alt.png" width="568" height="227" alt="Pollistics logo" />
<h3>Welcome</h3>
<p>Pollistics allows anyone to conduct an audience poll, using a simple website, and free app for mobile phones. </p>
<p>This is the future of audience polling. Available for Android and iPhone soon. <br>
</p>
<hr>

<!-- THIS IS THE FORM TO CHOOSE THE QUESTION -->
<form action="index.php" method="post" id="chooser">
  <label for="selected">Select Question Number:</label>
  <select name="pollquestion" >
    <option <?PHP if ($poll_id == "1") echo "selected = 'selected'" ?> value="1">Poll Q1</option>
    <option <?PHP if ($poll_id == "2") echo "selected = 'selected'" ?> value="2">Poll Q2</option>
    <option <?PHP if ($poll_id == "3") echo "selected = 'selected'" ?> value="3">Poll Q3</option>
    <option <?PHP if ($poll_id == "4") echo "selected = 'selected'" ?> value="4">Poll Q4</option> 
    <option <?PHP if ($poll_id == "0") echo "selected = 'selected'" ?> value="0">None</option>
  </select>
<input type="submit" name="submit" value="AllowVotes"/>
</form>
</p>


<div id="data_div" class='container shadow radius'>
<?PHP #DISPLAY POLL RESULTS TABLE
			//echo "In Poll Results Table Section";
if (isset($_GET['a'])) 		
{
			$qcount = "SELECT count_a, count_b, count_c, count_d, 
							text_a, text_b, text_c, text_d, question_text from poll_data WHERE poll_id=$poll_id";
			$result = mysqli_query ($dbc, $qcount)
					 or trigger_error("Query: $qcount\n<br />MySQL Error: " . mysqli_error($dbc));
			
			$row = mysqli_fetch_array($result);
			echo "<strong>";
			echo "<h3>Question&#8212;&nbsp;"."<hr>"."<span id='question'>". $row['question_text'] . "?</span></h3>";
			echo "<p>A)&nbsp; "."<span id='text_a'>".$row['text_a']."</span><span id='count_a'>".$row['count_a']."</span></p>";
			echo "<p>B)&nbsp; "."<span id='text_b'>".$row['text_b']."</span><span id='count_b'>".$row['count_b']."</span></p>";
			echo "<p>C)&nbsp; "."<span id='text_c'>".$row['text_c']."</span><span id='count_c'>".$row['count_c']."</span></p>";
			echo "<p>D)&nbsp; "."<span id='text_d'>".$row['text_d']."</span><span id='count_d'>".$row['count_d']."</span></p>";
			echo "</strong>";
mysqli_close($dbc);
}
else
{
		$qcount = "SELECT count_a, count_b, count_c, count_d, 
							text_a, text_b, text_c, text_d, question_text from poll_data WHERE poll_id=$poll_id";
			$result = mysqli_query ($dbc, $qcount)
					 or trigger_error("Query: $qcount\n<br />MySQL Error: " . mysqli_error($dbc));
			
				$row = mysqli_fetch_array($result);
				echo "<strong>";
				echo "<h3>Question&#8212;&nbsp;"."<hr>". "<span id='question'>".$row['question_text'] . "?</span></h3>";
				echo "<p>A)&nbsp; "."<span id='text_a'>". $row['text_a']."</span>"."</p>";
				echo "<p>B)&nbsp; "."<span id='text_b'>". $row['text_b']."</span>"."</p>";
				echo "<p>C)&nbsp; "."<span id='text_c'>". $row['text_c']."</span>"."</p>";
				echo "<p>D)&nbsp; "."<span id='text_d'>". $row['text_d']."</span>"."</p>";
				echo "</strong>";
mysqli_close($dbc);

}

?>
</h4>
</div>


<div class='container shadow radius'>
<?PHP 
if (isset($_GET['a']))
{
	echo "<div id='chart_div'>";
	echo "</div>";	
} else
echo "<button onclick=\"location.href='index.php?a=a'\" type=\"button\" name=\"\" value=\"\" class=\"sidebarbutton buttonbottomleft\">Results</button>";
?>

</div><!-- container shadow radius -->
<div id='footer_div'>  
</div><!-- footer_div end --> 
</div><!-- wrapper_div end -->

</body>

</html>
