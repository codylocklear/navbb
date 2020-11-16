<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function navbb_donation_render_individual_donation_page(){

	// check if user is allowed access
	if ( ! current_user_can( 'edit_posts' ) ) return;

	if ( ! isset( $_GET['donation_id'] ) ) return; //Make sure that we at least have the donation id to run our query

  global $wpdb;
 	$wp_prefix = $wpdb->prefix;

  $donation_id = $_GET['donation_id'];
  $donation = $wpdb->get_row(
    $wpdb->prepare( "SELECT * FROM ". $wp_prefix .  "navbb_donations WHERE id = %d", $donation_id )
  );

  $current_donor_id = ( ($donation->donor_id) != 0 ? ($donation->donor_id) : '');
  $current_donation_date = ( isset( $donation->donation_date ) ? $donation->donation_date : '');
	$current_amount_potential  = ( ($donation->amount_potential != 0) ? $donation->amount_potential : '');
  $current_amount_donated = ( ($donation->amount_donated != 0) ? $donation->amount_donated : '');
	$current_recumbency  = ( isset( $donation->recumbency ) ? $donation->recumbency : '');
	$current_vein  = ( isset( $donation->vein ) ? $donation->vein : '');
	$current_crt  = ( isset($donation->crt) ? $donation->crt : '');
	$current_mm  = ( isset( $donation->mm ) ? $donation->mm : '');
	$current_weight = ( ($donation->weight != 0) ? $donation->weight : '');
	$current_temperature = ( ($donation->temperature != 0) ? $donation->temperature : '');
	$current_heartrate = ( ($donation->heartrate != 0) ? $donation->heartrate : '');
	$current_respiration = ( isset( $donation->respiration ) ? $donation->respiration : '');
	$current_pcv = ( ($donation->pcv != 0) ? $donation->pcv : '');
	$current_ts = ( ($donation->ts != 0) ? $donation->ts : '');
	$current_donation_notes = ( isset( $donation->donation_notes ) ? $donation->donation_notes : '');

	$current_holder_id = ( isset( $donation->holder ) ? $donation->holder : '');
	$holder_info = get_userdata($current_holder_id);
  $holder_name = $holder_info->first_name .  " " . $holder_info->last_name ;

	$current_poker_id = ( isset( $donation->poker ) ? $donation->poker : '');
	$poker_info = get_userdata($current_poker_id);
  $poker_name = $poker_info->first_name .  " " . $poker_info->last_name ;


	$current_outcome = ( isset( $donation->outcome ) ? $donation->outcome : '');

	//Need to determine if you'll keep these
	$current_availability = ( ($donation->available != 0) ? 'Yes' : 'No');
	$expiration_date = date('Y-m-d', strtotime($donation->donation_date . ' + 35 days'));

  $current_first_name = get_the_title($current_donor_id);
  $current_blood_type = get_post_meta($donation->donor_id, '_navbb_donors_bloodtype',true);
  $current_owner_id = get_post_meta($donation->donor_id, '_navbb_donors_owner_id',true);
  $current_owner_name = get_post_meta($current_owner_id, '_navbb_owners_first_name',true) . " " . get_the_title($current_owner_id);
	$today = date("Y-m-d");
	if( date('Y-m-d', strtotime($donation->donation_date . ' + 35 days')) <   $today   ) {
		$current_availability = "No";
	}

	echo "<div class='navbb-donation-page-container'>";
	echo "<div class='navbb-metabox-container'><div class='navbb-column-left'>";
	echo "<h1>Pet Name: " . "<a href='". esc_url( admin_url( 'post.php?post='. $current_donor_id .'&action=edit' ) ) ."'>". $current_first_name."</a>" . "</h1>";
	echo "<h3>Owner's Name: " . "<a href='". esc_url( admin_url( 'post.php?post='. $current_owner_id .'&action=edit' ) ) ."'>". $current_owner_name."</a>" . "</h3>";
	echo "<h3>Blood Type: " . $current_blood_type . "</h3>";
	echo "</div><div class='navbb-column-right'>";
	echo "<a href='" . esc_url( admin_url( 'admin.php?page=edit_donation&action=edit_donation&donation_id=' . $donation_id ) ) . "' style='margin:10px;' class='button'>Edit</a>";
	?>
	<form id="deletedonationform" name="deletedonationform" type="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"  method="POST" >
		<input type="hidden" name="donation_id" id="donation_id" value="<?php echo $donation_id; ?>">
		<input type="hidden" name="action" value="deleteDonationForm">
		<input type="submit" name="deleteDonation" class="button" value="Delete"  onclick="return confirm('Are you sure you want to delete this donation?');">
	</form>
	<?php
	echo "";
	echo "</div></div>";

  ?>

		<div class="navbb-metabox-container">

			<div class="navbb-column-left">

				<div class="navbb-row-container">
		      <label class="navbb-row-saved-title">Donation Date:</label>
		      <div class=navbb-row-saved-value><?php echo $current_donation_date; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Volume to be Drawn (ml):</label>
					<div class=navbb-row-saved-value><?php echo $current_amount_potential; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Volume Drawn (ml):</label>
					<div class=navbb-row-saved-value><?php echo $current_amount_donated; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Patient Positioning:</label>
					<div class=navbb-row-saved-value><?php echo $current_recumbency; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Vein Used:</label>
					<div class=navbb-row-saved-value><?php echo $current_vein; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">CRT:</label>
					<div class="navbb-row-saved-value"><?php
						if($current_crt == 1){
							echo "<1 second";
						} elseif ($current_crt == 2) {
							echo "1-2 seconds";
						} elseif ($current_crt == 3){
							echo ">2 seconds";
						} else {
							echo "";
						};  ?>
					</div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">MM:</label>
					<div class="navbb-row-saved-value"><?php echo $current_mm; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Weight (kg):</label>
					<div class=navbb-row-saved-value><?php echo $current_weight; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Temperature (deg):</label>
					<div class=navbb-row-saved-value><?php echo $current_temperature; ?></div>
				</div>

			</div>

			<div class="navbb-column-right">

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Heart Rate (bpm):</label>
					<div class=navbb-row-saved-value><?php echo $current_heartrate; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Respiratory Rate (bpm):</label>
					<div class=navbb-row-saved-value><?php echo $current_respiration; ?></div>
				</div>

				<div class="navbb-row-container">
		      <label class="navbb-row-saved-title">PCV (%):</label>
		      <div class=navbb-row-saved-value><?php echo $current_pcv; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">TS (g/dl):</label>
					<div class=navbb-row-saved-value><?php echo $current_ts; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Holder:</label>
					<div class=navbb-row-saved-value><?php echo $holder_name; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Poker:</label>
					<div class=navbb-row-saved-value><?php echo $poker_name; ?></div>
				</div>

				<div class="navbb-row-container">
					<label class="navbb-row-saved-title">Outcome:</label>
					<div class=navbb-row-saved-value>
						<?php
						if($current_outcome == "Success"){
							echo "Successful" ;
						} elseif ($current_outcome == "Failure") {
							echo "Not Successful";
						} elseif ($current_outcome == "Ineligible") {
							echo "Ineligible";
						} else {
							echo "";
						};  ?>
					</div>
				</div>

			</div>
				<!-- This div is outside the column container but is still inside the metabox container -->
				<div class="navbb-row-container">
					<h3>General Notes:</h3>
					<div style="width: 40%; min-height: 100px; border: 1px solid rgba(0,0,0,.07);">
						<p style="margin: 0; white-space: pre-wrap;"><?php echo stripslashes($current_donation_notes); ?></p>
					</div>
				</div>

		</div>

	<hr>

		<div class="navbb-metabox-container">

			<h3>Products Generated From Donation:</h3>
			<div class="navbb-row-container">
				<a href= " <?php echo esc_url( admin_url( 'admin.php?page=add_products&action=add_products&donation_id=' . $donation_id ) ) ?> ">Add New Products</a>
			</div>

			<?php


				$products = $wpdb->get_results(
				  	$wpdb->prepare( "
							SELECT * FROM" . $wp_prefix . " navbb_products
							WHERE donation_id = %d",
							$donation_id
			      )
			  );

				echo "<table class='navbb_table' id='products_table'>";
				echo "<thead><tr>";
				//echo "<th>Product ID</th>";
				echo "<th>Product Name</th>";
				echo "<th>Status</th>";
				echo "<th>Edit</th>";
				echo "<th>Delete</th>";
				echo "</tr></thead>";

				foreach ($products as $product) {

					echo "<tr id='product" .$product->id .  "'>";
					echo "<td class='product_id' style='display: none;'>".$product->id."</td>";  //You need this product id somewhere in the row so we know the id of the product to delete
					echo "<td>".$product->product_name."</td>";
					echo "<td>".$product->product_status."</td>";
					echo "<td><a href= '".esc_url( admin_url( 'admin.php?page=edit_products&action=edit_products&product_id=' . $product->id ) ) . "'>Edit</a></td>";
					echo "<td><a href='#' class='deletemyajax'>Delete</a></td>";
				}
				echo "</table>";
				echo "</div></div>";

}
