<?php // MyPlugin - Core Functionality

// exit if file is called directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function url_for($script_path) {
  // add the leading '/' if not present
  if($script_path[0] != '/') {
    $script_path = "/" . $script_path;
  }
  return WWW_ROOT . $script_path;
}

function console_log( $data ){
  echo '<script>';
  echo 'console.log('. json_encode( $data ) .')';
  echo '</script>';
}

//Check the date submitted is in YYYY-MM-DD format (used currently in donation-form-process.php)
function checkDateFormat( $dateString ){
	list($y, $m, $d) = array_pad(explode('-', $dateString, 3), 3, 0);
	return ctype_digit("$y$m$d") && checkdate($m, $d, $y);
}

//Outpust the correct html when pulling donation outcome from the database
function check_donation_outcome( $input ){
  if ($input == "Success") {
    $outcome = "Successful: 2 Units";
  } elseif ($input == "SuccessOneUnit") {
    $outcome = "Successful: 1 Unit";
  } elseif ($input == "Failure") {
    $outcome = "Not Successful";
  } else {
    $outcome = "";
  }
  return $outcome;
}


///// These are for the Settings functions /////
function get_locations(){
  $originalString = get_option('drops_owner_locations');
  $locations = array_map('trim', explode(',', $originalString));
  $locations[] = "Not Applicable";
  return $locations;
}

//Outputs all options in the settings as options with appropriate markup
function option_locations( $placeholder, $current_location = NULL ){
  $locations = get_locations();
  if( ! empty ( $locations ) && is_array( $locations ) ){
    $options_markup = '';
    $options_markup .= '<option value = "" ' . ( is_null($current_location) ?: 'selected="selected"' ) . 'disabled hidden>' . $placeholder . '</option>';
    foreach( $locations as $location ){
      $options_markup .= sprintf( '<option value="%1$s" %2$s>%1$s</option>', $location, selected( $current_location, $location, false ) );
    }
    return $options_markup;
  }
}

//Wraps the options group with select tags of our choice
function select_locations( $options_markup, $select_id = NULL, $select_class = NULL ){
  $select_output = sprintf( '<select name="%1$s" id="%1$s" class="%2$s">%3$s</select>', $select_id, $select_class, $options_markup );
  return $select_output;
}
