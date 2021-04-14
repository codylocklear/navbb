<?php
//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


////////New Donation/////////
add_action( 'admin_post_newDonationForm', 'donation_new');
function donation_new() {

	global $wpdb;
 	$wp_prefix = $wpdb->prefix;


	if( isset( $_POST['navbb_add_user_meta_nonce'] ) && wp_verify_nonce( $_POST['navbb_add_user_meta_nonce'], 'navbb_add_user_meta_form_nonce') && $_POST['donor_id'] != NULL ){

		$donor_id = isset($_POST['donor_id']) ? $_POST['donor_id'] : '' ;
		$donation_date = isset($_POST['donation_date']) && checkDateFormat($_POST['donation_date']) ? $_POST['donation_date'] : date("Y-m-d") ;
		$amount_potential = isset($_POST['amount_potential']) ? $_POST['amount_potential'] : '' ;
		$amount_donated = isset($_POST['amount_donated']) ? $_POST['amount_donated'] : '' ;
		$recumbency = isset($_POST['recumbency']) ? $_POST['recumbency'] : '' ;
		$vein = isset($_POST['vein']) ? $_POST['vein'] : '' ;
		$crt = isset($_POST['crt']) ? $_POST['crt'] : '' ;
		$mm = isset($_POST['mm']) ? $_POST['mm'] : '' ;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : '' ;
		$temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '' ;
		$heartrate = isset($_POST['heartrate']) ? $_POST['heartrate'] : '' ;
		$respiration = isset($_POST['respiration']) ? $_POST['respiration'] : '' ;
		$pcv = isset($_POST['pcv']) ? $_POST['pcv'] : '' ;
		$ts = isset($_POST['ts']) ? $_POST['ts'] : '' ;
    $collections = isset($_POST['collections']) ? $_POST['collections'] : '';
		$donation_notes = isset($_POST['donation_notes']) ? $_POST['donation_notes'] : '' ;
		$donation_number = ($wpdb->get_var("SELECT COUNT(*) FROM " . $wp_prefix . "navbb_donations WHERE donor_id = $donor_id")) + 1;
		$holder = isset($_POST['holder']) ? $_POST['holder'] : '' ;
		$poker = isset($_POST['poker']) ? $_POST['poker'] : '' ;
		$outcome = isset($_POST['outcome']) ? $_POST['outcome'] : '' ;

		$result = array(
			'donor_id' => $donor_id,
			'donation_date' => $donation_date,
			'amount_potential' => $amount_potential,
			'amount_donated' => $amount_donated,
			'recumbency' => $recumbency,
			'vein' => $vein,
			'crt' => $crt,
			'mm' => $mm,
			'weight' => $weight,
			'temperature' => $temperature,
			'heartrate' => $heartrate,
			'respiration' => $respiration,
			'pcv' => $pcv,
			'ts'=> $ts,
      'collections'=>$collections,
			'donation_notes' => sanitize_textarea_field($donation_notes),
			'donation_number' => $donation_number,
			'holder' => $holder,
			'poker' => $poker,
			'outcome' => $outcome
		);

		$table = $wp_prefix .  "navbb_donations";
		$wpdb->insert( $table , $result );
		$url = esc_url_raw( admin_url('admin.php?page=donations') );
		wp_redirect($url);
		exit;

	} else {

		wp_die( __( 'Invalid nonce specified', 'navbb' ), __( 'Error', 'navbb'), array(
			'response' 	=> 403,
			'back_link' => 'admin.php?page=new_donation' ,
		) );

	}
}


////////Edit Donation/////////
add_action( 'admin_post_editDonationForm', 'donation_update');
function donation_update() {
	global $wpdb;
	$wp_prefix = $wpdb->prefix;
	//Only process if we have a donation id
	if(  isset($_POST['donation_id']) && $_POST['donor_id'] != NULL && $_POST['donation_id'] != NULL  )  {
		$donation_id = $_POST['donation_id'];
		$donor_id = isset($_POST['donor_id']) ? $_POST['donor_id'] : '' ;
		$donation_date = isset($_POST['donation_date']) && checkDateFormat($_POST['donation_date']) ? $_POST['donation_date'] : date("Y-m-d") ;
		$amount_potential = isset($_POST['amount_potential']) ? $_POST['amount_potential'] : '' ;
		$amount_donated = isset($_POST['amount_donated']) ? $_POST['amount_donated'] : '' ;
		$recumbency = isset($_POST['recumbency']) ? $_POST['recumbency'] : '' ;
		$vein = isset($_POST['vein']) ? $_POST['vein'] : '' ;
		$crt = isset($_POST['crt']) ? $_POST['crt'] : '' ;
		$mm = isset($_POST['mm']) ? $_POST['mm'] : '' ;
		$weight = isset($_POST['weight']) ? $_POST['weight'] : '' ;
		$temperature = isset($_POST['temperature']) ? $_POST['temperature'] : '' ;
		$heartrate = isset($_POST['heartrate']) ? $_POST['heartrate'] : '' ;
		$respiration = isset($_POST['respiration']) ? $_POST['respiration'] : '' ;
		$pcv = isset($_POST['pcv']) ? $_POST['pcv'] : '' ;
		$ts = isset($_POST['ts']) ? $_POST['ts'] : '' ;
    $collections = isset($_POST['collections']) ? $_POST['collections'] : '';
		$donation_notes = isset($_POST['donation_notes']) ? $_POST['donation_notes'] : '' ;
		$holder = isset($_POST['holder']) ? $_POST['holder'] : '' ;
		$poker = isset($_POST['poker']) ? $_POST['poker'] : '' ;
		$outcome = isset($_POST['outcome']) ? $_POST['outcome'] : '' ;

		$result = array(
			'donor_id' => $donor_id,
			'donation_date' => $donation_date,
			'amount_potential'=> $amount_potential,
			'amount_donated' => $amount_donated,
			'recumbency' => $recumbency,
			'vein' => $vein,
			'crt'=> $crt,
			'mm' => $mm,
			'weight' => $weight,
			'temperature' => $temperature,
			'heartrate' => $heartrate,
			'respiration' => $respiration,
			'pcv'=> $pcv,
			'ts' => $ts,
      'collections'=>$collections,
			'donation_notes' => sanitize_textarea_field( $donation_notes ),
			'holder' => $holder,
			'poker' => $poker,
			'outcome' => $outcome
		);

		$table = $wp_prefix .  "navbb_donations";
		$wpdb->update($table, $result, array( 'id' => $donation_id ) );

		$url = esc_url_raw( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id=' . $donation_id ) );
		wp_redirect($url);

	} else {
		echo ("Update Failed");
	}
}



add_action( 'admin_post_deleteDonationForm', 'donation_delete');
function donation_delete() {
	global $wpdb;
	$wp_prefix = $wpdb->prefix;
	if( isset($_POST['donation_id']) ) {
		$table = $wp_prefix .  "navbb_donations";
		$donation_id = isset($_POST['donation_id']) ? $_POST['donation_id'] : '' ;
		$wpdb->delete( $table, array( 'ID' => $donation_id ) );
		$url = esc_url_raw( admin_url( 'admin.php?page=donations' ) );
		wp_redirect($url);

	} else {
		echo ("Update Failed");
	}


}
