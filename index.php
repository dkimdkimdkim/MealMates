<!DOCTYPE html> 
<?php
ini_set('display_errors', 'On');
error_reporting(E_ALL);
?>
<html>

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1"> 
    <title>MealMates!</title>

    <link rel="stylesheet" href="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.css" />
    <link rel="stylesheet" href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/base/jquery-ui.css" />
    <link rel="stylesheet" href="style.css" />

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="http://code.jquery.com/mobile/1.1.0/jquery.mobile-1.1.0.min.js"></script>
<script src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.10/jquery-ui.js"></script>
<script src="jquery.ui.touch-punch.min.js"></script>
<script src="jquery-ui-timepicker-addon.js"></script>
<script src="https://raw.github.com/briangonzalez/pep.jquery.js/master/js/libs/jquery.pep.js"></script>
<script>
var mealDate;
var mealTime;
var mealPlace;
var mealInvitees = {};
var mealDescription;
function setDate() {
  mealDate = $('#calendar-input').val();
}
function setTime() {
  mealTime = $('#from-time-input').val();
}
function setPlace(place) {
  mealPlace = place;
}
function addInvitee(invitee) {
  mealInvitees[invitee] = true;
}
function removeInvitee(invitee) {
  if(mealInvitees[invitee] != null){
    mealInvitees[invitee] = false;
  }
}
function setDescription() {
  mealDescription = $('#description-textarea').val();
}

function displayTime() {
  if(mealDate == null){
    return "Go to the When page to set a Date for the Meal!";
  }
  else if(mealTime == null){
    return "Go to the When page to set a Time for the Meal!";
  }
  else{
    return "at " + mealTime + "on " mealDate;
  }
}

function displayLocation() {
  if(mealPlace == null){
    return "Go to the Where page to set a Location for the Meal!";
  }
}

function displayInvitees() {
  if(mealInvitees == null){
    return "Go to the Who page to invite people to the Meal!";
  }
  else{
    inviteeString = "";
    for(invitee in mealInvitees){
      if(mealInvitees[invitee] == true){
        inviteeString.concat("<img src='images/" + invitee + ".jpg' alt='" + invitee + "' width='50px' height='50px'/>");
      }	
    }
  }
}
function postData() {
  $.post('post_meal.php', {
    date: mealDate,
      time: mealTime,
      place: mealPlace,
      invitees: JSON.stringify(mealInvitees),
      description: mealDescription
  });
}

$(function() {

  /**
   * Listener for invitee buttons.
   */
  $('.invitee-button').click(function() {
    $(this).toggleClass('selected');
    if($(this).hasClass('selected')){
      addInvitee($(this).val());	
    }
    else{
      removeInvitee($(this).val());
    }
  });

  $('.invitee-button').click(function() {
    if(!$(this).hasClass('selected')) {
      console.log('on');
    }
    else {
      console.log('off');
    }
  });

  $('.location-button').click(function() {
    if(!$(this).hasClass('ui-state-disabled')){
      $(this).toggleClass('selected');
      if($(this).hasClass('selected')){
        setPlace($(this).val());
      }
      else{
        setPlace(null);
      }
      $('.location-button').toggleClass('deactivated');
      $(this).toggleClass('ui-state-disabled');
    }
  });



  /**
   * Load welcome screen from PHP backend.
   */
        /*
          $.get('retrieve_meals.php', function(data) {
          console.log(data);
          $.each(data, function(index, entry) {
            console.log(entry);
            var dayContainer = $('<div class="day-container"></div>');
            dayContainer.append($('<h3>' + entry['date']+ '</h3>'))
            var mealContainer = $('<div class="meal-container ui-grid-a"></div>');
            var restaurantButtonContainer = $('<div class="restaurant-button-container ui-block-a"></div>');
            restaurantButtonContainer.append($('<a class="restaurant-button" href="#FamilyDinner" data-role="button" data-inline="true">' + entry['restaurant'] + '</a>'));
            mealContainer.append(restaurantButtonContainer);
            dayContainer.append(mealContainer);
            $('#welcome-content').append(dayContainer);
          });
      });
         */
        /*
        <div class="day-container">
          <h3>Wednesday, May 16</h3>
          <div class="meal-container ui-grid-a">
            <div class="restaurant-button-container ui-block-a">
              <a class="restaurant-button" href="#FamilyDinner" data-role="button" data-inline="true">Cafeteria</a>
            </div>
            <span class="time-window ui-block-b">at <strong>7:00pm</strong></span>
          </div>  
        </div>
         */

  $('#calendar-input').datepicker();
  $('#from-time-input').timepicker({});
  $('#to-time-input').timepicker({});
  $('.am-pm-toggle').click(function() {
    if($(this).text() == 'AM') {
      $(this).html('PM');
    }
    else {
      $(this).html('AM');
    }
  });
  $('#confirm-meal-button').click(function() {
    alert('Success! Everyone you invited will see your meal invitation on your homepage.');
  });
  $('#cancel-meal').click(function() {
    alert('Your meal has been cancelled.  Everyone you invited will be notified of the cancellation');
  });
});
    </script>
  </head> 


  <body> 

    <!-- Start of first page: #one -->
    <div data-role="page" id="welcome">

      <div data-role="header">
        <h1>Hi, Justin!</h1>
      </div><!-- /header -->

      <div id="welcome-content" data-role="content" >        
        <h2>Upcoming Meals</h2>
<?php
// Retrieve meals.
$querytemplate = 'SELECT * FROM meals ORDER BY date, start_time;';
$queryreal = sprintf($querytemplate);

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not select meals table');

while($row = mysql_fetch_assoc($mealresult)) {
?>
        <div class="day-container">
          <h3><?php echo date("m/d/Y", strtotime($row['date']));?></h3>
          <div class="meal-containeri ui-grid-a">
            <div class="restaurant-button-container ui-block-a">
              <a class="restaurant-button" href=

              "#<?php

  $restaurantquery = 'select restaurant_name from restaurant_id_mappings where restaurant_id=\'' . $row['restaurant'] . '\';';
  $restaurantmappingresult = mysql_query($restaurantquery);
  $restaurantrow = mysql_fetch_assoc($restaurantmappingresult);
  //echo $restaurantrow['restaurant_name'];
  echo $row['restaurant'];
  //echo 'Confirm';

?>"

 data-theme="b" data-role="button" data-inline="true"><?php echo $restaurantrow['restaurant_name']; ?></a>
            </div>
            <span class="time-window ui-block-b">at <strong><?php echo date("g:i a", strtotime($row['start_time'])); ?></strong></span>
          </div>  
        </div>
<?php
}
?>
      </div><!-- /content -->
      <div data-role="footer" data-position="fixed">
        <a id="create-button" href="#When" data-theme="e" data-role="button">Create a New Meal</a>
        <h1>MealMates</h1>
      </div>
    </div><!-- /page one -->

    <div data-role="page" id="When">
      <div data-role="content">
        <div data-role="navbar" data-iconpos="top">
          <ul>
            <li>
            <a class="active-top-button" href="When" data-theme="" data-icon="" class="ui-btn-active">
              When
            </a>
            </li>
            <li>
            <a href="#Where" data-theme="" data-icon="">
              Where
            </a>
            </li>
            <li>
            <a href="#Who" data-theme="" data-icon="">
              Who
            </a>
            </li>
            <li>
            <a href="#Confirm" data-theme="" data-icon="">
              Confirm
            </a>
            </li>
          </ul>
        </div>
        <h2 id="select-time-header">
          Select a date and time that works for you:
        </h2>
        <h3 id="available-header">
          On which date should the meal take place?
        </h3>
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <input id="calendar-input" class="time-number" placeholder="Enter a date" value="" /> 
          </fieldset>
        </div>
        <h3 id="available-header">
          What time do you want the meal to be?
        </h3>
        <div data-role="fieldcontain">
          <fieldset data-role="controlgroup">
            <input id="from-time-input" class="time-number" placeholder="Enter a time" value="" />
          </fieldset>
        </div>
      </div>
  <div>
      <a data-role="button" data-transition="fade" href="#Where" onClick="setDate(); setTime();">
              Save and Continue
      </a>
  </div>
    </div>

    <div data-role="page" id="Where">
      <div data-role="content">
        <div data-role="navbar" data-iconpos="top">
          <ul>
            <li>
            <a href="#When"  data-theme="" data-icon="">
              When
            </a>
            </li>
            <li>
            <a class="active-top-button" href="#Where"  data-theme="" data-icon="" class="ui-btn-active">
              Where
            </a>
            </li>
            <li>
            <a href="#Who"  data-theme="" data-icon="">
              Who
            </a>
            </li>
            <li>
            <a href="#Confirm"  data-theme="" data-icon="">
              Confirm
            </a>
            </li>
          </ul>
        </div>
        <!-- <div class="ui-grid-a">
          <div class="ui-block-a">
            <div data-role="fieldcontain">
              <fieldset data-role="controlgroup">
                <input id="textinput11" placeholder="" value="" type="text" />
              </fieldset>
            </div>
          </div>
          <div class="ui-block-b">
            <a data-role="button" data-transition="fade" href="#page6" class="search">
              Search
            </a>
          </div>
  </div> -->
        <div class="ui-grid-a">
          <div class="ui-block-a">
            <div>
              <b>
                Places to be added
              </b>
            </div>
          </div>
    <div class="scrollgrid ui-btn-corner-all">
  <table>

       <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="aj_perez">
            <table>
              <tr><td><img src="images/aj_perez.jpg" alt="AJ Perez" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> AJ Perez </td></tr>
    </table>
    </a>
       </td>
       <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="akira_monri">
            <table>
              <tr><td><img src="images/akira_monri.jpg" alt="Akira Monri" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Akira Monri </td></tr>
    </table>
    </a>
       </td>
       <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="alex_wang">
            <table>
              <tr><td><img src="images/alex_wang.jpg" alt="Alex Wang" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Alex Wang </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="aviv_cukierman">
            <table>
              <tr><td><img src="images/aviv_cukierman.jpg" alt="Aviv Cukierman" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Aviv Cukierman </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="brian_bell">
            <table>
              <tr><td><img src="images/brian_bell.jpg" alt="Brian Bell" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Brian Bell </td></tr>
    </table>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="chris_haid">
            <table>
              <tr><td><img src="images/chris_haid.jpg" alt="Chris Haid" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Chris Haid </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="david_kim">
    <table>
              <tr><td><img src="images/david_kim.jpg" alt="David Kim" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> David Kim </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="feynman_liang">
            <table>
              <tr><td><img src="images/feynman_liang.jpg" alt="Feynman Liang" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Feynman Liang </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="jake_varley">
            <table>
              <tr><td><img src="images/jake_varley.jpg" alt="Jake Varley" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Jake Varley </td></tr>
    </table>
    </button>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="jimmy_pershken">
            <table>
              <tr><td><img src="images/jimmy_pershken.jpg" alt="Jimmy Pershken" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Jimmy P. </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="mark_zuckerberg">
            <table>
              <tr><td><img src="images/mark_zuckerberg.jpg" alt="Mark Zuckerberg" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Mark Z. </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="mercedes_oliva">
            <table>
              <tr><td><img src="images/mercedes_oliva.jpg" alt="Mercedes Oliva" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Mercedes Oliva </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="location-button" data-inline="true" data-mini="true" value="ron_rosenberg">
            <table class="pep-draggable">
              <tr><td><img src="images/ron_rosenberg.jpg" alt="Ron Rosenberg" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Ron R. </td></tr>
    </table>
    </a>
      </td>

  </table>
  </div>
          </div>
        </div>
        <div>
        <a data-role="button" data-transition="fade" href="#Who">
              Save and Continue
        </a>
    </div>
      </div>

    </div>

  </div>

  <div data-role="page" id="Who" class="drag-page">
    <div data-role="content">
      <div data-role="navbar" data-iconpos="top">
        <ul>
          <li>
          <a href="#When" data-theme="" data-icon="">
            When
          </a>
          </li>
          <li>
          <a href="#Where" data-theme="" data-icon="">
            Where
          </a>
          </li>
          <li>
          <a class="active-top-button" href="#Who" data-theme="" data-icon="" class="ui-btn-active">
            Who
          </a>
          </li>
          <li>
          <a href="#Confirm" data-theme="" data-icon="">
            Confirm
          </a>
          </li>
        </ul>
      </div>
     <!-- <div class="ui-grid-a">
        <div class="ui-block-a">
          <div data-role="fieldcontain">
            <fieldset data-role="controlgroup">
              <input id="textinput13" placeholder="" value="" type="text" />
            </fieldset>
          </div>
        </div>
        <div class="ui-block-b">
          <a data-role="button" data-transition="fade" href="#page6" class="search">
            Search
          </a>
        </div>
      </div> -->
      <div class="ui-grid-a">
        <div class="ui-block-a">
          <div>
            <b>
              Invite people!
            </b>
          </div>
        </div>
        </div>
  <div class="scrollgrid ui-btn-corner-all">
  <table>

       <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="AJ Perez">
            <table>
              <tr><td><img src="images/aj_perez.jpg" alt="AJ Perez" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> AJ Perez </td></tr>
    </table>
    </a>
       </td>
       <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Akira Monri">
            <table>
              <tr><td><img src="images/akira_monri.jpg" alt="Akira Monri" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Akira Monri </td></tr>
    </table>
    </a>
       </td>
       <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Alex Wang">
            <table>
              <tr><td><img src="images/alex_wang.jpg" alt="Alex Wang" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Alex Wang </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Aviv Cuckierman">
            <table>
              <tr><td><img src="images/aviv_cukierman.jpg" alt="Aviv Cukierman" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Aviv Cukierman </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Brian Bell">
            <table>
              <tr><td><img src="images/brian_bell.jpg" alt="Brian Bell" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Brian Bell </td></tr>
    </table>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Chris Haid">
            <table>
              <tr><td><img src="images/chris_haid.jpg" alt="Chris Haid" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Chris Haid </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="David Kim">
    <table>
              <tr><td><img src="images/david_kim.jpg" alt="David Kim" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> David Kim </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Feynman Liang">
            <table>
              <tr><td><img src="images/feynman_liang.jpg" alt="Feynman Liang" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Feynman Liang </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Jake Varley">
            <table>
              <tr><td><img src="images/jake_varley.jpg" alt="Jake Varley" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Jake Varley </td></tr>
    </table>
    </button>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Jimmy Pershken">
            <table>
              <tr><td><img src="images/jimmy_pershken.jpg" alt="Jimmy Pershken" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Jimmy P. </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Mark Zuckerberg">
            <table>
              <tr><td><img src="images/mark_zuckerberg.jpg" alt="Mark Zuckerberg" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Mark Z. </td></tr>
    </table>
    </a>
      </td>
      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Mercedes Oliva">
            <table>
              <tr><td><img src="images/mercedes_oliva.jpg" alt="Mercedes Oliva" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Mercedes Oliva </td></tr>
    </table>
    </a>
      </td>

      <td class="item">
    <a data-role="button" class="invitee-button" data-inline="true" data-mini="true" value="Ron Rosenberg">
            <table class="pep-draggable">
              <tr><td><img src="images/ron_rosenberg.jpg" alt="Ron Rosenberg" height="50px" width="50px"></img></td></tr>
              <tr><td class="draggableText"> Ron R. </td></tr>
    </table>
    </a>
      </td>

  </table>
  </div>
        </div>
        <div>
      <a data-role="button" data-transition="fade" href="#Confirm">
              Save and Continue
      </a>
  </div>
      </div>
    </div>

  </div>

  <div data-role="page" id="Confirm">
    <div data-role="content">
      <div data-role="navbar" data-iconpos="top">
        <ul>
          <li>
          <a href="#When" data-theme="" data-icon="">
            When
          </a>
          </li>
          <li>
          <a href="#Where" data-theme="" data-icon="">
            Where
          </a>
          </li>
          <li>
          <a href="#Who" data-theme="" data-icon="">
            Who
          </a>
          </li>
          <li>
          <a class="active-top-button" href="#Confirm" data-theme="" data-icon="" class="ui-btn-active">
            Confirm
          </a>
          </li>
        </ul>
      </div>

      <div class="ui-grid-a">
        <table align="center" width="100%">
          <tr>
            <td>

              <div class="ui-block-b">
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="ui-block-b">
                <h2>
                  Time:
                </h2>
                <div>
                  <b value="displayTime();"></b>
                </div>
              </div>
            </td>
            <td>
              <div class="ui-block-a">
                <a id="change" data-role="button" data-transition="fade" href="#When">
                  Change
                </a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="ui-block-b">
                <h2>
                  Location:
                </h2>
                <div>
                  <b value="displayLocation();"></b>
                </div>
              </div>
            </td>
            <td>
              <div class="ui-block-a">
                <a id="change" data-role="button" data-transition="fade" href="#Where">
                  Change
                </a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="ui-block-b">
                <h2>
                  Invitees:
                </h2>
                <div value="displayInvitees();"></div>
              </div>
            </td>
            <td>
              <div class="ui-block-a">
                <a id="change" data-role="button" data-transition="fade" href="#Who">
                  Change
                </a>
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <div class="ui-block-b">
                <h2>
                  Description:
                </h2>
                <div>
                  <div data-role="fieldcontain">
                    <fieldset data-role="controlgroup">
                      <textarea id="description-textarea" rows="2" cols="6" placeholder="Optional:  Give your meal a description so others know what's going on!" value="" type="text"></textarea>
                    </fieldset>
                  </div>
                </div>
              </div>
            </td>
            <td>
            </td>
          </tr>
        </table>
      </div>
      <div class="ui-grid-a">
        <div class="ui-block-a">
          <a data-role="button" data-transition="fade" href="#welcome">
            Start Over
          </a>
        </div>
        <div class="ui-block-b">
          <a id="confirm-meal-button" data-role="button" data-transition="fade" href="#welcome" onClick="setDescription();">
            Confirm Meal
          </a>
        </div>
      </div>
    </div>
  </div>

<?php
// dynamically generate pages for each meal
$querytemplate = 'SELECT * FROM meals ORDER BY date, start_time;';
$queryreal = sprintf($querytemplate);

$link = mysql_connect('sql.mit.edu', 'dmwkim', '97baystate')
  or die('Could not connect ' . mysql_error());
mysql_select_db('dmwkim+mealmates') or die('Could not select database');

$mealresult = mysql_query($queryreal) or die('Could not select meals table');

while($row = mysql_fetch_assoc($mealresult)) {
?>
  <div data-role="page" id="<?php echo $row['restaurant']; ?>">
  <div data-role="content">
  <div data-role="navbar" data-iconpos="top">
  <ul>
  <li>
  <a href="#welcome" data-theme="" data-icon="">
  Home
  </a>
  </li>
  <li>
  <a class="active-top-button" href="<?php echo $row['restaurant']; ?>" data-theme="" data-icon="" class="ui-btn-active">
<?php
  //echo $row['restaurant'];
  $restaurantquery = 'select restaurant_name from restaurant_id_mappings where restaurant_id=\'' . $row['restaurant'] . '\';';
  $restaurantmappingresult = mysql_query($restaurantquery);
  $restaurantrow = mysql_fetch_assoc($restaurantmappingresult);
  echo $restaurantrow['restaurant_name'];
?>
  </a>
    </li>
    </ul>
    </div>
    <h1>
    <?php echo $restaurantrow['restaurant_name']; ?>
  </h1>
    <h2>
    Time
    </h2>
    <div>
    <b>
<?php
  echo date("g:i a", strtotime($row['start_time']));
  echo ' on ';
  echo date("m/d/Y", strtotime($row['date']));
?>

          <br />
        </b>
      </div>
      <h2>
        Location
      </h2>
      <div>
        <b>
          <?php echo $restaurantrow['restaurant_name']; ?>
        </b>
      </div>
      <h2>
        Invitees
      </h2>
      <img src="images/david_kim.jpg" alt="image" width="50px" height="50px"/>
    </div>
  </div>
<?php
}
?>
<!--
  <div data-role="page" id="page9">
    <div data-role="content">
      <div data-role="navbar" data-iconpos="top">
        <ul>
          <li>
          <a href="#welcome" data-theme="" data-icon="">
            Home
          </a>
          </li>
          <li>
          <a class="active-top-button" href="#FamilyDinner" data-theme="" data-icon="" class="ui-btn-active">
            Family Dinner
          </a>
          </li>
        </ul>
      </div>
      <h1 class="title">
        Family Dinner
      </h1>

      <table>
        <tr>
          <td>
            <h2 class="content1">
              Time
            </h2>
          </td>
          <td>
            <div class="content1">
              <b>
                7:00 p.m. Tomorrow
                <br />
              </b>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <h2>
              Location
            </h2>
          </td>
          <td>
            <div>
              <b>
                &nbsp; &nbsp; &nbsp;Oishii
              </b>
            </div>
          </td>
        </tr>
        <tr>
          <td>
            <h2>
              Invitees
            </h2>
          </td>
          <td>
            <img src="images/david_kim.jpg" alt="image" width="50px" height="50px" />
          </td>
        </tr>
        <tr>
          <div data-role="fieldcontain">
            <fieldset data-role="controlgroup" data-type="vertical">
              <td>
                <legend>
                  <h2>RSVP:</h2>
                </legend>
              </td>
              <td>
                <input name="radiobuttons1" id="radio1" value="attending" type="radio" />
                <label for="radio1">
                  Attending
                </label>
                <input name="radiobuttons1" id="radio2" value="maybe" type="radio" />
                <label for="radio2">
                  Maybe attending
                </label>
                <input name="radiobuttons1" id="radio3" value="not" type="radio" />
                <label for="radio3">
                  Can't go
                </label>
              </fieldset>
            </td>
          </div>
        </tr>
        <tr>
          <td>
            <a id="cancel-meal" data-role="button" data-transition="fade" href="#pageend2">
              Cancel Meal
            </a>
          </td>
        </tr>
      </table>
    </div>
  </div>  
-->
</body>
</html>
