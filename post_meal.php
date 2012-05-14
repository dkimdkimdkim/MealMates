<?php
if (!function_exists('json_decode')) {
  function json_decode($json) {
    $comment = false;
    $out     = '$x=';
    for ($i=0; $i<strlen($json); $i++) {
      if (!$comment) {
        if (($json[$i] == '{') || ($json[$i] == '[')) {
          $out .= 'array(';
        }
        elseif (($json[$i] == '}') || ($json[$i] == ']')) {
          $out .= ')';
        }
        elseif ($json[$i] == ':') {
          $out .= '=>';
        }
        elseif ($json[$i] == ',') {
          $out .= ',';
        }
        elseif ($json[$i] == '"') {
          $out .= '"';
        }
        /*elseif (!preg_match('/\s/', $json[$i])) {
         *             return null;
         *                       }*/
      }
      else $out .= $json[$i] == '$' ? '\$' : $json[$i];
      if ($json[$i] == '"' && $json[($i-1)] != '\\') $comment = !$comment;
    }
    eval($out. ';');
    return $x;
  }
}

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

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not insert meal parameters.');

$inviteearray = json_decode($_POST['invitees']);

echo $_POST['invitees'];
echo $inviteearray;

$inviteequery = 'INSERT INTO invitees (meal_id, invitee, rsvp) values';
//(SELECT max(meal_id) FROM meals;)

foreach ($inviteearray as &$invitee) {
  $inviteequery .= '(' . '(SELECT max(meal_id) FROM meals;), ' . '\'' . $invitee . '\', \'attending\'),';  
}

echo $inviteequery;

$inviteequery = substr($inviteequery, 0, strlen($inviteequery)-1) . ';';

mysql_query($inviteequery);
?>
