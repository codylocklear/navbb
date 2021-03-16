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

//**** SANITIZE POST META WHEN WE FETCH AND DISPLAY IT ****//

//** Donation **//
//Donation outcome
function check_donation_outcome( $input ){
  if ($input == "Success") {
    $outcome = "Successful: 2 Units";
  } elseif ($input == "SuccessOneUnit") {
    $outcome = "Successful: 1 Unit";
  } elseif ($input == "Ineligible") {
    $outcome = "Ineligible";
  } elseif ($input == "Failure") {
    $outcome = "Not Successful";
  } else {
    $outcome = "";
  }
  return $outcome;
}

//** Donors **//
//Formats the date provided and converts into years
function get_donor_age( $date ) {
  return intval( date( 'Y', time( ) - strtotime( $date ) ) ) - 1970;
}

function get_full_donorID( $donor_id ){
  $internalDonorID = get_post_meta( $donor_id, '_navbb_donors_internalDonorID', true );
  $owner_id = get_post_meta( $donor_id, '_navbb_donors_owner_id', true );
  $internalOwnerID = ( get_post_meta( $owner_id, '_navbb_owners_internalOwnerID', true ) ?: "Not Set" );

  $fullDonorID = $internalOwnerID . "-" . $internalDonorID;
  return $fullDonorID;
}

function get_donor_status ( $status ){
  if( $status == "active" ){
    $output = "Active";
  } elseif ( $status == "pending" ) {
    $output = "Pending";
  } elseif ( $status == "retired" ) {
    $output = "Retired";
  } elseif ( $status == "not accepted" ){
    $output = "Not Accepted";
  } else {
    $output = "";
  }
  return $output;
}


//** Owners **//
function get_owner_fullname( $owner_id = "", $orderby = true ){
  $owner_fullname = "";
    if( !empty( $owner_id ) ) {
      $ownertype = get_post_meta( $owner_id, '_navbb_owners_ownertype', true );
      if( $ownertype == "Kennel Club" ){
        $owner_fullname = html_entity_decode( get_the_title( $owner_id ) );
      } else {
        if( $orderby == true ){
          $owner_fullname = html_entity_decode( get_the_title( $owner_id ) ) . " , ". ( get_post_meta( $owner_id, '_navbb_owners_first_name', true ) ?: "Not Set" );
        } else {
          $owner_fullname = ( get_post_meta( $owner_id, '_navbb_owners_first_name', true ) ?: "Not Set" ) . " " . html_entity_decode( get_the_title( $owner_id ) );
        }
      }
    }
  return $owner_fullname;
}

//Gets all location options from Settings
function get_locations(){
  $originalString = get_option('drops_owner_locations');
  $locations = array_map('trim', explode(',', $originalString));
  $locations[] = "Not Applicable";
  return $locations;
}

//Outputs all options in the settings as option values with appropriate markup
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
