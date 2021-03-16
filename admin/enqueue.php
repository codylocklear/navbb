<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


//Enqueues Date Picker Functionality on Date input fields, including both JS and CSS
add_action( 'admin_enqueue_scripts', 'navbb_enqueue_admin_js_and_css' );
function navbb_enqueue_admin_js_and_css() {

	///***Use these two function to determine your page name for conditional enqueueing***///
	// $screen = get_current_screen();
	// print_r($screen);

	//CSS FILES
	wp_register_style( 'admin', plugins_url('css/admin.css',__FILE__ ), array(), '1.0.4', 'all' );
	wp_enqueue_style( 'admin' );

	//This sets the spacing for input forms in metaboxes
	wp_register_style( 'navbb-metabox', plugins_url('css/navbb-metabox.css',__FILE__ ), array(), '1.0.4', 'all' );
	wp_enqueue_style( 'navbb-metabox' );

	//Datatables and General CSS Style for Tables
	wp_register_style( 'navbb-tables', plugins_url('css/navbb-tables.css',__FILE__ ), array(), '1.0.2', 'all' );
	wp_enqueue_style( 'navbb-tables' );

	//JQuery UI style
	wp_register_style( 'jquery-ui', 'https://code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css', array(), '', 'all' );
	wp_enqueue_style( 'jquery-ui' );

	///***JAVASCRIPT functionality and styling***///

	//Date Picker Javascript functionality on forms
	wp_enqueue_script( 'jquery-ui-datepicker' );
	wp_enqueue_script( 'navbb-custom-date', plugins_url('js/navbb-custom-date.js',__FILE__ ), array(), '1.0.0', 'all' );

	//This is all for the autocomplete functions on text fields
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'jquery-ui-core' );
  wp_enqueue_script( 'jquery-ui-autocomplete' );

	wp_enqueue_script('navbb-autocomplete-js', plugins_url('js/navbb-autocomplete.js',__FILE__ ), array(), '1.0.0', 'all' );


	//wp_enqueue_script('navbb-autocomplete-js', plugins_url('js/navbb-autocomplete.js',__FILE__ ), array('jquery','jquery-ui-autocomplete'), rand(111,9999));
	wp_localize_script('navbb-autocomplete-js', 'WPURLS', array( 'siteurl' => get_option('siteurl') )); //This provides the siteurl as a variable to the script

	//DataTables
	wp_register_script( 'dataTables-js', 'https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.js' , '', '', true );
  wp_register_style( 'dataTables-css', 'https://cdn.datatables.net/v/dt/dt-1.10.18/datatables.min.css', array(), '1.0.0', 'all');
  wp_enqueue_script( 'dataTables-js' );
	wp_enqueue_style( 'dataTables-css' );

}
