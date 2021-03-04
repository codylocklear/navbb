<?php


//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

//Only place we have defined navbb donations enqueue
add_action( 'admin_enqueue_scripts', 'navbb_donations_enqueue' );
function navbb_donations_enqueue( $pagehook ) {

	if ( 'navbb_page_donations' != $pagehook &&		//All Donations
		'navbb_page_new_donation' != $pagehook &&  	//New Donation
		'admin_page_view_donation' != $pagehook && 	//Individual Donation
		'admin_page_edit_donation' != $pagehook  		//Edit Donation
	) return;

	wp_enqueue_script( 'navbb_donations_enqueue', plugins_url('../js/navbb-donations.js',__FILE__ ), array(), rand(100,999), false );
	wp_localize_script('navbb_donations_enqueue', 'navbb_WPURLS', array('adminUrl' => admin_url() ));  //Provides adminUrl as a local variable in our JS script
}


// display the plugin dashboard page
function navbb_display_donations_page() {

	if ( ! current_user_can( 'edit_posts' ) ) return; // check if user is allowed access

	echo "<div class='wrap'>";
	echo "<h1>" . esc_html(get_admin_page_title())  . " Dashboard</h1>";
	echo "<a href='" . esc_url( admin_url('admin.php?page=new_donation') ) . "'>New Donation</a><br><br>";

	echo "<table class='navbb_table display' id='donation_table'>";
	echo "<thead><tr>";
	echo "<th>First Name</th>";
	echo "<th>Owner</th>";
	echo "<th>Blood Type</th>";
	echo "<th>Last Date Donated</th>";
	echo "<th>Outcome</th>";
	echo "<th></th>";
	echo "<th></th>";
	echo "</tr></thead><tbody>";

	global $wpdb;
 	$wp_prefix = $wpdb->prefix;

	$donations = $wpdb->get_results("SELECT * FROM " . $wp_prefix . "navbb_donations ORDER BY donation_date DESC;");

	foreach($donations as $donation){

		$first_name = get_the_title($donation->donor_id);
		$blood_type = get_post_meta($donation->donor_id, '_navbb_donors_bloodtype',true);
		$owner_id = get_post_meta($donation->donor_id, '_navbb_donors_owner_id',true);
		$owner_name = get_post_meta($owner_id, '_navbb_owners_first_name',true) . " " . get_the_title($owner_id);
    $outcome = check_donation_outcome(isset( $donation->outcome ) ? $donation->outcome : '');

		echo "<tr>";
		echo "<td><a href='". esc_url( admin_url( 'post.php?post='. ($donation->donor_id) .'&action=edit' ) ) ."'>". $first_name."</a></td>";
		echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $owner_id .'&action=edit' ) ) ."'>". $owner_name."</a></td>";
		echo "<td>".$blood_type."</td>";
		echo "<td>".$donation->donation_date."</td>";
		echo "<td>".$outcome."</td>";
		echo "<td><a href='" . esc_url( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id=' . $donation->id ) ) . "'>View Donation</a></td>";
		echo "<td><a href='" . esc_url( admin_url( 'admin.php?page=edit_donation&action=edit_donation&donation_id=' . $donation->id ) ) . "'>Edit Donation</a></td>";
		echo "</tr>";

	}
	echo "</tbody>";
	echo "</table>";
	echo "</div><br><br>";

}
