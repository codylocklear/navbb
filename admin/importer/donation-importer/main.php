<?php

/**
 * Plugin Name: WP CSV TO DB
 * Plugin URI: https://www.tipsandtricks-hq.com/wp-csv-to-database-plugin-import-excel-file-content-into-wordpress-database-2116
 * Description: Import CSV file content directly into your WordPress database table.
 * Version: 2.6
 * Author: Tips and Tricks HQ, josh401
 * Author URI: https://www.tipsandtricks-hq.com
 * License: GPL2
 */

class wp_csv_to_db {

  // Setup options variables
  protected $option_name = 'wp_csv_to_db';  // Name of the options array
  protected $data	= array( // Default options values
    'jq_theme' => 'smoothness'
  );

  public function __construct() {
	  // Check if is admin, we can later update this to include other user roles
    if ( is_admin() ) {
      add_action( 'admin_menu', array( $this, 'wp_csv_to_db_register' ) );  // Create admin menu page
      add_action( 'admin_init', array( $this, 'wp_csv_to_db_settings' ) ); // Create settings
    }
  }

  public function wp_csv_to_db_activate() {
    update_option( $this->option_name, $this->data );
  }

  public function wp_csv_to_db_register() {
    $wp_csv_to_db_page = add_submenu_page( 'navbb', __( 'CSV Donation Importer', 'wp_csv_to_db' ), __( 'CSV Donation Import', 'wp_csv_to_db' ), 'manage_options', 'wp_csv_to_db_menu_page', array( $this, 'wp_csv_to_db_menu_page' ) ); // Add submenu page to "Settings"
    add_action( 'admin_print_scripts-' . $wp_csv_to_db_page, array( $this, 'wp_csv_to_db_admin_scripts' ) );  // Load our admin page scripts (our page only)
    add_action( 'admin_print_styles-' . $wp_csv_to_db_page, array( $this, 'wp_csv_to_db_admin_styles' ) );  // Load our admin page stylesheet (our page only)
  }

  public function wp_csv_to_db_settings() {
	  register_setting( 'wp_csv_to_db_options', $this->option_name, array( $this, 'wp_csv_to_db_validate' ) );
  }

  public function wp_csv_to_db_validate( $input ) {
    $valid = array();
    $valid[ 'jq_theme' ] = $input[ 'jq_theme' ];
    return $valid;
  }

  public function wp_csv_to_db_admin_scripts() {
    wp_enqueue_script( 'jquery-ui-dialog' );  // For admin panel popup alerts
    wp_enqueue_script( 'wp_csv_to_db', plugins_url( '/js/admin_page.js', __FILE__ ), array( 'jquery' ) );  // Apply admin page scripts
    wp_localize_script( 'wp_csv_to_db', 'wp_csv_to_db_pass_js_vars', array( 'ajax_image' => plugin_dir_url( __FILE__ ) . 'images/loading.gif', 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
  }

  public function wp_csv_to_db_admin_styles() {
    wp_enqueue_style( 'sdm_admin_styles', plugins_url( '/css/admin_page.css', __FILE__ ) );  // Apply admin page styles
    // Get option for jQuery theme
    $options	 = get_option( $this->option_name );
    $select_theme	 = isset( $options[ 'jq_theme' ] ) ? $options[ 'jq_theme' ] : 'smoothness';
    ?><link rel="stylesheet" href="//code.jquery.com/ui/1.10.3/themes/<?php echo $select_theme; ?>/jquery-ui.css"><?php
    // For jquery ui styling - Direct from jquery
  }


  public function wp_csv_to_db_menu_page() {

    if ( ! current_user_can( 'manage_options' ) ) {
      wp_die( 'Error! Only site admin can perform this operation' );
    }

  	// Set variables
  	global $wpdb;
  	$error_message		 = '';
  	$success_message	 = '';
  	$message_info_style	 = '';
    $prefix = $wpdb->prefix;


  	// If button is pressed to "Import to DB"
  	if ( isset( $_POST[ 'execute_button' ] ) ) {

      // If the "Select Table" input field is empty
      if ( empty( $_POST[ 'table_select' ] ) ) {
  		  $error_message .= '* ' . __( 'No Database Table was selected. Please select a Database Table.', 'wp_csv_to_db' ) . '<br />';
      }

      if ( empty( $_FILES['csv_file']['name'] ) ) {
        $error_message .= '* ' . __( 'No Input File was selected. Please enter an Input File.', 'wp_csv_to_db' ) . '<br />';
      }

      $ext = pathinfo( $_FILES[ 'csv_file' ]['name'], PATHINFO_EXTENSION );
      if ( $ext !== 'csv' ) {
        $error_message .= '* ' . __( 'The Input File does not contain the .csv file extension. Please choose a valid .csv file.', 'wp_csv_to_db' );
      }

      // If all fields are input; and file is correct .csv format; continue
      if ( ! empty( $_POST[ 'table_select' ] ) && ! empty( $_FILES[ 'csv_file' ][ 'name' ] ) && ($ext === 'csv') ) {

    		// If "disable auto_inc" is checked.. we need to skip the first column of the returned array (or the column will be duplicated)
    		if ( isset( $_POST[ 'remove_autoinc_column' ] ) ) {
  		    $db_cols = $wpdb->get_col( "DESC " . $_POST[ 'table_select' ], 0 );
  		    unset( $db_cols[ 0 ] );  // Remove first element of array (auto increment column)
    		} else {
      	// Else we just grab all columns
      	  $db_cols = $wpdb->get_col( "DESC " . $_POST[ 'table_select' ], 0 );  // Array of db column names
      	}

    		// Get the number of columns from the hidden input field (re-auto-populated via jquery)
    		$numColumns = $_POST[ 'num_cols' ];

        //Open the .csv file and get it's contents
        $upload = wp_upload_bits($_FILES['csv_file']['name'], null, file_get_contents($_FILES['csv_file']['tmp_name']));
        $myCSV = $upload['url'];


    		if ( ( $fh = @fopen( $myCSV, 'r' )) !== false ) {

    	    // Set variables
    	    $values	= array();
    	    $too_many = '';  // Used to alert users if columns do not match

    	    while ( ( $row = fgetcsv( $fh )) !== false ) {  // Get file contents and set up row array
      			if ( count( $row ) == $numColumns ) {  // If .csv column count matches db column count
      			  $row = array_map( function($v) {
                return esc_sql( $v );
              }, $row );
      			  $values[] = '("' . implode( '", "', $row ) . '")';  // Each new line of .csv file becomes an array
      			}
    	    }

  		    //If user elects to input a starting row for the .csv file
  		    if ( isset( $_POST[ 'sel_start_row' ] ) && ( ! empty( $_POST[ 'sel_start_row' ] )) ) {
      			// Get row number from user
      			$num_var = $_POST[ 'sel_start_row' ] - 1;  // Subtract one to make counting easy on the non-techie folk!  (1 is actually 0 in binary)
      			// If user input number exceeds available .csv rows
      			if ( ( $num_var > count( $values ) ) && ( ! empty( $values ) ) ) {
    			    $error_message	 .= '* ' . __( 'Starting Row value exceeds the number of entries being updated to the database from the .csv file.' , 'wp_csv_to_db' ) . '<br />';
    			    $too_many	 = 'true';  // set alert variable
      			} else {
      			// Else splice array and remove number (rows) user selected
    			    $values = array_slice( $values, $num_var );
      			}
  		    }

  		    // If there are no rows in the .csv file AND the user DID NOT input more rows than available from the .csv file
  		    if ( empty( $values ) && ($too_many !== 'true') ) {
  			    $error_message	 .= '* ' . __( 'Columns do not match.', 'wp_csv_to_db' ) . '<br />';
  			    $error_message	 .= '* ' . __( 'The number of columns in the database for this table does not match the number of columns attempting to be imported from the .csv file.', 'wp_csv_to_db' ) . '<br />';
  			    $error_message	 .= '* ' . __( 'Please verify the number of columns attempting to be imported in the "Select Input File" exactly matches the number of columns displayed in the "Table Preview".', 'wp_csv_to_db' ) . '<br />';
  		    } else {
      			// If the user DID NOT input more rows than are available from the .csv file
      			if ( $too_many !== 'true' ) {

    			    $db_query_update = '';
    			    $db_query_insert = '';

    			    // Format $db_cols to a string
    			    $db_cols_implode = implode( ',', $db_cols );

    			    // Format $values to a string
    			    $values_implode = implode( ',', $values );


    			    //If "Update DB Rows" was checked
    			    if ( isset( $_POST[ 'update_db' ] ) ) {
        				// Setup sql 'on duplicate update' loop
        				$updateOnDuplicate = ' ON DUPLICATE KEY UPDATE ';
        				foreach ( $db_cols as $db_col ) {
        				  $updateOnDuplicate .= "$db_col=VALUES($db_col),";
        				}
        				$updateOnDuplicate = rtrim( $updateOnDuplicate, ',' );
        				$sql = 'INSERT INTO ' . $_POST[ 'table_select' ] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode . $updateOnDuplicate;
        				$db_query_update = $wpdb->query( $sql );
    			    } else {
        				$sql = 'INSERT INTO ' . $_POST[ 'table_select' ] . ' (' . $db_cols_implode . ') ' . 'VALUES ' . $values_implode;
        				$db_query_insert = $wpdb->query( $sql );
    			    }

    			    // If db db_query_update is successful
    			    if ( $db_query_update ) {
    				    $success_message = __( 'Congratulations!  The database has been updated successfully.', 'wp_csv_to_db' );
    			    }
    			    // If db db_query_insert is successful
    			    elseif ( $db_query_insert ) {
        				$success_message = __( 'Congratulations!  The database has been updated successfully.', 'wp_csv_to_db' );
        				$success_message .= '<br /><strong>' . count( $values ) . '</strong> ' . __( 'record(s) were inserted into the', 'wp_csv_to_db' ) . ' <strong>' . $_POST[ 'table_select' ] . '</strong> ' . __( 'database table.', 'wp_csv_to_db' );
    			    }
    			    // If db db_query_insert is successful AND there were no rows to udpate
    			    elseif ( ($db_query_update === 0) && ($db_query_insert === '') ) {
    				    $message_info_style .= '* ' . __( 'There were no rows to update. All .csv values already exist in the database.', 'wp_csv_to_db' ) . '<br />';
    			    } else {
        				$error_message	 .= '* ' . __( 'There was a problem with the database query.', 'wp_csv_to_db' ) . '<br />';
        				$error_message	 .= '* ' . __( 'A duplicate entry was found in the database for a .csv file entry.', 'wp_csv_to_db' ) . '<br />';
        				$error_message	 .= '* ' . __( 'If necessary; please use the option below to "Update Database Rows".', 'wp_csv_to_db' ) . '<br />';
    			    }
    			  }
    		  }
    		} else {
    		  $error_message .= '* ' . __( 'No valid .csv file was found at the specified url. Please check the "Select Input File" field and ensure it points to a valid .csv file.', 'wp_csv_to_db' ) . '<br />';
    		}
  	  }
  	}

  	// If there is a message - info-style
  	if ( ! empty( $message_info_style ) ) {
      echo '<div class="info_message_dismiss">';
      echo $message_info_style;
      echo '<br /><em>(' . __( 'click to dismiss', 'wp_csv_to_db' ) . ')</em>';
      echo '</div>';
  	}

  	// If there is an error message
  	if ( ! empty( $error_message ) ) {
      echo '<div class="error_message">';
      echo $error_message;
      echo '<br /><em>(' . __( 'click to dismiss', 'wp_csv_to_db' ) . ')</em>';
      echo '</div>';
  	}

  	// If there is a success message
  	if ( ! empty( $success_message ) ) {
      echo '<div class="success_message">';
      echo $success_message;
      echo '<br /><em>(' . __( 'click to dismiss', 'wp_csv_to_db' ) . ')</em>';
      echo '</div>';
  	}
  	?>

    <div class="wrap">

      <style>
        div.wpcsvdb-settings-grid {
          display: inline-block;
        }
        div.wpcsvdb-main-cont {
          width: 80%;
        }
        div.wpcsvdb-sidebar-cont {
          width: 19%;
          float: right;
        }
        div#poststuff {
          min-width: 19%;
        }
        .wpcsvdb-stars-container {
          text-align: center;
          margin-top: 10px;
        }
        .wpcsvdb-stars-container span {
          vertical-align: text-top;
          color: #ffb900;
        }
        .wpcsvdb-stars-container a {
          text-decoration: none;
        }
        @media (max-width: 782px) {
          div.wpcsvdb-settings-grid {
              display: block;
              float: none;
              width: 100%;
          }
        }
      </style>

      <h2><?php _e( 'WordPress CSV to Database Options', 'wp_csv_to_db' ); ?></h2>


      <p>This plugin allows you to insert CSV file data into your WordPress database table.</p>

      <div>
        <p>
          <h4>Templates:</h4>
          <a href="<?php echo plugins_url( '/uploads/bloodbank_donation.csv', __FILE__ );  ?>">New Donations Template</a><br>
          <a href="<?php echo plugins_url( '/uploads/bloodbank_donation_update.csv', __FILE__ );  ?>">Update Donations Template</a>
        </p>
      </div>

      <div>
        <p>
          <h4>Instructions:</h4>
          Import New Donations:
          <ol>
            <li>Select the donations database.</li>
            <li>Upload a csv file that uses the new donations template.</li>
            <li>Leave the starting row at 2.</li>
            <li>Select the Disable "auto_increment" Column option.</li>
            <li>Do not select the Update Existing Donations option.</li>
          </ol>
          <br>
          Update Existing Donations:
          <ol>
            <li>Select the donations database.</li>
            <li>Upload a csv file that uses the update donations template.</li>
            <li>Leave the starting row at 2.</li>
            <li>Do not select the Disable "auto_increment" Column option.</li>
            <li>Select the Update Existing Donations option.</li>
          </ol>
        </p>
      </div>


      <div class="wpcsvdb-settings-grid wpcsvdb-main-cont">
    		<form id="wp_csv_to_db_form" method="post" action="" enctype="multipart/form-data">
    			<table class="form-table">
    			  <tr valign="top"><th scope="row"><?php _e( 'Select Database Table:', 'wp_csv_to_db' ); ?></th>
    				  <td>
    				    <select id="table_select" name="table_select" value="">
    					    <option name="" value=""></option>
                  <option name="wp_navbb_donations" value="<?php echo $prefix ?>navbb_donations"><?php echo $prefix ?>navbb_donations</option>
        					<?php
        					// Get all db table names
        					// global $wpdb;
        					// $sql = "SHOW TABLES";
        					// $results = $wpdb->get_results( $sql );
        					// $repop_table = isset( $_POST[ 'table_select' ] ) ? $_POST[ 'table_select' ] : null;
        					// foreach ( $results as $index => $value ) {
        					//   foreach ( $value as $tableName ) {
                  //     echo "<option name='" . $tableName . "' value='" . $tableName . "'";
          				// 		if ( $repop_table === $tableName ) {
          				// 		  echo 'selected="selected"';
          				// 		}
                  //     echo ">" . $tableName . "</option>";
        					// 	}
      						// }
      						?>
    				    </select>
    				  </td>
    			  </tr>
    			  <tr valign="top"><th scope="row"><?php _e( 'Select Input File:', 'wp_csv_to_db' ); ?></th>
    				  <td>
    				    <?php //$repop_file	 = isset( $_POST[ 'csv_file' ] ) ? $_POST[ 'csv_file' ] : null; ?>
    				    <?php $repop_csv_cols	 = isset( $_POST[ 'num_cols_csv_file' ] ) ? $_POST[ 'num_cols_csv_file' ] : '0'; ?>
                <?php
                  $html = '<p class="description">';
                  $html .= 'Upload your CSV here.';
                  $html .= '</p>';
                  $html .= '<input type="file" id="csv_file" name="csv_file" value="" size="25" style="padding-bottom:20px;"/>';
                  echo $html;
                ?>
    				    <input id="num_cols" name="num_cols" type="hidden" value="" />
    				    <input id="num_cols_csv_file" name="num_cols_csv_file" type="hidden" value="" />
    				    <br><?php _e( 'File must end with a .csv extension.', 'wp_csv_to_db' ); ?>
    				    <br><span id="return_csv_col_count"></span>
    				  </td>
      	    </tr>
      	    <tr valign="top"><th scope="row"><?php _e( 'Select Starting Row:', 'wp_csv_to_db' ); ?></th>
      				<td>
        		    <?php
                $repop_row = 2 ;
                //$repop_row = isset( $_POST[ 'sel_start_row' ] ) ? $_POST[ 'sel_start_row' ] : null;
                ?>
        		    <input id="sel_start_row" name="sel_start_row" type="text" size="10" value="<?php echo $repop_row; ?>" />
        		    <br><?php _e( 'Defaults to row 2. If your file has no headers, enter 1 as the starting value.', 'wp_csv_to_db' ); ?>
      				</td>
    			  </tr>
    			  <tr valign="top"><th scope="row"><?php _e( 'Disable "auto_increment" Column:', 'wp_csv_to_db' ); ?></th>
    				  <td>
    				    <input id="remove_autoinc_column" name="remove_autoinc_column" type="checkbox" />
    				    <br><?php _e( 'Bypasses the "auto_increment" column;', 'wp_csv_to_db' ); ?>
    				    <br><?php _e( 'This will reduce (for the purposes of importation) the number of DB columns by "1".', 'wp_csv_to_db' ); ?>
    				  </td>
    			  </tr>
    			  <tr valign="top"><th scope="row"><?php _e( 'Update Database Rows:', 'wp_csv_to_db' ); ?></th>
    				  <td>
    				    <input id="update_db" name="update_db" type="checkbox" />
    				    <br><?php _e( 'Will update exisiting database rows when a duplicated primary key is encountered.', 'wp_csv_to_db' ); ?>
    				    <br><?php _e( 'Defaults to all rows inserted as new rows.', 'wp_csv_to_db' ); ?>
    				  </td>
    			  </tr>
    			</table>

    			<p class="submit">
    			  <input id="execute_button" name="execute_button" type="submit" class="button-primary" value="<?php _e( 'Import to DB', 'wp_csv_to_db' ) ?>" />
    			</p>
    		</form>
      </div> <!-- End .wpcsvdb-main-cont -->
    </div> <!-- End page wrap -->

  	<h3><?php _e( 'Table Preview:', 'wp_csv_to_db' ); ?><input id="repop_table_ajax" name="repop_table_ajax" value="<?php _e( 'Reload Table Preview', 'wp_csv_to_db' ); ?>" type="button" style="margin-left:20px;" /></h3>

  	<div id="table_preview">
  	</div>

  	<p><?php _e( 'After selecting a database table from the dropdown above; the table column names will be shown.', 'wp_csv_to_db' ); ?>
      <br><?php _e( 'This may be used as a reference when verifying the .csv file is formatted properly.', 'wp_csv_to_db' ); ?>
      <br><?php _e( 'If an "auto-increment" column exists; it will be rendered in the color "red".', 'wp_csv_to_db' ); ?>
    </p>
  	<!-- Alert invalid .csv file - jquery dialog -->
  	<div id="dialog_csv_file" title="<?php _e( 'Invalid File Extension', 'wp_csv_to_db' ); ?>" style="display:none;">
  	    <p><?php _e( 'This is not a valid .csv file extension.', 'wp_csv_to_db' ); ?></p>
  	</div>

  	<!-- Alert select db table - jquery dialog -->
    <div id="dialog_select_db" title="<?php _e( 'Database Table not Selected', 'wp_csv_to_db' ); ?>" style="display:none;">
      <p><?php _e( 'First, please select a database table from the dropdown list.', 'wp_csv_to_db' ); ?></p>
    </div>

    <?php
  }
}
//end of class

$wp_csv_to_db = new wp_csv_to_db();

//  Ajax call for showing table column names
add_action( 'wp_ajax_wp_csv_to_db_get_columns', 'wp_csv_to_db_get_columns_callback' );
function wp_csv_to_db_get_columns_callback() {

  // Set variables
  global $wpdb;
  $sel_val		 = isset( $_POST[ 'sel_val' ] ) ? $_POST[ 'sel_val' ] : null;
  $disable_autoinc	 = isset( $_POST[ 'disable_autoinc' ] ) ? $_POST[ 'disable_autoinc' ] : 'false';
  $enable_auto_inc_option	 = 'false';
  $content		 = '';

  // Ran when the table name is changed from the dropdown
  if ( $sel_val ) {

    // Get table name
    $table_name = $sel_val;

    // Setup sql query to get all column names based on table name
    $sql = 'SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = "' . $wpdb->dbname . '" AND TABLE_NAME ="' . $table_name . '" AND EXTRA like "%auto_increment%"';

    // Execute Query
    $run_qry = $wpdb->get_results( $sql );

    //
    // Begin response content
    $content .= '<table id="ajax_table"><tr>';

    // If the db query contains an auto_increment column
    if ( (isset( $run_qry[ 0 ]->EXTRA )) && (isset( $run_qry[ 0 ]->COLUMN_NAME )) ) {
      //$content .= 'auto: '.$run_qry[0]->EXTRA.'<br />';
      //$content .= 'column: '.$run_qry[0]->COLUMN_NAME.'<br />';
      // If user DID NOT check 'disable_autoinc'; we need to add that column back with unique formatting
      if ( $disable_autoinc === 'false' ) {
        $content .= '<td class="auto_inc"><strong>' . $run_qry[ 0 ]->COLUMN_NAME . '</strong></td>';
      }

      // Get all column names from database for selected table
      $column_names	 = $wpdb->get_col( 'DESC ' . $table_name, 0 );
      $counter	 = 0;

      //
      // IMPORTANT - If the db results contain an auto_increment; we remove the first column below; because we already added it above.
      foreach ( $column_names as $column_name ) {
        if ( $counter ++ < 1 )
          continue;  // Skip first iteration since 'auto_increment' table data cell will be duplicated
        $content .= '<td><strong>' . $column_name . '</strong></td>';
      }
    }
    // Else get all column names from database (unfiltered)
    else {
      $column_names = $wpdb->get_col( 'DESC ' . $table_name, 0 );
      foreach ( $column_names as $column_name ) {
        $content .= '<td><strong>' . $column_name . '</strong></td>';
      }
    }
  	$content .= '</tr></table><br />';
  	$content .= __( 'Number of Database Columns:', 'wp_csv_to_db' ) . ' <span id="column_count"><strong>' . count( $column_names ) . '</strong></span><br />';

  	// If there is an auto_increment column in the returned results
  	if ( (isset( $run_qry[ 0 ]->EXTRA )) && (isset( $run_qry[ 0 ]->COLUMN_NAME )) ) {
      // If user DID NOT click the auto_increment checkbox
      if ( $disable_autoinc === 'false' ) {
    		$content .= '<div class="warning_message">';
    		$content .= __( 'This table contains an "auto increment" column.', 'wp_csv_to_db' ) . '<br />';
    		$content .= __( 'Please be sure to use unique values in this column from the .csv file.', 'wp_csv_to_db' ) . '<br />';
    		$content .= __( 'Alternatively, the "auto increment" column may be bypassed by clicking the checkbox above.', 'wp_csv_to_db' ) . '<br />';
    		$content .= '</div>';

    		// Send additional response
    		$enable_auto_inc_option = 'true';
      }
      // If the user clicked the auto_increment checkbox
      if ( $disable_autoinc === 'true' ) {
    		$content .= '<div class="info_message">';
    		$content .= __( 'This table contains an "auto increment" column that has been removed via the checkbox above.', 'wp_csv_to_db' ) . '<br />';
    		$content .= __( 'This means all new .csv entries will be given a unique "auto incremented" value when imported (typically, a numerical value).', 'wp_csv_to_db' ) . '<br />';
    		$content .= __( 'The Column Name of the removed column is', 'wp_csv_to_db' ) . ' <strong><em>' . $run_qry[ 0 ]->COLUMN_NAME . '</em></strong>.<br />';
    		$content .= '</div>';

    		// Send additional response
    		$enable_auto_inc_option = 'true';
      }
  	}
  } else {
  	$content = '';
  	$content .= '<table id="ajax_table"><tr><td>';
  	$content .= __( 'No Database Table Selected.', 'wp_csv_to_db' );
  	$content .= '<br />';
  	$content .= __( 'Please select a database table from the dropdown box above.', 'wp_csv_to_db' );
  	$content .= '</td></tr></table>';
  }

  // Set response variable to be returned to jquery
  $response = json_encode( array( 'content' => $content, 'enable_auto_inc_option' => $enable_auto_inc_option ) );
  header( "Content-Type: application/json" );
  echo $response;
  die();
}

// Ajax call to process .csv file for column count
//add_action( 'wp_ajax_wp_csv_to_db_get_csv_cols', 'wp_csv_to_db_get_csv_cols_callback' );
function wp_csv_to_db_get_csv_cols_callback() {
  // Get file upload url
  $file_upload_url = $_POST[ 'file_upload_url' ];

  // Open the .csv file and get it's contents
  if ( ( $fh = @fopen( $_POST[ 'file_upload_url' ], 'r' )) !== false ) {

  	// Set variables
  	$values = array();

  	// Assign .csv rows to array
  	while ( ( $row = fgetcsv( $fh )) !== false ) {  // Get file contents and set up row array
	    //$values[] = '("' . implode('", "', $row) . '")';  // Each new line of .csv file becomes an array
	    $rows[] = array( implode( '", "', $row ) );
  	}

  	// Get a single array from the multi-array... and process it to count the individual columns
  	$first_array_elm = reset( $rows );
  	$xplode_string	 = explode( ", ", $first_array_elm[ 0 ] );

  	// Count array entries
  	$column_count = count( $xplode_string );
  } else {
	  $column_count = 'There was an error extracting data from the.csv file. Please ensure the file is a proper .csv format.';
  }

  // Set response variable to be returned to jquery
  $response = json_encode( array( 'column_count' => $column_count ) );
  header( "Content-Type: application/json" );
  echo $response;
  die();
}

// Add plugin settings link to plugins page
//add_filter( 'plugin_action_links', 'wp_csv_to_db_plugin_action_links', 10, 4 );
function wp_csv_to_db_plugin_action_links( $links, $file ) {
    $plugin_file = 'wp_csv_to_db/main.php';
    if ( $file == $plugin_file ) {
	     $settings_link = '<a href="' .
	      admin_url( 'options-general.php?page=wp_csv_to_db_menu_page' ) . '">' .
	      __( 'Settings', 'wp_csv_to_db' ) . '</a>';
	      array_unshift( $links, $settings_link );
    }
    return $links;
}

// Load plugin language localization
add_action( 'plugins_loaded', 'wp_csv_to_db_lang_init' );
function wp_csv_to_db_lang_init() {
    load_plugin_textdomain( 'wp_csv_to_db', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}
