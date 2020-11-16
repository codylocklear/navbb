<?php
//Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


add_action( 'admin_enqueue_scripts' , 'navbb_dashboard_enqueue' );
function navbb_dashboard_enqueue( $pagehook ) {
	// do nothing if we are not on the target pages
	if ( 'toplevel_page_navbb' != $pagehook ) return;

	//Style and Javascript for our primary dashboard
	wp_register_style( 'navbb-dashboard' , plugins_url( 'css/navbb-dashboard.css' , __FILE__ ), array( ), '1.0.1', 'all' );
	wp_enqueue_style( 'navbb-dashboard' );
	wp_enqueue_script( 'navbb-dashboard' , plugins_url( 'js/navbb-dashboard.js' , __FILE__ ), array( ) , '1.0.1' );
	wp_localize_script('navbb-dashboard', 'navbb_WPURLS', array('adminUrl' => admin_url() ));  //Provides adminUrl as a local variable in our JS script
}


function navbb_display_dashboard_page() {
	// Check if user is allowed access
	if ( ! current_user_can( 'edit_posts' ) ) return;

	global $wpdb;
	$wp_prefix = $wpdb->prefix;

	$args = array( 'numberposts' => -1, 'post_type' => 'navbb_donors', 'post_status' => 'publish' );
	$donors = get_posts( $args );

	//Create an Array of Donors and retrieve necessary information on each
	$donorObjects = array();
	foreach( $donors as $donor ){
		$donor_id = $donor->ID;
		$first_name = $donor->post_title;
		$owner_id = get_post_meta($donor_id, '_navbb_donors_owner_id',true);
		$owner_name = get_post_meta($owner_id, '_navbb_owners_first_name',true) . " " . get_the_title($owner_id);
		$bloodtype = get_post_meta($donor_id, '_navbb_donors_bloodtype',true);
		$acquired = get_post_meta($donor_id, '_navbb_donors_acquired', true);
		$emergency_donor = get_post_meta($donor_id, '_navbb_donors_emergency_donor', true);
		$status = get_post_meta($donor_id, '_navbb_donors_status', true);
    $ownertype = get_post_meta($owner_id, '_navbb_owners_ownertype', true);

		$location = get_post_meta($owner_id , '_navbb_owners_donation_location', true);
    // if( "Kennel Club" == $ownertype ){
    //   $location;
    // }

		//Retrieve all donation information for each donor
		$donation = $wpdb->get_results(
				$wpdb->prepare( "SELECT * FROM " . $wp_prefix . "navbb_donations WHERE donor_id = %d ORDER BY donation_date DESC" , $donor_id )
		);
		//Establish Last Donation Date
		if ( ! empty( $donation ) ) {
			$lastdonationdate = $donation[0]->donation_date;
		} else {
			$lastdonationdate = "Never Donated";
		}
		$donorObjects[] = [
			'donor_id' => $donor_id,
			'first_name' => $first_name,
			'owner_id'=> $owner_id,
			'owner_name'=> $owner_name,
			'bloodtype' => $bloodtype,
			'acquired'=> $acquired,
			'emergency_donor' =>$emergency_donor,
			'status'=>$status,
			'location'=>$location,
			'lastdonationdate' => $lastdonationdate
		];
	}

///***Start HTML Output***///

///**Page Title**///
	echo "<div class='navbb-dashboard-page-container'>";
	echo "<h1>" . esc_html( get_admin_page_title() ) . "</h1>";

///**Dynamic Table**///
	echo "<h3>Active Donors Table</h3>";
	echo "<select id='navbb-dashboard-location'>";
	echo "<option value='' selected='selected' disabled hidden>Select Filter</option>";
	echo "<option value='All'>All Donors</option>";
	echo "<option value='Bristow'>Bristow</option>";
	echo "<option value='Richmond'>Richmond</option>";
	echo "<option value='Stafford'>Stafford</option>";
	echo "<option value='Emergency'>Emergency</option>";
	echo "<option value='Hunt Club'>Hunt Club</option>";
	echo "</select>";
	echo "<button onclick='updateDashboard()' class='button'>Refresh</button><br><br>";

//Start Active Donor Table
	echo "<table class='navbb_table display' id='navbb_active_donor_table'>";
	echo "<thead><tr>";
	echo "<th>First Name</th>";
	echo "<th>Blood Type</th>";
	echo "<th>Owner</th>";
	echo "<th>Location</th>";
	echo "<th>Date Last Donated</th>";
	echo "</tr></thead><tbody>";
	foreach($donorObjects as $donorObject){
		if($donorObject['status'] =="active"){
			echo "<tr>";
			echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a></td>";
			echo "<td>".$donorObject['bloodtype']."</td>";
			echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a></td>";
			echo "<td>".$donorObject['location']."</td>";
			echo "<td>".$donorObject['lastdonationdate']."</td></tr>";
		}
	}
	echo "</tbody></table><br><br><hr>";


///***Lab Work Table***///
	echo "<h3>Lab Work</h3>";
	echo "<table class='navbb_table display' id='lab_table'>";
	echo "<thead><tr>";
	echo "<th>First Name</th>";
	echo "<th>Owner</th>";
	echo "<th>Next Projected Donation</th>";
	echo "<th>Lab Work Expires</th>";
	echo "</tr></thead><tbody>";

	foreach($donorObjects as $donorObject){
		//If statement: if the donor's next donation date is within 30 days of expiring lab work, add to the table
		if( date('Y-m-d', strtotime($donorObject['lastdonationdate'] . ' + 30 days')) > date('Y-m-d', strtotime($donorObject['acquired'] . ' + 335 days'))   ) {
			echo "<tr>";
			echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $donorObject['donor_id'] .'&action=edit' ) ) ."'>". $donorObject['first_name']."</a></td>";
			echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $donorObject['owner_id'] .'&action=edit' ) ) ."'>". $donorObject['owner_name']."</a></td>";
			echo "<td>". date('Y-m-d', strtotime($donorObject['lastdonationdate'] . ' + 30 days'))  . "</td>";
			echo "<td style='background-color:#ff8080'>" . date('Y-m-d', strtotime($donorObject['acquired'] . ' + 365 days'))  .  "</td>";
			echo "</tr>";
		}
	}

	echo "</tbody></table>";
	echo "</div></div>";
}


//Change the Wordpress standard admin footer
add_filter('admin_footer_text', 'change_footer_admin');
function change_footer_admin () {
	return ' ';
}
