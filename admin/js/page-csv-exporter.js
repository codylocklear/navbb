////**** Javascript for our Query Builder and Report page ****////
jQuery(document).ready(function($) {

  //Attach ajaxSubmit to the Submit Button located on the Query Builder Page
  $('#Custom-Query').submit(ajaxSubmit);

  //This is for our CSV exporter button, making a SQL request to the server
  $('#navbb_donations_to_csv').click(function(){
    var adminURL = navbb_csv_exporter_WPURLS.adminUrl + 'admin-ajax.php';
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=handle_donations_to_csv_action",
      success: function( response ){
        if(response == 0){
          console.log("Error! Only site admin can perform this operation");
        } else if(response == 1) {
          console.log("Can't get database columns info.");
        } else if (response == 2){
          console.log("No Table Selected");
        } else {
          var today = new Date();
          var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
          const data = response;
          const fileName = "Donations-" + date + ".csv";
          const a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          const blob = new Blob([data], {type: "octet/stream"}), url = window.URL.createObjectURL(blob);
          a.href = url;
          a.download = fileName;
          a.click();
          window.URL.revokeObjectURL(url);
        }
      }
    });
  });

  $('#navbb_donors_to_csv').click(function(){
    var adminURL = navbb_csv_exporter_WPURLS.adminUrl + 'admin-ajax.php';
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=handle_donors_to_csv_action",
      success: function( response ){
        if(response == 0){
          console.log("Error! Only site admin can perform this operation");
        } else if(response == 1) {
          console.log("Can't get database columns info.");
        } else if (response == 2){
          console.log("No Table Selected");
        } else {
          var today = new Date();
          var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
          const data = response;
          const fileName = "Donors-" + date + ".csv";
          const a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          const blob = new Blob([data], {type: "octet/stream"}), url = window.URL.createObjectURL(blob);
          a.href = url;
          a.download = fileName;
          a.click();
          window.URL.revokeObjectURL(url);
        }
      }
    });
  });

  $('#navbb_owners_to_csv').click(function(){
    var adminURL = navbb_csv_exporter_WPURLS.adminUrl + 'admin-ajax.php';
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=handle_owners_to_csv_action",
      success: function( response ){
        if(response == 0){
          console.log("Error! Only site admin can perform this operation");
        } else if(response == 1) {
          console.log("Can't get database columns info.");
        } else if (response == 2){
          console.log("No Table Selected");
        } else {
          var today = new Date();
          var date = today.getFullYear()+'-'+(today.getMonth()+1)+'-'+today.getDate();
          const data = response;
          const fileName = "Owners-" + date + ".csv";
          const a = document.createElement("a");
          document.body.appendChild(a);
          a.style = "display: none";
          const blob = new Blob([data], {type: "octet/stream"}), url = window.URL.createObjectURL(blob);
          a.href = url;
          a.download = fileName;
          a.click();
          window.URL.revokeObjectURL(url);
        }
      }
    });
  });

});


function ajaxSubmit(){
  var adminURL = navbb_csv_exporter_WPURLS.adminUrl + 'admin-ajax.php';

  jQuery(document).ready(function($) {
    var customquery = jQuery('#Custom-Query').serialize();
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: customquery ,
      success: function( response ){
        //console.log(response);
        if(response == 0){
          alert("Either Query Failed or there are no eligible donors at this time.")
        } else {
          $("#variable_query_table_body").empty();
          for(var i = 0; i < response.length; i++) {
            var obj = response[i];
            var newstring = '<tr><td>' + obj['donor_id'] +
            '</td><td>' + obj['first_name'] +
            '</td><td>' + obj['blood-type'] + '</td><td>' +
            '<a href="' + adminURL + 'post.php?post=' + obj['owner_id'] + '&action=edit" target="_blank">' + obj['owner_first_name'] + '</a>' +
            '</td></tr>';
            $('#variable_query_table > tbody:last-child').append(newstring);
          }
        }
      }
    });
  })
  return false;
}





/*
jQuery('select[name="specie"]').change(function(){
  var e = document.getElementById("specie");
  var strSpecie = e.options[e.selectedIndex].value;
  if(strSpecie == "feline"){
    document.getElementById("Canine Positive").setAttribute("hidden", "true");
    document.getElementById("Canine Negative").setAttribute("hidden", "true");
    document.getElementById("Canine Positive").removeAttribute("selected", "true");
    document.getElementById("Canine Negative").removeAttribute("selected", "true");
    document.getElementById("Feline A").setAttribute("selected", "true");
    document.getElementById("Feline A").removeAttribute("hidden");
    document.getElementById("Feline B").removeAttribute("hidden");
  } else {
    document.getElementById("Feline A").setAttribute("hidden", "true");
    document.getElementById("Feline B").setAttribute("hidden", "true");
    document.getElementById("Feline A").removeAttribute("selected", "true");
    document.getElementById("Feline B").removeAttribute("selected", "true");
    document.getElementById("Canine Positive").setAttribute("selected", "true");
    document.getElementById("Canine Positive").removeAttribute("hidden");
    document.getElementById("Canine Negative").removeAttribute("hidden");
  }
});
*/

//Obsolete code, however the php handler is still located in ajax callbacks
function server_test_javascript() {
  jQuery(document).ready(function($) {
    var data = {'action': 'server_test',
                'whatever': 1234};
    //var temp = 'http://localhost/mysite/wp-admin/admin-ajax.php';
    var adminURL = '<?php echo admin_url("admin-ajax.php"); ?>';
    // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
    jQuery.post(adminURL, data, function(response) {
        $("#test_empty").empty();
      for(var i = 0; i < response.length; i++) {
        var obj = response[i];
        var newstring = '<tr><td>' + obj['donor_id'] + '</td><td>' + obj['first_name'] + '</td><td>' + obj['blood-type'] + '</td><td>' + obj['owner_first_name'] + '</td></tr>';
        $('#query_table > tbody:last-child').append(newstring);
      }
      document.getElementById("demo").innerHTML = "Paragraph changed!";
    });
  })
}
