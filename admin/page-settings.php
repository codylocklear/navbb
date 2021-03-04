<?php

//Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class NAVBB_Settings {

  public function __construct() {
    // Hook into the admin menu
    add_action( 'admin_menu', array( $this, 'create_plugin_settings_page' ) );
    add_action( 'admin_init', array( $this, 'setup_sections' ) );
    add_action( 'admin_init', array( $this, 'setup_fields' ) );
  }

  public function create_plugin_settings_page() {
    //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $slug, $callback );
    add_submenu_page( 'navbb', 'Settings', 'Settings', 'manage_options', 'drops_settings', array( $this, 'plugin_settings_page_content' ) );
  }

  public function plugin_settings_page_content() {
    ?>
    <div class="wrap">
      <h2>DROPS Settings Page</h2>
      <form method="post" action="options.php">
        <?php
          settings_fields( 'drops_settings' );
          do_settings_sections( 'drops_settings' );
          submit_button();
        ?>
      </form>
    </div>
    <?php

  }

  // This Sets up our different sections which all share the same callback function
  public function section_callback( $arguments ) {
    switch( $arguments['id'] ){
      case 'our_first_section':
        echo 'Settings for fields available on Owner and Donor pages.';
        break;
      case 'our_second_section':
        echo 'This one is number two';
        break;
      case 'our_third_section':
        echo 'Third time is the charm!';
        break;
    }
  }

  public function setup_sections() {
    //register_setting( 'drops_settings', 'our_first_field' );
    add_settings_section( 'our_first_section', 'Donor and Owner Fields', array( $this, 'section_callback' ), 'drops_settings' );
    //add_settings_section( 'our_second_section', 'My Second Section Title', array( $this, 'section_callback' ), 'drops_settings' );
    //add_settings_section( 'our_third_section', 'My Third Section Title', array( $this, 'section_callback' ), 'drops_settings' );
  }

  public function setup_fields() {
    $fields = array(
      array(
        'uid' => 'drops_owner_locations',
        'label' => 'Donation Locations',
        'section' => 'our_first_section',
        'type' => 'textarea',
        'options' => false,
        'placeholder' => 'Type Each Location',
        'helper' => '',
        'supplemental' => 'Type each location separated by a comma',
        'default' => 'Bristow, Not Applicable'
      )
    );

    foreach( $fields as $field ){
      add_settings_field( $field['uid'], $field['label'], array( $this, 'field_callback' ), 'drops_settings', $field['section'], $field );
      register_setting( 'drops_settings', $field['uid'] );
    }

  }

  public function field_callback( $arguments ) {
    $value = get_option( $arguments['uid'] ); // Get the current value, if there is one
    if( ! $value ) { // If no value exists
      $value = $arguments['default']; // Set to our default
    }

    // Check which type of field we want
    switch( $arguments['type'] ){
      case 'text': // If it is a text field
        printf( '<input name="%1$s" id="%1$s" type="%2$s" placeholder="%3$s" value="%4$s" />', $arguments['uid'], $arguments['type'], $arguments['placeholder'], $value );
        break;
      case 'textarea': // If it is a textarea
        printf( '<textarea name="%1$s" id="%1$s" placeholder="%2$s" rows="5" cols="50">%3$s</textarea>', $arguments['uid'], $arguments['placeholder'], $value );
        break;
      case 'select': // If it is a select dropdown
        if( ! empty ( $arguments['options'] ) && is_array( $arguments['options'] ) ){
          $options_markup = '';
          foreach( $arguments['options'] as $key => $label ){
            $options_markup .= sprintf( '<option value="%s" %s>%s</option>', $key, selected( $value, $key, false ), $label );
          }
          printf( '<select name="%1$s" id="%1$s">%2$s</select>', $arguments['uid'], $options_markup );
        }
        break;
    }

    // If there is help text
    if( $helper = $arguments['helper'] ){
      printf( '<span class="helper"> %s</span>', $helper ); // Show it
    }

    // If there is supplemental text
    if( $supplemental = $arguments['supplemental'] ){
      printf( '<p class="description">%s</p>', $supplemental ); // Show it
    }
  }

}
new NAVBB_Settings();
