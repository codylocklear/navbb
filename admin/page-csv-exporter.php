<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_enqueue_scripts', 'navbb_csv_exporter_enqueue' );
function navbb_csv_exporter_enqueue( $pagehook ) {

	if ( 'navbb_page_csv_exporter' != $pagehook ) {
		return;
	}

	wp_enqueue_script( 'navbb_csv_exporter', plugins_url('../admin/js/page-csv-exporter.js',__FILE__ ), array(), rand(111,9999), false );
	wp_localize_script( 'navbb_csv_exporter', 'navbb_csv_exporter_WPURLS', array('adminUrl' => admin_url() ));  //Provides adminUrl as a local variable in our JS script
}


function navbb_display_csv_exporter_page() {
	// check if user is allowed access
	if ( ! current_user_can( 'manage_options' ) ) return;

	?>
	<div class="wrap">
		<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
		<h2>This is currently our CSV Exporter </h2>
	</div>
<br>
	<div>
		<h3>Export All Owners</h3>
		<input id="navbb_owners_to_csv" name="navbb_owners_to_csv" type="submit" class="button-secondary" value="<?php _e( 'Owners to CSV', 'navbb_owners_to_csv' ) ?>" />
	</div>

	<div>
		<h3>Export All Donors</h3>
		<input id="navbb_donors_to_csv" name="navbb_donors_to_csv" type="submit" class="button-secondary" value="<?php _e( 'Donors to CSV', 'navbb_donors_to_csv' ) ?>" />
	</div>

	<div>
		<h3>Export All Donations</h3>
		<input id="navbb_donations_to_csv" name="navbb_donations_to_csv" type="submit" class="button-secondary" value="<?php _e( 'Donations to CSV', 'wp_csv_to_db' ) ?>" />
	</div>

	<?php

}


//This is used in page-csv-exporter.php to export a csv of owners
add_action( 'wp_ajax_handle_owners_to_csv_action', 'handle_owners_to_csv_action' );
function handle_owners_to_csv_action() {

	if ( ! current_user_can( 'manage_options' ) ) {
    echo 0;
		wp_die();
	}

  ob_end_clean();
  global $wpdb;

	$args = array( 'numberposts' => -1, 'post_type' => 'navbb_owners', 'post_status' => 'publish' );
  $owners = get_posts( $args );
  $ownerObjects = array();

  //Loop through each donor to retrieve various information
  foreach($owners as $owner){
    //Retrieve all information about the owner stored in metadata
    $owner_name = html_entity_decode(get_the_title( $owner->ID ));
    $ownertype = get_post_meta( $owner->ID, '_navbb_owners_ownertype', true );
    $internalOwnerID = get_post_meta( $owner->ID, '_navbb_owners_internalOwnerID', true );
    $first_name = get_post_meta( $owner->ID, '_navbb_owners_first_name', true );
    $email = get_post_meta( $owner->ID, '_navbb_owners_email', true );
    $phone_number = get_post_meta( $owner->ID, '_navbb_owners_phone_number', true );
    $donation_location = get_post_meta( $owner->ID, '_navbb_owners_donation_location', true);
    $address_1 = get_post_meta( $owner->ID, '_navbb_owners_address_1', true );
    $address_2 = get_post_meta( $owner->ID, '_navbb_owners_address_2', true );
    $city = get_post_meta( $owner->ID, '_navbb_owners_city', true );
    $state = get_post_meta( $owner->ID, '_navbb_owners_state', true );
    $postcode = get_post_meta( $owner->ID, '_navbb_owners_postcode', true );
    $notes = get_post_meta( $owner->ID, '_navbb_owners_notes', true );

    if ($ownertype == "Kennel Club"){
      $owner_fullname = $owner_name;
    } else {
      $owner_fullname = $owner_name . " , ". $first_name;
    };

    $ownerObjects[] = [
      'database_Owner_ID'=> $owner->ID,
      'owner_name'=> $owner_fullname,
      'internalOwnerID' => $internalOwnerID,
      'ownertype' => $ownertype,
      'email' => $email,
      'phone_number' => $phone_number,
      'donation_location'=> $donation_location,
      'address_1' => $address_1,
      'address_2' => $address_2,
      'city' => $city,
      'state' => $state,
      'postcode' => $postcode,
      'notes' => $notes ];
  }

  $sub = "Database Owner ID,Owner Name,Internal Owner ID,Owner Type,Email,Phone Number,Donation Location,Address 1,Address 2,City,State,Zipcode,Notes";
  $fields		 = $sub . "\n"; // Get fields names

  $csv_file_name	 = 'All_Owners_' . date( 'Ymd_His' ) . '.csv';

  foreach($ownerObjects as $owner){

    foreach ( $owner as $data ) {
      $value	 = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $data ); // Replace new line with tab
      //$value	 = str_replace( array( "\\\"" ), "'", $data ); // Replace quotationmark with tab
      $value	 = str_getcsv( $value, ",", "\"", "\\" ); // SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
      $fields	 .= $value[ 0 ] . ','; // Separate fields with comma
    }

    $fields	 = substr_replace( $fields, '', -1 ); // Remove extra space at end of string
    $fields	 .= "\n"; // Force new line if loop complete
  }

  header( "Content-type: text/csv" );
  header( "Content-Transfer-Encoding: binary" );
  header( "Content-Disposition: attachment; filename=" . $csv_file_name );
  header( "Content-type: application/x-msdownload" );
  header( "Pragma: no-cache" );
  header( "Expires: 0" );

  wp_send_json($fields);
  wp_die();
}


//This is used in page-csv-exporter.php to export a csv of donors
add_action( 'wp_ajax_handle_donors_to_csv_action', 'handle_donors_to_csv_action' );
function handle_donors_to_csv_action() {

	if ( ! current_user_can( 'manage_options' ) ) {
    echo 0;
		wp_die();
	}

  ob_end_clean();
  global $wpdb;
  $wp_prefix = $wpdb->prefix;

	$args = array( 'numberposts' => -1, 'post_type' => 'navbb_donors', 'post_status' => 'publish' );
  $donors = get_posts( $args );
  $donorObjects = array();

  //Loop through each donor to retrieve various information
  foreach($donors as $donor){
    //Retrieve all information about the donor stored in metadata
    $donor_name = html_entity_decode(get_the_title( $donor->ID ));
    $owner_id = get_post_meta( $donor->ID, '_navbb_donors_owner_id', true );
    $internalDonorID = get_post_meta( $donor->ID, '_navbb_donors_internalDonorID', true );
    $microchip = get_post_meta( $donor->ID, '_navbb_donors_microchip', true );
    $specie = get_post_meta( $donor->ID, '_navbb_donors_specie', true );
    $gender = get_post_meta( $donor->ID, '_navbb_donors_gender', true );
    $reproduction = get_post_meta( $donor->ID, '_navbb_donors_reproduction', true );
    $age = get_post_meta( $donor->ID, '_navbb_donors_age', true );
    $breed = get_post_meta( $donor->ID, '_navbb_donors_breed', true );
    $bloodtype = get_post_meta( $donor->ID, '_navbb_donors_bloodtype', true );
    $color = get_post_meta( $donor->ID, '_navbb_donors_color', true );
    $weight = get_post_meta( $donor->ID, '_navbb_donors_weight', true );
    $rabies = get_post_meta( $donor->ID, '_navbb_donors_rabies', true );
    $distemper = get_post_meta( $donor->ID, '_navbb_donors_distemper', true );
    $temperament = get_post_meta( $donor->ID, '_navbb_donors_temperament', true );
    $donation_location = get_post_meta($owner_id, '_navbb_owners_donation_location', true );
    $emergency_donor = get_post_meta( $donor->ID, '_navbb_donors_emergency_donor', true );
    $date_entered_program = get_post_meta( $donor->ID, '_navbb_donors_date_entered_program', true );
    $date_retired = get_post_meta( $donor->ID, '_navbb_donors_date_retired', true );
    $status = get_post_meta( $donor->ID, '_navbb_donors_status', true );
    $ownertype = get_post_meta( $owner_id, '_navbb_owners_ownertype', true );
    $internalOwnerID = ( get_post_meta( $owner_id, '_navbb_owners_internalOwnerID', true ) ?: "Not Set" );
    $ownertype = get_post_meta( $owner_id, '_navbb_owners_ownertype', true );
    $donor_notes = get_post_meta( $donor->ID, '_navbb_donors_donor_notes', true );

		//Retrieve all donation information for each donor
		$donation = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . $wp_prefix . "navbb_donations WHERE donor_id = %d ORDER BY donation_date DESC" , $donor->ID )
		);

    if( ! empty($donation) ) {
		  $date_last_donated = $donation[0]->donation_date;
      $number_of_donations = count($donation);
		} else {
      $date_last_donated = "Never Donated";
      $number_of_donations = 0;
    }

    $countfailure = 0;
    $countsuccess = 0;
    foreach( $donation as $donations ) {
      if( $donations->outcome == "Failure" ){
        $countfailure = $countfailure + 1;
      } elseif( $donations->outcome == "Success" ) {
        $countsuccess = $countsuccess + 1;
      }
    }

    $fullDonorID = $internalOwnerID . "-".$internalDonorID;
    if (empty($owner_id)){
      $owner_fullname = "";
    } elseif($ownertype == "Kennel Club") {
      $owner_fullname = html_entity_decode(get_the_title($owner_id));
    } else {
      $owner_fullname = html_entity_decode(get_the_title($owner_id)) . " , ". get_post_meta( $current_owner_id, '_navbb_owners_first_name', true );
    };

    $donorObjects[] = [
      'database_Donor_ID'=> $donor->ID,
      'donor_name' => $donor_name,
      'internalDonorID' => $fullDonorID,
      'owner_fullname' => $owner_fullname,
      'owner_id' => $owner_id,
      'ownertype' => $ownertype,
      'microchip' => $microchip,
      'specie' => $specie,
      'gender' => $gender,
      'reproduction' => $reproduction,
      'age' => $age,
      'breed' => $breed,
      'bloodtype' => $bloodtype,
      'color' => $color,
      'weight' => $weight,
      'rabies' => $rabies,
      'distemper' => $distemper,
      'temperament' => $temperament,
      'donation_location' => $donation_location,
      'emergency_donor' => $emergency_donor,
      'date_last_donated' => $date_last_donated,
      'number_of_donations' => $number_of_donations,
      'successful' => $countsuccess,
      'failure' => $countfailure,
      'date_entered_program' => $date_entered_program,
      'date_retired' => $date_retired,
      'status' => $status,
      'donor_notes' => $donor_notes ];
  }

  $sub = "Database Donor ID,Donor Name,Internal Donor ID,Owner Name,Database Owner ID,Owner Type,Microchip,Specie,Gender,Reproduction,DOB,Breed,Bloodtype,Color,Weight,Rabies,Distemper,Temperament,Location,Emergency,Last Donated,Number of Donations,Successes,Failures,Date Entered,Date Retired,Status,Donor Notes";
  $fields		 = $sub . "\n"; // Get fields names

  $csv_file_name	 = 'All_Donors_' . date( 'Ymd_His' ) . '.csv';

  foreach($donorObjects as $donor){

    foreach ( $donor as $data ) {
      $value	 = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $data ); // Replace new line with tab
      //$value	 = str_replace( array( "\\\"" ), "'", $data ); // Replace quotationmark with tab
      $value	 = str_getcsv( $value, ",", "\"", "\\" ); // SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
      $fields	 .= $value[ 0 ] . ','; // Separate fields with comma
    }

    $fields	 = substr_replace( $fields, '', -1 ); // Remove extra space at end of string
    $fields	 .= "\n"; // Force new line if loop complete
  }

  header( "Content-type: text/csv" );
  header( "Content-Transfer-Encoding: binary" );
  header( "Content-Disposition: attachment; filename=" . $csv_file_name );
  header( "Content-type: application/x-msdownload" );
  header( "Pragma: no-cache" );
  header( "Expires: 0" );

  wp_send_json($fields);
  wp_die();
}


//This is used in page-csv-exporter.php to export a csv of all donations
add_action( 'wp_ajax_handle_donations_to_csv_action', 'handle_donations_to_csv_action' );
function handle_donations_to_csv_action() {

	if ( ! current_user_can( 'manage_options' ) ) {
    echo 0;
		wp_die();
	}

  ob_end_clean();
  global $wpdb;
  $wp_prefix = $wpdb->prefix;

  $donationsTable = $wp_prefix . 'navbb_donations';
  $postsTable = $wp_prefix . 'posts';

  $tableQuery =
      "SELECT " .
      $donationsTable . ".id," .
      $donationsTable . ".donor_id,".
      $postsTable . ".post_title," .
      $donationsTable . ".donation_date," .
      $donationsTable . ".amount_donated," .
      $donationsTable . ".recumbency," .
      $donationsTable . ".vein," .
      $donationsTable . ".weight," .
      $donationsTable . ".temperature," .
      $donationsTable . ".respiration," .
      $donationsTable . ".pcv," .
      $donationsTable . ".ts," .
      $donationsTable . ".crt," .
      $donationsTable . ".mm," .
      $donationsTable . ".amount_potential," .
      $donationsTable . ".heartrate," .
      $donationsTable . ".donation_number," .
      $donationsTable . ".outcome
      FROM `" . $donationsTable . "` INNER JOIN `" . $postsTable . "` ON " . $donationsTable . ".donor_id = " . $postsTable . ".id;" ;

  $result	= $wpdb->get_results( $tableQuery );

  $sub = "Donation ID,Donor Id,Donor Name,Donation Date,Amount Donated,Recumbency,Vein,Weight,Temperature,Respiration,PCV,TS,CRT,MM,Amount Potential,Heartrate,Donation Number,Outcome,Specie,Bloodtype,Donor Type";

  $fields		 = $sub . "\n"; // Get fields names
  $csv_file_name	 = $getTable . '_' . date( 'Ymd_His' ) . '.csv';

  // Get fields values with last comma excluded
  foreach ( $result as $row ) {
    $row->specie = get_post_meta( $row->donor_id, '_navbb_donors_specie', true );
    $row->bloodtype = get_post_meta( $row->donor_id, '_navbb_donors_bloodtype', true);
    $owner_id = get_post_meta( $row->donor_id, '_navbb_donors_owner_id', true );
    $row->donor_type = get_post_meta( $owner_id, '_navbb_owners_ownertype', true );
    foreach ( $row as $data ) {
      $value	 = str_replace( array( "\n", "\n\r", "\r\n", "\r" ), "\t", $data ); // Replace new line with tab
      //$value	 = str_replace( array( "\\\"" ), "'", $data ); // Replace quotationmark with tab
      $value	 = str_getcsv( $value, ",", "\"", "\\" ); // SEQUENCING DATA IN CSV FORMAT, REQUIRED PHP >= 5.3.0
      $fields	 .= $value[ 0 ] . ','; // Separate fields with comma
    }
    $fields	 = substr_replace( $fields, '', -1 ); // Remove extra space at end of string
    $fields	 .= "\n"; // Force new line if loop complete
  }

  header( "Content-type: text/csv" );
  header( "Content-Transfer-Encoding: binary" );
  header( "Content-Disposition: attachment; filename=" . $csv_file_name );
  header( "Content-type: application/x-msdownload" );
  header( "Pragma: no-cache" );
  header( "Expires: 0" );

  wp_send_json($fields);
  wp_die();
}
