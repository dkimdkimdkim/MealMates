<?php
if (!function_exists('json_encode')) {
  function json_encode($data) {
    switch ($type = gettype($data)) {
    case 'NULL':
      return 'null';
    case 'boolean':
      return ($data ? 'true' : 'false');
    case 'integer':
    case 'double':
    case 'float':
      return $data;
    case 'string':
      return '"' . addslashes($data) . '"';
    case 'object':
      $data = get_object_vars($data);
    case 'array':
      $output_index_count = 0;
      $output_indexed = array();
      $output_associative = array();
      foreach ($data as $key => $value) {
        $output_indexed[] = json_encode($value);
        $output_associative[] = json_encode($key) . ':' . json_encode($value);
        if ($output_index_count !== NULL && $output_index_count++ !== $key) {
          $output_index_count = NULL;
        }
      }
      if ($output_index_count !== NULL) {
        return '[' . implode(',', $output_indexed) . ']';
      } else {
        return '{' . implode(',', $output_associative) . '}';
      }
    default:
      return ''; // Not supported
    }
  }
}

function escape_str($str) {
  return strtr($str, array(
    "\0" => "",
    "'"  => "&#39;",
    "\"" => "&#34;",
    "\\" => "&#92;",
    // more secure
    "<"  => "&lt;",
    ">"  => "&gt;",
  ));
}

$maxposts = $_GET['n'];
if(!$maxposts OR $maxposts < 1) {
  $maxposts = 25;
}

// Retrieve meals.
$querytemplate = 'SELECT * FROM meals ORDER BY date, start_time;';
$queryreal = sprintf($querytemplate);

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not select meals table');



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
