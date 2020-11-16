<?php
//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// **** New Product **** //
function product_new() {
	global $wpdb;

	if(  isset($_POST['donation_id']) && $_POST['donation_id'] != NULL  )  {

		$donation_id = isset($_POST['donation_id']) ? $_POST['donation_id'] : '' ;
		$product = isset($_POST['product']) ? $_POST['product'] : '' ;
		$status = isset($_POST['status']) ? $_POST['status'] : '' ;

	  $donation = $wpdb->get_row(
	      $wpdb->prepare( "
	          SELECT donor_id, donation_number FROM bloodbank_donation
	          WHERE id = %d",
	          $donation_id
	      )
	  );

		//Build together our internal_product_id
		$product_number = ($wpdb->get_var("SELECT COUNT(*) FROM bloodbank_products WHERE donation_id = $donation_id")) + 1;
		$donation_number = $donation->donation_number;
		$donor_id = $donation->donor_id;
		$internalDonorID = get_post_meta( $donor_id, '_navbb_donors_internalDonorID', true );
  	$owner_id = get_post_meta( $donor_id, '_navbb_donors_owner_id', true );
  	$internalOwnerID = get_post_meta( $owner_id, '_navbb_owners_internalOwnerID', true );

		$internal_product_id = $internalOwnerID . "-" . $internalDonorID . "-" . $donation_number . "-" . $product_number;

		//Insert the results into our database
		$result = array( 'donation_id' => $donation_id, 'product_name'=>$product, 'product_status' => $status, 'product_internal_id' => $internal_product_id );
		$wpdb->insert('bloodbank_products', $result);

		$url = esc_url_raw( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id=' . $donation_id ) ) ;


		wp_redirect($url);


	}

}

add_action( 'admin_post_addNewProductForm', 'product_new');


// **** Edit Product **** //
function product_edit() {
  global $wpdb;
	//Only process if we have a donation id
	if(  isset($_POST['product_id']) && $_POST['product_id'] != NULL  )  {

    $product_id = isset($_POST['product_id']) ? $_POST['product_id'] : '' ;
		$donation_id = isset($_POST['donation_id']) ? $_POST['donation_id'] : '' ;
		$product_name = isset($_POST['product']) ? $_POST['product'] : '' ;
		$product_status = isset($_POST['status']) ? $_POST['status'] : '' ;

    $result = array('product_name' => $product_name, 'product_status'=>$product_status);

    $wpdb->update('bloodbank_products', $result, array( 'id' => $product_id ) );

		$url = esc_url_raw( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id=' . $donation_id ) ) ;
    wp_redirect($url);

  } else {
    echo ("Edit Failed");
  }

}

add_action( 'admin_post_editProductForm', 'product_edit');


// **** Delete Product **** //

//This is an ajax response to a delete request from individual donation page
add_action('wp_ajax_deleteProduct', 'product_delete');

function product_delete() {
  global $wpdb;
	//Only process if we have a donation id

  if ( ! isset( $_POST['product_id'] ) ) return;

  $product_id = $_POST['product_id'] ;

	if ( $wpdb->delete( 'bloodbank_products', array( 'id' => $product_id ) ) ) {
		wp_send_json($product_id);
	} else {
		echo 0;
	};

	wp_die(); //stop "0" from being output

}
