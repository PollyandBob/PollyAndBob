

var UserContactsViewModel = ( function() {

    function UserContactsViewModel(
        gcoder, 
        baseSearchViewUri
    ) {
        var self = this;
        self.gGeocoder = gcoder;
        self.baseSearchViewUri = baseSearchViewUri;
    
        self.putStateSupport = ko.computed( function() {
            if (('replaceState' in window.history) && window.history['replaceState'] !== null)
                return true;
            else
                return false;
        });
    
        self.fcGFormattedAddressVal = ko.observable()
        self.fcGFormattedAddress = ko.computed({
            read: self.fcGFormattedAddressVal,
            write: function(value) {
                self.fcGLat(null);
                self.fcGLng(null);
                self.fcGFormattedAddressVal(value);
            }
        });
        self.fcGLat = ko.observable(null);
        self.fcGLng = ko.observable(null);
    
        self.fcPhrase = ko.observable();
    
        // For jQuery UI Autocomplete binding
        self.fenSuggestionsArr = ko.observableArray([]);
        // END - For jQuery UI Autocomplete binding

        self.toGoFilterUri = ko.computed( {
            read: function() {
                var uriComponents = new Array();
                var uriFromCriteria = '';

                var fcGLat = self.fcGLat();
                var fcGLng = self.fcGLng();

                if ( fcGLat && fcGLng ) {
                    uriComponents.push('lt='+self.fcGLat());
                    uriComponents.push('lg='+self.fcGLng());
                }

                var fcPhrase = self.fcPhrase();
                if ( fcPhrase && fcPhrase.length > 0) {
                    uriComponents.push('phr='+window.encodeURIComponent(fcPhrase));
                }
            
                uriFromCriteria = uriComponents.join('/');
                return uriFromCriteria;
            }
        });
    }

    UserContactsViewModel.prototype.doSearch = function() {
        if ( ( this.fcGLat == null || this.fcLng == null ) && 
            this.fcGFormattedAddress() && this.fcGFormattedAddress().length > 0 ) {
            this.gGeocoder.geocode( {'address': this.fcGFormattedAddress() }, function(results, status) {
                if (status == google.maps.GeocoderStatus.OK ) {
                    this.fcGFormattedAddress(results[0].formatted_address);
                    this.fcGLat(results[0].geometry.location.lat());
                    this.fcGLng(results[0].geometry.location.lng());
                }
                else {
                    this.fcGFormattedAddress('');
                    this.fcGLat(null);
                    this.fcGLng(null);  
                }
            });
        };
        var resultLocation = this.baseSearchViewUri;
        var toGoFilterUriVal =  this.toGoFilterUri();
        if ( toGoFilterUriVal.length > 0 ) {
            if ( !this.putStateSupport() ) {
                resultLocation = resultLocation + '#';
            }
            resultLocation = resultLocation + '/' + toGoFilterUriVal;
        }
        window.document.location.pathname = resultLocation;
    };

    // For jQuery UI Autocomplete binding
    UserContactsViewModel.prototype.fenRetrieveSuggestions = function(searchTerm, sourceArray) {
        var request = jQuery.ajax({
            url: "/app_dev.php/listing/acsuggest",
            type: "POST",
            data: {phrase: searchTerm},
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
                converters: { 'application json': jQuery.parseJSON   },   
            });

        request.done(function( data, textStatus, jqXHR ) {
            sourceArray(data);
        });

        request.fail(function( jqXHR, textStatus, errorThrown ) {
        });
    };
    // END - For jQuery UI Autocomplete binding    

    UserContactsViewModel.prototype.anchorNoOp = function( data, event ) {
        event.preventDefault();
    };
    
    UserContactsViewModel.prototype.inputPressEnter = function(data, event) {
       var self = this;
       if ( event.keyCode == 13 )
           self.doSearch( false );
       else
           return true;
   };

   return UserContactsViewModel;

}).call(this);


