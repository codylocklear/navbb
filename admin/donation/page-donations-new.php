<?php
//New Donations Form

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function navbb_display_new_donation_page() {
//$new_donation_add_meta_nonce = wp_create_nonce( 'navbb_new_donation_form_nonce' );



// check if user is allowed access
if ( ! current_user_can( 'edit_posts' ) ) return;
$navbb_add_meta_nonce = wp_create_nonce( 'navbb_add_user_meta_form_nonce' );

if ( isset( $_GET['donor_id'] ) ) {
	$donor_id = $_GET['donor_id'];
	$owner_id = get_post_meta( $donor_id, '_navbb_donors_owner_id', true );
	$donor_fullname = get_the_title($donor_id) . " , ". get_the_title( $owner_id );
}

?>

<div class="navbb-donation-page-container">

  <form id="myform" name="myform" type="POST" action="<?php echo esc_url( admin_url('admin-post.php') ); ?>"  method="POST" onsubmit="return checkForm(this)" >

		<h1>Add New Donation</h1><br>
			<div class="navbb-metabox-container">

				<div class="navbb-column-left">

					<div class="navbb-row-container">
			      <label for="donor_name" class="navbb-row-title">Donor Name:</label>
            <div class="navbb-row-content">
						  <input type="text" name="donor_name" id="donor_name" class="autocomplete_donors navbb-row-input" value="<?php echo $donor_fullname; ?>" placeholder="Select Donor"  onchange="donorDonationChange()">
            </div>
					</div>

					<div class="navbb-row-container">
			      <label for="donation_date" class="navbb-row-title">Donation Date:</label>
            <div class="navbb-row-content">
			        <input type="text" name="donation_date" id="donation_date" class="custom_date navbb-row-input">
            </div>
					</div>

					<div class="navbb-row-container">
						<label for="amount_potential" class="navbb-row-title">Volume to be Drawn (ml):</label>
            <div class="navbb-row-content">
						  <input type="number" name="amount_potential" id="amount_potential" class="navbb-row-input" min="0" max="2000">
            </div>
					</div>

					<div class="navbb-row-container">
			      <label for="amount_donated" class="navbb-row-title">Volume Drawn (ml):</label>
            <div class="navbb-row-content">
			        <input type="number" name="amount_donated" id="amount_donated" class="navbb-row-input" min="0" max="2000">
            </div>
					</div>

		      <div class="navbb-row-container">
		        <label for="recumbency" class="navbb-row-title">Patient Positioning:</label>
		        <div class="navbb-row-content">
		          <select name="recumbency" id="recumbency" class="navbb-row-input">
		            <option value="" selected="selected" disabled hidden>Select Patient Positioning</option>
		            <option value="Right Lateral">Right Lateral</option>
		            <option value="Left Lateral">Left Lateral</option>
		            <option value="Sternal">Sternal</option>
								<option value="Sitting">Sitting</option>
								<option value="Standing">Standing</option>
		          </select>
		        </div>
		      </div>

					<div class="navbb-row-container">
						<label for="vein" class="navbb-row-title">Vein Used:</label>
						<div class="navbb-row-content">
							<select name="vein" id="vein" class="navbb-row-input">
								<option value="" selected="selected" disabled hidden>Select Vein Used</option>
								<option value="Right Jugular">Right Jugular</option>
								<option value="Left Jugular">Left Jugular</option>
								<option value="Right Cephalic">Right Cephalic </option>
								<option value="Left Cephalic">Left Cephalic</option>
								<option value="Right Saphenous">Right Saphenous</option>
								<option value="Left Saphenous">Left Saphenous</option>
							</select>
						</div>
					</div>

					<div class="navbb-row-container">
			      <label for="crt" class="navbb-row-title">CRT:</label>
			      <div class="navbb-row-content">
			        <select name="crt" id="crt" class="navbb-row-input">
			          <option value="" selected="selected" disabled hidden>Select CRT</option>
			          <option value="1">&lt;1 second</option>
			          <option value="2">1-2 seconds</option>
			          <option value="3">&gt;2 seconds</option>
			        </select>
			      </div>
			    </div>

			    <div class="navbb-row-container">
			      <label for="mm" class="navbb-row-title">MM:</label>
			      <div class="navbb-row-content">
			        <select name="mm" id="mm" class="navbb-row-input">
			          <option value="" selected="selected" disabled hidden>Select Color</option>
			          <option value="pink">Pink</option>
			          <option value="blue">Blue</option>
			          <option value="grey">Grey</option>
			          <option value="white">White</option>
			          <option value="pale">Pale</option>
			          <option value="yellow">Yellow</option>
			          <option value="pigmented">Pigmented</option>
			          <option value="tacky">Tacky</option>
			          <option value="pale pink">Pale Pink</option>
			          <option value="injected">Injected</option>
			          <option value="red">Red</option>
			        </select>
			      </div>
			    </div>

					<div class="navbb-row-container">
			      <label for="weight" class="navbb-row-title">Weight (kg):</label>
            <div class="navbb-row-content">
			        <input type="number" name="weight" id="weight" class="navbb-row-input"  pattern="^\d+(?:\.\d{1,2})?$" step="0.1">
            </div>
					</div>

					<div class="navbb-row-container">
			      <label for="temperature" class="navbb-row-title">Temperature (deg):</label>
            <div class="navbb-row-content">
			        <input type="number" name="temperature" id="temperature" class="navbb-row-input"  pattern="^\d+(?:\.\d{1})?$" step="0.1" >
            </div>
					</div>

				</div>

				<div class="navbb-column-right">

					<div class="navbb-row-container">
						<label for="heartrate" class="navbb-row-title">Heart Rate (bpm):</label>
            <div class="navbb-row-content">
						  <input type="number" name="heartrate" id="heartrate" class="navbb-row-input" pattern="^[\d]*$" step="1">
            </div>
					</div>

					<div class="navbb-row-container">
						<label for="respiration" class="navbb-row-title">Respiratory Rate (bpm):</label>
            <div class="navbb-row-content">
						  <input type="text" name="respiration" id="respiration" class="navbb-row-input"  placeholder="Pant or ##.#">
            </div>
					</div>

					<div class="navbb-row-container">
			      <label for="pcv" class="navbb-row-title">PCV (%):</label>
            <div class="navbb-row-content">
			        <input type="number" name="pcv" id="pcv" class="navbb-row-input">
            </div>
					</div>

					<div class="navbb-row-container">
						<label for="ts" class="navbb-row-title">TS (g/dl):</label>
            <div class="navbb-row-content">
						  <input type="number" name="ts" id="ts" class="navbb-row-input" step="0.1">
            </div>
					</div>

          <div class="navbb-row-container">
            <label for="collections" class="navbb-row-title">Number of Collection Units:</label>
            <div class="navbb-row-content">
              <select name="collections" id="collections" class="navbb-row-input">
                <option value="" selected="selected" disabled hidden>Select Number</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
                <option value="4">Four</option>
                <option value="5">Five</option>
              </select>
            </div>
          </div>

					<div class="navbb-row-container">
						<label for="holder" class="navbb-row-title">Holder:</label>
						<div class="navbb-row-content">
							<?php wp_dropdown_users( array( 'show_option_all' => 'Select Holder', 'include_selected' => true, 'role__in' => array( 'navbb_employee', 'administrator'), 'name' => 'holder', 'id' => 'holder', 'class' => 'navbb-row-input' ) ); ?>
						</div>
					</div>

					<div class="navbb-row-container">
						<label for="poker" class="navbb-row-title">Poker:</label>
						<div class="navbb-row-content">
							<?php wp_dropdown_users( array( 'show_option_all' => 'Select Poker', 'include_selected' => true, 'role__in' => array( 'navbb_employee', 'administrator'), 'name' => 'poker', 'id' => 'poker', 'class' => 'navbb-row-input' ) ); ?>
						</div>
					</div>

					<div class="navbb-row-container">
						<label for="outcome" class="navbb-row-title">Outcome:</label>
						<div class="navbb-row-content">
							<select name="outcome" id="outcome" class="navbb-row-input">
								<option value="" selected="selected" disabled hidden>Select Outcome</option>
								<option value="Success">Successful: 2 Units</option>
                <option value="SuccessOneUnit">Successful: 1 Unit</option>
								<option value="Failure">Not Successful</option>
								<option value="Ineligible">Ineligible</option>
							</select>
						</div>
					</div>

					<div class="navbb-row-container">
						<p>General Notes:</p>
						<textarea rows="4"  class="navbb-notes" id="donation_notes" name="donation_notes" style="width:80%"></textarea>
					</div>

				</div>

			</div>
<br><br>

	<input type="hidden" name="navbb_add_user_meta_nonce" value="<?php echo $navbb_add_meta_nonce ?>" />
	<input type="hidden" name="donor_id" id="donor_id"  value="<?php echo $donor_id; ?>" >
	<input type="hidden" name="action" value="newDonationForm">
	<input type="submit" name="mySubmit">
</form>
<!--End of the Submit Form  -->
<br>

<hr>

<br>

	<h3> Previous Donation Visit:</h3>
	<div class="navbb-metabox-container">

		<div class="navbb-column-left">

			<div class="navbb-row-container">
	      <label class="navbb-row-saved-title">Donation Date:</label>
	      <div class="navbb-row-saved-value" id="previous_donation_date"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Volume to be Drawn (ml):</label>
				<div class="navbb-row-saved-value" id="previous_amount_potential"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Volume Drawn (ml):</label>
				<div class="navbb-row-saved-value" id="previous_amount_donated"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Patient Positioning:</label>
				<div class="navbb-row-saved-value" id="previous_recumbency"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Vein Used:</label>
				<div class="navbb-row-saved-value" id="previous_vein"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">CRT:</label>
				<div class="navbb-row-saved-value" id="previous_crt"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">MM:</label>
				<div class="navbb-row-saved-value" id="previous_mm"></div>
			</div>

      <div class="navbb-row-container">
        <label class="navbb-row-saved-title">Number of Collection Units:</label>
        <div class="navbb-row-saved-value" id="previous_collections"></div>
      </div>

			<div class="navbb-row-container">
	      <label class="navbb-row-saved-title">Weight (kg):</label>
	      <div class="navbb-row-saved-value" id="previous_weight"></div>
			</div>

			<div class="navbb-row-container">
	      <label class="navbb-row-saved-title">Temperature (deg):</label>
	      <div class="navbb-row-saved-value" id="previous_temperature"></div>
			</div>

		</div>

		<div class="navbb-column-right">

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Heart Rate (bpm):</label>
				<div class="navbb-row-saved-value" id="previous_heartrate"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Respiratory Rate (bpm):</label>
				<div class="navbb-row-saved-value" id="previous_respiration"></div>
			</div>

			<div class="navbb-row-container">
	      <label class="navbb-row-saved-title">PCV (%):</label>
	      <div class="navbb-row-saved-value" id="previous_pcv"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">TS (g/dl):</label>
				<div class="navbb-row-saved-value" id="previous_ts"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Holder:</label>
				<div class="navbb-row-saved-value" id="previous_holder"></div>
			</div>

			<div class="navbb-row-container">
				<label class="navbb-row-saved-title">Poker:</label>
				<div class="navbb-row-saved-value" id="previous_poker"></div>
			</div>

			<div class="navbb-row-container">
				<h3>General Notes:</h3>
				<div style="width: 80%; min-height: 100px; border: 1px solid rgba(0,0,0,.07);">
					<p id="previous_donation_notes" style="margin: 0;"></p>
				</div>
			</div>

		</div>

	</div>

<!-- End of the Page Container -->
</div>

	<?php

}
