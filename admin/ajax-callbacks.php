<?php

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}


//This is the autocomplete function on the page New donations
add_action('wp_ajax_get_donor_names', 'ajax_donors');
function ajax_donors() {
  global $wpdb; //get access to the WordPress database object variable
  $donors = array();

  //Get post variable of recently typed input
  $name = '%'.$wpdb->esc_like(stripslashes($_POST['name'])).'%'; //escape for use in LIKE statement

  //Searches our post database next based off of Animal name
  $sql = "select post_title, ID
    from $wpdb->posts
    where post_title like %s
    and post_type='navbb_donors' and post_status='publish'";
  $sql = $wpdb->prepare($sql, $name);
  $results = $wpdb->get_results($sql);
  foreach( $results as $r ){
    $owner_id = get_post_meta($r->ID, '_navbb_donors_owner_id',true);
    $donor_fullname = ($r->post_title). " , ". html_entity_decode(get_the_title($owner_id)) ;
    $donors[] =  array('label' => $donor_fullname,'value' => $r->ID);
  }

  // Searches our postmeta database by owner last name
  $sql = "select post_title, ID
    from $wpdb->posts
    where post_title like %s
    and post_type='navbb_owners' and post_status='publish'";
  $sql = $wpdb->prepare($sql, $name);
  $results = $wpdb->get_results($sql);
  foreach( $results as $r ){
    $owner_id = ($r->ID);
    $sql = "select post_id
      from $wpdb->postmeta
      where meta_key='_navbb_donors_owner_id'
      and meta_value= %d ";
    $sql = $wpdb->prepare($sql, $owner_id);
    $donor_ids = $wpdb->get_results($sql);
    foreach($donor_ids as $donor_id){
      $donorfirstname = $wpdb->get_var( $wpdb->prepare( "SELECT post_title from $wpdb->posts where post_status = 'publish' and ID = %d", $donor_id->post_id ) );
      $donor_fullname = ( $donorfirstname . " , ".  ($r->post_title) );
      $donors[] = array('label' => $donor_fullname,'value' => $donor_id->post_id);
    }
  }

  //echo json_encode($donors, JSON_UNESCAPED_SLASHES); //encode into JSON format and output
  wp_send_json($donors);
  //echo json_encode($donors);
  wp_die(); //stop "0" from being output
}


add_action('wp_ajax_get_owner_names', 'ajax_owners');
function ajax_owners() {
	global $wpdb; //get access to the WordPress database object variable
  $owners = array();
	//get names of all owners
	$name = '%'.$wpdb->esc_like(stripslashes($_POST['name'])).'%'; //escape for use in LIKE statement

  //Searches our postmeta database by first name
  $sql = "select meta_value, post_id
    from $wpdb->postmeta
    where meta_value like %s
    and meta_key='_navbb_owners_first_name'";

  $sql = $wpdb->prepare($sql, $name);
  $results = $wpdb->get_results($sql);

  foreach( $results as $r ){
    if(get_post_status($r->post_id) == "publish"){
      $owner_fullname = html_entity_decode(get_the_title($r->post_id)) . " , ". addslashes($r->meta_value) ;

      $owners[] = array('label' => $owner_fullname,'value' => $r->post_id);
    }
  }

  //Searches our post database next based off of title (Last Name)
  $sql = "select post_title, ID
		from $wpdb->posts
		where post_title like %s
		and post_type='navbb_owners' and post_status='publish'";

	$sql = $wpdb->prepare($sql, $name);
	$results = $wpdb->get_results($sql);

  foreach( $results as $r ){

    $ownertype = get_post_meta($r->ID, '_navbb_owners_ownertype',true);
    //Since Kennel Clubs don't have first names, we add this if statement
    if($ownertype == "Kennel Club"){
      //$owner_fullname = addslashes($r->post_title);
      $owner_fullname = ($r->post_title);
    } else {
      //$owner_fullname = addslashes($r->post_title). " , ". get_post_meta($r->ID, '_navbb_owners_first_name',true);
      $owner_fullname = ($r->post_title). " , ". ( get_post_meta($r->ID, '_navbb_owners_first_name',true) ?: "Not Set" );
    }

    $owners[] =  array('label' => $owner_fullname,'value' => $r->ID);
  }

	echo json_encode($owners); //encode into JSON format and output
	wp_die(); //stop "0" from being output
}


//This is used in the donor metabox to look up internal owner id and append it the front of the internal donor id
add_action('wp_ajax_get_owner_internal_id', 'ajax_owner_internal_id');
function ajax_owner_internal_id() {
  	global $wpdb; //get access to the WordPress database object variable
    $owner_id = $wpdb->esc_like(stripslashes($_POST['owner_id']));
    $currentInternalOwnerID = get_post_meta( $owner_id, '_navbb_owners_internalOwnerID', true );
    wp_send_json($currentInternalOwnerID);
    wp_die(); // this is required to terminate immediately and return a proper response
}


//This is used on the new donation page to display the last donation information for the currently selected donor
add_action('wp_ajax_get_last_donation', 'ajax_last_donation');
function ajax_last_donation() {
  	global $wpdb;
    $wp_prefix = $wpdb->prefix;

    //Set the conditions sent from the server request
    $donor_id = isset($_POST['donor_id']) ? $_POST['donor_id'] : '' ;

    //Run our query to ensure we have at least one donation
    $donations = $wpdb->get_results("SELECT id , donation_date FROM " . $wp_prefix . "navbb_donations WHERE donor_id = " . $donor_id . " ORDER BY donation_date DESC LIMIT 1;");
    if ( count ( $donations ) > 0 ) {

      $individualDonation =  $wpdb->get_row("SELECT * FROM " . $wp_prefix . "navbb_donations WHERE donor_id = " . $donor_id . " ORDER BY donation_date DESC LIMIT 1;");

      $individualDonation_crt = $individualDonation->crt;
      if($individualDonation_crt == 0){
        $crt_value = "<1 second" ;
      } elseif ($individualDonation_crt == 1) {
        $crt_value = "1-2 seconds";
      } else {
        $crt_value = ">2 seconds";
      };

      $individualDonation_holder_id = ( isset( $individualDonation->holder ) ? $individualDonation->holder : '');
      $holder_info = get_userdata($individualDonation_holder_id);
      $holder_name = $holder_info->first_name .  " " . $holder_info->last_name ;

      $individualDonation_poker_id = ( isset( $individualDonation->poker ) ? $individualDonation->poker : '');
      $poker_info = get_userdata($individualDonation_poker_id);
      $poker_name = $poker_info->first_name .  " " . $poker_info->last_name ;


      $result = array( 'donor_id' => ($donor_id),
        'donation_date' => ($individualDonation->donation_date),
        'amount_potential'=> ($individualDonation->amount_potential),
        'amount_donated' => ($individualDonation->amount_donated),
        'recumbency' => ($individualDonation->recumbency),
        'sedation' => ($individualDonation->sedation),
        'vein' => ($individualDonation->vein),
        'crt' => ($crt_value),
        'mm' => ($individualDonation->mm),
        'collections' => ($individualDonation->collections),
        'weight' => ($individualDonation->weight),
        'temperature' => ($individualDonation->temperature),
        'heartrate' => ($individualDonation->heartrate),
        'respiration' => ($individualDonation->respiration),
        'pcv' => ($individualDonation->pcv),
        'ts' => ($individualDonation->ts),
        'holder' => ($holder_name),
        'poker' => ($poker_name),
        'donation_notes' => ($individualDonation->donation_notes),
        'history' => ($individualDonation->history),
        'physical_exam' => ($individualDonation->physical_exam)
      );

      wp_send_json($result);
      wp_die(); // this is required to terminate immediately and return a proper response

    } else {
      echo 0;
      wp_die();
    }

}


add_action( 'wp_ajax_misha_save_bulk', 'misha_save_bulk_edit_hook' );
function misha_save_bulk_edit_hook() {

	// you can check the same nonce we added in Quick Edit tutorial
	// if ( !wp_verify_nonce( $_POST['nonce'], 'quick_edit_misha_nonce' ) ) {
	// 	die();
	// }

	// well, if post IDs are empty, it is nothing to do here
	if( empty( $_POST[ 'post_ids' ] ) ) {
		die();
	}

	// for each post ID
	foreach( $_POST[ 'post_ids' ] as $id ) {

    if( !empty( $_POST[ 'owner' ] ) ) {
			update_post_meta( $id, '_navbb_donors_owner_id', $_POST['owner'] );
		}

		if( !empty( $_POST[ 'status' ] ) && $_POST[ 'status' ] != -1 ) {
			update_post_meta( $id, '_navbb_donors_status', $_POST['status'] );
		}

		if ( !empty( $_POST['bloodtype'] ) && $_POST[ 'bloodtype' ] != -1 ) {
			update_post_meta( $id, '_navbb_donors_bloodtype', $_POST['bloodtype'] );
		}

	}

	die();
}


add_action( 'wp_ajax_update_dashboard' , 'navbb_update_dashboard_hook');
function navbb_update_dashboard_hook() {
  $location_filter = isset($_POST['location']) ? $_POST['location'] : '' ;

  global $wpdb;
  $wp_prefix = $wpdb->prefix;
	$args = array(
		'numberposts' => -1,
		'post_type' => 'navbb_donors',
		'post_status'      => 'publish'
	);
  $donors = get_posts( $args );

  $donorObjects = array();
	foreach( $donors as $donor ){

		$donor_id = $donor->ID;
		$first_name = $donor->post_title;
		$owner_id = get_post_meta($donor_id, '_navbb_donors_owner_id',true);
		$owner_name = get_post_meta($owner_id, '_navbb_owners_first_name',true) . " " . get_the_title($owner_id);
    $ownertype = get_post_meta( $owner_id, '_navbb_owners_ownertype', true );
		$bloodtype = get_post_meta($donor_id, '_navbb_donors_bloodtype',true);
		$acquired = get_post_meta($donor_id, '_navbb_donors_acquired', true);
		$emergency_donor = get_post_meta($donor_id, '_navbb_donors_emergency_donor', true);
		$status = get_post_meta($donor_id, '_navbb_donors_status', true);
    $location = get_post_meta($owner_id, '_navbb_owners_donation_location', true );		//Retrieve all donation information for each donor
		$donation = $wpdb->get_results(
				$wpdb->prepare( "
						SELECT * FROM " . $wp_prefix . "navbb_donations
						WHERE donor_id = %d
						ORDER BY donation_date DESC" ,
						$donor_id
				)
		);
		//Establish Last Donation Date and Color
		if ( ! empty( $donation ) ) {
			$lastdonationdate = $donation[0]->donation_date;
		} else {
			$lastdonationdate = "Never Donated";
		}

    //Only add active donors for this loop
    if ($status=='active') {
      $donorObjects[] = [
        'donor_id' => $donor_id,
  			'first_name' => $first_name,
  			'owner_id'=> $owner_id,
  			'owner_name'=> $owner_name,
        'ownertype' => $ownertype,
  			'bloodtype' => $bloodtype,
  			'acquired'=> $acquired,
  			'emergency_donor' =>$emergency_donor,
  			'status'=>$status,
  			'location'=>$location,
  			'lastdonationdate' => $lastdonationdate
  		];
    }
	}

  $data = array();
  foreach( $donorObjects as $donorObject ){
    //If the location is Richmond, Bristow, or Stafford
    if ( $location_filter == $donorObject['location'] ){
      $first_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a>";
      $owner_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a>";
      $donor_data = array('first_name' => ($first_name), 'bloodtype' => ( $donorObject['bloodtype'] ), 'owner' => ( $owner_name ), 'location'=> ( $donorObject['location'] ), 'date_last_donated' => ( $donorObject['lastdonationdate']));
      $data[] = $donor_data;

      //If the Donor is an Emergency Donor
    } elseif ( ( $location_filter == "Emergency" ) && ( $donorObject['emergency_donor'] == "yes" ) ) {
      $first_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a>";
      $owner_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a>";
      $donor_data = array('first_name' => ($first_name), 'bloodtype' => ( $donorObject['bloodtype'] ), 'owner' => ( $owner_name ), 'location'=> ( $donorObject['location'] ), 'date_last_donated' => ( $donorObject['lastdonationdate']));
      $data[] = $donor_data;

      //If the filter is for Hunt Clubs
    } elseif ( ( $location_filter == "Hunt Club" ) && ( $donorObject['ownertype'] == "Kennel Club" ) ) {
      $first_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a>";
      $owner_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a>";
      $donor_data = array('first_name' => ($first_name), 'bloodtype' => ( $donorObject['bloodtype'] ), 'owner' => ( $owner_name ), 'location'=> ( 'Hunt Club' ), 'date_last_donated' => ( $donorObject['lastdonationdate']));
      $data[] = $donor_data;

    } elseif ( $location_filter == "All" ) {
      $first_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a>";
      $owner_name = "<a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a>";
      $donor_data = array('first_name' => ($first_name), 'bloodtype' => ( $donorObject['bloodtype'] ), 'owner' => ( $owner_name ), 'location'=> ( $donorObject['location'] ), 'date_last_donated' => ( $donorObject['lastdonationdate']));
      $data[] = $donor_data;
    }

  }

  if ( $location_filter == '' ) {
    echo 0;
  } else {
    wp_send_json($data);
  }

  wp_die(); // this is required to terminate immediately and return a proper response
}


add_action( 'wp_ajax_reorder_donations' , 'navbb_update_donations');
function navbb_update_donations() {
  $donor_id = isset($_POST['DonorID']) ? $_POST['DonorID'] : '' ;

  global $wpdb;
  $wp_prefix = $wpdb->prefix;
  $donationsTable = $wp_prefix . 'navbb_donations';

  $donations = $wpdb->get_results(
      $wpdb->prepare( "
          SELECT * FROM " . $donationsTable .
          " WHERE donor_id = %d
          ORDER BY donation_date ASC" ,
          $donor_id
      )
  );

  if ( ! empty( $donations ) ) {
    $counter = 1;
    foreach($donations as $donation){
      $donation_id = ( isset( $donation->id ) ? $donation->id : '');
      $update = array(
        'id' => $donation_id,
        'donation_number' => $counter
      );
      $wpdb->update($donationsTable, $update, array( 'id' => $donation_id ) );
      $counter = $counter + 1;
    }
    echo 1;
  } else {
    echo 0;
  }

  wp_die(); // this is required to terminate immediately and return a proper response
}
