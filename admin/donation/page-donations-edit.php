<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function navbb_donation_render_edit_donation_page(){

	if ( ! current_user_can( 'edit_posts' ) ) return; 	// check if user is allowed access

	//Make sure that we at least have the donation id to run our query
  if ( ! isset( $_GET['donation_id'] ) ) return;

  global $wpdb;
 	$wp_prefix = $wpdb->prefix;

  $donation_id = $_GET['donation_id'];
  $donation = $wpdb->get_row(
      $wpdb->prepare( "SELECT * FROM ". $wp_prefix .  "navbb_donations WHERE id = %d", $donation_id )
  );

  $current_donor_id = ( ( $donation->donor_id ) != 0 ? ( $donation->donor_id ) : '' );
  $current_donation_date = ( isset( $donation->donation_date ) ? $donation->donation_date : '' );
	$current_amount_potential  = ( ( $donation->amount_potential != 0 ) ? $donation->amount_potential : '' );
  $current_amount_donated = ( ( $donation->amount_donated != 0 ) ? $donation->amount_donated : '' );
	$current_recumbency  = ( isset( $donation->recumbency ) ? $donation->recumbency : '' );
	$current_sedation  = ( isset( $donation->sedation ) ? $donation->sedation : '' );
	$current_vein  = ( isset( $donation->vein ) ? $donation->vein : '' );
	$current_crt  = ( isset( $donation->crt ) ? $donation->crt : '' );
	$current_mm  = ( isset( $donation->mm ) ? $donation->mm : '' );
	$current_weight = ( ( $donation->weight != 0 ) ? $donation->weight : '' );
	$current_temperature = ( ( $donation->temperature != 0 ) ? $donation->temperature : '' );
	$current_heartrate = ( ( $donation->heartrate != 0 ) ? $donation->heartrate : '' );
	$current_respiration = ( isset( $donation->respiration ) ? $donation->respiration : '' );
	$current_pcv = ( ( $donation->pcv != 0 ) ? $donation->pcv : '' );
	$current_ts = ( ( $donation->ts != 0 ) ? $donation->ts : '' );
  $current_collections = ( ( $donation->collections != 0 ) ? $donation->collections: '' );
	$current_donation_notes = ( isset( $donation->donation_notes ) ? $donation->donation_notes : '' );
	$current_history = ( isset( $donation->history ) ? $donation->history : '' );
	$current_physical_exam = ( isset( $donation->physical_exam ) ? $donation->physical_exam : '' );
	$current_holder = ( isset( $donation->holder ) ? $donation->holder : '' );
	$current_poker = ( isset( $donation->poker ) ? $donation->poker : '' );
	$current_outcome = ( isset( $donation->outcome) ? $donation->outcome : '' );

	//Need to determine if you'll keep these
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
	echo "<h1>Pet Name: " . "<a href='". esc_url( admin_url( 'post.php?post='. $current_donor_id .'&action=edit' ) ) ."'>". $current_first_name."</a>" . "</h1>";
	echo "<h3>Owner's Name: " . "<a href='". esc_url( admin_url( 'post.php?post='. $current_owner_id .'&action=edit' ) ) ."'>". $current_owner_name."</a>" . "</h3>";
	echo "<h3>Blood Type: " . $current_blood_type . "</h3>";

	?>

	<form type="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"  method="POST" >

		<div class="navbb-metabox-container">

			<div class="navbb-column-left">
				<div class="navbb-row-container">
		      <label for="donation_date" class="navbb-row-title">Donation Date:</label>
		      <input type="text" id="donation_date" class="custom_date navbb-row-input" name="donation_date" value="<?php echo $current_donation_date; ?>">
				</div>

				<div class="navbb-row-container">
					<label for="amount_potential" class="navbb-row-title">Volume to be Drawn (ml):</label>
					<input type="number" name="amount_potential" id="amount_potential" class="navbb-row-input" value="<?php echo $current_amount_potential; ?>" min="0" max="2000">
				</div>

				<div class="navbb-row-container">
					<label for="amount_donated" class="navbb-row-title">Volume Drawn (ml):</label>
					<input type="number" name="amount_donated" id="amount_donated" class="navbb-row-input" value="<?php echo $current_amount_donated; ?>" min="0" max="2000">
				</div>

				<div class="navbb-row-container">
					<label for="recumbency" class="navbb-row-title">Patient Positioning:</label>
					<div class="navbb-row-content">
						<select name="recumbency" id="recumbency" class="navbb-row-input">
							<option value="" selected="selected" disabled hidden>Select Patient Positioning</option>
							<option value="Right Lateral" <?php if( "Right Lateral" == $current_recumbency ): ?> selected="selected"<?php endif; ?>>Right Lateral</option>
							<option value="Left Lateral" <?php if( "Left Lateral" == $current_recumbency ): ?> selected="selected"<?php endif; ?>>Left Lateral</option>
							<option value="Sternal" <?php if( "Sternal" == $current_recumbency ): ?> selected="selected"<?php endif; ?>>Sternal</option>
							<option value="Sitting" <?php if( "Sitting" == $current_recumbency ): ?> selected="selected"<?php endif; ?>>Sitting</option>
							<option value="Standing" <?php if( "Standing" == $current_recumbency ): ?> selected="selected"<?php endif; ?>>Standing</option>
						</select>
					</div>
				</div>

				<div class="navbb-row-container">
					<label for="vein" class="navbb-row-title">Vein Used:</label>
					<div class="navbb-row-content">
						<select name="vein" id="vein" class="navbb-row-input">
							<option value="" selected="selected" disabled hidden>Select Vein Used</option>
							<option value="Right Jugular" <?php if( "Right Jugular" == $current_vein ): ?> selected="selected"<?php endif; ?>>Right Jugular</option>
							<option value="Left Jugular" <?php if( "Left Jugular" == $current_vein ): ?> selected="selected"<?php endif; ?>>Left Jugular</option>
							<option value="Right Cephalic" <?php if( "Right Cephalic" == $current_vein ): ?> selected="selected"<?php endif; ?>>Right Cephalic </option>
							<option value="Left Cephalic" <?php if( "Left Cephalic" == $current_vein ): ?> selected="selected"<?php endif; ?>>Left Cephalic</option>
							<option value="Right Saphenous" <?php if( "Right Saphenous" == $current_vein ): ?> selected="selected"<?php endif; ?>>Right Saphenous</option>
							<option value="Left Saphenous" <?php if( "Left Saphenous" == $current_vein ): ?> selected="selected"<?php endif; ?>>Left Saphenous</option>
						</select>
					</div>
				</div>

				<div class="navbb-row-container">
					<label for="crt" class="navbb-row-title">CRT:</label>
					<div class="navbb-row-content">
						<select name="crt" id="crt" class="navbb-row-input">
							<option value="" selected="selected" disabled hidden>Select CRT</option>
							<option value="1" <?php if( "1" == $current_crt ): ?> selected="selected"<?php endif; ?>>&lt;1 second</option>
							<option value="2" <?php if( "2" == $current_crt ): ?> selected="selected"<?php endif; ?>>1-2 seconds</option>
							<option value="3" <?php if( "3" == $current_crt ): ?> selected="selected"<?php endif; ?>>&gt;2 seconds</option>
						</select>
					</div>
				</div>

				<div class="navbb-row-container">
					<label for="mm" class="navbb-row-title">MM:</label>
					<div class="navbb-row-content">
						<select name="mm" id="mm" class="navbb-row-input">
							<option value="" selected="selected" disabled hidden>Select Color</option>
							<option value="pink" <?php if( "pink" == $current_mm ): ?> selected="selected"<?php endif; ?>>Pink</option>
							<option value="blue" <?php if( "blue" == $current_mm ): ?> selected="selected"<?php endif; ?>>Blue</option>
							<option value="grey" <?php if( "grey" == $current_mm ): ?> selected="selected"<?php endif; ?>>Grey</option>
							<option value="white" <?php if( "white" == $current_mm ): ?> selected="selected"<?php endif; ?>>White</option>
							<option value="pale" <?php if( "pale" == $current_mm ): ?> selected="selected"<?php endif; ?>>Pale</option>
							<option value="yellow" <?php if( "yellow" == $current_mm ): ?> selected="selected"<?php endif; ?>>Yellow</option>
							<option value="pigmented" <?php if( "pigmented" == $current_mm ): ?> selected="selected"<?php endif; ?>>Pigmented</option>
							<option value="tacky" <?php if( "tacky" == $current_mm ): ?> selected="selected"<?php endif; ?>>Tacky</option>
							<option value="pale pink" <?php if( "pale pink" == $current_mm ): ?> selected="selected"<?php endif; ?>>Pale Pink</option>
							<option value="injected" <?php if( "injected" == $current_mm ): ?> selected="selected"<?php endif; ?>>Injected</option>
							<option value="red" <?php if( "red" == $current_mm ): ?> selected="selected"<?php endif; ?>>Red</option>
						</select>
					</div>
				</div>

				<div class="navbb-row-container">
		      <label for="weight" class="navbb-row-title">Weight (kg):</label>
		      <input type="number" name="weight" id="weight" class="navbb-row-input" value="<?php echo $current_weight; ?>" pattern="^\d+(?:\.\d{1,2})?$" step="0.1">
				</div>

				<div class="navbb-row-container">
		      <label for="temperature" class="navbb-row-title">Temperature (deg):</label>
		      <input type="number" name="temperature" id="temperature" class="navbb-row-input" value="<?php echo $current_temperature; ?>" pattern="^\d+(?:\.\d{1})?$" step="0.1">
				</div>

			</div>

			<div class="navbb-column-right">

				<div class="navbb-row-container">
					<label for="heartrate" class="navbb-row-title">Heart Rate (bpm):</label>
					<input type="number" name="heartrate" id="heartrate" class="navbb-row-input" value="<?php echo $current_heartrate; ?>" pattern="^[\d]*$" step="1">
				</div>

				<div class="navbb-row-container">
					<label for="respiration" class="navbb-row-title">Respiratory Rate (bpm):</label>
					<input type="text" name="respiration" id="respiration" class="navbb-row-input" value="<?php echo $current_respiration; ?>">
				</div>

				<div class="navbb-row-container">
		      <label for="pcv" class="navbb-row-title">PCV (%):</label>
		      <input type="number" name="pcv" id="pcv" class="navbb-row-input" value="<?php echo $current_pcv; ?>">
				</div>

				<div class="navbb-row-container">
					<label for="ts" class="navbb-row-title">TS (g/dl):</label>
					<input type="number" name="ts" id="ts" class="navbb-row-input" value="<?php echo $current_ts; ?>" step="0.1">
				</div>

        <div class="navbb-row-container">
          <label for="collections" class="navbb-row-title">Number of Collection Units:</label>
          <div class="navbb-row-content">
            <select name="collections" id="collections" class="navbb-row-input">
              <option value="" selected="selected" disabled hidden>Select Number</option>
              <option value="1" <?php if( "1" == $current_collections ): ?> selected="selected"<?php endif; ?>>One</option>
              <option value="2" <?php if( "2" == $current_collections ): ?> selected="selected"<?php endif; ?>>Two</option>
              <option value="3" <?php if( "3" == $current_collections ): ?> selected="selected"<?php endif; ?>>Three</option>
              <option value="4" <?php if( "4" == $current_collections ): ?> selected="selected"<?php endif; ?>>Four</option>
              <option value="5" <?php if( "5" == $current_collections ): ?> selected="selected"<?php endif; ?>>Five</option>
            </select>
          </div>
        </div>

				<div class="navbb-row-container">
					<label for="holder" class="navbb-row-title">Holder:</label>
					<div class="navbb-row-content">
						<?php wp_dropdown_users( array( 'show_option_all' => 'Select Holder', 'selected' => $current_holder, 'role__in' => array( 'navbb_employee', 'administrator'), 'name' => 'holder', 'id' => 'holder', 'class' => 'navbb-row-input' ) ); ?>
					</div>
				</div>

				<div class="navbb-row-container">
					<label for="poker" class="navbb-row-title">Poker:</label>
					<div class="navbb-row-content">
						<?php wp_dropdown_users( array(  'show_option_all' => 'Select Poker', 'selected' => $current_poker, 'role__in' => array( 'navbb_employee', 'administrator'), 'name' => 'poker', 'id' => 'poker', 'class' => 'navbb-row-input' ) ); ?>
					</div>
				</div>

				<div class="navbb-row-container">
					<label for="outcome" class="navbb-row-title">Outcome:</label>
					<div class="navbb-row-content">
						<select name="outcome" id="outcome" class="navbb-row-input">
							<option value="" selected="selected" disabled hidden>Select Outcome</option>
							<option value="Success" <?php if( "Success" == $current_outcome ): ?> selected="selected"<?php endif; ?>>Successful: 2 Units</option>
              <option value="SuccessOneUnit" <?php if( "SuccessOneUnit" == $current_outcome ): ?> selected="selected"<?php endif; ?>>Successful: 1 Unit</option>
							<option value="Failure" <?php if( "Failure" == $current_outcome ): ?> selected="selected"<?php endif; ?>>Not Successful</option>
							<option value="Ineligible" <?php if( "Ineligible" == $current_outcome ): ?> selected="selected"<?php endif; ?>>Ineligible</option>
						</select>
					</div>
				</div>

			</div>

			<div class="navbb-row-container">
				<p>General Notes:</p>
				<textarea rows="4"  class="navbb-notes" id="donation_notes" name="donation_notes" style="width:40%"><?php echo stripslashes($current_donation_notes); ?></textarea>
			</div>

		</div>

		<br><br>

		<input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
		<input type="hidden" name="donor_id" value="<?php echo $current_donor_id; ?>">
		<input type="hidden" name="action" value="editDonationForm">
	  <input type="submit" class="button" style="margin:10px;">
		<a href='<?php echo esc_url( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id=' . $donation_id ) ) ?>' style='margin:10px;' class='button'>Cancel</a>

  </form>

</div>

<?php


}
