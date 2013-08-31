

var ListingShowViewModel = ( function() {

    function ListingShowViewModel(
        gcoder, 
        baseSearchViewUri, 
        pagination,
        usersOwnListing,
        displayUserId,
        loggedInUserId,
        initialReviewsP,
        initialReviewsN,
        routes,
        listingId
    ) {
        var self = this;
        self.gGeocoder = gcoder;
        self.baseSearchViewUri = baseSearchViewUri;
        self.routes = routes;
        self.listingId = listingId;
        self.displayUserId = displayUserId;
    
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
    
        self.usersOwnListing = usersOwnListing;
        self.loggedInUserId = loggedInUserId;
        self.pagination = pagination;

        self.reviewsPCount = ko.observable( initialReviewsP.count );
        if ( initialReviewsP.list.length > self.pagination ) {
            initialReviewsP.list.pop();
            self.reviewsPLoadMore = ko.observable(true);
        } else
            self.reviewsPLoadMore = ko.observable(false);
        self.reviewsP = ko.observableArray( initialReviewsP.list );
        self.reviewsPRangeMin = 0;
        self.reviewsPRangeMax = self.reviewsPRangeMin + self.pagination - 1;
        
        self.reviewsNCount = ko.observable( initialReviewsN.count );
        if ( initialReviewsN.list.length > self.pagination ) {
            initialReviewsN.list.pop();
            self.reviewsNLoadMore = ko.observable(true);
        } else
            self.reviewsNLoadMore = ko.observable(false);
        self.reviewsN = ko.observableArray( initialReviewsN.list );
        self.reviewsNRangeMin = 0;
        self.reviewsNRangeMax = self.reviewsNRangeMin + self.pagination - 1;
                
        self.leaveReviewFormLoaded = false;


    
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

    ListingShowViewModel.prototype.doSearch = function() {
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
    ListingShowViewModel.prototype.fenRetrieveSuggestions = function(searchTerm, sourceArray) {
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
    
    
    ListingShowViewModel.prototype.loadLeaveReviewFrom = function() {
        var self = this;
        
        if ( !self.leaveReviewFormLoaded && !self.usersOwnListing && self.loggedInUserId) {
            var request = jQuery.ajax({
                url: self.routes.fenchy_regular_user_reviewseditform,
                type: "POST",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
            });

            self.leaveReviewFormLoaded = true; 
            $('#tabs-1').prepend( spinnerHTML );

            request.done(function( html, textStatus, jqXHR ){ 
                self.reviewAttachForm(html, null);
            });
        }
    };
    
    ListingShowViewModel.prototype.loadEditReview = function( data, event, parent ) {
        var self = parent;
        var articleElement = $(event.target).parents('article');

        if (  !self.leaveReviewFormLoaded && !self.usersOwnProfile  ) {
            var request = jQuery.ajax({
                url: self.routes.fenchy_regular_user_reviewseditform + '/' + data.id.toString(),
                type: "POST",
                contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
            });
            self.leaveReviewFormLoaded = true; 
            $(articleElement[0]).replaceWith( spinnerHTML );
            
            request.done(function( html, textStatus, jqXHR ){
                self.reviewAttachForm(html, data.id.toString());
            });
        }
    };
    
    ListingShowViewModel.prototype.reviewAttachForm = function(html, editId) {
        var self = this;
        
        $('#spinner-div').replaceWith( html );
        $('form#leave-review a.leave-review-submit').click( function(event) { 
            self.reviewSubmit.apply(self, [event, editId]); 
        });
        $('form#leave-review a.leave-review-cancel').click( function(event) {
            self.reviewCancel.apply(self, [event]);
        });
    };
    
    ListingShowViewModel.prototype.reviewSubmit = function(event, editId) {
        var self = this;
        var req_url = '';
        if (editId)
            req_url = self.routes.fenchy_regular_user_reviewssave + '/' + editId;
        else
            req_url = self.routes.fenchy_regular_user_reviewssave+'//'+self.listingId.toString();
        
        var request = jQuery.ajax({
            url: req_url,
            type: "POST",
            data: $('form#leave-review').serialize(),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        });

        request.done(function( html, textStatus, jqXHR ) {
            var validate = jqXHR.getResponseHeader('validate');
            if ( validate === 'pass' ) {
                self.reloadReviews( function() { $('#spinner-div').detach(); } );
                self.leaveReviewFormLoaded = false;                
            }
            else {
                self.reviewAttachForm(html, editId);                
            }
        });
        
        $('form#leave-review').replaceWith( spinnerHTML );
        event.preventDefault();
    };
    
    ListingShowViewModel.prototype.reviewCancel = function(event) {
        var self = this;
        $('form#leave-review').detach();
        self.leaveReviewFormLoaded = false;
        event.preventDefault();
    };
    
    
    
    ListingShowViewModel.prototype.reloadReviews = function( doneCallback ) {
        var self = this;

        var requestP = jQuery.ajax({
            url: self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId + '/' + self.listingId,
            type: "POST",
            data: 'reload=reviews&rtype=p',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        });
        
        var requestN = jQuery.ajax({
            url: self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId + '/' + self.listingId,
            type: "POST",
            data: 'reload=reviews&rtype=n',
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'
        });

        requestP.done(function( data, textStatus, jqXHR ){
            if ( data.list.length > self.pagination ) {
                self.reviewsPLoadMore(true);
                data.list.pop();
            }
            else {
                self.reviewsPLoadMore(false);
            }
            self.reviewsP( data.list );
            self.reviewsPCount( data.count );
            self.reviewsPRangeMin = 0;
            self.reviewsPRangeMax = self.pagination - 1;
        });

        requestN.done(function( data, textStatus, jqXHR ){
            if ( data.list.length > self.pagination ) {
                self.reviewsPLoadMore(true);
                data.list.pop();
            }
            else {
                self.reviewsNLoadMore(false);
            }
            self.reviewsN( data.list );
            self.reviewsNCount( data.count );
            self.reviewsNRangeMin = 0;
            self.reviewsNRangeMax = self.pagination - 1;
        });
        
        jQuery.when(requestP, requestN).done(function() {
            if ( typeof doneCallback == 'function' )
                doneCallback();
        });
    }

    ListingShowViewModel.prototype.anchorNoOp = function( data, event ) {
        event.preventDefault();
    };
    
    
    ListingShowViewModel.prototype.loadMorePReviews = function() {
        var self = this;
        var request = jQuery.ajax({
            url:  self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId + '/' + self.listingId,
            type: "POST",
            data: 'loadmore=reviews&rtype=p'+
                  '&min='+(self.reviewsPRangeMax+1).toString()+
                  '&max='+(self.reviewsPRangeMax+self.pagination).toString(),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'        
        });
        request.done(function( data, textStatus, jqXHR) {
            if ( data.list.length > self.pagination ) {
                self.reviewsPLoadMore(true);
                data.list.pop();
            }
            else {
                self.reviewsPLoadMore(false);
            }
            self.reviewsP( self.reviewsP().concat(data.list) );
            self.reviewsPCount( data.count );

            self.reviewsPRangeMin = self.reviewsPRangeMin + self.pagination;
            self.reviewsPRangeMax = self.reviewsPRangeMax + self.pagination;

            $('#spinner-div').detach();
        });

        $('div#tabs-1  .load-more-button').insertBefore( spinnerHTML );        
    };
    
    ListingShowViewModel.prototype.loadMoreNReviews = function() {
        var self = this;
        var request = jQuery.ajax({
            url:  self.routes.fenchy_regular_user_reviewslist + '/' + self.displayUserId + '/' + self.listingId,
            type: "POST",
            data: 'loadmore=reviews&rtype=n'+
                  '&min='+(self.reviewsNRangeMax+1).toString()+
                  '&max='+(self.reviewsNRangeMax+self.pagination).toString(),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8'        
        });
        request.done(function( data, textStatus, jqXHR) {
            if ( data.list.length > self.pagination ) {
                self.reviewsNLoadMore(true);
                data.list.pop();
            }
            else {
                self.reviewsNLoadMore(false);
            }
            self.reviewsN( self.reviewsN().concat(data.list) );
            self.reviewsNCount( data.count );

            self.reviewsNRangeMin = self.reviewsNRangeMin + self.pagination;
            self.reviewsNRangeMax = self.reviewsNRangeMax + self.pagination;

            $('#spinner-div').detach();
        });

        $('div#tabs-2  .load-more-button').insertBefore( spinnerHTML );        
    };
    
    ListingShowViewModel.prototype.inputPressEnter = function(data, event) {
       var self = this;
       if ( event.keyCode == 13 )
           self.doSearch( false );
       else
           return true;
   };

   return ListingShowViewModel;

}).call(this);


