$(document).ready(function() {

    function submitForm() {
        $('form.twitter-registration-form').unbind('submit');
        $('form.twitter-registration-form').submit();
    }

    $('form.twitter-registration-form').submit(function() {

        var res = false;

        if ($('#fenchy_userbundle_twitter_location_location').is('input')) {

            if (!landingPageViewModel.fcGLat() || !landingPageViewModel.fcGLng() || !landingPageViewModel.fcGFormattedAddress()) {

                var ajaxgif = $('<span />').attr('class', 'ajaxgif').append($('<img />').attr('src', '/images/ajaxgif.gif').attr('alt', '')).appendTo($('#fenchy_userbundle_twitter_location_location').parent());
                var submitBtn = $('input[type="submit"]', 'form.twitter-registration-form').attr('disabled', 'disabled');

                geocoder.geocode({'address': $('#fenchy_userbundle_twitter_location_location').val()}, function(results, status) {
                    if (status == 'OK') {

                        $('#fenchy_userbundle_twitter_location_location').val(results[0].formatted_address);
                        $('#fenchy_userbundle_twitter_location_latitude').val(results[0].geometry.location.lat);
                        $('#fenchy_userbundle_twitter_location_longitude').val(results[0].geometry.location.lng);

                        $(ajaxgif).remove();
                        submitForm();
                        return;

                    } else {
                        $('#fenchy_userbundle_twitter_location_location').val('');
                        res = false;

                        //TODO: display error location etc.
                        alert('Location is invalid !');

                        $(ajaxgif).remove();
                        $(submitBtn).removeAttr('disabled');
                    }
                });

            } else {

                $('#fenchy_userbundle_twitter_location_location').val(landingPageViewModel.fcGFormattedAddress());
                $('#fenchy_userbundle_twitter_location_latitude').val(landingPageViewModel.fcGLat());
                $('#fenchy_userbundle_twitter_location_longitude').val(landingPageViewModel.fcGLng());

                res = true;
            }

        } else {
            res = true;
        }

        return res;

    });

});