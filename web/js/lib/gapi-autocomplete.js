
function GApiAutocomplete(inputElement, viewModel) {
// This option for street/city
    var options = { types: ['geocode'] };
// This option for Country/city
//    var options = { types: ['(regions)'] };
    var self = this;
    self.googleAutocomplete = new google.maps.places.Autocomplete(inputElement,options);
            
    google.maps.event.addListener(self.googleAutocomplete, 'place_changed', function() {
        var place = self.googleAutocomplete.getPlace();
        if ( !place.geometry ) return;
              
        viewModel.fcGFormattedAddress(place.formatted_address);
        viewModel.fcGLat(place.geometry.location.lat());
        viewModel.fcGLng(place.geometry.location.lng());
        if(typeof setLocation != 'undefined') {
            setLocation(place.formatted_address);
        }
        if($(inputElement).attr('id') === 'location')
            viewModel.doSearch( false );
    });
};