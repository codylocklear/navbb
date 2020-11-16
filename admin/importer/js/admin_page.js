jQuery(document).ready(function ($) {


  // Click "Table Preview" button each time page is loaded
  $('#repop_table_ajax').trigger('click');

  // Disable 'disable auto-increment' button until needed
  $('#remove_autoinc_column').prop('disabled', true);

  // Click to hide error/success messages
  $('.error_message, .success_message, .info_message_dismiss').click(function () {
  	$(this).fadeOut("slow", function () {
  	});
  });

// ******* Begin 'Select Table' dropdown change function ******* //
  $('#table_select').change(function () {  // Get column count and load table

  	// Begin ajax loading image
  	$('#table_preview').html('<img src="' + wp_csv_to_db_pass_js_vars.ajax_image + '" />');

  	// Clear 'disable auto_inc' checkbox
  	$('#remove_autoinc_column').prop('checked', false);

  	// Get new table name from dropdown
  	sel_val = $('#table_select').val();

  	// Setup ajax variable
  	var data = {
	    action: 'wp_csv_to_db_get_columns',
	    sel_val: sel_val
		    //disable_autoinc: disable_autoinc
  	};

  	// Run ajax request
  	$.post(wp_csv_to_db_pass_js_vars.ajaxurl, data, function (response) {

      // Populate Table Preview HTML from response
      $('#table_preview').html(response.content);

      // Determine if column has an auto_inc value.. and enable/disable the checkbox accordingly
      if (response.enable_auto_inc_option == 'true') {
  	    $("#remove_autoinc_column").prop('disabled', false);
      }
      if (response.enable_auto_inc_option == 'false') {
  	    $("#remove_autoinc_column").prop('disabled', true);
      }


      // Get column count from ajax table and populate hidden div for form submission comparison
      var colCount = 0;
      $('#ajax_table tr:nth-child(1) td').each(function () {  // Array of table td elements
  	    if ($(this).attr('colspan')) {  // If the td element contains a 'colspan' attribute
  	      colCount += +$(this).attr('colspan');  // Count the 'colspan' attributes
  	    } else {
  	      colCount++;  // Else count single columns
  	    }
      });

      // Populate #num_cols hidden input with number of columns
      $('#num_cols').val(colCount);
  	});


  });
    // ******* End 'Select Table' dropdown change function ******* //



  // ******* Begin 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //
  $('#repop_table_ajax, #remove_autoinc_column').click(function () {  // Reload Table

  	// Begin ajax loading image
  	$('#table_preview').html('<img src="' + wp_csv_to_db_pass_js_vars.ajax_image + '" />');

  	// Get value of disable auto-increment column checkbox
  	if ($('#remove_autoinc_column').is(':checked')) {
      disable_autoinc = 'true';
  	} else {
      disable_autoinc = 'false';
  	}
  	// Get new table name from dropdown
  	sel_val = $('#table_select').val();

  	// Setup ajax variable
  	var data = {
      action: 'wp_csv_to_db_get_columns',
      sel_val: sel_val,
      disable_autoinc: disable_autoinc
  	};

  	// Run ajax request
  	$.post(wp_csv_to_db_pass_js_vars.ajaxurl, data, function (response) {

      // Populate Table Preview HTML from response
      $('#table_preview').html(response.content);

      // Determine if column has an auto_inc value.. and enable/disable the checkbox accordingly
      if (response.enable_auto_inc_option == 'true') {
  		  $("#remove_autoinc_column").prop('disabled', false);
  	  }
      if (response.enable_auto_inc_option == 'false') {
  	    $("#remove_autoinc_column").prop('disabled', true);
      }

      // Get column count from ajax table and populate hidden div for form submission comparison
      var colCount = 0;
      $('#ajax_table tr:nth-child(1) td').each(function () {  // Array of table td elements
  		  if ($(this).attr('colspan')) {  // If the td element contains a 'colspan' attribute
  		    colCount += +$(this).attr('colspan');  // Count the 'colspan' attributes
  		  } else {
  		    colCount++;  // Else count single columns
  		  }
  	  });

      // Populate #num_cols hidden input with number of columns
      $('#num_cols').val(colCount);

      // Re-populate column count value
      remove_auto_col_val = $('#column_count').html('<strong>' + colCount + '</strong>');
  	});
  });
    // ******* End 'Reload Table Preview' button AND 'Disable auto-increment Column' checkbox click function ******* //


}); //End Jquery Document Function
