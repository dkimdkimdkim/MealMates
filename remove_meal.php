<?php

$query = 'DELETE FROM meals WHERE meal_id='. $_POST['meal_id'] . ';';

echo $query;

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate');
mysql_select_db('dmwkim+mealmates');

$result = mysql_query($query);

?>
