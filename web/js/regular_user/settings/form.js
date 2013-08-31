$(document).ready(function() {

    function submitForm() {
        $('form.form-settings').unbind('submit');
        $('form.form-settings').submit();
    }

    $('form.form-settings').submit(function(e) {

        console.log(e.keyCode);

        var res = false;

        var input = $('#fenchy_userbundle_user_locationtype_location_location');

        if (input.is('input')) {

            if (!userSettingsViewModel.fcGLat() || !userSettingsViewModel.fcGLng() || !userSettingsViewModel.fcGFormattedAddress()) {

                var ajaxgif = $('<span />').attr('class', 'ajaxgif').append($('<img />').attr('src', '/images/ajaxgif.gif').attr('alt', '')).appendTo(input.parent());
                var submitBtn = $('input[type="submit"]', 'form.form-settings').attr('disabled', 'disabled');

                geocoder.geocode({'address': input.val()}, function(results, status) {
                    if (status == 'OK') {

                        $('#fenchy_userbundle_user_locationtype_location_location').val(results[0].formatted_address);
                        $('#fenchy_userbundle_user_locationtype_location_latitude').val(results[0].geometry.location.lat);
                        $('#fenchy_userbundle_user_locationtype_location_longitude').val(results[0].geometry.location.lng);

                        $(ajaxgif).remove();
                        submitForm();
                        return;

                    } else {
                        input.val('');
                        res = false;

                        //TODO: display error location etc.
                        alert('Location is invalid !');

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

        return res;

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

});