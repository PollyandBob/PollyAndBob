

var UserSettingsViewModel = ( function() {

    function UserSettingsViewModel(
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

    UserSettingsViewModel.prototype.doSearch = function() {
        
        return false;
    };

    // For jQuery UI Autocomplete binding
    UserSettingsViewModel.prototype.fenRetrieveSuggestions = function(searchTerm, sourceArray) {
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

    UserSettingsViewModel.prototype.anchorNoOp = function( data, event ) {
        event.preventDefault();
    };
    
    UserSettingsViewModel.prototype.inputPressEnter = function(data, event) {
           return true;
   };

   return UserSettingsViewModel;

}).call(this);


