<?php
//Create Donor Metabox on the admin page

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

//CSS for this page is located in metabox.css
add_action( 'add_meta_boxes_navbb_donors', 'navbb_donors_add_meta_boxes' );
function navbb_donors_add_meta_boxes( $post ){
  add_meta_box( 'navbb_donors_information_meta_box', __( 'Donor Information', 'navbb_donors_plugin' ), 'navbb_donors_build_information_meta_box', 'navbb_donors', 'normal' );
  add_meta_box( 'navbb_donors_donations_meta_box', __( 'Donor Donations', 'navbb_donors_plugin' ), 'navbb_donors_build_donation_meta_box', 'navbb_donors', 'normal' );
  add_meta_box( 'navbb_donors_lab_meta_box', __( 'Lab Work', 'navbb_donors_plugin' ), 'navbb_donors_build_lab_meta_box', 'navbb_donors', 'normal' );
  add_meta_box( 'navbb_donors_physical_meta_box', __( 'Annual Physical', 'navbb_donors_plugin' ), 'navbb_donors_build_physical_meta_box', 'navbb_donors', 'normal' );
  add_meta_box( 'wp_custom_attachment', 'Custom Attachment', 'wp_custom_attachment', 'navbb_donors', 'side' );
}


add_action( 'admin_enqueue_scripts', 'navbb_donors_meta_box_enqueue' );
function navbb_donors_meta_box_enqueue( $pagehook ) {
	// do nothing if we are not on the target pages
	if ( ('post.php' != $pagehook) && ( 'post-new.php' != $pagehook ) ) {
		return;
	}
	wp_enqueue_script( 'meta-box-donors-javascript', plugins_url('js/meta-box-donors-javascript.js',__FILE__ ), array( ) , rand(111,9999));
	wp_localize_script('meta-box-donors-javascript', 'navbb_WPURLS', array('adminUrl' => admin_url() ));  //Provides adminUrl as a local variable in our JS script

}


//Used as a response after updating the donor's birthday
function getAge($date) {
  return intval(date('Y', time() - strtotime($date))) - 1970;
}


////////Donor Info Box////////
function navbb_donors_build_information_meta_box( $post ){

	wp_nonce_field( basename( __FILE__ ), 'navbb_donors_info_nonce' );

  $current_internalDonorID = get_post_meta( $post->ID, '_navbb_donors_internalDonorID', true );
  $current_microchip = get_post_meta( $post->ID, '_navbb_donors_microchip', true );
	$current_specie = get_post_meta( $post->ID, '_navbb_donors_specie', true );
	$current_gender = get_post_meta( $post->ID, '_navbb_donors_gender', true );
	$current_reproduction = get_post_meta( $post->ID, '_navbb_donors_reproduction', true );
	$current_age = get_post_meta( $post->ID, '_navbb_donors_age', true );
  $current_breed = get_post_meta( $post->ID, '_navbb_donors_breed', true );
  $current_bloodtype = get_post_meta( $post->ID, '_navbb_donors_bloodtype', true );
  $current_color = get_post_meta( $post->ID, '_navbb_donors_color', true );
  $current_weight = get_post_meta( $post->ID, '_navbb_donors_weight', true );
  $current_rabies = get_post_meta( $post->ID, '_navbb_donors_rabies', true );
  $current_distemper = get_post_meta( $post->ID, '_navbb_donors_distemper', true );
  $current_temperament = get_post_meta( $post->ID, '_navbb_donors_temperament', true );
  $current_emergency_donor = get_post_meta( $post->ID, '_navbb_donors_emergency_donor', true );
  $current_date_entered_program = get_post_meta( $post->ID, '_navbb_donors_date_entered_program', true );
  $current_date_retired = get_post_meta( $post->ID, '_navbb_donors_date_retired', true );
  $current_status = get_post_meta( $post->ID, '_navbb_donors_status', true );
  $current_owner_id = get_post_meta( $post->ID, '_navbb_donors_owner_id', true );
  $current_ownertype = get_post_meta( $current_owner_id, '_navbb_owners_ownertype', true );
  $current_donor_notes = get_post_meta( $post->ID, '_navbb_donors_donor_notes', true );

  //Set the Owner's Full Name
  if (empty($current_owner_id)){
    $owner_fullname = "";
  } elseif($current_ownertype == "Kennel Club") {
    $owner_fullname = get_the_title($current_owner_id);
  } else {
    $owner_fullname = get_the_title($current_owner_id) . " , ". ( get_post_meta( $current_owner_id, '_navbb_owners_first_name', true ) ?: "Not Set" );
  }

  //Calculate the age of the animal from their birthDate
  if(empty($current_age)){
    $age = "Not Set";
  } else {
    $age = getAge($current_age);
  }

  if(empty($current_owner_id)){
    $interalOwnerID = "Not Set";
  } else {
    $internalOwnerID = get_post_meta( $current_owner_id, '_navbb_owners_internalOwnerID', true );
  }

	?>
  <div class="navbb-metabox-container">
    <div class="navbb-column-left">

      <div class="navbb-row-container">
        <label for="internalDonorID" class="navbb-row-title">Donor ID:</label>
        <div class="navbb-row-content">
          <span id="internalOwnerID"> <?php echo (empty($internalOwnerID) ? "Not Set" : $internalOwnerID) ; ?>- </span>
          <input type="text" name="internalDonorID" id="internalDonorID" class="navbb-row-input" value="<?php echo $current_internalDonorID; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="microchip" class="navbb-row-title">Microchip:</label>
        <div class="navbb-row-content">
          <input type="text" name="microchip" id="microchip" class="navbb-row-input" value="<?php echo $current_microchip; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <span class="navbb-row-title">Gender:</span>
        <div class="navbb-row-content">
          <label for="meta-gender-male">
            <input type="radio" name="gender" id="meta-gender-male" value="Male" <?php checked( $current_gender, 'Male' ); ?> /> Male
          </label>
          <label for="meta-gender-female">
            <input type="radio" name="gender" id="meta-gender-female" value="Female" <?php checked( $current_gender, 'Female' ); ?> /> Female
          </label>
        </div>
      </div>

      <div class="navbb-row-container">
        <span class="navbb-row-title">Species:</span>
        <div class="navbb-row-content">
          <label for="meta-specie-canine">
            <input type="radio" name="specie" id="meta-specie-canine" value="Canine" onclick="updateSpecies(this.value)" <?php checked( $current_specie, 'Canine' ); ?> />Canine
          </label>
          <label for="meta-specie-feline">
            <input type="radio" name="specie" id="meta-specie-feline" value="Feline" onclick="updateSpecies(this.value)" <?php checked( $current_specie, 'Feline' ); ?> />Feline
          </label>
        </div>
      </div>

      <div class="navbb-row-container">
        <span class="navbb-row-title">Spayed/Neutered:</span>
        <div class="navbb-row-content">
          <label for="meta-reproduction-yes">
            <input type="radio" name="reproduction" id="meta-reproduction-yes" value="Yes" onclick="updateReproduction(this.value)" <?php checked( $current_reproduction, 'Yes' ); ?> /> Yes
          </label>
          <label for="meta-reproduction-no">
            <input type="radio" name="reproduction" id="meta-reproduction-no" value="No" onclick="updateReproduction(this.value)" <?php checked( $current_reproduction, 'No' ); ?> /> No
          </label>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="breed" class="navbb-row-title">Breed:</label>
        <div class="navbb-row-content">
          <input type="text" name="breed" id="breed" class="navbb-row-input" value="<?php echo $current_breed; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="color" class="navbb-row-title">Color:</label>
        <div class="navbb-row-content">
          <input type="text" name="color" id="color" class="navbb-row-input" value="<?php echo $current_color; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="age" class="navbb-row-title">Age:</label>
        <div class="navbb-row-content">
          <input type="text" name="age" id="age" value="<?php echo $current_age; ?>" class="custom_date navbb-row-input" onchange="calculateAge(this.value)" placeholder="Select Birthdate"/>
          <span id="p1"> <?php if( $age == "Not Set"): ?> Not Set<?php else: echo $age . " years old"?> <?php endif; ?> </span>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="bloodtype" class="navbb-row-title">Bloodtype:</label>
        <div class="navbb-row-content">
          <select name="bloodtype" id="bloodtype" class="navbb-row-input">
            <option value="" <?php if( empty($current_bloodtype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Blood Type</option>
            <option value="DEA 1.1 Negative"<?php if( "DEA 1.1 Negative" == $current_bloodtype ): ?> selected="selected"<?php endif; ?>>DEA 1.1 Negative</option>
            <option value="DEA 1.1 Positive"<?php if( "DEA 1.1 Positive" == $current_bloodtype ): ?> selected="selected"<?php endif; ?>>DEA 1.1 Positive</option>
            <option value="Feline A"<?php if( "Feline A" == $current_bloodtype ): ?> selected="selected"<?php endif; ?>>Feline Type A</option>
            <option value="Feline B"<?php if( "Feline B" == $current_bloodtype ): ?> selected="selected"<?php endif; ?>>Feline Type B</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="weight" class="navbb-row-title">Weight:</label>
        <div class="navbb-row-content">
          <input type="number" name="weight" id="weight" class="navbb-row-input" step=0.1 value="<?php echo $current_weight; ?>" /> kg
        </div>
      </div>

    </div>

    <div class="navbb-column-right">

      <div class="navbb-row-container">
        <label for="rabies" class="navbb-row-title">Rabies Date Expires:</label>
        <div class="navbb-row-content">
          <input type="text" name="rabies" id="rabies" value="<?php echo $current_rabies; ?>" class="custom_date navbb-row-input"  placeholder="Select Rabies Date"/>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="distemper" class="navbb-row-title">Distemper Date Expires:</label>
        <div class="navbb-row-content">
          <input type="text" name="distemper" id="distemper" value="<?php echo $current_distemper; ?>" class="custom_date navbb-row-input"  placeholder="Select Distemper Date"/>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="temperament" class="navbb-row-title">Temperament:</label>
        <div class="navbb-row-content">
          <select name="temperament" id="temperament" class="navbb-row-input">
            <option value="" <?php if( empty($current_temperament) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Temperament</option>
            <option value="1"<?php if( "1" == $current_temperament ): ?> selected="selected"<?php endif; ?>>Very Excited</option>
            <option value="2"<?php if( "2" == $current_temperament ): ?> selected="selected"<?php endif; ?>>Generally Nervous</option>
            <option value="3"<?php if( "3" == $current_temperament ): ?> selected="selected"<?php endif; ?>>Medium</option>
            <option value="4"<?php if( "4" == $current_temperament ): ?> selected="selected"<?php endif; ?>>Fairly Docile</option>
            <option value="5"<?php if( "5" == $current_temperament ): ?> selected="selected"<?php endif; ?>>Calm</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="emergency_donor" class="navbb-row-title">Emergency Donor:</label>
        <div class="navbb-row-content">
          <select name="emergency_donor" id="emergency_donor" class="navbb-row-input">
            <option value="" <?php if( empty($current_emergency_donor) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Status</option>
            <option value="yes"<?php if( "yes" == $current_emergency_donor ): ?> selected="selected"<?php endif; ?>>Yes</option>
            <option value="no"<?php if( "no" == $current_emergency_donor ): ?> selected="selected"<?php endif; ?>>No</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="status" class="navbb-row-title">Donor Status:</label>
        <div class="navbb-row-content">
          <select name="status" id="status" class="navbb-row-input" onchange="donorStatusChange(this.value)">
            <option value="" <?php if( empty($current_status) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Status </option>
            <option value="active"<?php if( "active" == $current_status ): ?> selected="selected"<?php endif; ?>>Active</option>
            <option value="pending"<?php if( "pending" == $current_status ): ?> selected="selected"<?php endif; ?>>Pending</option>
            <option value="retired"<?php if( "retired" == $current_status ): ?> selected="selected"<?php endif; ?>>Retired</option>
            <option value="not accepted"<?php if( "not accepted" == $current_status ): ?> selected="selected"<?php endif; ?>>Not Accepted</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="date_entered_program" class="navbb-row-title">Date Entered Program:</label>
        <div class="navbb-row-content">
          <input type="text" name="date_entered_program" id="date_entered_program" value="<?php echo $current_date_entered_program; ?>" class="custom_date navbb-row-input" placeholder="Select Date"/>
        </div>
      </div>

      <div class="navbb-row-container" id="row_date_retired" <?php if($current_status!="retired"): ?> style="display:none;" <?php endif; ?>>
        <label for="date_retired" class="navbb-row-title">Date Retired:</label>
        <div class="navbb-row-content">
          <input type="text" name="date_retired" id="date_retired" value="<?php echo $current_date_retired; ?>" class="custom_date navbb-row-input" placeholder="Select Date"/>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="owner_test" class="navbb-row-title">Owner Select:</label>
        <div class="navbb-row-content">
          <input type="text" name="owner_test" id="owner_test" class="autocomplete_owners navbb-row-input" value="<?php echo $owner_fullname; ?>" onchange="ownerInternalID()">
          <input type="hidden" name="owner_id" id="owner_id" value="<?php echo $current_owner_id; ?>">
          <span><a href=" <?php echo  esc_url( admin_url( 'post.php?post='. $current_owner_id .'&action=edit' ) ) ?> "> View Owner </a></span>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="donor_notes" class="navbb-row-title">Animal Notes:</label>
        <div class="navbb-row-content">
          <textarea rows="10"  id="donor_notes" name="donor_notes" class="navbb-row-input" style="width:300px;"><?php echo $current_donor_notes; ?></textarea>
        </div>
      </div>

    </div>
	</div>
	<?php
}


add_action( 'save_post_navbb_donors', 'navbb_donors_save_information_meta_boxes_data', 10, 2 );
function navbb_donors_save_information_meta_boxes_data( $post_id ){
	//Verifies our Nonce variable has not expired
	if ( !isset( $_POST['navbb_donors_info_nonce'] ) || !wp_verify_nonce( $_POST['navbb_donors_info_nonce'], basename( __FILE__ ) ) ){
		return;
	}

	//Checks the permission of the current user
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
  if ( isset( $_REQUEST['internalDonorID'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_internalDonorID', sanitize_text_field( $_POST['internalDonorID'] ) );
	}

  if ( isset( $_REQUEST['microchip'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_microchip', sanitize_text_field( $_POST['microchip'] ) );
  }

  if ( isset( $_REQUEST['gender'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_gender', sanitize_text_field( $_POST['gender'] ) );
	}

	if ( isset( $_REQUEST['specie'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_specie', sanitize_text_field( $_POST['specie'] ) );
	}

  if ( isset( $_REQUEST['reproduction'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_reproduction', sanitize_text_field( $_POST['reproduction'] ) );
  }

  if ( isset( $_REQUEST['breed'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_breed', sanitize_text_field( $_POST['breed'] ) );
  }

  if ( isset( $_REQUEST['color'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_color', sanitize_text_field( $_POST['color'] ) );
  }

  if ( isset( $_REQUEST['age'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_age', sanitize_text_field( $_POST['age'] ) );
	}

  if ( isset( $_REQUEST['bloodtype'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_bloodtype', sanitize_text_field( $_POST['bloodtype'] ) );
  }

  if ( isset( $_REQUEST['weight'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_weight', sanitize_text_field( $_POST['weight'] ) );
  }

  if ( isset( $_REQUEST['rabies'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_rabies', sanitize_text_field( $_POST['rabies'] ) );
  }

  if ( isset( $_REQUEST['distemper'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_distemper', sanitize_text_field( $_POST['distemper'] ) );
  }

  if ( isset( $_REQUEST['temperament'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_temperament', sanitize_text_field( $_POST['temperament'] ) );
  }

  if ( isset( $_REQUEST['emergency_donor'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_emergency_donor', sanitize_text_field( $_POST['emergency_donor'] ) );
  }

  if ( isset( $_REQUEST['status'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_status', sanitize_text_field( $_POST['status'] ) );
  }

  if ( isset( $_REQUEST['date_entered_program'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_date_entered_program', sanitize_text_field( $_POST['date_entered_program'] ) );
  }

  if ( isset( $_REQUEST['date_retired'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_date_retired', sanitize_text_field( $_POST['date_retired'] ) );
  }

  if ( isset( $_REQUEST['owner_id'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_owner_id', sanitize_text_field( $_POST['owner_id'] ) );
  }

  if ( isset( $_REQUEST['donor_notes'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_donor_notes', sanitize_textarea_field( $_POST['donor_notes'] ) );
  }
}


////////Donation Meta Box////////
function navbb_donors_build_donation_meta_box( $post ){

  global $wpdb;
  $wp_prefix = $wpdb->prefix;

  $donor_id = $post->ID;

	echo "<a href='" .  esc_url( admin_url( 'admin.php?page=new_donation&donor_id=' . $donor_id . '') ) . "' style='display:inline-block;padding:10px'>New Donation</a>";
  echo "<button type='button' onclick='reorderDonations(" . $donor_id .")'>Reorder Donations</button>";
  echo "<table class='navbb_table'>";
  echo "<thead><tr>";
  echo "<th>Donation Number</th>";
  echo "<th>Donation Date</th>";
  echo "<th>Amount Donated</th>";
  echo "<th>Jugular</th>";
  echo "<th>Outcome</th>";
  echo "<th>View Donation</th>";
  echo "<th>Edit Donation</th>";
	echo "</tr></thead><tbody>";

  $donor_id = $post->ID;
  $donations = $wpdb->get_results(
    $wpdb->prepare( "
        SELECT * FROM " . $wp_prefix . "navbb_donations
        WHERE donor_id = %d
        ORDER BY donation_date",
        $donor_id
    )
  );

  foreach($donations as $donation){
    $donation_number = ( isset( $donation->donation_number ) ? $donation->donation_number : '');
    $donation_date = ( isset( $donation->donation_date ) ? $donation->donation_date : '');
    $amount_donated = ( isset( $donation->amount_donated ) ? $donation->amount_donated : '');
    $jugular = ( isset( $donation->vein) ? $donation->vein : '');
	  $current_outcome = ( isset( $donation->outcome ) ? $donation->outcome : '');
    if($current_outcome == "Success"){
      $outcome = "Successful" ;
    } elseif ($current_outcome == "Failure") {
      $outcome =  "Not Successful";
    } elseif ($current_outcome == "Ineligible") {
      $outcome = "Ineligible";
    } else {
      $outcome = "";
    };

    echo "<tr>";
    echo "<td>".$donation_number."</td>";
    echo "<td>".$donation_date."</td>";
    echo "<td>".$amount_donated."</td>";
    echo "<td>".$jugular."</td>";
    echo "<td>".$outcome."</td>";
    echo "<td><a href='" . esc_url( admin_url( 'admin.php?page=view_donation&action=view_donation&donation_id='.$donation->id ) ) . "'>View Donation</a></td>";
    echo "<td><a href='" . esc_url( admin_url( 'admin.php?page=edit_donation&action=edit_donation&donation_id=' . $donation->id ) ) . "'>Edit Donation</a></td>";
    echo "</tr>";
  }
	echo "</tbody></table>";
}

////////Lab Meta Box////////
function navbb_donors_build_lab_meta_box( $post ){

  wp_nonce_field( basename( __FILE__ ), 'navbb_donors_lab_nonce' );

  $current_pcv = get_post_meta( $post->ID, '_navbb_donors_pcv', true );

  $current_acquired = get_post_meta( $post->ID, '_navbb_donors_acquired', true );
  $current_received = get_post_meta( $post->ID, '_navbb_donors_received', true );

	$current_cbc = get_post_meta( $post->ID, '_navbb_donors_cbc', true );
	$current_chem = get_post_meta( $post->ID, '_navbb_donors_chem', true );
  $current_lytes = get_post_meta( $post->ID, '_navbb_donors_lytes', true );

  $current_hwt = get_post_meta( $post->ID, '_navbb_donors_hwt', true );
  $current_dea1 = get_post_meta( $post->ID, '_navbb_donors_dea1', true );
  $current_dea4 = get_post_meta( $post->ID, '_navbb_donors_dea4', true );
  $current_dea5 = get_post_meta( $post->ID, '_navbb_donors_dea5', true );
  $current_dea7 = get_post_meta( $post->ID, '_navbb_donors_dea7', true );
  $current_lab_notes = get_post_meta( $post->ID, '_navbb_donors_lab_notes', true );

  $current_antibody = get_post_meta( $post->ID, '_navbb_donors_antibody', true );
  $current_anaplasma = get_post_meta( $post->ID, '_navbb_donors_anaplasma', true );
  $current_bartonella = get_post_meta( $post->ID, '_navbb_donors_bartonella', true );
  $current_babesia = get_post_meta( $post->ID, '_navbb_donors_babesia', true );
  $current_ehrlichia = get_post_meta( $post->ID, '_navbb_donors_ehrlichia', true );
  $current_hepatozoon = get_post_meta( $post->ID, '_navbb_donors_hepatozoon', true );
  $current_leishmania = get_post_meta( $post->ID, '_navbb_donors_leishmania', true );
  $current_neoricketsia = get_post_meta( $post->ID, '_navbb_donors_neoricketsia', true );
  $current_rmsf = get_post_meta( $post->ID, '_navbb_donors_rmsf', true );
  $current_mycoplasma = get_post_meta( $post->ID, '_navbb_donors_mycoplasma', true );
  $current_lyme = get_post_meta( $post->ID, '_navbb_donors_lyme', true );
  $current_brucella = get_post_meta( $post->ID, '_navbb_donors_brucella', true);
  $current_feline_aids = get_post_meta( $post->ID, '_navbb_donors_feline_aids', true);

  //These are used to determine which lab inputs to display on load
  $current_specie = get_post_meta( $post->ID, '_navbb_donors_specie', true );
  $current_feline_bloodtype = get_post_meta( $post->ID, '_navbb_donors_feline_bloodtype', true);
  $current_reproduction = get_post_meta( $post->ID, '_navbb_donors_reproduction', true );

  ?>

  <div class="navbb-row-container">
    <!-- This function is located in meta-box-donors-javascript.js -->
    <button class="button" type="button" style="margin:8px" onclick="labToDefault();">Set All to Default</button>
  </div>

  <div class="navbb-metabox-container navbb-lab-column-row">
    <div class="navbb-column-three">

      <div class="navbb-row-container">
        <label for="pcv" class="navbb-row-title">PCV Value:</label>
        <div class="navbb-row-content">
          <input type="number" name="pcv" id="pcv" class="navbb-row-input" value="<?php echo $current_pcv; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="acquired" class="navbb-row-title">Date Acquired:</label>
        <div class="navbb-row-content">
          <input type="text" name="acquired" class="navbb-row-input custom_date" id="acquired" value="<?php echo $current_acquired; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="received" class="navbb-row-title">Date Results Received:</label>
        <div class="navbb-row-content">
          <input type="text" name="received" class="navbb-row-input custom_date" id="received" value="<?php echo $current_received; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="cbc" class="navbb-row-title">CBC:</label>
        <div class="navbb-row-content">
          <input type="text" name="cbc" class="navbb-row-input" id="cbc" value="<?php echo $current_cbc; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="chem" class="navbb-row-title">CHEM:</label>
        <div class="navbb-row-content">
          <input type="text" name="chem" class="navbb-row-input" id="chem" value="<?php echo $current_chem; ?>" />
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="lytes" class="navbb-row-title">Lytes:</label>
        <div class="navbb-row-content">
          <input type="text" name="lytes" class="navbb-row-input" id="lytes" value="<?php echo $current_lytes; ?>" />
        </div>
      </div>

    <?php if ( $current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="hwt" class="navbb-row-title">HWT:</label>
        <div class="navbb-row-content">
          <select name="hwt" class="navbb-row-input" id="hwt">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_hwt ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_hwt ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

    <?php if ( $current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="dea1" class="navbb-row-title">DEA 1.1</label>
        <div class="navbb-row-content">
          <select name="dea1" class="navbb-row-input" id="dea1">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_dea1  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_dea1  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="dea4" class="navbb-row-title">DEA 1.4</label>
        <div class="navbb-row-content">
          <select name="dea4" class="navbb-row-input" id="dea4">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_dea4  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_dea4  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="dea5" class="navbb-row-title">DEA 1.5</label>
        <div class="navbb-row-content">
          <select name="dea5" class="navbb-row-input" id="dea5">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_dea5  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_dea5  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="dea7" class="navbb-row-title">DEA 1.7</label>
        <div class="navbb-row-content">
          <select name="dea7" class="navbb-row-input" id="dea7">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_dea7  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_dea7  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

    </div>

    <div class="navbb-column-three">

    <?php if ($current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="antibody" class="navbb-row-title">Antibody Screen:</label>
        <div class="navbb-row-content">
          <select name="antibody" class="navbb-row-input" id="antibody">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_antibody  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_antibody  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

      <div class="navbb-row-container">
        <label for="anaplasma" class="navbb-row-title">Anaplasma PCR:</label>
        <div class="navbb-row-content">
          <select name="anaplasma" class="navbb-row-input" id="anaplasma">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_anaplasma  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_anaplasma  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="bartonella" class="navbb-row-title">Bartonella PCR:</label>
        <div class="navbb-row-content">
          <select name="bartonella" class="navbb-row-input" id="bartonella">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_bartonella  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_bartonella  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

    <?php if ($current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="babesia" class="navbb-row-title">Babesia PCR:</label>
        <div class="navbb-row-content">
          <select name="babesia" class="navbb-row-input" id="babesia">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_babesia  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_babesia  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

      <div class="navbb-row-container">
        <label for="ehrlichia" class="navbb-row-title">Ehrlichia PCR:</label>
        <div class="navbb-row-content">
          <select name="ehrlichia" class="navbb-row-input" id="ehrlichia">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_ehrlichia  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_ehrlichia  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

    <?php if ($current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="hepatozoon" class="navbb-row-title">Hepatozoon PCR:</label>
        <div class="navbb-row-content">
          <select name="hepatozoon" class="navbb-row-input" id="hepatozoon">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_hepatozoon  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_hepatozoon  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="leishmania" class="navbb-row-title">Leishmania PCR:</label>
        <div class="navbb-row-content">
          <select name="leishmania" class="navbb-row-input" id="leishmania">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_leishmania  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_leishmania  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="neoricketsia" class="navbb-row-title">Neoricketsia PCR:</label>
        <div class="navbb-row-content">
          <select name="neoricketsia" class="navbb-row-input" id="neoricketsia">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_neoricketsia  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_neoricketsia  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container canine-lab">
        <label for="rmsf" class="navbb-row-title">RMSF PCR:</label>
        <div class="navbb-row-content">
          <select name="rmsf" class="navbb-row-input" id="rmsf">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_rmsf  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_rmsf  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

      <div class="navbb-row-container">
        <label for="mycoplasma" class="navbb-row-title">Mycoplasma PCR:</label>
        <div class="navbb-row-content">
          <select name="mycoplasma" class="navbb-row-input" id="mycoplasma">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_mycoplasma  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_mycoplasma  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

    <?php if ($current_specie == "Canine" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container canine-lab">
        <label for="lyme" class="navbb-row-title">Lyme Quant 6:</label>
        <div class="navbb-row-content">
          <select name="lyme" class="navbb-row-input" id="lyme">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_lyme  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_lyme  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

      <div class="navbb-row-container navbb-reproduction-no" id="row_brucella" <?php if($current_reproduction != "No"): ?> style="display:none;" <?php endif; ?>>
        <label for="brucella" class="navbb-row-title">Brucella:</label>
        <div class="navbb-row-content">
          <select name="brucella" class="navbb-row-input" id="brucella">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_brucella  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_brucella  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>

    <?php if ($current_specie == "Feline" || empty($current_specie) == true ) {?>
      <div class="navbb-row-container feline-lab">
        <label for="feline_aids" class="navbb-row-title">FeLV and FIV:</label>
        <div class="navbb-row-content">
          <select name="feline_aids" class="navbb-row-input" id="feline_aids">
            <option value="" <?php if( empty($current_ownertype) ): ?> selected="selected" <?php endif; ?> disabled hidden>Select Value</option>
            <option value="positive"<?php if( "positive" == $current_feline_aids  ): ?> selected="selected"<?php endif; ?>>Positive</option>
            <option value="negative"<?php if( "negative" == $current_feline_aids  ): ?> selected="selected"<?php endif; ?>>Negative</option>
          </select>
        </div>
      </div>
    <?php } ?>

    </div>

    <div class="navbb-column-three">

      <div>
      <!-- <div class="navbb-row-container"> -->

        <!-- <label for="lab_notes" class="navbb-row-title">Lab Notes:</label> -->
        <label for="lab_notes" class="navbb-row-title" style="width:100px">Lab Notes:</label>
        <div class="navbb-row-content">
          <textarea rows="10"  id="lab_notes" name="lab_notes" class="donor-notes" style="width:300px" ><?php echo $current_lab_notes; ?></textarea>
        </div>

      </div>

    </div>

  </div>
  <?php
}


add_action( 'save_post_navbb_donors', 'navbb_donors_save_lab_meta_boxes_data', 10, 2 );
function navbb_donors_save_lab_meta_boxes_data( $post_id ){
	//Verifies our Nonce variable has not expired
	if ( !isset( $_POST['navbb_donors_lab_nonce'] ) || !wp_verify_nonce( $_POST['navbb_donors_lab_nonce'], basename( __FILE__ ) ) ){
		return;
	}
	//Checks the permission of the current user
	if ( ! current_user_can( 'edit_post', $post_id ) ){
		return;
	}
  if ( isset( $_REQUEST['pcv'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_pcv', sanitize_text_field( $_POST['pcv'] ) );
  }
  if ( isset( $_REQUEST['acquired'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_acquired', sanitize_text_field( $_POST['acquired'] ) );
  }
  if ( isset( $_REQUEST['received'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_received', sanitize_text_field( $_POST['received'] ) );
  }
	if ( isset( $_REQUEST['cbc'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_cbc', sanitize_text_field( $_POST['cbc'] ) );
	}
	if ( isset( $_REQUEST['chem'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_chem', sanitize_text_field( $_POST['chem'] ) );
	}
  if ( isset( $_REQUEST['lytes'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_lytes', sanitize_text_field( $_POST['lytes'] ) );
	}
  if ( isset( $_REQUEST['hwt'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_hwt', sanitize_text_field( $_POST['hwt'] ) );
  }
  if ( isset( $_REQUEST['dea1'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_dea1', sanitize_text_field( $_POST['dea1'] ) );
  }
  if ( isset( $_REQUEST['dea4'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_dea4', sanitize_text_field( $_POST['dea4'] ) );
  }
  if ( isset( $_REQUEST['dea5'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_dea5', sanitize_text_field( $_POST['dea5'] ) );
  }
  if ( isset( $_REQUEST['dea7'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_dea7', sanitize_text_field( $_POST['dea7'] ) );
  }
  if ( isset( $_REQUEST['feline_bloodtype'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_feline_bloodtype', sanitize_text_field( $_POST['feline_bloodtype'] ) );
  }
  if ( isset( $_REQUEST['lab_notes'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_lab_notes', sanitize_textarea_field( $_POST['lab_notes'] ) );
  }
  if ( isset( $_REQUEST['antibody'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_antibody', sanitize_text_field( $_POST['antibody'] ) );
  }
  if ( isset( $_REQUEST['anaplasma'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_anaplasma', sanitize_text_field( $_POST['anaplasma'] ) );
  }
  if ( isset( $_REQUEST['bartonella'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_bartonella', sanitize_text_field( $_POST['bartonella'] ) );
  }
  if ( isset( $_REQUEST['babesia'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_babesia', sanitize_text_field( $_POST['babesia'] ) );
  }
  if ( isset( $_REQUEST['ehrlichia'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_ehrlichia', sanitize_text_field( $_POST['ehrlichia'] ) );
  }
  if ( isset( $_REQUEST['hepatozoon'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_hepatozoon', sanitize_text_field( $_POST['hepatozoon'] ) );
  }
  if ( isset( $_REQUEST['leishmania'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_leishmania', sanitize_text_field( $_POST['leishmania'] ) );
  }
  if ( isset( $_REQUEST['neoricketsia'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_neoricketsia', sanitize_text_field( $_POST['neoricketsia'] ) );
  }
  if ( isset( $_REQUEST['rmsf'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_rmsf', sanitize_text_field( $_POST['rmsf'] ) );
  }
  if ( isset( $_REQUEST['mycoplasma'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_mycoplasma', sanitize_text_field( $_POST['mycoplasma'] ) );
  }
  if ( isset( $_REQUEST['lyme'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_lyme', sanitize_text_field( $_POST['lyme'] ) );
  }
  if ( isset( $_REQUEST['brucella'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_brucella', sanitize_text_field( $_POST['brucella'] ) );
  }
  if ( isset( $_REQUEST['feline_aids'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_feline_aids', sanitize_text_field( $_POST['feline_aids'] ) );
  }
}


////////Physical Exam Meta Box////////
function navbb_donors_build_physical_meta_box( $post ){
  wp_nonce_field( basename( __FILE__ ), 'navbb_donors_physical_nonce' );
  $current_exam_date = get_post_meta( $post->ID, '_navbb_donors_exam_date', true );
  $current_exam_weight = get_post_meta( $post->ID, '_navbb_donors_exam_weight', true );
  $current_exam_temperature = get_post_meta( $post->ID, '_navbb_donors_exam_temperature', true );
  $current_exam_heartrate = get_post_meta( $post->ID, '_navbb_donors_exam_heartrate', true );
  $current_exam_respiration = get_post_meta( $post->ID, '_navbb_donors_exam_respiration', true );
  $current_exam_crt = get_post_meta( $post->ID, '_navbb_donors_exam_crt', true );
  $current_exam_mm = get_post_meta( $post->ID, '_navbb_donors_exam_mm', true );
  $current_physical_notes = get_post_meta( $post->ID, '_navbb_donors_physical_notes', true );
  ?>

  <div class="navbb-metabox-container">
    <div class="navbb-column-left">

      <div class="navbb-row-container">
        <label for="exam_date" class="navbb-row-title">Exam Date:</label>
        <div class="navbb-row-content">
          <input type="text" name="exam_date" id="exam_date" class="custom_date navbb-row-input" value="<?php echo $current_exam_date; ?>">
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="exam_weight" class="navbb-row-title">Weight (kg):</label>
        <div class="navbb-row-content">
          <input type="number" name="exam_weight" id="exam_weight" class="navbb-row-input" value="<?php echo $current_exam_weight; ?>"  pattern="^\d+(?:\.\d{1,2})?$" step="0.1">
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="exam_temperature" class="navbb-row-title">Temperature (deg):</label>
        <div class="navbb-row-content">
          <input type="number" name="exam_temperature" id="exam_temperature" class="navbb-row-input" value="<?php echo $current_exam_temperature; ?>"  pattern="^\d+(?:\.\d{1})?$" step="0.1" >
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="exam_heartrate" class="navbb-row-title">Heart Rate (bpm):</label>
        <div class="navbb-row-content">
          <input type="number" name="exam_heartrate" id="exam_heartrate" class="navbb-row-input" value="<?php echo $current_exam_heartrate; ?>" pattern="^[\d]*$" step="1">
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="exam_respiration" class="navbb-row-title">Respiratory Rate (bpm):</label>
        <div class="navbb-row-content">
          <input type="number" name="exam_respiration" id="exam_respiration" class="navbb-row-input" value="<?php echo $current_exam_respiration; ?>">
        </div>
      </div>

    </div>
    <div class="navbb-column-right">

      <div class="navbb-row-container">
        <label for="exam_crt" class="navbb-row-title">CRT:</label>
        <div class="navbb-row-content">
          <select name="exam_crt" id="exam_crt" class="navbb-row-input">
            <option value="" selected="selected" disabled hidden>Select CRT:</option>
            <option value="0" <?php if( "0" == $current_exam_crt ): ?> selected="selected"<?php endif; ?>>&lt;1 second</option>
            <option value="1" <?php if( "1" == $current_exam_crt ): ?> selected="selected"<?php endif; ?>>1-2 seconds</option>
            <option value="2" <?php if( "2" == $current_exam_crt ): ?> selected="selected"<?php endif; ?>>&gt;2 seconds</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="exam_mm" class="navbb-row-title">MM:</label>
        <div class="navbb-row-content">
          <select name="exam_mm" id="exam_mm" class="navbb-row-input">
            <option value="" selected="selected" disabled hidden>Select Color:</option>
            <option value="pink" <?php if( "pink" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Pink</option>
            <option value="blue" <?php if( "blue" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Blue</option>
            <option value="grey" <?php if( "grey" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Grey</option>
            <option value="white" <?php if( "white" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>White</option>
            <option value="pale" <?php if( "pale" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Pale</option>
            <option value="yellow" <?php if( "yellow" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Yellow</option>
            <option value="pigmented" <?php if( "pigmented" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Pigmented</option>
            <option value="tacky" <?php if( "tacky" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Tacky</option>
            <option value="pale pink" <?php if( "pale pink" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Pale Pink</option>
            <option value="injected" <?php if( "injected" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Injected</option>
            <option value="red" <?php if( "red" == $current_exam_mm ): ?> selected="selected"<?php endif; ?>>Red</option>
          </select>
        </div>
      </div>

      <div class="navbb-row-container">
        <label for="physical_notes" class="navbb-row-title">Doctor Notes:</label>
        <div class="navbb-row-content">
          <textarea rows="10" name="physical_notes" class="donor-notes" id="physical_notes" style="width:300px;"><?php echo $current_physical_notes; ?></textarea>
        </div>
      </div>
    </div>

  </div>
  <?php

}


add_action( 'save_post_navbb_donors', 'navbb_donors_save_physical_meta_boxes_data', 10, 2 );
function navbb_donors_save_physical_meta_boxes_data( $post_id ){
  //Verifies our Nonce variable has not expired
  if ( !isset( $_POST['navbb_donors_physical_nonce'] ) || !wp_verify_nonce( $_POST['navbb_donors_physical_nonce'], basename( __FILE__ ) ) ){
    return;
  }
  //Checks the permission of the current user
  if ( ! current_user_can( 'edit_post', $post_id ) ){
    return;
  }
  if ( isset( $_REQUEST['exam_date'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_date', sanitize_text_field( $_POST['exam_date'] ) );
  }
  if ( isset( $_REQUEST['exam_weight'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_weight', sanitize_text_field( $_POST['exam_weight'] ) );
  }
  if ( isset( $_REQUEST['exam_temperature'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_temperature', sanitize_text_field( $_POST['exam_temperature'] ) );
  }
  if ( isset( $_REQUEST['exam_heartrate'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_heartrate', sanitize_text_field( $_POST['exam_heartrate'] ) );
  }
  if ( isset( $_REQUEST['exam_respiration'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_respiration', sanitize_text_field( $_POST['exam_respiration'] ) );
  }
  if ( isset( $_REQUEST['exam_crt'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_crt', sanitize_text_field( $_POST['exam_crt'] ) );
  }
  if ( isset( $_REQUEST['exam_mm'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_exam_mm', sanitize_text_field( $_POST['exam_mm'] ) );
  }
  if ( isset( $_REQUEST['physical_notes'] ) ) {
    update_post_meta( $post_id, '_navbb_donors_physical_notes', sanitize_textarea_field( $_POST['physical_notes'] ) );
  }
}


function wp_custom_attachment() {
  wp_nonce_field( plugin_basename( __FILE__ ), 'wp_custom_attachment_nonce' );
  $html = '<p class="description">';
  $html .= 'Upload your PDF or Jpeg file here.';
  $html .= '</p>';
  $html .= '<input type="file" id="wp_custom_attachment" name="wp_custom_attachment" value="" size="25" style="padding-bottom:20px;"/>';
  echo $html;
  $filearrays = get_post_meta( get_the_ID(), 'wp_custom_attachment', false );

  foreach ($filearrays as $filearray ) {
    $this_file = $filearray['url'];
    if($this_file != ""){
     $filehtml = "<object data='" . $this_file . "' type='application/pdf' width='600' height='500'>";
     $filehtml .= "<a href='".$this_file."' class='navbb-pdf-list'>".basename($this_file)."</a>";
     $filehtml .=  "</object><br>";
    }
    echo $filehtml;
  }
} // end wp_custom_attachment

add_action('save_post', 'save_custom_meta_data');
function save_custom_meta_data($id) {
  /* --- security verification --- */
  if( !wp_verify_nonce( $_POST['wp_custom_attachment_nonce'], plugin_basename( __FILE__ ) ) ) {
    return $id;
  }
  if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
    return $id;
  }
  if('page' == $_POST['post_type']) {
    if(!current_user_can('edit_page', $id)) {
      return $id;
    }
  } else {
    if(!current_user_can('edit_page', $id)) {
      return $id;
    }
  }
  /* - end security verification - */
  if(!empty($_FILES['wp_custom_attachment']['name'])) {
    $supported_types = array('application/pdf','image/jpeg');

    $arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment']['name']));
    $uploaded_type = $arr_file_type['type'];

    if(in_array($uploaded_type, $supported_types)) {
      $upload = wp_upload_bits($_FILES['wp_custom_attachment']['name'], null, file_get_contents($_FILES['wp_custom_attachment']['tmp_name']));
      if(isset($upload['error']) && $upload['error'] != 0) {
        wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
      } else {
        add_post_meta($id, 'wp_custom_attachment', $upload);
        //update_post_meta($id, 'wp_custom_attachment', $upload);
      }
    }
    else {
      wp_die("The file type that you've uploaded is not a PDF or Jpeg.");
    }
  }
}

//This is necessary to allow file uploads in the post editor screen
add_action('post_edit_form_tag', 'update_edit_form');
function update_edit_form() {
  echo ' enctype="multipart/form-data"';
} // end update_edit_form


//This adds admin notices on donor pages. For example, if the lab work has expired, show a notice at the top of the page
add_action( 'admin_notices', 'my_error_notice' );
function my_error_notice() {
  global $pagenow;
  if ( $pagenow == 'post.php' ) {
    if( get_post_type() == 'navbb_donors') {
      $post_id = get_the_ID();
      $today = date("Y-m-d");
      $date_acquired = get_post_meta( $post_id, '_navbb_donors_acquired', true );
      if(date('Y-m-d', strtotime($date_acquired . ' + 335 days')) < $today) {
        echo '<div class="notice error is-dismissible">
               <p>The lab work for this donor is either expiring soon or has expired already.</p>
              </div>';
      }
    }
  }

}
