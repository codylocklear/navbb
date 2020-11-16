<?php
/*
 * Plugin Name: DROPS
 * Plugin URI:
 * Description: This plugin provides a database for the NAVBB
 * Version: 1.0.3
 * Author: Cody Locklear
 * Author URI:
 * License: GPL v2+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: NAVBB
 * Domain Path: /languages
 */

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// if admin area
if ( is_admin() ) {

	//include dependencies
	require_once plugin_dir_path( __FILE__ ) . 'admin/page-dashboard.php';        							//displays html

	require_once plugin_dir_path( __FILE__ ) . 'admin/donation/page-donations.php';        			//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/donation/page-donations-new.php';        	//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/donation/page-donations-individual.php'; 	//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/donation/page-donations-edit.php'; 				//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/donation/donation-form-process.php';			//Currently processes new donation form, edit donation form

	require_once plugin_dir_path( __FILE__ ) . 'admin/products/page-products-new.php'; 					//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/products/page-products-edit.php'; 				//displays html


	require_once plugin_dir_path( __FILE__ ) . 'admin/page-csv-exporter.php';    							//displays html

	require_once plugin_dir_path( __FILE__ ) . 'admin/custom-post-owners.php';									//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/custom-post-donors.php';									//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/meta-box-owners.php';											//displays html
	require_once plugin_dir_path( __FILE__ ) . 'admin/meta-box-donors.php';											//displays html


	//require_once plugin_dir_path( __FILE__ ) . 'rs-csv-importer-master/rs-csv-importer.php';  	//located in tools
	//require_once plugin_dir_path( __FILE__ ) . 'wp-csv-to-database/main.php';  									//located in settings
	//require_once plugin_dir_path( __FILE__ ) . 'navbb-donations-import/navbb-donations-importer.php';



	require_once plugin_dir_path( __FILE__ ) . 'admin/ajax-callbacks.php';											//All functions for ajax processing
	require_once plugin_dir_path( __FILE__ ) . 'admin/products/product-form-process.php';				//Currently processes new donation form, edit donation form
	require_once plugin_dir_path( __FILE__ ) . 'admin/enqueue.php';															//Enqueues all of our Javascript and Style sheets
	require_once plugin_dir_path( __FILE__ ) . 'admin/class-admin.php';        									//displays html

}

	require_once plugin_dir_path( __FILE__ ) . 'includes/core-functions.php';


// add top-level administrative menu
add_action( 'admin_menu', 'navbb_add_toplevel_menu' );
function navbb_add_toplevel_menu() {
	add_menu_page('NAVBB Dashboard', 'NAVBB', 'edit_posts', 'navbb', 'navbb_display_dashboard_page', 'dashicons-id-alt', 30);
}


add_action( 'admin_menu', 'navbb_add_dashboardlevel_menus' );
function navbb_add_dashboardlevel_menus() {

// **** MISC Pages **** //

	add_submenu_page('navbb', 'Dashboard', 'Dashboard', 'edit_posts', 'navbb', 'navbb_display_dashboard_page');

// **** DONATION PAGES **** //

	//All Donations Page, code in page-donations.php
	add_submenu_page('navbb', 'Donations', 'View Donations', 'edit_posts', 'donations', 'navbb_display_donations_page');

	//New Donation page, code in page-donations-new.php
	add_submenu_page('navbb', 'New Donation', 'New Donation', 'edit_posts', 'new_donation', 'navbb_display_new_donation_page');

	//Individual Donation page, code in page-donations-individual.php
	add_action( 'admin_action_view_donation', 'navbb_donation_render_individual_donation_page' );
  add_submenu_page('donations', 'View Donation', 'Hidden!', 'edit_posts', 'view_donation', 'navbb_donation_render_individual_donation_page');

	//Edit Donation page, code in page-donations-edit.php
	add_action( 'admin_action_edit_donation', 'navbb_donation_render_edit_donation_page' );
	add_submenu_page('donations', 'Edit Donation', 'Hidden!', 'edit_posts', 'edit_donation', 'navbb_donation_render_edit_donation_page');


// **** PRODUCTS PAGES **** //

	//add_action( 'admin_action_add_products', 'navbb_donation_render_add_products_page' );
	add_submenu_page('donations', 'Add Products', 'Hidden!', 'edit_posts', 'add_products', 'navbb_donation_render_add_products_page');
	add_submenu_page('donations', 'Edit Products', 'Hidden!', 'edit_posts', 'edit_products', 'navbb_donation_render_edit_products_page');

	add_submenu_page('navbb', 'CSV Exporter', 'CSV Exporter', 'manage_options', 'csv_exporter', 'navbb_display_csv_exporter_page');

}
	//This adds our csv importer. needs to be here as it has a dependency on the functions above
	require_once plugin_dir_path( __FILE__ ) . 'admin/importer/main.php';
