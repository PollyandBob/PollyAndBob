$(document).ready(function() {

    function submitForm() {
        
    }

    $('form.form-settings').submit(function(e) {

       

    });
    
    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });    
    
    $('#fenchy_userbundle_user_locationtype_location_location').change(function(e){
        
        setLocation();
        
    });
     $('#fenchy_regularuserbundle_usergroup_locationtype_location_location').change(function(e){
                    setLocation();
                }); 

});
