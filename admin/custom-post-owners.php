<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Add the pet owners as a custom post type
add_action('init','navbb_owners_post_type');
function navbb_owners_post_type() {
  $labels = array(
    'name'               => _x( 'Owners', 'post type general name', 'navbb' ),
    'singular_name'      => _x( 'Owner', 'post type singular name', 'navbb' ),
    'add_new_item'       => _X( 'Add New Owner', '', 'navbb'),
    'menu_name'          => _x( 'Owners', 'admin menu', 'navbb' ),
    'name_admin_bar'     => _x( 'Owners', 'add new on admin bar', 'navbb' ),
  );
  $args = array(
		'labels'             => $labels,
		'public'             => false,
		'publicly_queryable' => false,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'owner' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => 31,
		'supports'           => array( 'title', 'thumbnail' ),
    'menu_icon'          => 'dashicons-universal-access-alt'
	);
  register_post_type('navbb_owners', $args);
}


//This function changes the placeholder in the title box in add new owner
add_filter( 'enter_title_here', 'navbb_owners_change_title_text' );
function navbb_owners_change_title_text( $title ){
  $screen = get_current_screen();
    if  ( 'navbb_owners' == $screen->post_type ) {
      $title = "Enter Owner's Last Name";
    }
  return $title;
}


//This is a filter hook that adds Donor information to the All Owners table
//This adds the headers for the table
add_filter('manage_navbb_owners_posts_columns', 'navbb_owners_table_head');
function navbb_owners_table_head( $defaults ) {
	$defaults['title'] = 'Last Name';
  $defaults['first_name']  = 'First Name';
	$defaults['internalOwnerID']  = 'Owner ID';
  $defaults['location'] = 'Location';
  $defaults['email']    = 'Email';
  $defaults['phone_number']   = 'Phone Number';
	unset($defaults['date']);  //This removes the date published column
  return $defaults;
}


//This function adds our custom column to the list of sortable columns for our post type
add_filter( 'manage_edit-navbb_owners_sortable_columns', 'navbb_owners_sortable_columns');
function navbb_owners_sortable_columns( $columns ) {
	$columns['internalOwnerID'] = 'Owner ID';
  $columns['first_name']  = 'First Name';
  $columns['location'] = 'Location';
  return $columns;
}


//This queries the post_meta database and populates the table
add_action( 'manage_navbb_owners_posts_custom_column', 'navbb_owners_table_content', 10, 2 );
function navbb_owners_table_content( $column_name, $post_id ) {
	if ($column_name == 'first_name') {
		$status = get_post_meta( $post_id, '_navbb_owners_first_name', true );
		echo $status;
	}
	if ($column_name == 'internalOwnerID') {
		$status = get_post_meta( $post_id, '_navbb_owners_internalOwnerID', true );
		echo $status;
	}
  if ($column_name == 'location') {
    $status = get_post_meta( $post_id, '_navbb_owners_donation_location', true );
    echo $status;
  }
	if ($column_name == 'email') {
		$status = get_post_meta( $post_id, '_navbb_owners_email', true );
		echo $status;
	}
	if ($column_name == 'phone_number') {
		echo get_post_meta( $post_id, '_navbb_owners_phone_number', true );
	}
}


//Set the Query by which we sort the posts
add_action( 'pre_get_posts', 'navbb_owners_orderby' );
function navbb_owners_orderby( $query ) {
  if( ! is_admin() || ! $query->is_main_query() ) {
    return;
  }
	if ( 'Owner ID' === $query->get( 'orderby') ) {
		$query->set( 'orderby', 'meta_value' );
		$query->set( 'meta_key', '_navbb_owners_internalOwnerID' );
	}
  if ( 'First Name' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', '_navbb_owners_first_name' );
  }
  if ( 'Location' === $query->get( 'orderby') ) {
    $query->set( 'orderby', 'meta_value' );
    $query->set( 'meta_key', '_navbb_owners_donation_location' );
  }
}

add_action('bulk_edit_custom_box',  'navbb_quick_edit_owner_fields', 10, 2);
add_action('quick_edit_custom_box',  'navbb_quick_edit_owner_fields', 10, 2);
function navbb_quick_edit_owner_fields( $column_name, $post_type ) {

	switch( $column_name ) :
		case 'location': {
			// you can also print Nonce here, do not do it ouside the switch() because it will be printed many times
			wp_nonce_field( 'navbb_q_edit_owner_nonce', 'navbb_nonce' );
			echo '<fieldset class="inline-edit-col-right">
				<div class="inline-edit-col">';
      echo '<div class="inline-edit-group wp-clearfix">
						<label class="alignleft">
							<span class="title">Location</span>
							<span class="input-text-wrap">';
      echo( select_locations( option_locations( 'Select Location' ,"" ), 'location', 'navbb-row-input' ) );
			echo '</span>
						</label>
					</div>';
  		echo '</div></fieldset>';
			break;
		}
	endswitch;
}


add_action( 'save_post', 'navbb_quick_edit_owner_save' );
function navbb_quick_edit_owner_save( $post_id ){
	// check user capabilities
	if ( !current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	// check nonce
	if ( !wp_verify_nonce( $_POST['navbb_nonce'], 'navbb_q_edit_owner_nonce' ) ) {
		return;
	}
	// update the location
	if ( isset( $_POST['location'] ) ) {
		update_post_meta( $post_id, '_navbb_owners_donation_location', $_POST['location'] );
	}
}
