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
