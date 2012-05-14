<?php
// Retrieve meals.
$querytemplate = 'INSERT INTO meals (creator, description, start_time, date, canceled, restaurant) values (\'%s\', \'%s\', \'%s\', \'%s\', %s, \'%s\')';
$queryreal = sprintf($querytemplate,
  'justin_merritt',
  $_POST['description'],
  $_POST['time'],
  $_POST['date'],
  '0',
  $_POST['place']
);

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not insert meal parameters.');

$inviteearray = unserialize($_POST['invitees']);

$jsonarray = array();
$inviteearray = array();

while($row = mysql_fetch_assoc($mealresult)) {
  $row = array_map("utf8_encode", $row);

  // Retrieve invitees.
  $querytemplate = 'SELECT * FROM invitees WHERE meal_id=' . $row['meal_id'] . ';';
  $queryreal = sprintf($querytemplate);

  $inviteeresult = mysql_query($queryreal) or die('Could not select invitees table');

  while($inviteerow = mysql_fetch_assoc($inviteeresult)) {
    $inviteearray[] = array($inviteerow['invitee'], $inviteerow['rsvp']);
  }

  $singlemeal = array('creator' => $row['creator'],
                      'restaurant' => $row['restaurant'],
                      'description' => $row['description'],
                      'start_time' => $row['start_time'],
                      'date' => $row['date'],
                      'canceled' => $row['canceled'],
                      'invitees' => $inviteearray);
  $jsonarray[] = $singlemeal;
  $inviteearray = array();
}
header("Content-Type: application/json");
echo json_encode($jsonarray);

?>
