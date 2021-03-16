//Used for Quick Edits of posts, specifically for donors and owners
jQuery(document).ready(function($) {
  // it is a copy of the inline edit function
  var wp_inline_edit_function = inlineEditPost.edit;

  // we overwrite the it with our own
  inlineEditPost.edit = function( post_id ) {

    // let's merge arguments of the original function
    wp_inline_edit_function.apply( this, arguments );

    // get the post ID from the argument
    var id = 0;
    if ( typeof( post_id ) == 'object' ) { // if it is object, get the ID number
      id = parseInt( this.getId( post_id ) );
    }

    //if post id exists
    if ( id > 0 ) {

      // add rows to variables
      var specific_post_edit_row = $( '#edit-' + id ),
          specific_post_row = $( '#post-' + id ),
          donor_owner = $( '.column-owner', specific_post_row ).text(),
          donor_status = $( '.column-status', specific_post_row ).text().toLowerCase(), //  remove $ sign
          donor_bloodtype = $( '.column-bloodtype', specific_post_row ).text(); //  remove $ sign
          owner_location = $( '.column-location', specific_post_row ).text();

      // populate the inputs with column data
      $( ':input[name="owner"]', specific_post_edit_row ).val( donor_owner );
      $( ':input[name="status"]', specific_post_edit_row ).val( donor_status );
      $( ':input[name="bloodtype"]', specific_post_edit_row ).val( donor_bloodtype );
      $( ':input[name="location"]', specific_post_edit_row ).val( owner_location );
    }
  }
});


jQuery(function($){
	$( 'body' ).on( 'click', 'input[name="bulk_edit"]', function() {

		// let's add the WordPress default spinner just before the button
		$( this ).after('<span class="spinner is-active"></span>');


		// define: prices, featured products and the bulk edit table row
		var bulk_edit_row = $( 'tr#bulk-edit' ),
		    post_ids = new Array(),
        donor_owner = bulk_edit_row.find( 'input[name="owner_id"]' ).val(),
        donor_status = bulk_edit_row.find( 'select[name="status"]' ).val(),
        donor_bloodtype = bulk_edit_row.find( 'select[name="bloodtype"]' ).val();

		// now we have to obtain the post IDs selected for bulk edit
		bulk_edit_row.find( '#bulk-titles' ).children().each( function() {
			post_ids.push( $( this ).attr( 'id' ).replace( /^(ttle)/i, '' ) );
		});

    // console.log(post_ids);
    // console.log(donor_owner);
    // console.log(donor_status);
    // console.log(donor_bloodtype)

		// save the data with AJAX
		$.ajax({
			url: ajaxurl, // WordPress has already defined the AJAX url for us (at least in admin area)
			type: 'POST',
			async: false,
			cache: false,
			data: {
				action: 'misha_save_bulk', // wp_ajax action hook
				post_ids: post_ids, // array of post IDs
        owner: donor_owner, // new owner id
				status: donor_status, // new donor status
				bloodtype: donor_bloodtype, // new donor bloodtype
				nonce: $('#misha_nonce').val() // I take the nonce from hidden #misha_nonce field
			}
		});
	});
});
