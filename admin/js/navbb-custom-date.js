/*! jQuery v1.12.4 | (c) jQuery Foundation | jquery.org/license */

//This adds the datepicker functionality to Date inpput fields with the class custom_date
jQuery(document).ready(function($) {
  $('.custom_date').datepicker({
    dateFormat : 'yy-mm-dd',
    changeMonth: true,
    changeYear: true
  });
});
