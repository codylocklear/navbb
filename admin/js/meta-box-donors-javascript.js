//All javascript functions used in donor post metaboxes

function calculateAge(val) {
  var today = new Date();
  var birthDate = new Date(val);
  var age = today.getFullYear() - birthDate.getFullYear();
  var m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
      age--;
  }
  if (isNaN(age)){
    document.getElementById("p1").innerHTML = "Not Set";
  } else{
    document.getElementById("p1").innerHTML = age + " years old";
  }
}

function ownerTypeChange(val){
  if(val == "Kennel Club"){
    document.getElementById("row_first_name").setAttribute("style","display:none;");
  } else{
    document.getElementById("row_first_name").setAttribute("style","display:inline-block;");
  }
}

//This updates the displayed donor ID as the owner is changed. The Ajax process is located in ajax-callbacks.php
function ownerInternalID( ){

  var owner_id = document.getElementById("owner_id").value;
  var adminURL = navbb_WPURLS.adminUrl + 'admin-ajax.php';

  jQuery(document).ready(function($) {
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=get_owner_internal_id&owner_id="+ owner_id,
      success: function( response ){
        if(response == 0){
          console.log("No can do Jack");
        } else {
          document.getElementById("internalOwnerID").innerHTML = response + "-";
        }
      }
    });
  })
}

function reorderDonations(val){
  // console.log("Reorder Donations Fired");
  // console.log(val);
  var adminURL = navbb_WPURLS.adminUrl + 'admin-ajax.php';
  var donor_id = val;

  jQuery(document).ready(function($) {
    jQuery.ajax({
      method:'POST',
      url: adminURL,
      data: "action=reorder_donations&DonorID="+ donor_id,
      success: function( response ){
        if(response == 0){
          //console.log("The database query did not return any donations.");
          var x = "The database query did not return any donations."
          alert(x);
        } else {
          // console.log("It updated " + response);
          var x = "The donation count successfully updated!"
          alert(x);
          location.reload();
        }
      }
    });
  })
}



function donorStatusChange(val){
  if(val == "retired"){
    document.getElementById("row_date_retired").setAttribute("style","display:block;");
  } else {
    document.getElementById("row_date_retired").setAttribute("style","display:none;");
  }
}

function updateSpecies(val) {
//This toggles elements on our form based on species
  jQuery(document).ready(function($) {
    if (val == "Canine") {
      //Hide these elements
        $(".feline-lab").hide();
      //Show these elements
        $(".canine-lab").show();
    } else {
      //Hide these elements
        $(".canine-lab").hide();
      //Show these elements
        $(".feline-lab").show();
    }
  })
}

function updateReproduction(val) {
  jQuery(document).ready(function($) {
    if (val == "Yes") {
      //Hide these elements
      $(".navbb-reproduction-no").hide();
    } else if (val == "No") {
      //Show these elements
      $(".navbb-reproduction-no").show();
    } else {
      //nothing
    }
  })
}

function labToDefault(){
  document.getElementById("cbc").value = "WNL";
  document.getElementById("chem").value = "WNL";
  document.getElementById("lytes").value = "WNL";
  document.getElementById("hwt").selectedIndex = 2;

  //These are all the Positive/Negative Tests
  document.getElementById("antibody").selectedIndex = 2;
  document.getElementById("anaplasma").selectedIndex = 2;
  document.getElementById("bartonella").selectedIndex = 2;
  document.getElementById("babesia").selectedIndex = 2;
  document.getElementById("ehrlichia").selectedIndex = 2;
  document.getElementById("hepatozoon").selectedIndex = 2;
  document.getElementById("leishmania").selectedIndex = 2;
  document.getElementById("neoricketsia").selectedIndex = 2;
  document.getElementById("rmsf").selectedIndex = 2;
  document.getElementById("mycoplasma").selectedIndex = 2;
  document.getElementById("lyme").selectedIndex = 2;
  document.getElementById("brucella").selectedIndex = 2;
  document.getElementById("feline_aids").selectedIndex = 2;
}
