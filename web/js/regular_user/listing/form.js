$(document).ready(function() {


    function submitForm() {
        $('form.listing-form').unbind('submit');
        $('form.listing-form').submit();
    }

    $('form.listing-form').submit(function() {

        var res = false;

        if ($('#location_gapi').is('input')) {

            if (!landingPageViewModel.fcGLat() || !landingPageViewModel.fcGLng() || !landingPageViewModel.fcGFormattedAddress()) {

                var ajaxgif = $('<span />').attr('class', 'ajaxgif').append($('<img />').attr('src', '/images/ajaxgif.gif').attr('alt', '')).appendTo($('#location_gapi').parent());
                var submitBtn = $('input[type="submit"]', 'form.listing-form').attr('disabled', 'disabled');

                geocoder.geocode({'address': $('#location_gapi').val()}, function(results, status) {
                    if (status == 'OK') {

                        $('#fenchy_noticebundle_noticetype_location_location').val(results[0].formatted_address);
                        $('#fenchy_noticebundle_noticetype_location_latitude').val(results[0].geometry.location.lat);
                        $('#fenchy_noticebundle_noticetype_location_longitude').val(results[0].geometry.location.lng);

                        $(ajaxgif).remove();
                        submitForm();
                        return;

                    } else {
                        $('#location_gapi').val('');
                        res = false;

                        //TODO: display error location etc.
                        alert('Location is invalid !');

                        $(ajaxgif).remove();
                        $(submitBtn).removeAttr('disabled');
                    }
                });

            } else {

                $('#fenchy_noticebundle_noticetype_location_location').val(landingPageViewModel.fcGFormattedAddress());
                $('#fenchy_noticebundle_noticetype_location_latitude').val(landingPageViewModel.fcGLat());
                $('#fenchy_noticebundle_noticetype_location_longitude').val(landingPageViewModel.fcGLng());

                res = true;
            }

        } else {
            res = true;
        }

        return res;

    });
    
    function refreshSelectCategory(selector) {
        
        var selected = $(selector).find('option:selected');
        var val = $(selected).text();
        
        var wrapper = $(selector).closest('div.select-wrapper');
        var placement = $('.select-value', wrapper);
        
        $(placement).text(val);        
        
    }
    
    $('select[id^="fenchy_noticebundle_noticetype_type_value"]:visible', 'div.select-wrapper').change(function(){
        refreshSelectCategory(this);
    });
    
    refreshSelectCategory($('select[id^="fenchy_noticebundle_noticetype_type_value"]:visible', 'div.select-wrapper'));

});