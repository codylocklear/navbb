////**** Javascript for our Donations pages ****////

//Form Submit and Process
jQuery(document).ready(function($) {
	// Store form state at page load
	var initial_form_state = $('#myform').serialize();

	// Store form state after form submit
	$('#myform').submit(function(){
	  initial_form_state = $('#myform').serialize();
	});

	// Check form changes before leaving the page and warn user if needed
	$(window).bind('beforeunload', function(e) {
	  var form_state = $('#myform').serialize();
	  if(initial_form_state != form_state){
	    var message = "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
	    e.returnValue = message; // Cross-browser compatibility (src: MDN)
	    return message;
	  }
	});

	//This fills in our previous donation if we passed a donor id to the new donation page
	if($('#donor_id').val()	) {  //only calls the function if we have a donor id
		donorDonationChange(); //this calls the function below on load
	}

});

//Ajax request to fill in previous donation information after donor select in new donation page
function donorDonationChange(val){
	var donor_id = document.getElementById("donor_id").value;
  var adminURL = navbb_WPURLS.adminUrl + 'admin-ajax.php';

	jQuery(document).ready(function($) {
		jQuery.ajax({
			method:'POST',
			url: adminURL,
			data: "action=get_last_donation&donor_id="+ donor_id,
			success: function( response ){
				if(response == 0){
					console.log("The database query did not return any previous donations.");
				} else {
					document.getElementById("previous_donation_date").innerHTML = response['donation_date'];
					document.getElementById("previous_amount_potential").innerHTML = response['amount_potential'];
					document.getElementById("previous_amount_donated").innerHTML = response['amount_donated'];
					document.getElementById("previous_recumbency").innerHTML = response['recumbency'];
					document.getElementById("previous_vein").innerHTML = response['vein'];
					document.getElementById("previous_crt").innerHTML = response['crt'];
					document.getElementById("previous_mm").innerHTML = response['mm'];
          document.getElementById("previous_collections").innerHTML = response['collections'];
					document.getElementById("previous_weight").innerHTML = response['weight'];
					document.getElementById("previous_temperature").innerHTML = response['temperature'];
					document.getElementById("previous_heartrate").innerHTML = response['heartrate'];
					document.getElementById("previous_respiration").innerHTML = response['respiration'];
					document.getElementById("previous_pcv").innerHTML = response['pcv'];
					document.getElementById("previous_ts").innerHTML = response['ts'];
					document.getElementById("previous_holder").innerHTML = response['holder'];
					document.getElementById("previous_poker").innerHTML = response['poker'];
					document.getElementById("previous_donation_notes").innerHTML = response['donation_notes'];
				}
			}
		});
	})
}

// Submit button clicked: changes submit button to display please wait
function checkForm(form) {
	form.mySubmit.disabled = true;
	form.mySubmit.value = "Please wait...";
	return true;
}


jQuery(document).ready( function ($) {
	//Loads Datatables on our donations page
	$('#donation_table').DataTable({
				//"info":     false
				"aaSorting": [[3,'desc'], [2,'asc']],
				"pageLength": 25,
				"columns": [   //This disables the sort function on specified columns
											null,
											null,
											null,
											null,
											null,
											{ "orderable": false },
											{ "orderable": false }
										]
			}
	);
} );



function confirm_alert(node) {
		return confirm("Are you sure you want to delete this product?");
}

//Delete Function on Individual Donations Pages
jQuery(document).ready(function($) {
	var adminURL = '<?php echo admin_url(); ?>';
	var temp = adminURL + "admin-ajax.php";
	$('.deletemyajax').on( 'click', function( event ){
	//do something when link with class deletemyajax is clicked
		event.preventDefault();
		if(confirm_alert()){
			var product_id = $(this).closest('tr').find('.product_id').text();
			var productid = $(this).closest('tr').attr('id');

			jQuery.ajax({
				method:'POST',
				url: temp,
				data: "action=deleteProduct&product_id="+ product_id,
				success: function( response ){
					if(response == 0){
						alert("Error, Product Not Deleted");
					} else {
						alert("Product Deleted");
						// Remove row from HTML Table
						$('#'+productid).css('background','tomato');
						$('#'+productid).fadeOut(800,function(){
							 $(this).remove();
						});
					}
				}
			});
		} else {
			return;
		}
	});
})
