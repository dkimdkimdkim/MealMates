<?php
// Retrieve meals.
$querytemplate = 'INSERT INTO meals (creator, description, start_time, date, canceled, restaurant) values (\'%s\', \'%s\', \'%s\', \'%s\', %s, \'%s\');';
$queryreal = sprintf($querytemplate,
  "justin_merritt",
  $_POST["description"],
  $_POST["time"],
  date("Y-m-d", strtotime($_POST["date"])),
  "0",
  $_POST["place"]
);

echo $queryreal;
echo ' \n ';

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not insert meal parameters.');

$inviteearray = unserialize($_POST['invitees']);

$inviteequery = 'INSERT INTO invitees (meal_id, invitee, rsvp) values';
//(SELECT max(meal_id) FROM meals;)

foreach ($inviteearray as &$invitee) {
  $inviteequery .= '(' . '(SELECT max(meal_id) FROM meals;), ' . '\'' . $invitee . '\', \'attending\'),';  
}

echo $inviteequery;

$inviteequery = substr($inviteequery, 0, strlen($inviteequery)-1) . ';';

mysql_query($inviteequery);
?>
