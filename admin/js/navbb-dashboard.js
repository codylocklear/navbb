//All of our javascript necessary for our dashboard

jQuery(document).ready(function($) {
  //Initialize all datatables
  $('#navbb_active_donor_table').DataTable({
        "columns": [
          { "data": "first_name" },
          { "data": "bloodtype" },
          { "data": "owner" },
          { "data": "location" },
          { "data": "date_last_donated" }
        ],
        "order": [[ 4, "asc" ]],
        "createdRow": function ( row, data, index ) {
            if (data['date_last_donated'] == 'Never Donated' ) {
              $('td', row).eq(4).addClass('navbb-dataTables-green');
            } else {
              var today = new Date( );
              var lastdonationdate = new Date(data['date_last_donated']);
              var difference_In_Time = today.getTime() - lastdonationdate.getTime();
              var difference_In_Days = difference_In_Time / (1000 * 3600 * 24);
              if (difference_In_Days >= 30) {
                $('td', row).eq(4).addClass('navbb-dataTables-green');
              } else if (difference_In_Days < 26) {
                $('td', row).eq(4).addClass('navbb-dataTables-red');
              } else {
                $('td', row).eq(4).addClass('navbb-dataTables-yellow');
              }
            }
        },
        "orderClasses": false
      }
  );
  $('#lab_table').DataTable({
        "columns": [
          { "data": "first_name" },
          { "data": "owner" },
          { "data": "next_donation_date" },
          { "data": "lab_expire_date" }
        ],
        "order": [[ 3, "asc" ]],
      }
  );
});


function updateDashboard(){

  var location = document.getElementById('navbb-dashboard-location').value;
  var adminURL = navbb_WPURLS.adminUrl + 'admin-ajax.php';

  jQuery(document).ready(function($) {
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=update_dashboard&location="+ location,
      success: function( response ){
        if(response == 0){
          console.log("The database query did not return any donors.");
        } else {
          var table = $('#navbb_active_donor_table').DataTable();
          //var responseLength = Object.keys(response).length;
          if ( Object.keys( response ).length == 1 ) {
            table.clear();
            table.row.add( response[0] ).draw();
          } else {
            table.clear();
            table.rows.add( response ).draw();
          }
        }
      }
    });
  })
}
