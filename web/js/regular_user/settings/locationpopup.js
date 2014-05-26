$(document).ready(function() {


    function submitForm() {
        $('form.form-popup-location').unbind('submit');
        $('form.form-popup-location').submit();
    }

    $('form.form-popup-location').submit(function() {

        var res = false;

        var input = $('#fenchy_userbundle_user_locationtype_location_location');

        if (input.is('input')) {

            if (!userSettingsViewModel.fcGLat() || !userSettingsViewModel.fcGLng() || !userSettingsViewModel.fcGFormattedAddress()) {

                var submitBtn = $('input[type="submit"]', 'form.form-popup-location').attr('disabled', 'disabled');
                
                var ajaxgif = $('<span />').attr('class', 'ajaxgif').append($('<img />').attr('src', '/images/ajaxgif.gif').attr('alt', '')).appendTo(submitBtn.closest('footer'));

                geocoder.geocode({'address': input.val()}, function(results, status) {
                    if (status == 'OK') {

                        $('#fenchy_userbundle_user_locationtype_location_location').val(results[0].formatted_address);
                        $('#fenchy_userbundle_user_locationtype_location_latitude').val(results[0].geometry.location.lat);
                        $('#fenchy_userbundle_user_locationtype_location_longitude').val(results[0].geometry.location.lng);

                        
                        res = true;

                    } else {
                        //input.val('');
                        res = false;

                        //TODO: display error location etc.
                        //alert('Location is invalid !');

                        $(ajaxgif).remove();
                        $(submitBtn).removeAttr('disabled');
                    }
                });

            } else {

                $('#fenchy_userbundle_user_locationtype_location_location').val(userSettingsViewModel.fcGFormattedAddress());
                $('#fenchy_userbundle_user_locationtype_location_latitude').val(userSettingsViewModel.fcGLat());
                $('#fenchy_userbundle_user_locationtype_location_longitude').val(userSettingsViewModel.fcGLng());

                res = true;
            }

        } else {
            res = true;
        }


        if(res) {
            
            var action = $('form.form-popup-location').attr('action');
            var data = $('form.form-popup-location').serialize();
            
            $.post(action, data, function(res){
                
                if(typeof res.saved != 'undefined') {
                    
                    if(res.saved == 1) {
                        $('#sitebody','html').children().remove();
                        var content = $(res.content);
                        
                        $('#sitebody','html').append(content);
                    }
                    
                }
                
            });
        }
        
        return false;

    });
    

    $(window).keydown(function(event){
      if(event.keyCode == 13) {
        event.preventDefault();
        return false;
      }
    });        

});