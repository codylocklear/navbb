//Autocomplete functions for both donors and owners

//Donors
jQuery(document).ready(function($) {
  var temp = WPURLS['siteurl'] + "/wp-admin/admin-ajax.php";
  $('.autocomplete_donors').autocomplete({
    autoFocus: true,
    minLength: 2,
    source: function(name, response) {
      jQuery.ajax({
        type: 'POST',
        dataType: 'json',
        url: temp,
        data: 'action=get_donor_names&name='+name.term,
        success: function(data) {
          //If there are no matches, display no matches and close autocomplete
          if(!data.length){
            var result = [{label: 'No matches', value:response.term}];
            $('.autocomplete_donors').autocomplete("close");
            response(result);
          } else {
            response(jQuery.map(data, function (item) {
              return {
                label: item.label,
                value: item.value
              }
            }));
          }
        }
      });
    },
    select: function(event, ui){
      //After select, label is put in box and id is put in hidden field
      event.preventDefault();
      $('.autocomplete_donors').val(ui.item.label);
      $('#donor_id').val(ui.item.value);
    },
    change: function (event, ui) {
    //If clicked away without choosing option, input loses value
      if(!ui.item){
          $('.autocomplete_donors').val('');
          $('#donor_id').val('');
      }
    },
    focus: function(event, ui) {
        event.preventDefault();
    }
  });
});


//Owners
jQuery(document).ready(function($) {
  $(document).on('click', '.autocomplete_owners', function(){
    var temp = WPURLS['siteurl'] + "/wp-admin/admin-ajax.php";
    $('.autocomplete_owners').autocomplete({
      autoFocus: true,
      minLength: 2,
      source: function(name, response) {
        jQuery.ajax({
          type: 'POST',
          dataType: 'json',
          url: temp,
          data: 'action=get_owner_names&name='+name.term,
          success: function(data) {
            console.log(data);
            if(!data.length){
              var result = [{label: 'No matches', value:response.term}];
              $('.autocomplete_owners').autocomplete("close");
              response(result);
            } else {
              response(jQuery.map(data, function (item) {
                return {
                  label: item.label,
                  value: item.value
                }
              }));
            }
          }
        });
      },
      select: function(event, ui){
        //After select, label is put in box and id is put in hidden field
        event.preventDefault();
        $('.autocomplete_owners').val(ui.item.label);
        $('#owner_id').val(ui.item.value);
      },
      change: function (event, ui) {
      //If clicked away without choosing option, input loses value
        if(!ui.item){
            $('.autocomplete_owners').val('');
            $('#owner_id').val('');
        }
      },
      focus: function(event, ui) {
          event.preventDefault();
      }
    });

  });
});
