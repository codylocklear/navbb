<?php
//Create Owner Metabox on the admin page

if ( ! defined( 'ABSPATH' ) ) {
 exit;
}

//This creates a meta box in order to connect our two custom posts (navbb_donors and navbb_owners)
add_action( 'admin_init', 'navbb_owners_add_meta_boxes' );
function navbb_owners_add_meta_boxes() {
    add_meta_box( 'navbb_owners_meta_box', __( 'Owner Information', 'navbb' ), 'navbb_owners_build_meta_box', 'navbb_owners', 'normal', 'low' );
}

function navbb_owners_build_meta_box($post) {
  // make sure the form request comes from WordPress
	wp_nonce_field( basename( __FILE__ ), 'navbb_owners_meta_box_nonce' );

  // retrieve the _navbb_owners_first_name current value
  $current_ownertype = get_post_meta( $post->ID, '_navbb_owners_ownertype', true );
  $current_donation_location = get_post_meta( $post->ID, '_navbb_owners_donation_location', true);
  $current_internalOwnerID = get_post_meta( $post->ID, '_navbb_owners_internalOwnerID', true );
  $current_first_name = get_post_meta( $post->ID, '_navbb_owners_first_name', true );
  $current_email = get_post_meta( $post->ID, '_navbb_owners_email', true );
  $current_phone_number = get_post_meta( $post->ID, '_navbb_owners_phone_number', true );
  $current_address_1 = get_post_meta( $post->ID, '_navbb_owners_address_1', true );
  $current_address_2 = get_post_meta( $post->ID, '_navbb_owners_address_2', true );
  $current_city = get_post_meta( $post->ID, '_navbb_owners_city', true );
  $current_state = get_post_meta( $post->ID, '_navbb_owners_state', true );
  $current_postcode = get_post_meta( $post->ID, '_navbb_owners_postcode', true );
  $current_notes = get_post_meta( $post->ID, '_navbb_owners_notes', true );

    ?>
  <div class="navbb-metabox-container">

    <div class="navbb-column-left">
      <div class="navbb-column-content">

        <div class="navbb-row-container">
          <label for="ownertype" class="navbb-row-title">Owner Type:</label>
          <select name="ownertype" id="ownertype" class="navbb-row-input" onchange="ownerTypeChange(this.value)">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Owner Type</option>
            <option value="Kennel Club"<?php if( "Kennel Club" == $current_ownertype ): ?> selected="selected"<?php endif; ?>>Hunt Club</option>
            <option value="Individual"<?php if( "Individual" == $current_ownertype ): ?> selected="selected"<?php endif; ?>>Individual</option>
          </select>
        </div>

        <div class="navbb-row-container">
          <label for="donation_location" class="navbb-row-title">Donation Location:</label>
          <?php  echo( select_locations( option_locations( 'Select Location', $current_donation_location ), 'donation_location', 'navbb-row-input' ) );  ?>
        </div>

        <div class="navbb-row-container">
          <label for="internalOwnerID" class="navbb-row-title">Owner ID:</label>
          <input type="text" name="internalOwnerID" id="internalOwnerID" class="navbb-row-input" value="<?php echo $current_internalOwnerID; ?>" />
        </div>

        <div class="navbb-row-container" id="row_first_name" <?php if($current_ownertype=="Kennel Club"): ?> style="display:none;" <?php endif; ?>>
          <label for="first_name" class="navbb-row-title">First Name:</label>
          <input type="text" name="first_name" id="first_name" class="navbb-row-input" value="<?php echo $current_first_name; ?>" />
        </div>

        <div class="navbb-row-container">
          <label for="email"  class="navbb-row-title">Email:</label>
          <input type="text" name="email" id="email" class="navbb-row-input" value="<?php echo $current_email; ?>" />
        </div>

        <div class="navbb-row-container">
          <label for="phone_number" class="navbb-row-title">Phone Number:</label>
          <input type="tel" name="phone_number" id="phone_number" class="navbb-row-input" placeholder="123-456-7890" pattern="[0-9]{3}-[0-9]{3}-[0-9]{4}" value="<?php echo $current_phone_number; ?>" />
        </div>

      </div>
    </div>

      <div class="navbb-column-right">
        <div class="navbb-column-content">
          <div class="navbb-row-container">
            <label for="address_1" class="navbb-row-title">Address Line 1:</label>
            <input type="text" name="address_1" id="address_1" class="navbb-row-input" value="<?php echo $current_address_1; ?>" />
          </div>

          <div class="navbb-row-container">
            <label for="address_2" class="navbb-row-title">Address Line 2:</label>
            <input type="text" name="address_2" id="address_2" class="navbb-row-input" value="<?php echo $current_address_2; ?>" />
          </div>

          <div class="navbb-row-container">
            <label for="city" class="navbb-row-title">Town/City:</label>
            <input type="text" name="city" id="city" class="navbb-row-input" value="<?php echo $current_city; ?>" />
          </div>

          <div class="navbb-row-container">
            <label for="state" class="navbb-row-title">State:</label>
            <input type="text" name="state" id="state" class="navbb-row-input" value="<?php echo $current_state; ?>" />
          </div>

          <div class="navbb-row-container">
            <label for="postcode" class="navbb-row-title">Zip Code:</label>
            <input type="text" name="postcode" id="postcode" class="navbb-row-input" value="<?php echo $current_postcode; ?>" />
          </div>
        </div>
      </div>
    </div>
  <hr>

  <div class="navbb-metabox-container">
    <div class="navbb-column-left-small">
      <p>Notes:</p>
      <textarea rows="10"  class="navbb-notes" id="notes" name="notes" style="width:80%"><?php echo $current_notes; ?></textarea>
    </div>
    <!--We need to query the database to find all of the dogs for this owner-->
    <div class="navbb-column-right-big">
      <!-- <div class="navbb-column-content"> -->
        <p>Donors:</p>
        <a href=' <?php echo esc_url( admin_url( "post-new.php?post_type=navbb_donors") )?> '>Create New Donor</a><br><br>
        <table class='navbb_table display' id='pets'>
          <thead>
            <tr>
              <th>Donor Name</th>
              <th>Donor ID</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>

      <?php
      global $wpdb;
      $owner_id = $post->ID;
      $donors = $wpdb->get_results(
          $wpdb->prepare( "
            SELECT * FROM ".$wpdb->prefix."postmeta
            WHERE meta_value = %d AND meta_key = '_navbb_donors_owner_id'",
            $owner_id
          )
        );

      foreach($donors as $donor){

        $donor_id = $donor->post_id;
        $donor_internal_id = get_post_meta( $donor_id, '_navbb_donors_internalDonorID', true );
        $first_name = get_the_title($donor->post_id);
        $status = get_donor_status( get_post_meta( $donor_id, '_navbb_donors_status',true ) );

        echo "<tr>";
        echo "<td><a href='". esc_url( admin_url( 'post.php?post='. $donor_id .'&action=edit' ) ) ."'>" . $first_name . "</a></td>";
        echo "<td>".$current_internalOwnerID."-".$donor_internal_id."</td>";
        echo "<td>".$status."</td>";
        echo "</tr>";
      }
      echo "</tbody></table></div></div>";

    ?>
      <script type="text/javascript">
        jQuery(document).ready( function ($) {
          $('#pets').DataTable({
            "order": [[ 1, "asc" ]],
          });
        });
      </script>
    <?php

}

function navbb_owners_save_meta_boxes_data( $post_id ){

	//Verifies our Nonce variable has not expired
	if ( !isset( $_POST['navbb_owners_meta_box_nonce'] ) || !wp_verify_nonce( $_POST['navbb_owners_meta_box_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	//Checks the permission of the current user
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
  if ( isset( $_REQUEST['ownertype'] ) ) {
		update_post_meta( $post_id, '_navbb_owners_ownertype', sanitize_text_field( $_POST['ownertype'] ) );
	}
  if ( isset( $_REQUEST['donation_location'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_donation_location', sanitize_text_field( $_POST['donation_location'] ) );
  }
	if ( isset( $_REQUEST['first_name'] ) ) {
		update_post_meta( $post_id, '_navbb_owners_first_name', sanitize_text_field( $_POST['first_name'] ) );
	}
  if ( isset( $_REQUEST['email'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_email', sanitize_text_field( $_POST['email'] ) );
  }
  if ( isset( $_REQUEST['phone_number'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_phone_number', sanitize_text_field( $_POST['phone_number'] ) );
  }
  if ( isset( $_REQUEST['internalOwnerID'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_internalOwnerID', sanitize_text_field( $_POST['internalOwnerID'] ) );
  }
  if ( isset( $_REQUEST['address_1'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_address_1', sanitize_text_field( $_POST['address_1'] ) );
  }
  if ( isset( $_REQUEST['address_2'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_address_2', sanitize_text_field( $_POST['address_2'] ) );
  }
  if ( isset( $_REQUEST['city'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_city', sanitize_text_field( $_POST['city'] ) );
  }
  if ( isset( $_REQUEST['state'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_state', sanitize_text_field( $_POST['state'] ) );
  }
  if ( isset( $_REQUEST['postcode'] ) ) {
    update_post_meta( $post_id, '_navbb_owners_postcode', sanitize_text_field( $_POST['postcode'] ) );
  }
  if ( isset ( $_REQUEST['notes'] ) ){
    update_post_meta( $post_id, '_navbb_owners_notes', sanitize_text_field( $_POST['notes'] ) );
  }

}
add_action( 'save_post_navbb_owners', 'navbb_owners_save_meta_boxes_data', 10, 2 );
