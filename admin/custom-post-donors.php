<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Add the animal donors as a custom post type
add_action('init','navbb_donors_post_type');
function navbb_donors_post_type() {
  $labels = array(
    'name'               => _x( 'Donors', 'post type general name', 'navbb' ),
    'singular_name'      => _x( 'Donor', 'post type singular name', 'navbb' ),
		'add_new'						 => _x( 'Add New Donor', '', 'navbb'),
    'add_new_item'       => _X( 'Add New Donor', '', 'navbb'),
		'edit_item'					 => _x( 'Edit Donor Information', '', 'navbb'),
		'new_item'	 				 => _x( 'New Donor', '', 'navbb'),
		'view_item'					 => _x( 'View Donor', '', 'navbb'),
		'view_items'				 => _x( 'View Donors', '', 'navbb'),
		'search_items'			 => _x( 'Search Donors', '', 'navbb'),
		'attributes' 				 => _x( 'Donor Information', '', 'navbb'),
    'menu_name'          => _x( 'Donors', 'admin menu', 'navbb' ),
    'name_admin_bar'     => _x( 'Donors', 'add new on admin bar', 'navbb' ),
  );
  $args = array(
		'labels'             => $labels,
		'public'             => false,
		'exclude_from search'=> true,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'donor' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => false,
		'menu_position'      => 32,
		'supports'           => array( 'title', 'thumbnail' ),
    'menu_icon'          => 'dashicons-buddicons-activity'
	);
  register_post_type('navbb_donors', $args);
}


//This is a filter hook that adds Donor information to the All Donors table
//This adds the headers for the table
add_filter('manage_navbb_donors_posts_columns', 'navbb_donors_table_head');
function navbb_donors_table_head( $defaults ) {
	$defaults['title'] = 'Name';
	$defaults['owner'] = 'Owner';
	$defaults['internalDonorID'] = 'Donor ID';
  $defaults['status'] = 'Status';
  $defaults['age'] = 'Age';
	$defaults['bloodtype'] = 'Bloodtype';
	unset($defaults['date']);  //This removes the date published column
  return $defaults;
}


//This queries the post_meta database and populates the table
add_action( 'manage_navbb_donors_posts_custom_column', 'navbb_donors_table_content', 10, 2 );
function navbb_donors_table_content( $column_name, $post_id ) {
  if ($column_name == 'owner') {
    $owner_id = get_post_meta( $post_id, '_navbb_donors_owner_id', true );
    $owner_name = get_owner_fullname( $owner_id, true );
    echo $owner_name ;
  }
	if ($column_name == 'internalDonorID') {
    $fullDonorID = get_full_donorID( $post_id );
    echo $fullDonorID;
	}
  if ($column_name == 'status') {
    echo get_donor_status( get_post_meta( $post_id, '_navbb_donors_status', true ) );
  }
  if ($column_name == 'age') {
    echo get_donor_age( get_post_meta( $post_id, '_navbb_donors_age', true ) );
  }
	if ($column_name == 'bloodtype') {
		echo get_post_meta( $post_id, '_navbb_donors_bloodtype', true );
	}
}


//This function changes the placeholder in the title box in add new donor
add_filter( 'enter_title_here', 'navbb_donors_change_title_text' );
function navbb_donors_change_title_text( $title ){
  $screen = get_current_screen();
  if  ( 'navbb_donors' == $screen->post_type ) {
    $title = 'Enter Donor First Name';
  }
  return $title;
}


//This function adds our custom column to the list of sortable columns for our post type
add_filter( 'manage_edit-navbb_donors_sortable_columns', 'navbb_donors_sortable_columns');
function navbb_donors_sortable_columns( $columns ) {
	$columns['owner'] = 'Owner';
  $columns['status']  = 'Status';
  $columns['age']  = 'Age';
  $columns['bloodtype']  = 'Bloodtype';
  return $columns;
}


add_action( 'pre_get_posts', 'navbb_donors_orderby' );
function navbb_donors_orderby( $query ) {
  if( ! is_admin() || ! $query->is_main_query() ) {
    return;
  }
	if ( 'Owner' === $query->get( 'orderby') ) {
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', '_navbb_donors_owner_id' );
		$query->set( 'meta_type', 'numeric' );
	}
  if ( 'Status' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', '_navbb_donors_status' );
    $query->set( 'meta_type', 'char' );
  }
	if ( 'Age' === $query->get( 'orderby') ) {
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', '_navbb_donors_age' );
		//$query->set( 'meta_type', 'char' );
	}
	if ( 'Bloodtype' === $query->get( 'orderby') ) {
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', '_navbb_donors_bloodtype' );
		$query->set( 'meta_type', 'char' );
	}
}


/*
 * quick_edit_custom_box allows to add HTML in Quick Edit
 * Please note: it files for EACH column, so it is similar to manage_posts_custom_column
 */
add_action('bulk_edit_custom_box',  'navbb_quick_edit_donor_fields', 10, 2);
add_action('quick_edit_custom_box',  'navbb_quick_edit_donor_fields', 10, 2);
function navbb_quick_edit_donor_fields( $column_name, $post_type ) {

	switch( $column_name ) :
		case 'owner': {

			// you can also print Nonce here, do not do it ouside the switch() because it will be printed many times
			wp_nonce_field( 'navbb_q_edit_donor_nonce', 'navbb_nonce' );

			// please note: the <fieldset> classes could be:
			// inline-edit-col-left, inline-edit-col-center, inline-edit-col-right
			// each class for each column, all columns are float:left,
			// so, if you want a left column, use clear:both element before
			// the best way to use classes here is to look in browser "inspect element" at the other fields

			// for the FIRST column only, it opens <fieldset> element, all our fields will be there
			echo '<fieldset class="inline-edit-col-right">
				<div class="inline-edit-col">';

			echo '
					<div class="inline-edit-group wp-clearfix">
						<label class="alignleft">
							<span class="title">Owner</span>
							<span class="input-text-wrap">
							<input type="text" name="owner" id="quick_edit_owners" class="autocomplete_owners" value="">
		          <input type="hidden" name="owner_id" id="owner_id" value="">
							</span>
						</label>
					</div>
				';

			break;

		}		case 'status': {

			echo '<div class="inline-edit-group wp-clearfix">
						<label class="alignleft">
							<span class="title">Donor Status</span>
							<span class="input-text-wrap">
				        <select name="status">
									<option value="-1">— No Change —</option>
				          <option value="active">Active</option>
				          <option value="pending">Pending</option>
				          <option value="retired">Retired</option>
				          <option value="not accepted">Not Accepted</option>
				        </select>
							</span>
						</label>
					</div>';

			break;

		}

		case 'bloodtype': {

		echo '
			<div class="inline-edit-group wp-clearfix">
				<label class="alignleft">
					<span class="title">Bloodtype</span>
					<span class="input-text-wrap">
						<select name="bloodtype">
							<option value="-1">— No Change —</option>
							<option value="DEA 1.1 Negative">DEA 1.1 Negative</option>
	            <option value="DEA 1.1 Positive">DEA 1.1 Positive</option>
          	</select>
					</span>
				</label>
		';

			// for the LAST column only - closing the fieldset element
			echo '</div></div></fieldset>';

			break;

		}
	endswitch;
}

/*
 * Quick Edit Save
 */
add_action( 'save_post', 'navbb_quick_edit_donor_save' );
function navbb_quick_edit_donor_save( $post_id ){
	// check user capabilities
	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	// check nonce
	if ( !wp_verify_nonce( $_POST['navbb_nonce'], 'navbb_q_edit_donor_nonce' ) ) {
		return;
	}
	// update the owner
	if ( isset( $_POST['owner_id'] ) && empty( $_POST['owner_id'] ) == false ) {
		update_post_meta( $post_id, '_navbb_donors_owner_id', $_POST['owner_id'] );
	}
	// update the gender
	if ( isset( $_POST['status'] ) ) {
 		update_post_meta( $post_id, '_navbb_donors_status', $_POST['status'] );
	}
	// update checkbox
	if ( isset( $_POST['bloodtype'] ) ) {
		update_post_meta( $post_id, '_navbb_donors_bloodtype', $_POST['bloodtype'] );
	}
}


add_action( 'admin_enqueue_scripts', 'navbb_enqueue_quick_edit_population' );
function navbb_enqueue_quick_edit_population( $pagehook ) {
	// do nothing if we are not on the target pages
	if ( 'edit.php' != $pagehook ) {
		return;
	}
	wp_enqueue_script( 'populatequickedit', plugins_url('js/post-edit-populate.js',__FILE__ ), array( 'jquery' , 'jquery-ui-autocomplete' ) , '1.0.2');
}
